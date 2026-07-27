<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Interfaces\DatabaseInterface;

class OrganizationController
{
    public function __construct(
        public DatabaseInterface $db,
        public Request $request,
        public Session $session
    ) {
    }

    public function index(): void
    {
        $user_id = $this->session->get('user_id');

        if ($user_id === null) {
            http_response_code(401);
            echo 'Unauthorized';
            return;
        }

        $sql = 'SELECT name FROM organizations WHERE owner_id = ?';

        $orgs = $this->db->select($sql, [$user_id]);

        if (empty($orgs)) {
            echo 'You do not have access to any organization yet';
            return;
        }
        echo json_encode($orgs);
    }
}
