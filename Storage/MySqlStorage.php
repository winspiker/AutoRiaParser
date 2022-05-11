<?php
declare(strict_types=1);

class MySqlStorage implements Storage
{
    public const DSN = 'mysql:host=%s;dbname=%s';

    public const DATABASE = 'parser';

    public array $items = [];
    private ?PDO $db;

    public function __construct(string $host, string $username, string $password, string $dbname = self::DATABASE)
    {
        $this->db = new PDO(\sprintf(self::DSN, $host, $dbname), $username, $password);
        $this->load();
    }

    public function __destruct()
    {
        $this->db = null;
    }

    private function load(): void
    {
        $carList = $this->db->query('SELECT * FROM cars');

        foreach ($carList->fetchAll(PDO::FETCH_ASSOC) as $carData) {
            $this->add(new Car(
                $carData['id'],
                $carData['name'],
                $carData['year'],
                $carData['mileage'],
                $carData['fuel'],
                $carData['transmission']));
        }
    }

    public function get(string $id): Car
    {
        return $this->find($id) ?? throw new RuntimeException("Item '{$id}' does not exists.");
    }

    public function find(string $id): ?Car
    {
        return $this->items[$id] ?? null;
    }

    public function add(Car $car): void
    {
        $this->items[$car->id] = $car;
    }

    public function save(): void
    {
        /** @var Car $item */
        foreach ($this->items as $item) {
            $query = $this->db->prepare("SELECT count(*) as count FROM cars WHERE id = ?");
            $query->bindParam(1, $item->id);
            [$count] = $this->db->exec($query);

            if (0 !== $count) {
                $updateQuery = $this->db->prepare("UPDATE IGNORE cars SET id = ?, name = ?, year = ?, mileage = ?, transmission = ? WHERE id = ?");
                $updateQuery->execute([$item->id, $item->name, $item->createdYear, $item->carMileage, $item->transmission, $item->id]);
            } else {
                $insertQuery = $this->db->prepare("INSERT IGNORE INTO cars (`id`, `name`, `year`, `mileage`, `fuel`, `transmission`) VALUES(?, ?, ?, ?, ?, ?)");
                $insertQuery->execute([$item->id, $item->name, $item->createdYear, $item->carMileage, $item->transmission]);
            }
        }
    }
}