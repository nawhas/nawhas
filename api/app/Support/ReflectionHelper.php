<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

class ReflectionHelper
{
    public static function fqnFromFilePath(string $path): string
    {
        $class = trim(Str::replaceFirst(app()->path(), '', $path), DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return app()->getNamespace() . $class;
    }
}
