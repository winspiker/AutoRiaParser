<?php

declare(strict_types=1);

final class JsonStorage implements Storage
{
    public array $items = [];
    private string $fileName;
    
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->load();
    }

    public function load(): void
    {
        if (!\file_exists($this->fileName)) {
            return;
        }

        $contents = \file_get_contents($this->fileName);

        try {
            $decoded = \json_decode(json: $contents, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return;
        }

        foreach ($decoded->data as $item) {
            $this->add(new Car($item->id, $item->name, $item->year, $item->carMileage, $item->fuel, $item->transmission));
        }
    }

    public function save(): void
    {
        $json = json_encode(['data' => $this->items],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

        if (\file_exists($this->fileName)) {
            \touch(\sprintf('%s/%s', __DIR__, $this->fileName));
        }

        \file_put_contents($this->fileName, $json);
    }

    public function get(string $id): Car
    {
    return $this->find($id) ?? throw new RuntimeException("Item '$id' does not exists.");
    }

    public function find(string $id): ?Car
    {
        return $this->items[$id] ?? null;
    }

    public function add(Car $car): void
    {
        $this->items[$car->id] = $car;
    }
}