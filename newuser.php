<?php	
include_once 'includes/header.php';

if(!$user->checkLoginStatus() || !$user->checkUserRole(200)) {
  header("Location: index.php");
}

if(isset($_POST['register-submit'])) {
	$feedbackMessages = $user->checkUserRegisterInput(
		cleanInput($_POST['uname']), 
		cleanInput($_POST['umail']), 
		cleanInput($_POST['upass']), 
		cleanInput($_POST['upassrepeat'])
	);

    if($feedbackMessages === 1) {
        $signUpFeedback = $user->register(
			cleanInput($_POST['uname']), 
			cleanInput($_POST['umail']), 
			cleanInput($_POST['upass']), 
			cleanInput($_POST['ufname']), 
			cleanInput($_POST['ulname'])
		);
		if($signUpFeedback === 1) {
			echo "<div class='container'>
					<div class='alert alert-success text-center' role='alert'>
						Användaren har skapats.
					</div>
				</div>";
		}

    } else {
		echo "<div class='container'>";
		foreach($feedbackMessages as $message) {
			echo "<div class='alert alert-danger text-center' role='alert'>";
			echo 	$message;
			echo "</div>";
		}
		echo "</div>";
    }
}
?>


<div class="container">
	<div class="mw-500 mx-auto">
		<h1 class="my-5">Skapa ny användare</h1>
		<form action="" method="post" class="">
			<label class="form-label" for="uname">Användarnamn</label><br>
			<input class="form-control" type="text" name="uname" id="uname" required="required"><br>

			<label class="form-label" for="umail">E-post</label><br>
			<input class="form-control" type="email" name="umail" id="umail" required="required"><br>

			<label class="form-label" for="upass">Lösenord</label><br>
			<input class="form-control 2" type="password" name="upass" id="upass" required="required"><br>

			<label class="form-label" for="upassrepeat">Upprepa lösenord</label><br>
			<input class="form-control " type="password" name="upassrepeat" id="upassrepeat" required="required"><br><br>

			<label class="form-label" for="ufname">Förnamn</label><br>
			<input class="form-control " type="text" name="ufname" id="ufname" required="required"><br>

			<label class="form-label" for="ulname">Efternamn</label><br>
			<input class="form-control " type="text" name="ulname" id="ulname" required="required"><br>

			<input class="btn btn-primary py-2" type="submit" name="register-submit" value="Skapa ny användare">
		</form>
	</div>
</div>

<?php
include_once 'includes/footer.php';
?>