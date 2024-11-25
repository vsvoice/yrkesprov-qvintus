<?php 
//Security risk with alax folder accessible via browser?
include_once '../includes/config.php';

$input = $_GET["id"];

$stmt_selectCustomer = $pdo->prepare('SELECT * FROM table_customers WHERE customer_id=:input');
$stmt_selectCustomer->bindParam(':input', $input, PDO::PARAM_STR);
$stmt_selectCustomer->execute();
$selectedCustomer = $stmt_selectCustomer->fetch();

echo $selectedCustomer['customer_fname'] . ' ' . $selectedCustomer['customer_lname'] . '<span class="ms-4">' . $selectedCustomer['customer_phone'] . '</span> <span class="ms-4">' . $selectedCustomer['customer_email'] . '</span>	<span class="ms-4">' . $selectedCustomer['customer_address'] . ' ' . $selectedCustomer['customer_zip'] . ' ' . $selectedCustomer['customer_area'] . '</span>';
?>