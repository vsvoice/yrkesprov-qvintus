<?php
include_once '../includes/config.php';
include_once '../includes/class.user.php';
$user = new User($pdo);

$fromDate = $_GET["from"];
$toDate = $_GET["to"];

$hoursArray = $user->getAllWorkingHours($fromDate, $toDate);

$user->populateWorkingHoursField($hoursArray);
?>