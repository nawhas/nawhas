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

    private GitHubManager $manager;
    private Cache $cache;

    public function __construct(GitHubManager $manager, Cache $cache)
    {
        $this->manager = $manager;
        $this->cache = $cache;
    }

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
            now()->addMinutes(45), // cache for 45 minutes
            function () {
                $installation = config('github.connections.private.installation');
                $token = $this->manager->api('apps')->createInstallationToken($installation);
                return $token['token'];
            }
        );

        $this->manager->authenticate($token, null,  Client::AUTH_HTTP_TOKEN);

        return $this->manager;
    }
}
