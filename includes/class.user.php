<?php
include_once 'functions.php';

class User {

    private $username;
    private $role;
    private $pdo;
    private $errorMessages = [];
    private $errorState = 0;


    function __construct($pdo) {
        $this->role = 4;
        $this->username = "RandomGuest123";
        $this->pdo = $pdo;
    }

    public function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function checkUserRegisterInput(string $uname, string $umail, string $upass, string $upassrepeat, int $uid = null) {
        // START Check if user-entered username or email exists in the database
        if (isset($_POST['register-submit'])) {
            $this->errorState = 0;
            $stmt_checkUsername = $this->pdo->prepare('SELECT * FROM t_users WHERE username = :uname OR email = :email');
            $stmt_checkUsername->bindParam(':uname', $uname, PDO::PARAM_STR);
            $stmt_checkUsername->bindParam(':email', $umail, PDO::PARAM_STR);
            $stmt_checkUsername->execute();
    
            // Check if query returns any result
            if ($stmt_checkUsername->rowCount() > 0) {
                array_push($this->errorMessages, "Användarnamn eller e-postadress är upptagen!");
                $this->errorState = 1;
            }
        } else {
            // Only check for email if user ID is not provided or the email has changed
            if ($uid !== null) {
                $stmt_checkUserEmail = $this->pdo->prepare('SELECT * FROM t_users WHERE email = :email AND user_id != :uid');
                $stmt_checkUserEmail->bindParam(':email', $umail, PDO::PARAM_STR);
                $stmt_checkUserEmail->bindParam(':uid', $uid, PDO::PARAM_INT);
                $stmt_checkUserEmail->execute();
            } else {
                $stmt_checkUserEmail = $this->pdo->prepare('SELECT * FROM t_users WHERE email = :email');
                $stmt_checkUserEmail->bindParam(':email', $umail, PDO::PARAM_STR);
                $stmt_checkUserEmail->execute();
            }
    
            // Check if query returns any result
            if ($stmt_checkUserEmail->rowCount() > 0) {
                array_push($this->errorMessages, "E-postadressen är upptagen!");
                $this->errorState = 1;
            }
        }
        // END Check if user-entered username or email exists in the database
        
        // START Conditionally check passwords if they are provided
        if (isset($_POST['register-submit']) || (!empty($upass) || !empty($upassrepeat))) {
            // Check if passwords match
            if ($upass !== $upassrepeat) {
                array_push($this->errorMessages, "Angivna lösenorden matchar inte!");
                $this->errorState = 1;
            } else {
                // Check if password length is at least 8 characters
                if (strlen($upass) < 8) {
                    array_push($this->errorMessages, "Angivna lösenordet är för kort!");
                    $this->errorState = 1;
                }
            }
        }
        // END Conditionally check passwords if they are provided
    
        // START Check if user-entered email is a proper email address
        if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorMessages, "E-postadressen är inte i rätt format!");
            $this->errorState = 1;
        }
        // END Check if user-entered email is a proper email address
    
        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }
    }
    


    public function register(string $uname, string $umail, string $upass, string $fname, string $lname) {
        // Hash password and clean inputs
        $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);
        $uname = $this->cleanInput($uname);
        $fname = $this->cleanInput($fname);
        $lname = $this->cleanInput($lname);

        if(password_verify($upass, $hashedPassword)) {
            $stmt_insertNewUser = $this->pdo->prepare('INSERT INTO t_users (username, password, email, role_id_fk, status, fname, lname) 
            VALUES 
            (:uname, :upass, :umail, 1, 1, :fname, :lname)');
            $stmt_insertNewUser->bindParam(':uname', $uname, PDO::PARAM_STR);
            $stmt_insertNewUser->bindParam(':upass', $hashedPassword, PDO::PARAM_STR);
            $stmt_insertNewUser->bindParam(':umail', $umail, PDO::PARAM_STR);
            $stmt_insertNewUser->bindParam(':fname', $fname, PDO::PARAM_STR);
            $stmt_insertNewUser->bindParam(':lname', $lname, PDO::PARAM_STR);
        }
        
        if($stmt_insertNewUser->execute()) {
            return 1;
        } else {
            array_push($this->errorMessages, "Lyckades inte registrera användaren! Kontakta support!");
            return $this->errorMessages;
        }

    }

    public function login(string $unamemail, string $upass) {
        
        $stmt_checkUsername = $this->pdo->prepare('SELECT * FROM t_users WHERE username = :uname OR email = :email');
        $stmt_checkUsername->bindParam(':uname', $unamemail, PDO::PARAM_STR);
        $stmt_checkUsername->bindParam(':email', $unamemail, PDO::PARAM_STR);
        $stmt_checkUsername->execute();

        // Check if query returns a result
        if($stmt_checkUsername->rowCount() === 0) {
            array_push($this->errorMessages, "Användarnamnet eller e-postadressen finns inte! ");
            return $this->errorMessages;
            
        }
        // Save user data to an array
        $userData = $stmt_checkUsername->fetch();

        // Check if password is correct
        if(password_verify($upass, $userData['password'])) {

            // Check if user account is deactivated
            if ($userData['status'] === 0) {
                array_push($this->errorMessages, "Detta konto har inaktiverats! Kontakta administratören och be om hjälp ");
                return $this->errorMessages;
            }

            $_SESSION['user_id'] = $userData['user_id'];
            $_SESSION['user_name'] = $userData['username'];
            $_SESSION['user_email'] = $userData['email'];
            $_SESSION['user_role'] = $userData['role_id_fk'];

            header("Location: index.php");
            exit();
        } else {
            array_push($this->errorMessages, "Lösenordet är fel! ");
            return $this->errorMessages;
        }
    }

    public function checkLoginStatus() {
        if(isset($_SESSION['user_id'])) {
            return TRUE;
        } else {
            /*header("Location: index.php");  
            exit();*/
        }
    }



    public function checkUserRole(int $requiredValue) {
        /*$stmt_checkUserRole = $this->pdo->prepare(
        'SELECT role_id_fk, role_level
        FROM t_users
        INNER JOIN t_roles ON t_users.role_id_fk = t_roles.role_id
        WHERE user_id = :id');
        $stmt_checkUserRole->bindParam(':id', $userRoleValue, PDO::PARAM_INT);
        $stmt_checkUserRole->execute();*/
        
        $stmt_checkUserRole = $this->pdo->prepare(
            'SELECT role_level FROM t_roles WHERE role_id = :rid');
        $stmt_checkUserRole->bindParam(':rid', $_SESSION['user_role'], PDO::PARAM_INT);
        $stmt_checkUserRole->execute();

        $userRoleData = $stmt_checkUserRole->fetch();

        if ($userRoleData['role_level'] >= $requiredValue) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function editUserInfo(string $umail, string $upassold, string $upassnew, int $uid, int $role, string $ufname, string $ulname, int $status) {
        // Clean and validate first name
        $cleanedFname = $this->cleanInput($ufname);
        if (empty($cleanedFname) || !preg_match("/^[a-zA-Z\s]+$/", $cleanedFname)) {
            array_push($this->errorMessages, "Förnamn får inte vara tomt och får endast innehålla bokstäver! ");
            return $this->errorMessages;
            //return "Förnamn får inte vara tomt och får endast innehålla bokstäver!";
        }
    
        // Clean and validate last name
        $cleanedLname = $this->cleanInput($ulname);
        if (empty($cleanedLname) || !preg_match("/^[a-zA-Z\s]+$/", $cleanedLname)) {
            array_push($this->errorMessages, "Efternamn får inte vara tomt och får endast innehålla bokstäver! ");
            return $this->errorMessages;
            //return "Efternamn får inte vara tomt och får endast innehålla bokstäver!";
        }
    
        // Get password and current email of the user
        $stmt_getUserDetails = $this->pdo->prepare('SELECT password, email FROM t_users WHERE user_id = :uid');
        $stmt_getUserDetails->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt_getUserDetails->execute();
        $userDetails = $stmt_getUserDetails->fetch();
        
        // If user edits their own data (legacy)
        if (isset($_POST['edit-user-submit'])) {
            // Check if entered password is correct
            if (!password_verify($upassold, $userDetails['password'])) {
                array_push($this->errorMessages, "Lösenordet är inte giltigt ");
                return $this->errorMessages;    
                //return "The password is invalid";
            }
        }
    
        // Update fields
        $hashedPassword = password_hash($upassnew, PASSWORD_DEFAULT);
        
        // Update password if new password field isn't empty
        if (!empty($upassnew)) {
            $updatePassword = "password = :upassnew, ";
        } else {
            $updatePassword = "";
        }
        // Only set email if it has changed
        $updateEmail = $umail !== $userDetails['email'] ? ", email = :umail" : "";
    
        // Update in the database 
        $stmt_editUserInfo = $this->pdo->prepare("
            UPDATE t_users
            SET $updatePassword role_id_fk = :role, status = :status, fname = :ufname, lname = :ulname 
            $updateEmail
            WHERE user_id = :uid
        ");
        
        // Bind parameters
        if (!empty($upassnew)) {
            $stmt_editUserInfo->bindParam(':upassnew', $hashedPassword, PDO::PARAM_STR);
        }

        if ($updateEmail) {
            $stmt_editUserInfo->bindParam(':umail', $umail, PDO::PARAM_STR);
        }
        
        $stmt_editUserInfo->bindParam(':role', $role, PDO::PARAM_INT);
        $stmt_editUserInfo->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt_editUserInfo->bindParam(':ufname', $cleanedFname, PDO::PARAM_STR); // Use cleaned name
        $stmt_editUserInfo->bindParam(':ulname', $cleanedLname, PDO::PARAM_STR); // Use cleaned name
        $stmt_editUserInfo->bindParam(':uid', $uid, PDO::PARAM_INT);
        
        // Execute the statement
        if ($stmt_editUserInfo->execute() && $uid == $_SESSION['user_id']) {
            $_SESSION['user_email'] = $umail; // Update session email if changed
        }

        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }
    }
    
    

    public function searchUsers(string $input, int $includeInactive) {
        $input = cleanInput($input);

        // Replace all whitespace characters with % wildcards
        $input = preg_replace('/\s+/', '%', $input);

        $inputJoker = "%".$input."%";

        // Start building the query
        $searchQuery = 'SELECT * FROM t_users WHERE (username LIKE :uname OR email LIKE :email OR fname LIKE :fname OR lname LIKE :lname OR CONCAT(fname, lname) LIKE :fullname)';

         // Conditionally add status filter
        if (!$includeInactive) {
            $searchQuery .= ' AND status = 1';
        }

        // Add ORDER BY clause to sort by fname, then lname
        $searchQuery .= ' ORDER BY fname ASC, lname ASC';

        $stmt_searchUsers = $this->pdo->prepare($searchQuery);
        $stmt_searchUsers->bindParam(':uname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':email', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':fname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':lname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':fullname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->execute();
        $usersList = $stmt_searchUsers->fetchAll();
        
        return $usersList;
    }

    public function populateUserField(array $usersArray) {
        foreach ($usersArray as $user) {
            echo "
            <tr " . ($user['status'] === 0 ? "class='table-danger'" : "") . " onclick=\"window.location.href='admin-account.php?uid={$user['user_id']}';\" style=\"cursor: pointer;\">
                <td>{$user['fname']} {$user['lname']}</td>
                <td>{$user['username']}</td>
                <td>{$user['email']}</td>
            </tr>";
        }
    }

    public function getUserInfo(int $uid) {
        $stmt_selectUserData = $this->pdo->prepare('SELECT * FROM t_users WHERE user_id = :uid');
        $stmt_selectUserData->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt_selectUserData->execute();
        $userInfo = $stmt_selectUserData->fetch();
        return $userInfo;
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php");
    }

    public function deleteUser(int $uid) {
        $stmt_deleteUser = $this->pdo->prepare('DELETE FROM t_users WHERE user_id = :uid');
        $stmt_deleteUser->bindParam(':uid', $uid, PDO::PARAM_INT);

        if($stmt_deleteUser->execute()) {
            return "Användaren har raderats";
        } else {
            return "Något gick snett ... Försök igen.";
        }
    }

    public function getAllWorkingHours(string $fromDate, string $toDate) {
        $stmt_selectWorkingHours = $this->pdo->prepare('SELECT 
                u.user_id,
                u.fname,
                u.lname,
                SUM(h.h_amount) AS total_hours
            FROM 
                t_hours h
            JOIN 
                t_users u ON h.user_id_fk = u.user_id
            WHERE 
                h.h_date BETWEEN :fromDate AND :toDate
                AND u.status = 1
                AND u.role_id_fk = 1
            GROUP BY 
                u.user_id, u.fname, u.lname;');
        $stmt_selectWorkingHours->bindParam(':fromDate', $fromDate, PDO::PARAM_STR);
        $stmt_selectWorkingHours->bindParam(':toDate', $toDate, PDO::PARAM_STR);
        $stmt_selectWorkingHours->execute();
        $workingHours = $stmt_selectWorkingHours->fetchAll();
        return $workingHours;
    }

    public function populateWorkingHoursField(array $hoursArray) {
        foreach ($hoursArray as $user) {
            echo "<div class='row list-group-item d-flex py-3'>
                    <div class='col'>
                        {$user['fname']} {$user['lname']}
                    </div>
                    <div class='col'>
                        {$user['total_hours']}
                    </div>
                </div>";
        }
    }

}

?>