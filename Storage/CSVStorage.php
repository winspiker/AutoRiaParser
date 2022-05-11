<?php
declare(strict_types=1);

class CSVStorage implements Storage
{
    private string $fileName;
    public array $items = [];

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->load();
    }

    public function save():void
    {
        $fp = fopen($this->fileName, 'w');
        foreach (array_values($this->items)[0] as $key => $val) {
            $title [] = $key;
        }
        array_unshift($this->items, $title);
        foreach ($this->items as $car) {
            fputcsv($fp, (array) $car, ";");
        }
        fclose($fp);
    }
    private function load():void
    {
        if (!\file_exists($this->fileName)) {
            return;
        }

        $file = \fopen($this->fileName, 'r');
        while (($data = \fgetcsv($file, separator: ";")) !== false) {
            if ($data[0]==="id"){
                continue;
            }
            $this->add(new Car($data[0], $data[1], $data[2], $data[3], $data[4], $data[5]));
        }
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