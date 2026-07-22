<?php

namespace App\Services;

use App\Core\Interfaces\DatabaseInterface;
use App\Models\User;

class AnalizerService
{
    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>|object>
     */
    public function run(array $filters, DatabaseInterface $db): array
    {
        $inputs = $filters;

        $sql = 'SELECT * FROM users WHERE 1=1 ';
        $params = [];

        $map_rules = [
            'country' => 'equals',
            'city' => 'equals',
            'is_active' => 'equals',
            'gender' => 'equals',
            'has_children' => 'equals',
            'family_status' => 'equals',
            'salary' => 'range',
            'birth_date' => 'range',
            'registration_date' => 'range'
        ];

        foreach ($map_rules as $column => $type) {
            switch ($type) {
                case 'equals':
                    if ((isset($inputs[$column])) and ($inputs[$column] !== '')) {
                        $sql .= " AND $column = :$column ";
                        $params[':' . $column] = $inputs[$column];
                    }
                    break;
                case 'range':
                    $fromKey = $column . '_from';
                    $toKey = $column . '_to';

                    if (!empty($inputs[$fromKey])) {
                        $sql .= " AND $column >= :$fromKey ";
                        $params[':' . $fromKey] = $inputs[$fromKey];
                    }if (!empty($inputs[$toKey])) {
                        $sql .= " AND $column <= :$toKey ";
                        $params[':' . $toKey] = $inputs[$toKey];
                    }
                    break;
            }
        }

        $users = $db->select($sql, $params, User::class);

        return $users;

    }
}
