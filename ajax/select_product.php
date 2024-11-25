<?php 
include_once '../includes/config.php';

$pid = $_GET["id"];

$stmt_selectProduct = $pdo->prepare('SELECT * FROM table_products WHERE product_id = :pid');
$stmt_selectProduct->bindParam(':pid', $pid, PDO::PARAM_INT);
$stmt_selectProduct->execute();

$product = $stmt_selectProduct->fetch();

// Return JSON response
if ($product) {
    echo json_encode([
        "name" => $product['name'],
        "price" => number_format($product['price'], 2, ',', ' '),
        "number" => $product['invoice_number'],
        "id" => $product['product_id']
    ]);
}

?>