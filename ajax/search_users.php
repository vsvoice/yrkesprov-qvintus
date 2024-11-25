<?php
include_once '../includes/config.php';
include_once '../includes/class.user.php';
$user = new User($pdo);

if( isset($_GET["q"])) {
    $search = $_GET["q"];
} else {
    $search = " ";
}

// Retrieve checkbox status
$includeInactive = isset($_GET['includeInactive']) && $_GET['includeInactive'] == '1';

$usersArray = $user->searchUsers($search, $includeInactive);

$user->populateUserField($usersArray);
?>