<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_all_stored_event_classes_mapped_explicitly(): void
    {
        $files = Finder::create()->in(app_path())->name('*.php');

        collect($files)
            ->reject(fn (SplFileInfo $file) => in_array($file->getPathname(), $this->getIgnoredFiles()))
            ->map(fn (SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
            ->filter(fn (string $class) => is_subclass_of($class, ShouldBeStored::class))
            ->filter(fn (string $class) => $this->isInstantiable($class))
            ->each(fn (string $class) => $this->assertMappingExists($class));
    }

    private function isInstantiable(string $class): bool
    {
        $reflection = new ReflectionClass($class);

        return $reflection->isInstantiable();
    }

    private function fullQualifiedClassNameFromFile(SplFileInfo $file): string
    {
        $class = trim(Str::replaceFirst(app()->path(), '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return app()->getNamespace() . $class;
    }

    private function assertMappingExists(string $class): void
    {
        $mappings = config('event-sourcing.event_class_map', []);

        $this->assertContains($class, $mappings);

        $occurrences = collect($mappings)
            ->filter(fn ($value, $key) => $value === $class)
            ->toArray();

        $this->assertCount(1, $occurrences);
    }

    /**
     * @return array<string>
     */
    private function getIgnoredFiles(): array
    {
        return [
            app_path('Support/helpers.php'),
        ];
    }
}
