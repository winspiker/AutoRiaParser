<?php
declare(strict_types=1);

include "Car.php";
include "Http/Client.php";
include "CarParser.php";
include "Storage/Storage.php";
include "Storage/CSVStorage.php";

$numPage = 1;
$url = "https://auto.ria.com/uk/legkovie/";

//$db = new MySqlStorage(
//    host: "localhost",
//    username: "root",
//    password: ""
//);

$db = new CSVStorage("data.csv");
$client = new Client($url);
$parser = new CarParser($client);

foreach ($parser->getCarList($numPage) as $car){
    $db->add($car);
}

$db->save();

//namespace, autoloading, use