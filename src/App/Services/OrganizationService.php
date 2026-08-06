<?php

namespace App\Services;

use App\Interfaces\DatabaseInterface;
use App\ValueObjects\OrganizationId;

class OrganizationService
{
    public function __construct(
        public DatabaseInterface $db,
    ) {
    }

    public function getOrgId(int $user_id): ?OrganizationId
    {
        $sql = 'SELECT id FROM organizations WHERE owner_id = ? LIMIT 1 ';

        /** @var array<int, array<string, mixed>> $orgRes */
        $orgRes = $this->db->select($sql, [$user_id]);
        $orgData = $orgRes[0] ?? null;
        return $orgData ? new OrganizationId((int)$orgData['id']) : null;
    }

    public function getOwnerId(int $org_id): ?int
    {
        $sql = 'SELECT owner_id FROM organizations WHERE id = ? LIMIT 1 ';

        /** @var array<int, array<string, mixed>> $orgRes */
        $orgRes = $this->db->select($sql, [$org_id]);
        $orgOwner = $orgRes[0]['owner_id'] ?? null;
        return $orgOwner !== null ? (int)$orgOwner : null;
    }

    /**
     * @return array<int, array<string, mixed>|object>
     */
    public function getOrgList(int $user_id): array
    {
        $sql = 'SELECT name FROM organizations WHERE owner_id = ?';

        return $this->db->select($sql, [(int)$user_id]);
    }

    public function storeToDb(string $name, int $user_id): void
    {
        $sql = 'INSERT INTO organizations (name, owner_id)
        VALUES (?, ?)';
        $this->db->beginTransaction();
        try {
            $this->db->execute($sql, [$name, $user_id]);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException($e);
        }
    }

    public function updateToBd(string $name, int $orgId): void
    {
        $sql = 'UPDATE organizations SET name = ? WHERE id = ?';
        $this->db->beginTransaction();
        try {
            $this->db->execute($sql, [$name, $orgId]);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException($e);
        }
    }

    public function deleteFromBd(int $orgId): void
    {
        $sql = 'DELETE FROM organizations WHERE id = ?';
        $this->db->beginTransaction();
        try {
            $this->db->execute($sql, [$orgId]);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException($e);
        }
    }
}
