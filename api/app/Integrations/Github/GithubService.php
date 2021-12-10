<?php

declare(strict_types=1);

namespace App\Integrations\Github;

use Github\Client;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Contracts\Cache\Repository as Cache;

class GithubService
{
    private const REPO_NAME = 'nawhas';
    private const REPO_OWNER = 'nawhas';
    private const CACHE_TTL = 45;

    public function __construct(
        private GitHubManager $manager,
        private Cache $cache
    ) {}

    public function createIssue(Issue $issue): void
    {
        if (!$this->enabled()) {
            return;
        }

        $this->authenticate()->issues()->create(self::REPO_OWNER, self::REPO_NAME, [
            'title' => $issue->getTitle(),
            'body' => $issue->getBody(),
            'labels' => $issue->getLabels(),
        ]);
    }

    public function enabled(): bool
    {
        return config('github.enabled') === true;
    }

    private function authenticate(): GitHubManager
    {
        $token = $this->cache->remember(
            'github.token',
            now()->addMinutes(self::CACHE_TTL), // cache for 45 minutes
            function () {
                $installation = config('github.connections.private.installation');
                $token = $this->manager->connection()->apps()->createInstallationToken($installation);
                return $token['token'];
            }
        );

        $this->manager->authenticate($token, null,  Client::AUTH_ACCESS_TOKEN);

        return $this->manager;
    }
}
