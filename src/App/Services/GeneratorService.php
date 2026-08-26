<?php

namespace App\Services;

use Exception;
use Faker\Factory;
use RuntimeException;

class GeneratorService
{
    public function __construct(
        public Factory $factory,
    ) {
    }

    public function run(int $quantity, string $file): void
    {
        $faker = $this->factory::create();

        $fp = null;

        try {
            if (!file_exists($file) || !is_readable($file)) {
                throw new RuntimeException('Failed to open file');
            }
            $fp = fopen($file, "w");
            if(!$fp){
                        throw new \RuntimeException('Failed to open file');
                    }
            fputcsv($fp, ['country', 'city', 'is_active', 'gender', 'birth_date', 'salary', 'has_children', 'family_status', 'registration_date', 'organization_id']);
            for ($i = 0; $i < $quantity; $i++) {
                $arr = [
                    $faker->country(),
                    $faker->city(),
                    $faker->boolean() ? 'true' : 'false',
                    $faker->randomElement(['male', 'female']),
                    $faker->dateTimeBetween('-30 years', '-10 years')->format('Y-m-d'),
                    $faker->numberBetween(20000, 150000),
                    $faker->boolean() ? 'true' : 'false',
                    $faker->randomElement(['single', 'married', 'divorced']),
                    $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                    $faker->numberBetween(1, 10),
                ];
                fputcsv($fp, $arr);
            }
            return;

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        } finally {
            if ($fp) {
                fclose($fp);
            }
        }
    }
}
