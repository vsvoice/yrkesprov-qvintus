<?php
include_once 'includes/header.php';

if(!$user->checkLoginStatus() || !$user->checkUserRole(200)) {
  header("Location: index.php");
}

$userInfoArray = $user->getUserInfo($_GET['uid']);


if (isset($_POST['delete-user-submit'])) {
    $deleteFeedback = $user->deleteUser($_GET['uid']);
}
?>

<div class="container justify-content-center text-center">

<?php
if (!isset($deleteFeedback)) {
    echo "<h2 class='mb-5'>Är du säker på att du vill radera användaren <span class='fw-bold'>{$userInfoArray['username']}</span>?</h2>";

    echo "
    <div class='row flex-column justify-content-center'>
        <div class='col-4 mb-3 mx-auto'>
            <a class='btn btn-warning w-100' href='admin-account.php?uid={$_GET['uid']}'>Nej, för mig tillbaks!</a>
        </div>
        <div class='col-4 mx-auto'>
            <form action='' method='post'>
                <input type='submit' name='delete-user-submit' value='Radera användare' class='btn btn-danger w-100'>
            </form>
        </div>
    </div>";
} else {
    echo "<h2 class='mb-5'>{$deleteFeedback}</h2>"; 

    echo " 
    <div class='row flex-column justify-content-center'>
        <div class='col-4 mb-3 mx-auto'>
            <a class='btn btn-secondary w-100' href='admin.php'>Återgå till sidan Administratör</a>
        </div>
    </div>";
}
?>
    

</div>

<?php
include_once 'includes/footer.php';
?>