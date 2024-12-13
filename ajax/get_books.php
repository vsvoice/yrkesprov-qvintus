<?php
include_once '../includes/config.php';
include_once '../includes/class.book.php';
$book = new Book($pdo);

if( isset($_GET["q"])) {
  $search = $_GET["q"];
} else {
  $search = " ";
}

$booksArray = $book->getBooks($search);

$book->populateBooksField($booksArray);
?>