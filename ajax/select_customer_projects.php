<?php
include_once '../includes/config.php';
include_once '../includes/class.customer.php';
$customer = new Customer($pdo);

$customerId = $_GET["id"];

$customerProjectsArray = $customer->selectCustomerProjects($customerId);

$customer->populateCustomerProjectsField($customerProjectsArray);
?>