<?php
include_once 'includes/header.php';
/*
if(isset($_SESSION['user_id'])) {
	header("Location: index.php");  
	exit();
}*/

if(isset($_POST['user-login'])) {
 $errorMessages = $user->login($_POST['uname'], $_POST['upass']);

}
?>


<div class="container">

	<?php
	if(isset($_GET['newuser'])) {
		echo "<div class='alert alert-success text-center' role='alert'>
			You have successfully signed up. Please login using the form below.
		</div>";
	}

	if(isset($errorMessages)) {
		
		echo "<div class='alert alert-danger text-center' role='alert'>";
		foreach($errorMessages as $message) {
			echo $message;
		}
		echo "</div>";$message;
	}
	?>

	<div class="mw-500 mx-auto">
		<h1 class="my-5">Logga in</h1>
		<form action="" method="post">

			<label class="form-label" for="uname">Användarnamn eller e-post</label><br>
			<input class="form-control" type="text" name="uname" id="uname"><br>

			<label class="form-label" for="upass">Lösenord</label><br>
			<input class="form-control" type="password" name="upass" id="upass"><br>

			<input class="btn btn-primary py-2 px-4" type="submit" name="user-login" value="Logga in">
			
		</form>
	</div>
</div>

<?php
include_once 'includes/footer.php';
?>