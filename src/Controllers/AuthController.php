<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Exceptions\ValidationException;
use App\Interfaces\DatabaseInterface;

class AuthController
{
    public function __construct(
        public DatabaseInterface $db,
        public Request $request,
        public Session $session
    ) {
    }

    public function login(): void
    {
        $params = $this->request->getParams();
        $required = ['email', 'password'];

        foreach ($required as $key) {
            $value = $params[$key] ?? null;
            if (!is_string($value) or trim($value) === '') {
                throw new ValidationException('Invalid inputs values');
            }
        }
        $email = $params['email'];
        $sql = 'SELECT id, password_hash FROM users WHERE email = ? LIMIT 1';

        $res = $this->db->select($sql, [$email]);

        /** @var array{id: int, password_hash: string}|null $user */
        $user = $res[0] ?? null;

        if (! $user or ! password_verify($params['password'], $user['password_hash'])) {
            throw new ValidationException('Invalid email or password');
        }
        $user_id = $user['id'];
        $this->session->set('user_id', $user_id);

        echo 'You are logged in';
    }

    public function register(): void
    {
        $params = $this->request->getParams();
        $required = ['name', 'email', 'password'];

        $sql = 'SELECT id FROM users WHERE email = ? LIMIT 1';
        foreach ($required as $key) {
            $value = $params[$key] ?? null;
            if (!is_string($value) or trim($value) === '') {
                throw new ValidationException('Invalid inputs values');
            }
        }
        $name = $params['name'];
        $email = $params['email'];
        $original = $this->db->select($sql, [$email]);
        if (!empty($original)) {
            throw new ValidationException('User with this email has already exists');
        }
        $password = password_hash($params['password'], PASSWORD_BCRYPT);

        $sqlIns = 'INSERT INTO users (name, email, password_hash)
        VALUES (?, ?, ?)';

        $this->db->execute($sqlIns, [$name, $email, $password]);

        $sqlForSession = 'SELECT id FROM users WHERE email = ?';
        $id = $this->db->select($sqlForSession, [$email]);

        /** @var array<int, array{id: int}> $id */
        $user_id = $id[0]['id'] ?? 0;
        $this->session->set('user_id', $user_id);

        echo 'Registration complete succefully';
    }
}
