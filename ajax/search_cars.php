<?php
include_once '../includes/config.php';
include_once '../includes/class.car.php';
$car = new Car($pdo);

if( isset($_GET["q"])) {
    $search = $_GET["q"];
} else {
    $search = " ";
}

$carsArray = $car->searchCars($search);

$car->populateCarSearchField($carsArray);
?>