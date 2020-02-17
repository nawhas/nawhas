<?php

declare(strict_types=1);

namespace App\Console\Helpers;

class WriteUpCleaner
{
    public function cleanup(string $contents): string
    {
        // First, we're going to clean up the line endings.
        $contents = str_replace("\r\n", "\n", $contents);

        // And now, let's remove junk lines.
        $contents = $this->removeJunkLines($contents);

        // Now, let's trim the newlines from the top and bottom.
        $contents = trim($contents);

        // Next, we'll clean up extra empty lines.
         $contents = preg_replace("/(\n{5,})/", "\n\n", $contents);

        // Finally, we'll trim extra whitespace at the end of lines.
        $contents = collect(explode("\n", $contents))
            ->map(fn ($line) => rtrim($line))
            ->join("\n");

        return $contents . "\n"; // Preserve a new line at the end of the file.
    }

    private function removeJunkLines(string $contents): string
    {
        /*
         Replaces something that looks like:
         **_

         Nai aundha chain

         **_
        */
        $contents = preg_replace("/(\*\*_\n\n.+\n\n\*\*_)/", '', $contents);

        /*
         Replaces something that looks like:
         **Nai aundha chain audio file**
         */
        $contents = preg_replace("/(\*\*_?.+\sAUDIO( FILE)?\s?_?\*\*)/i", '', $contents);

        /*
         Replaces something that looks like:
         **_EY MEREY BEY KAFAN EY GHARIBUL WATAN_**
         */
        $contents = preg_replace("/(\*\*_?.+_?\*\*)/", '', $contents);

        /*
         Replaces something that looks like:
         **_Shaam chali

         **_
         */
        $contents = preg_replace("/(\*\*_?.+\n\n\*\*_)/", '', $contents);

        // Replace any line that starts with a #
        $contents = preg_replace("/^(#.*\n)/m", '', $contents);

        // Replace any line that only contains an _
        $contents = preg_replace("/^\S(\n)$/m", '', $contents);


        return $contents;
    }
}
