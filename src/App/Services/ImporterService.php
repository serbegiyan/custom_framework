<?php

namespace App\Services;

use App\DTO\CsvRow;
use App\Exceptions\ValidationException;
use App\Repositories\StatisticRepository;
use App\Validators\CsvValidator;
use App\ValueObjects\OrganizationId;

class ImporterService
{
    public function __construct(
        public CsvValidator $validator,
        public StatisticRepository $statRep,
        public CsvReader $csvReader,
    ) {
    }
    /**
     * @return array<int, string>
     */
    public function import(OrganizationId $organizationId, string|null $file, int $chunkSize = 500): array
    {
        $chank = [];
        $skippedRowsErrors = [];
        $fileLine = 1;
        $rowCount = 0;
        $headers = [];
        /** @var array<int, string> $headers */
        foreach ($this->csvReader->readRows($file) as $key => $value) {
            if ($key === 'headers') {
                $headers = $value;
            } else {
                $row = $value;
                $fileLine++;
                try {
                    /** @var CsvRow $dto */
                    $dto = $this->validator->validate($row, $headers);
                    $batch = $dto->toDatabaseArray();
                    $chank[] = $batch;
                    $rowCount++;
                } catch (ValidationException $message) {
                    $skippedRowsErrors[$fileLine] = 'Строка ' . $fileLine .': ' . $message->getMessage();
                }

                if ($rowCount == $chunkSize) {
                    $this->statRep->insertBatch($chank, $organizationId->orgId);
                    $chank = [];
                    $rowCount = 0;
                }
            }
        }
        if (!empty($chank)) {
            $this->statRep->insertBatch($chank, $organizationId->orgId);
        }
        return $skippedRowsErrors;
    }
}
