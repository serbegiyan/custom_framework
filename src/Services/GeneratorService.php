<?php

namespace App\Services;

use Exception;
use Faker\Factory;

class GeneratorService
{
    public function run(int $quantity, string $file): void
    {
        $faker = Factory::create();

        $fp = null;

        try {
            $fp = fopen($file, "w");
            if ($fp === false) {
                echo 'File not found';
                return;
            }

            fputcsv($fp, ['country', 'city', 'is_active', 'gender', 'birth_date', 'salary', 'has_children', 'family_status', 'registration_date']);
            for ($i = 0; $i < $quantity; $i++) {
                $arr = [
                    $faker->country,
                    $faker->city,
                    $faker->randomElement(['true', 'false']),
                    $faker->randomElement(['male', 'female']),
                    $faker->dateTimeBetween('-30 years', 'now')->format('Y-m-d'),
                    $faker->numberBetween(20000, 150000),
                    $faker->randomElement(['true', 'false']),
                    $faker->randomElement(['single', 'married', 'divorced']),
                    $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d')
                ];
                fputcsv($fp, $arr);
            }

            echo "Generation complete";

        } catch (Exception $e) {
            echo $e;
        } finally {
            if ($fp) {
                fclose($fp);
            }
        }
    }
}
