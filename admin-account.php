<?php
include_once 'includes/header.php';

if(!$user->checkLoginStatus() || !$user->checkUserRole(200)) {
  header("Location: index.php");
}

$userInfoArray = $user->getUserInfo($_GET['uid']);
$roleArray = $pdo->query("SELECT * FROM t_roles")->fetchAll();

if (isset($_POST['admin-edit-user-submit'])) {
    $uStatus = isset($_POST['is-disabled']) ? 0 : 1;
    $feedback = $user->checkUserRegisterInput(
        cleanInput($_POST['uname']), 
        cleanInput($_POST['umail']), 
        cleanInput($_POST['upassnew']), 
        cleanInput($_POST['upassrepeat']), 
        cleanInput($_GET['uid']) // Pass the user ID here
    );

    if ($feedback === 1) {
        $editFeedback = $user->editUserInfo(
            cleanInput($_POST['umail']), 
            cleanInput($_POST['upassold']), 
            cleanInput($_POST['upassnew']), 
            cleanInput($_GET['uid']), 
            cleanInput($_POST['urole']), 
            cleanInput($_POST['ufname']), 
            cleanInput($_POST['ulname']), 
            cleanInput($uStatus)
        );
        if ($editFeedback === 1) {
            echo "<div class='container'>
					<div class='alert alert-success text-center' role='alert'>
						Användarens uppgifter har uppdaterats
					</div>
				</div>";
        } else {
            foreach ($editFeedback as $message) {
                echo "<div class='container'>
                        <div class='alert alert-danger text-center' role='alert'>
                            {$message}
                        </div>
                    </div>";
            }
        }
    } else {
        foreach ($feedback as $message) {
            echo "<div class='container'>
                    <div class='alert alert-danger text-center' role='alert'>
                        {$message}
                    </div>
                </div>";
        }
    }
}

?>

<div class="container min-vh-100">
    <div class="mw-500 mx-auto">
        <h1 class="mb-5">Redigera användare</h1>

        <form action="" method="post" class="border p-4 rounded shadow-sm">

            <div class="mb-3">
                <label for="ufname" class="form-label">Förnamn</label>
                <input type="text" class="form-control" name="ufname" id="ufname" value="<?php echo $userInfoArray['fname'] ?>">
            </div>
        
            <div class="mb-3">
                <label for="ulname" class="form-label">Efternamn</label>
                <input type="text" class="form-control" name="ulname" id="ulname" value="<?php echo $userInfoArray['lname'] ?>" >
            </div>    

            <div class="mb-3">
                <label for="uname" class="form-label">Användarnamn</label>
                <input type="text" class="form-control" name="uname" id="uname" value="<?php echo $userInfoArray['username'] ?>" readonly required>
            </div>

            <div class="mb-3">
                <label for="umail" class="form-label">E-post</label>
                <input type="email" class="form-control" name="umail" id="umail" value="<?php echo $userInfoArray['email'] ?>" required>
            </div>

            <input type="hidden" name="upassold" id="upassold" value="asdfs123" readonly required>

            <div class="mb-3">
                <label for="upassnew" class="form-label">Nytt lösenord</label>
                <input type="password" class="form-control" name="upassnew" id="upassnew">
            </div>

            <div class="mb-3">
                <label for="upassrepeat" class="form-label">Upprepa nytt lösenord</label>
                <input type="password" class="form-control" name="upassrepeat" id="upassrepeat">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Användarroll</label>
                <select class="form-select" name="urole" id="role">
                    <?php
                        foreach ($roleArray as $role) {
                            $selected = $role['r_id'] === $userInfoArray['role_fk'] ? "selected" : "";
                            echo "<option {$selected} value='{$role['r_id']}'>{$role['r_name']}</option>";
                        }
                    ?>
                </select>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="is-disabled" name="is-disabled" value="1" <?php if($userInfoArray['status'] === 0){echo "checked";} ?>>
                <label class="form-check-label" for="is-disabled">Inaktivera kontot</label>
            </div>

            <div class="d-grid">
                <input type="submit" class="btn btn-primary mt-3 me-auto" name="admin-edit-user-submit" value="Uppdatera">
            </div>
            
        </form>

        <div class="text-center mt-4">
            <a class="btn btn-danger" href="confirm-delete.php?uid=<?php echo $_GET['uid']; ?>">Radera denna användare</a>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
