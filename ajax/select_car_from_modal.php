<?php 
//Security risk with alax folder accessible via browser?
include_once '../includes/config.php';

$input = $_GET["id"];

$stmt_selectCar = $pdo->prepare('SELECT * FROM table_cars WHERE car_id=:input');
$stmt_selectCar->bindParam(':input', $input, PDO::PARAM_STR);
$stmt_selectCar->execute();
$selectedCar = $stmt_selectCar->fetch();

echo $selectedCar['car_brand'] . ' ' . $selectedCar['car_model'] . ' <span class="ms-4">' . $selectedCar['car_license'] . '</span>';

?>