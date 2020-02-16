<?php

declare(strict_types=1);

namespace App\Console\Helpers;

use Illuminate\Support\Str;

class TrackNameIdentifier
{
    public function getTrackNameFromContents(string $contents): string
    {
        $contents = str_replace("\r\n", "\n", $contents);
        $contents = preg_replace("/ {2,}/", " ", $contents);
        $matches = [];

        $allowedChars = '[#().:,A-Za-z\- ]+';
        if (preg_match("/\*\*_\n\n({$allowedChars})\n\n\*\*_/", $contents, $matches)) {
            return $this->cleanUpName($matches[1]);
        }

        if (preg_match("/\*\*_({$allowedChars})\n\n\*\*_/", $contents, $matches)) {
            return $this->cleanUpName($matches[1]);
        }

        if (preg_match("/\*\*_?({$allowedChars})\sAUDIO( FILE)?\s?_?\*\*/i", $contents, $matches)) {
            return $this->cleanUpName($matches[1]);
        }

        if (preg_match("/^({$allowedChars})\sAUDIO( FILE)?\s?$/im", $contents, $matches)) {
            return $this->cleanUpName($matches[1]);
        }

        if (preg_match("/^\*\*_(.+)_\*\*$/m", $contents, $matches)) {
            return $this->cleanUpName($matches[1]);
        }

//        // Try trimming and grabbing the first line.
//        if ($match = $this->getNameFromFirstLine($contents)) {
//            return $this->cleanUpName($match);
//        }

        $message = "Unable to determine track name. Contents:\n";
        $message .= substr($contents, 0, 300);
        throw new \LogicException($message);
    }

    private function getNameFromFirstLine($contents): ?string
    {
        $trimmed = trim($contents);
        [$first] = explode("\n", $trimmed);
        if (!Str::startsWith($first, '*')) {
            return null;
        }
        return str_replace(['*', '_'], '', $first);
    }

    private function cleanUpName(string $name): string
    {
        $name = trim(Str::title($name));

        if (Str::startsWith($name, '# ')) {
            $name = Str::replaceFirst('# ', '', $name);
        }

        $name = str_replace(['*', '_'], '', $name);
        $name = preg_replace("/ {2,}/", ' ', $name);

        return trim($name);
    }
}
