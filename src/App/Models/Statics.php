<?php

namespace App\Models;

use App\Enums\FamilyStatus;
use App\Enums\Gender;
use App\Exceptions\ValidationException;
use DateTime;

/**
 * @property string $country
 * @property string $city
 * @property bool $is_active
 * @property string $gender
 * @property int $salary 
 * @property bool $has_children
 * @property string $family_status
 * @property string $birth_date
 * @property string $registration_date
 */
#[\AllowDynamicProperties]
class Statics
{
    protected int $id = 0;
    protected string $country = '';
    protected string $city = '';
    protected bool $is_active = false;
    protected string $gender = '';
    protected int $salary = 0;
    protected bool $has_children = false;
    protected string $family_status = '';
    protected string $birth_date = '';
    protected string $registration_date = '';
    protected int $organization_id = 0;

    public function __get(string $name): mixed
    {
        return $this->$name ?? null;
    }

    public function getGender(): ?Gender
    {
        return Gender::tryFrom($this->gender);
    }
    public function getFamilyStatus(): ?FamilyStatus
    {
        return FamilyStatus::tryFrom($this->family_status);
    }
    public function getBirthDate(): ?DateTime
    {
        return $this->birth_date ? new DateTime($this->birth_date) : null;
    }
    public function getRegistrationDate(): ?DateTime
    {
        return $this->registration_date ? new DateTime($this->registration_date) : null;
    }

    public function __set(string $name, mixed $value): void
    {
        if ($name === 'salary' && (int)$value < 0) {
            throw new ValidationException("Ошибка уровня модели: Зарплата не может быть отрицательной");
        }

        if ($name === 'birth_date' && $value) {
            $date = new DateTime($value);
            if ($date > new DateTime()) {
                throw new ValidationException("Ошибка уровня модели: Дата рождения не может быть в будущем");
            }
        }

        if ($name === 'registration_date' && $value && $this->birth_date) {
            $regDate = new DateTime($value);
            $birthDate = new DateTime($this->birth_date);
            if ($regDate < $birthDate) {
                throw new ValidationException("Ошибка уровня модели: Дата регистрации не может быть раньше даты рождения");
            }
        }

        if ($name === 'gender' && $value) {
            $val = $value instanceof Gender ? $value->value : $value;
            if (!Gender::tryFrom($val)) {
                throw new \TypeError("Invalid gender value");
            }
            $value = $val;
        }

        if ($name === 'family_status' && $value) {
            $val = $value instanceof FamilyStatus ? $value->value : $value;
            if (!FamilyStatus::tryFrom($val)) {
                throw new \TypeError("Invalid family status value");
            }
            $value = $val;
        }

        $this->$name = $value;
    }
}
