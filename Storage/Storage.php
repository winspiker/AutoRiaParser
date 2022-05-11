<?php

interface Storage
{
    public function get(string $id): Car;

    public function find(string $id): ?Car;

    public function add(Car $car): void;

    public function save(): void;
}




