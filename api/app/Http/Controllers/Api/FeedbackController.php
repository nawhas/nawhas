<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Integrations\Github\GithubService;
use App\Integrations\Github\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedbackController extends Controller
{
    public function __construct(
        private GithubService $github
    ) {}

    public function submit(FeedbackRequest $request): Response
    {
        $issue = new Issue(
            $this->generateTitle($request),
            $this->generateBody($request),
            $this->generateLabels($request),
        );

        $this->github->createIssue($issue);

        return response()->make('', Response::HTTP_CREATED);
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
        return ['feedback', 'feedback:' . $request->get('type')];
    }
}
