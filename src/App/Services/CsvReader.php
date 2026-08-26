<?php

namespace App\Services;

use Generator;

class CsvReader
{
    public function readRows(string $file): Generator
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new \RuntimeException('Failed to open file');
        }
        $fp = fopen($file, "r");
        if(!$fp){
            throw new \RuntimeException('Failed to open file');
        }
        $headers = fgetcsv($fp) ?: [];
        yield 'headers' => $headers;

        while (($row = fgetcsv($fp)) !== false) {
            if ($row == null || $row == [null] || $row[0] == null) {
                continue;
            }
            yield 'row' => $row;
        }

        fclose($fp);
    }
}
