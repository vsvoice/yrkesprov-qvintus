<?php
include_once '../includes/config.php';
include_once '../includes/class.car.php';
$car = new Car($pdo);

$carId = $_GET["id"];

$carProjectsArray = $car->selectCarProjects($carId);

$car->populateCarProjectsField($carProjectsArray);
?>