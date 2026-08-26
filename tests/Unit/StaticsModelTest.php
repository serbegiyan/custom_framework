<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Statics;
use App\Exceptions\ValidationException;
use DateTime;

class StaticsModelTest extends TestCase
{
    public function testSalaryCannotBeNegative(): void
    {
        $model = new Statics();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Зарплата не может быть отрицательной');

        $model->salary = -100;
    }

    public function testBirthDateCannotBeInFuture(): void
    {
        $model = new Statics();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Дата рождения не может быть в будущем');

        $model->birth_date = (new DateTime('+1 day'))->format('Y-m-d');
    }

    public function testRegistrationDateCannotBeBeforeBirthDate(): void
    {
        $model = new Statics();
        
        $model->birth_date = '2000-01-01';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Дата регистрации не может быть раньше даты рождения');

        $model->registration_date = '1999-12-31';
    }

    public function testTypeErrorIsThrownIfInvalidGenderPassed(): void
    {
        $model = new Statics();

        $this->expectException(\TypeError::class);
        
        $model->gender = 'alien';
    }
}
