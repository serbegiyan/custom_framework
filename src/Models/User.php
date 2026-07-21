<?php

namespace App\Models;

class User
{
    public int $id = 0;
    public string $country = '';
    public string $city = '';
    public bool $is_active = false;
    public string $gender = '';
    public string $birth_date = '';
    public int $salary = 0;
    public bool $has_children = false;
    public string $family_status = '';
    public string $registration_date = '';
}
