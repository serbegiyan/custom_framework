<?php

namespace App\Services;

use App\Services\Localization;
use App\Exceptions\ValidationException;
use App\Interfaces\DatabaseInterface;

class AuthService
{
    public function __construct(
        public DatabaseInterface $db,
        public Localization $local,
    ) {
    }

    public function registerUser(string $name, string $email, string $password): int
    {
        $sql = 'SELECT id FROM users WHERE email = ? LIMIT 1';

        if (trim($email) === '' || trim($password) === '' || trim($name) === '') {
            throw new ValidationException($this->local->translate('auth.invalid_values'));
        }

        $original = $this->db->select($sql, [$email]);
        if (!empty($original)) {
            throw new ValidationException($this->local->translate('auth.user_already_exists'));
        }
        $password = password_hash($password, PASSWORD_BCRYPT);

        $sqlIns = 'INSERT INTO users (name, email, password_hash)
        VALUES (?, ?, ?) RETURNING id';

        $id = $this->db->select($sqlIns, [$name, $email, $password]);

        /** @var array<int, array{id: int}> $id */
        $user_id = $id[0]['id'] ?? 0;
        return $user_id;
    }

    public function loginUser(string $email, string $password): int
    {
        if (trim($email) === '' || trim($password) === '') {
            throw new ValidationException('Invalid inputs values');
        }

        $sql = 'SELECT id, password_hash FROM users WHERE email = ? LIMIT 1';

        $res = $this->db->select($sql, [$email]);

        /** @var array{id: int, password_hash: string}|null $user */
        $user = $res[0] ?? null;

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            throw new ValidationException($this->local->translate('auth.invalid_credentials'));
        }
        $user_id = $user['id'];

        return $user_id;
    }
}
