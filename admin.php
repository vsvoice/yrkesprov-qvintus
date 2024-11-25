<?php
include_once 'includes/header.php';

if ($user->checkLoginStatus()) {
    if(!$user->checkUserRole(200)) {
        header("Location: home.php");
    }
}


if (isset($_POST['search-users-submit']) && !empty($_POST['search'])) {
    $usersArray = $user->searchUsers($_POST['search']);
}
?>

<div class="container">
    <div class="mw-500 mx-auto">

        <h1 class="my-5">Administratör</h1>

        <a class="btn btn-primary mb-2" href="newuser.php">Skapa ny användare</a>

        <div class="card rounded-4 text-start shadow-sm px-3 py-4 mt-2">
            <div class="mb-3">
                <label for="search" class="form-label">Sök bland användare (namn, användarnamn eller e-post)</label><br>
                <input class="form-control mb-2" type="text" name="search" id="search" onkeyup="searchUsers(this.value)">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-inactive" name="include-inactive" onchange="searchUsers(this.value)">
                    <label class="form-check-label" for="include-inactive">
                        Inkludera inaktiverade användare
                    </label>
                </div>
            </div>

            <p class="mt-4 mb-2 fst-italic">Tryck på valfri användare för att redigera dess uppgifter.</p>
            <div class="table-responsive">
                <table class='table table-striped table-hover'>
                    <thead>
                        <tr>
                        <th scope='col'>Namn</th>
                        <th scope='col'>Användarnamn</th>
                        <th scope='col'>E-post</th>
                        </tr>
                    </thead>
                    <tbody id="user-field">
                    </tbody>
                </table>
            
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Code to run when the DOM is ready
        searchUsers();
    });

    document.getElementById("include-inactive").addEventListener("change", function() {
        var str = document.getElementById("search").value;
        searchUsers(str);
    });

    function searchUsers(str) {
        if (str === undefined || str === null || str.length === 0) {
            str = " ";
        }

        // Get the checkbox status
        var includeInactive = document.getElementById("include-inactive").checked ? 1 : 0;
        
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("user-field").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "ajax/search_users.php?q=" + str + "&includeInactive=" + includeInactive, true);
        xmlhttp.send();
    }
</script>

<?php
include_once 'includes/footer.php';
?>