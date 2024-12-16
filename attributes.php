<?php
include_once 'includes/header.php';

if(!$user->checkLoginStatus() || !$user->checkUserRole(50)) {
  header("Location: index.php");
}
  
$authorsArray = $book->getAllAuthors();
$illustratorsArray = $book->getAllIllustrators();
$genresArray = $book->getAllGenres();
$seriesArray = $book->getAllSeries();
$languagesArray = $book->getAllLanguages();
$publishersArray = $book->getAllPublishers();
//var_dump($authorsArray);

if (isset($_POST['search-users-submit']) && !empty($_POST['search'])) {
    $usersArray = $user->searchUsers($_POST['search']);
}
?>

<div class="container">
  <div class="mw-500 mx-auto">

    <h1 class="my-5">Hantera attribut</h1>

    <div>
      <h2 class="h3 mb-4">Författare</h2>

      <a class="btn btn-primary mb-2" href="newuser.php">Skapa ny författare</a>

      <div class="card rounded-4 text-start shadow-sm px-3 py-4 mt-2">
          
        <p class="mb-2 fst-italic">Tryck på valfri författare för att redigera dess namn.</p>
        
        <div class="table-responsive">
          <table class='table table-striped table-hover'>
              <thead>
                  <tr>
                  <th scope='col'>Namn</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  foreach($authorsArray as $author) {
                    echo "
                    <tr onclick=\"window.location.href='editattribute.php?authorId={$author['author_id']}';\" style=\"cursor: pointer;\">
                        <td class='ps-4'>{$author['author_name']}</td>
                    </tr>";
                  }
                ?>
              </tbody>
          </table>
        </div>

      </div>
    </div>


    <div>
      <h2 class="h3 mt-5 mb-4">Formgivare och illustratörer</h2>

      <a class="btn btn-primary mb-2" href="newuser.php">Skapa ny formgivare eller illustratör</a>

      <div class="card rounded-4 text-start shadow-sm px-3 py-4 mt-2">
          
        <p class="mb-2 fst-italic">Tryck på valfri formgivare eller illustratör för att redigera dess namn.</p>

        <div class="table-responsive">
          <table class='table table-striped table-hover'>
              <thead>
                  <tr>
                  <th scope='col'>Namn</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  foreach($illustratorsArray as $illustrator) {
                    echo "
                    <tr onclick=\"window.location.href='editattribute.php?illustratorId={$illustrator['illustrator_id']}';\" style=\"cursor: pointer;\">
                        <td class='ps-4'>{$illustrator['illustrator_name']}</td>
                    </tr>";
                  }
                ?>
              </tbody>
          </table>
        </div>

      </div>
    </div>

    <div>
      <h2 class="h3 mt-5 mb-4">Genrer</h2>

      <a class="btn btn-primary mb-2" href="newuser.php">Skapa ny genre</a>

      <div class="card rounded-4 text-start shadow-sm px-3 py-4 mt-2">
          
        <p class="mb-2 fst-italic">Tryck på valfri genre för att redigera den.</p>

        <div class="table-responsive">
          <table class='table table-striped table-hover'>
              <thead>
                  <tr>
                  <th scope='col'>Namn</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  foreach($genresArray as $genre) {
                    echo "
                    <tr onclick=\"window.location.href='editattribute.php?genreId={$genre['genre_id']}';\" style=\"cursor: pointer;\">
                        <td class='ps-4'>{$genre['genre_name']}</td>
                    </tr>";
                  }
                ?>
              </tbody>
          </table>
        </div>

      </div>
    </div>

    <div>
      <h2 class="h3 mt-5 mb-4">Serier</h2>

      <a class="btn btn-primary mb-2" href="newuser.php">Skapa ny serie</a>

      <div class="card rounded-4 text-start shadow-sm px-3 py-4 mt-2">
          
        <p class="mb-2 fst-italic">Tryck på valfri serie för att redigera dess namn.</p>

        <div class="table-responsive">
          <table class='table table-striped table-hover'>
              <thead>
                  <tr>
                  <th scope='col'>Namn</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  foreach($seriesArray as $series) {
                    echo "
                    <tr onclick=\"window.location.href='editattribute.php?seriesId={$series['series_id']}';\" style=\"cursor: pointer;\">
                        <td class='ps-4'>{$series['series_name']}</td>
                    </tr>";
                  }
                ?>
              </tbody>
          </table>
        </div>

      </div>
    </div>

    <div>
      <h2 class="h3 mt-5 mb-4">Språk</h2>

      <a class="btn btn-primary mb-2" href="newuser.php">Skapa nytt språk</a>

      <div class="card rounded-4 text-start shadow-sm px-3 py-4 mt-2">
          
        <p class="mb-2 fst-italic">Tryck på valfritt språk för att redigera dess namn.</p>

        <div class="table-responsive">
          <table class='table table-striped table-hover'>
              <thead>
                  <tr>
                  <th scope='col'>Namn</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  foreach($languagesArray as $language) {
                    echo "
                    <tr onclick=\"window.location.href='editattribute.php?languageId={$language['language_id']}';\" style=\"cursor: pointer;\">
                        <td class='ps-4'>{$language['language_name']}</td>
                    </tr>";
                  }
                ?>
              </tbody>
          </table>
        </div>

      </div>
    </div>

    <div>
      <h2 class="h3 mt-5 mb-4">Förlag</h2>

      <a class="btn btn-primary mb-2" href="newuser.php">Skapa nytt förlag</a>

      <div class="card rounded-4 text-start shadow-sm px-3 py-4 mt-2">
          
        <p class="mb-2 fst-italic">Tryck på valfritt förlag för att redigera dess namn.</p>

        <div class="table-responsive">
          <table class='table table-striped table-hover'>
              <thead>
                  <tr>
                  <th scope='col'>Namn</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  foreach($publishersArray as $publisher) {
                    echo "
                    <tr onclick=\"window.location.href='editattribute.php?publisherId={$publisher['publisher_id']}';\" style=\"cursor: pointer;\">
                        <td class='ps-4'>{$publisher['publisher_name']}</td>
                    </tr>";
                  }
                ?>
              </tbody>
          </table>
        </div>

      </div>
    </div>
    
  </div>
</div>

<div class="modal fade" id="newAuthorModal" tabindex="-1" aria-labelledby="newAuthorModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="newAuthorModalLabel">Ny författare</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
            <form id="new-author-form" action="" method="post">
                <div class="modal-body">
                    <label class="form-label" for="new-author-name">Författarens namn</label><br>
                    <input type="text" class="form-control" id="new-author-name" name="new-author-name" placeholder="För- och efternamn">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
                    <input type="submit" name="new-author-submit" class="btn btn-primary" value="Lägg till">
                </div>
            </form>
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