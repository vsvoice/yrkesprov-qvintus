<?php
include_once 'includes/functions.php';	
include_once 'includes/header.php';

$user->checkLoginStatus();

if ($user->checkLoginStatus()) {
    if(!$user->checkUserRole(200)) {
        header("Location: home.php");
    }
}

// var_dump($test);

if(isset($_POST['edit-user-submit'])) {
	$feedback = $user->checkUserRegisterInput($_SESSION['user_name'], $_POST['umail'], $_POST['upassnew'], $_POST['upassrepeat']);

    if($feedback === 1) {
        $user->editUserInfo($_POST['umail'], $_POST['upassold'], $_POST['upassnew'], $_SESSION['user_id'], $_SESSION['user_role'], 1);
    } else {
		foreach($feedback as $message) {
			echo $message;
		}
    }
}
?>

<div class="container">

<h1>Edit User</h1>

<form action="" method="post">

    <label for="uname">Username</label><br>
    <input class="mb-2" type="text" name="uname" id="uname" value="<?php echo $_SESSION['user_name'] ?>" required="required" disabled><br>

    <label for="umail">Email</label><br>
    <input class="mb-2" type="email" name="umail" id="umail" value="<?php echo $_SESSION['user_email'] ?>" required="required"><br>

    <label for="upassold">Current Password</label><br>
    <input class="mb-2" type="password" name="upassold" id="upassold" required="required"><br>

    <label for="upassnew">New Password</label><br>
    <input class="mb-2" type="password" name="upassnew" id="upassnew" required="required"><br>

    <label for="upassrepeat">Repeat Password</label><br>
    <input class="mb-2" type="password" name="upassrepeat" id="upassrepeat" required="required"><br>

    <input type="submit" name="edit-user-submit" value="Update">
    
</form>



</div>

<?php
include_once 'includes/footer.php';
?>