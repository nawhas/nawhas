<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Tests\Feature\Events\CoversEvent;
use Tests\TestCase;

use function collect;
use function str_contains;

class EventCoverageTest extends TestCase
{
    private const IGNORED_EVENTS = [
        'auth.login',
        'auth.logout',
        'auth.password.reset.requested',
    ];

    /**
     * @test
     */
    public function it_verifies_all_events_are_covered_by_a_test(): void
    {
        $finder = Finder::create()
            ->in(base_path('tests/Feature/Events'))
            ->name('*Test.php')
            ->exclude(__FILE__);

        $coveredEvents = collect($finder)
            ->map(fn (SplFileInfo $file) => $this->getFqn($file->getRealPath()))
            ->flatMap(fn (string $class) => $this->getCoveredEvents($class));

        $this->collectDefinedEvents()
            ->each(fn (string $event) => $this->assertContains($event, $coveredEvents->all()));
    }

    private function getCoveredEvents(string $class): Collection
    {
        $reflection = new ReflectionClass($class);

        return collect($reflection->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method) => $method->getDocComment() && str_contains($method->getDocComment(), '@test'))
            ->flatMap(fn (ReflectionMethod $method) => $method->getAttributes(CoversEvent::class))
            ->map(fn (ReflectionAttribute $attribute) => $attribute->newInstance())
            ->map(fn (CoversEvent $attribute) => $attribute->event)
            ->values();
    }

    private function getFqn(string $path): string
    {
        $class = trim(Str::replaceFirst(base_path('tests'), '', $path), DIRECTORY_SEPARATOR);

        $class = str_replace(
            DIRECTORY_SEPARATOR,
            '\\',
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return 'Tests\\' . $class;
    }

    private function collectDefinedEvents(): Collection
    {
        return collect(config('event-sourcing.event_class_map'))
            ->keys()
            ->reject(fn (string $event) => in_array($event, self::IGNORED_EVENTS));
    }
}
