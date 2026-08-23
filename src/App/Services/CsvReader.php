<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use Generator;

class CsvReader
{
    public function readRows(string $file): Generator
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new \RuntimeException('Failed to open file');
        }
        $fp = fopen($file, "r");
        if (! $fp) {
            throw new ValidationException('File incorrect');
        }

        $headers = fgetcsv($fp) ?: [];
        yield 'headers' => $headers;

        while (($row = fgetcsv($fp)) !== false) {
            if ($row === null || $row === [null] || empty($row) || $row[0] === null) {
                continue;
            }
            yield 'row' => $row;
        }

        fclose($fp);
    }
}
