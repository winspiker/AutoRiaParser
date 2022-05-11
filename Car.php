<?php

declare(strict_types=1);

final class Car
{
    public function __construct(
        public string $id,
        public string $name,
        public string $createdYear,
        public string $carMileage,
        public string $fuel,
        public string $transmission)
    {
    }
}