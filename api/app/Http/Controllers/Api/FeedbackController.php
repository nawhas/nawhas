<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use GrahamCampbell\GitHub\GitHubManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedbackController extends Controller
{
    private GitHubManager $github;

    public function __construct(GitHubManager $github)
    {
        $this->github = $github;
    }

    public function submit(FeedbackRequest $request): Response
    {
        if (env('GITHUB_TOKEN') === null) {
            return response()->noContent(); // For integration and staging environments without token.
        }

        $this->github->issue()->create(config('github.repository.user'), config('github.repository.repo'), [
            'title' => $this->generateTitle($request),
            'body' => $this->generateBody($request),
            'labels' => $this->generateLabels($request),
        ]);

        return response(null, Response::HTTP_CREATED);
    }

    private function generateTitle(Request $request): string
    {
        $type = $request->get('type');
        $summary = $request->get('summary');

        return "[{$type}] {$summary}";
    }

    private function generateBody(Request $request): string
    {
        $details = $request->get('details');
        $email = $request->get('email');

        if ($email) {
            // Prevent emails from being automatically scraped.
            $email = str_replace('@', '(at)', $email);
            $email = str_replace('.', '(dot)', $email);
        } else {
            $email = 'Anonymous';
        }

        return "$details\n\nSubmitted By: $email";
    }

    /**
     * @return array|string[]
     */
    private function generateLabels(Request $request): array
    {
        return ['feedback:' . $request->get('type')];
    }
}
