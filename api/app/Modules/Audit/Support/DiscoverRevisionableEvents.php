<?php

declare(strict_types=1);

namespace App\Modules\Audit\Support;

use App\Modules\Audit\Events\RevisionableEvent;
use Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class DiscoverRevisionableEvents
{
    private const CACHE_KEY = 'audit.revisionables';
    private array $directories;
    private string $basePath;
    private string $rootNamespace;

    public function __construct()
    {
        $this->basePath = app()->path();
        $this->rootNamespace = app()->getNamespace();
        $this->directories = [app_path('Modules')];
    }

    public function forget(): self
    {
        Cache::forget(self::CACHE_KEY);

        return $this;
    }

    public function discover(): array
    {
        return Cache::rememberForever(
            self::CACHE_KEY, function () {
            if (empty($this->directories)) {
                return [];
            }

            $files = (new Finder())->files()->in($this->directories);

            return collect($files)
                ->map(fn(SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
                ->filter(fn(string $class) => is_subclass_of($class, RevisionableEvent::class))
                ->values()
                ->all();
        });
    }

    private function fullQualifiedClassNameFromFile(SplFileInfo $file): string
    {
        $class = trim(Str::replaceFirst($this->basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return $this->rootNamespace . $class;
    }
}
