<?php
include_once 'includes/header.php';

if(!$user->checkLoginStatus() || !$user->checkUserRole(50)) {
  header("Location: index.php");
}

if(isset($_GET['authorId'])) {
  $attributeData = $book->getAuthorName($_GET['authorId']);
} 
else if (isset($_GET['illustratorId'])) {
  $attributeData = $book->getIllustratorName($_GET['illustratorId']);
} 
else if (isset($_GET['genreId'])) {
  $attributeData = $book->getGenreName($_GET['genreId']);
}
else if (isset($_GET['seriesId'])) {
  $attributeData = $book->getSeriesName($_GET['seriesId']);
}
else if (isset($_GET['languageId'])) {
  $attributeData = $book->getLanguageName($_GET['languageId']);
}
else if (isset($_GET['publisherId'])) {
  $attributeData = $book->getPublisherName($_GET['publisherId']);
}
//var_dump($attributeData);


if(isset($_POST['update-attribute-submit'])) {

  if(isset($_GET['authorId'])) {
    $errorMessage = $book->updateAuthor($_POST['attribute-name'], $_GET['authorId']);
    if(isset($errorMessage) && $errorMessage === true) {
        $_SESSION['success_message'] = "<a href='attributes.php' class='btn btn-light me-4'>Tillbaka till Hantera attribut</a> Författaren har uppdaterats.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?authorId=" . $_GET['authorId']);
        exit();
    }
  } 
  else if(isset($_GET['illustratorId'])) {
    $errorMessage = $book->updateIllustrator($_POST['attribute-name'], $_GET['illustratorId']);
    if(isset($errorMessage) && $errorMessage === true) {
        $_SESSION['success_message'] = "<a href='attributes.php' class='btn btn-light me-4'>Tillbaka till Hantera attribut</a> Formgivaren eller illustratören har uppdaterats.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?illustratorId=" . $_GET['illustratorId']);
        exit();
    }
  } 
  else if(isset($_GET['genreId'])) {
    $errorMessage = $book->updateGenre($_POST['attribute-name'], $_GET['genreId'], $_POST['genre-img-field-name']);
    if(isset($errorMessage) && $errorMessage === true) {
        $_SESSION['success_message'] = "<a href='attributes.php' class='btn btn-light me-4'>Tillbaka till Hantera attribut</a> Genren har uppdaterats.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?genreId=" . $_GET['genreId']);
        exit();
    }
  } 
  else if(isset($_GET['seriesId'])) {
    $errorMessage = $book->updateSeries($_POST['attribute-name'], $_GET['seriesId']);
    if(isset($errorMessage) && $errorMessage === true) {
        $_SESSION['success_message'] = "<a href='attributes.php' class='btn btn-light me-4'>Tillbaka till Hantera attribut</a> Serien har uppdaterats.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?seriesId=" . $_GET['seriesId']);
        exit();
    }
  } 
  else if(isset($_GET['languageId'])) {
    $errorMessage = $book->updateLanguage($_POST['attribute-name'], $_GET['languageId']);
    if(isset($errorMessage) && $errorMessage === true) {
        $_SESSION['success_message'] = "<a href='attributes.php' class='btn btn-light me-4'>Tillbaka till Hantera attribut</a> Språket har uppdaterats.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?languageId=" . $_GET['languageId']);
        exit();
    }
  } 
  else if(isset($_GET['publisherId'])) {
    $errorMessage = $book->updatePublisher($_POST['attribute-name'], $_GET['publisherId']);
    if(isset($errorMessage) && $errorMessage === true) {
        $_SESSION['success_message'] = "<a href='attributes.php' class='btn btn-light me-4'>Tillbaka till Hantera attribut</a> Förlaget har uppdaterats.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?publisherId=" . $_GET['publisherId']);
        exit();
    }
  } 


}

if(isset($_POST['new-illustrator-submit'])) {
  $errorMessage = $book->insertNewIllustrator($_POST['new-illustrator-name']);
  if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "<button id='refresh-page-btn' type='button' class='btn btn-light me-4'>Uppdatera sidan</button> Formgivaren/illustratören har lagts till. Uppdatera sidan för att se ändringarna.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
  }
}

if(isset($_POST['new-genre-submit'])) {
  $errorMessage = $book->insertNewGenre($_POST['new-genre-name'], $_POST['new-genre-img-field-name']);
  if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "<button id='refresh-page-btn' type='button' class='btn btn-light me-4'>Uppdatera sidan</button> Genren har lagts till. Uppdatera sidan för att se ändringarna.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
  }
}

if(isset($_POST['new-series-submit'])) {
  $errorMessage = $book->insertNewSeries($_POST['new-series-name']);
  if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "<button id='refresh-page-btn' type='button' class='btn btn-light me-4'>Uppdatera sidan</button> Serien har lagts till. Uppdatera sidan för att se ändringarna.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
  }
}

if(isset($_POST['new-language-submit'])) {
  $errorMessage = $book->insertNewLanguage($_POST['new-language-name']);
  if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "<button id='refresh-page-btn' type='button' class='btn btn-light me-4'>Uppdatera sidan</button> Språket har lagts till. Uppdatera sidan för att se ändringarna.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
  }
}

if(isset($_POST['new-publisher-submit'])) {
  $errorMessage = $book->insertNewPublisher($_POST['new-publisher-name']);
  if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "<button id='refresh-page-btn' type='button' class='btn btn-light me-4'>Uppdatera sidan</button> Förlaget har lagts till. Uppdatera sidan för att se ändringarna.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
  }
}
?>

<div class="container min-vh-100">
    <div class="mw-500 mx-auto">
      <?php
        if(isset($errorMessage) && $errorMessage !== true) {
            echo errorMessage($errorMessage);
        }
        if(isset($_SESSION['success_message'])) {
            echo successMessage($_SESSION['success_message']);
            unset($_SESSION['success_message']); 
        }
      ?>
        <h1 class="mb-5">Redigera attribut</h1>

        <form action="" method="post" enctype="multipart/form-data" class="border p-4 rounded shadow-sm">

            <div class="mb-3">
                <label for="attribute-name" class="form-label">Namn</label>
                <input type="text" class="form-control" name="attribute-name" id="attribute-name" value="<?php echo $attributeData['name'] ?>">
            </div>

            <?php
            if(isset($_GET['genreId'])) {
              echo "
              <div class=''>
                <label class='form-label' for='genre-img'>Ny genrebild</label><br>
                <input class='form-control' type='file' id='genre-img' name='genre-img'><br>
                <input type='hidden' name='genre-img-field-name' value='genre-img'>
              </div>";
            }
            ?>

            <div class="d-grid">
                <input type="submit" class="btn btn-primary mt-3 me-auto" name="update-attribute-submit" value="Spara">
            </div>
            
        </form>
    </div>
</div>

<script>
const refreshButton = document.getElementById('refresh-page-btn');
if (refreshButton) {
    refreshButton.addEventListener('click', function () {
        // Reload the page cleanly (GET request only)
        window.location.href = window.location.href.split('?')[0];
    });
}
</script>

<?php
include_once 'includes/footer.php';
?>