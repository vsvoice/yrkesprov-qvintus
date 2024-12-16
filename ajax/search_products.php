<?php
include_once '../includes/config.php';
include_once '../includes/class.book.php';
$book = new Book($pdo);

$book->searchProducts();
?>