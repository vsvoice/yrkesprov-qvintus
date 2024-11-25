<?php
include_once '../includes/config.php';
include_once '../includes/class.customer.php';
$customer = new Customer($pdo);

if( isset($_GET["q"])) {
    $search = $_GET["q"];
} else {
    $search = " ";
}

$customersArray = $customer->searchCustomers($search);

$customer->populateCustomerSearchField($customersArray);
?>