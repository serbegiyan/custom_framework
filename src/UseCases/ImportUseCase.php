<?php

namespace App\UseCases;

use App\Interfaces\DatabaseInterface;
use App\Services\AnalizerService;
use App\Services\ImporterService;
use App\ValueObjects\OrganizationId;

class ImportUseCase
{
    public function __construct(
        public DatabaseInterface $db,
        public AnalizerService $analizer,
        public ImporterService $importer,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function runTransaction(OrganizationId $organizationId, string $file): array
    {
        $this->db->beginTransaction();
        try {
            $skippedRows = $this->importer->import($organizationId, $file);
            $statics = $this->analizer->run([], $organizationId);
            $this->db->commit();
            return ['statics' => $statics, 'skippedRows' => $skippedRows];
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
