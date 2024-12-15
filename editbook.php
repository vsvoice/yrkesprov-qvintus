<?php  
include_once 'includes/header.php';
include_once 'includes/functions.php';

if (isset($_GET['id'])) {
  $bookData = $book->getBookDataEdit($_GET['id']);
  var_dump($bookData);
}

$allAuthorsArray = $book->getAllAuthors();
$allIllustratorsArray = $book->getAllIllustrators();
$allCategoriesArray = $book->getAllCategories();
$allGenresArray = $book->getAllGenres();
$allSeriesArray = $book->getAllSeries();
$allLanguagesArray = $book->getAllLanguages();
$allPublishersArray = $book->getAllPublishers();
$allAgeRangesArray = $book->getAllAgeRanges();

$firstLetter = null;
$errorMessage = null;

if(isset($_POST['update-book-submit'])) {
    $visibility = isset($_POST['visibility']) ? 1 : 0;
    $displayed = isset($_POST['displayed']) ? 1 : 0;
    $errorMessage = $book->updateExistingBook($_GET['id'], $_POST['title'], $_POST['description'], $_POST['price'], $_POST['publishing-date'], $_POST['cover-img-field-name'], $_POST['page-amount'], $_POST['authors'], $_POST['illustrators'], $_POST['category'], $_POST['genres'], $_POST['series'], $_POST['publisher'], $_POST['age-range'], $visibility, $displayed, $_SESSION['user_id']);
    if(isset($errorMessage) && $errorMessage === true) {
        $_SESSION['success_message'] = "<a href='product.php?id={$_GET['id']}'><button type='button' class='btn btn-light me-4'>Visa bok</button></a> Ändringarna hara sparats.";
    }
}

if(isset($_POST['new-author-submit'])) {
    $errorMessage = $book->insertNewAuthor($_POST['new-author-name']);
    if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "<button id='refresh-page-btn' type='button' class='btn btn-light me-4'>Uppdatera sidan</button> Författaren har lagts till. Uppdatera sidan för att se ändringarna.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
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

<div class="container-fluid">
    <div class="mx-auto mw-1240 px-2 px-sm-4">
    <?php
        if(isset($errorMessage) && $errorMessage !== true) {
            echo $errorMessage;
        }
        if(isset($_SESSION['success_message'])) {
            echo successMessage($_SESSION['success_message']);
            unset($_SESSION['success_message']); 
        }
    ?>
    <h1 class="my-5 font-taviraj">Redigera bok</h1>
		<form action="" method="post" enctype="multipart/form-data" class="">
            <div class="row">
                <div class="col-sm-6">
                    <label class="form-label" for="title">Titel</label><br>
                    <input class="form-control" type="text" name="title" id="title" required="required" value="<?php if(isset($bookData['title'])) {echo $bookData['title'];} ?>"><br>
                </div>

                <div class="col-sm-6">
                    <label class="form-label" for="price">Pris</label><br>
                    <input class="form-control" type="text" name="price" id="price" required="required" value="<?php if(isset($bookData['price'])) {echo $bookData['price'];} ?>"><br>
                </div>
            </div>

            <label class="form-label" for="cover-img">Ny omslagsbild</label><br>
            <input class="form-control" type="file" id="cover-img" name="cover-img"><br>
            <input type="hidden" name="cover-img-field-name" value="cover-img">

			<label class="form-label" for="description">Beskrivning</label><br>
			<textarea class="form-control" name="description" id="description" rows="4" required="required"><?php if(isset($bookData['description'])) {echo $bookData['description'];} ?></textarea><br>

            <div class="row">
            <div class="col-sm-6">
                <label class="form-label" for="publishing-date">Utgivningsdatum</label><br>
                <input class="form-control" type="date" name="publishing-date" id="publishing-date" required="required" value="<?php if(isset($bookData['date_published'])) {echo $bookData['date_published'];} ?>"><br>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="page-amount">Antal sidor</label><br>
                <input class="form-control" type="number" name="page-amount" id="page-amount" required="required" value="<?php if(isset($bookData['page_amount'])) {echo $bookData['page_amount'];} ?>"><br>
            </div>
            </div><br><br>


            <label class="form-label" for="author-selection">Författare</label><br>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="author-filter-input" placeholder="Sök och filtrera författare ...">
                <label for="category-filter-input">Sök och filtrera författare ...</label>
                <!--<button class="btn btn-outline-primary shadow-sm" type="button" id="button-addon2">Ny författare</button>-->
            </div>
            <p id="checked-authors-counter">Valda författare: 0</p>

            <div class="d-flex flex-wrap row-gap-3" id="author-selection" style='overflow: auto; max-height: 350px;'>
                <?php
                    $firstLetter = null;
                    foreach ($allAuthorsArray as $author) {
                        $checked = "";
                        $orderFirst = "";
                          if (isset($bookData['authors']) && in_array($author['author_id'], $bookData['authors'])) {
                            $checked =  " checked";
                            $orderFirst = " order-first";
                          }
                        echo "
                          <div class='form-check form-check-inline ps-2";
                        echo $orderFirst;
                        echo "'>
                            <input class='btn-check' type='checkbox' id='author-{$author['author_id']}' name='authors[]' value='{$author['author_id']}' autocomplete='off'";
                        echo $checked;
                        echo ">
                            <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='author-{$author['author_id']}'>{$author['author_name']}</label>
                        </div>";
                    }
                    echo "<button type='button' class='btn btn-dark p-1 px-3 ms-2 rounded-pill' data-bs-toggle='modal' data-bs-target='#newAuthorModal'>
					        + Ny författare
				        </button>";
                ?>
            </div><br><br>


            <label class="form-label" for="illustrator-selection">Formgivare eller illustratör</label><br>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="illustrator-filter-input" placeholder="Sök och filtrera författare ...">
                <label for="category-filter-input">Sök och filtrera formgivare och illustratörer ...</label>
                <!--<button class="btn btn-outline-primary shadow-sm" type="button" id="button-addon2">Ny författare</button>-->
            </div>
            <p id="checked-illustrators-counter">Valda formgivare/illustratörer: 0</p>

            <div class="d-flex flex-wrap row-gap-3" id="illustrator-selection" style='overflow: auto; max-height: 350px;'>
                <?php
                    $firstLetter = null;
                    foreach ($allIllustratorsArray as $illustrator) {
                      $checked = "";
                      $orderFirst = "";
                        if (isset($bookData['illustrators']) && in_array($illustrator['illustrator_id'], $bookData['illustrators'])) {
                          $checked =  " checked";
                          $orderFirst = " order-first";
                        }
                        echo "
                        <div class='form-check form-check-inline ps-2". $orderFirst . "'>
                            <input class='btn-check' type='checkbox' id='illustrator-{$illustrator['illustrator_id']}' name='illustrators[]' value='{$illustrator['illustrator_id']}' autocomplete='off'" . $checked . ">
                            <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='illustrator-{$illustrator['illustrator_id']}'>{$illustrator['illustrator_name']}</label>
                        </div>";
                    }
                    echo "<button type='button' class='btn btn-dark p-1 px-3 ms-2 rounded-pill' data-bs-toggle='modal' data-bs-target='#newIllustratorModal'>
					        + Ny formgivare eller illustratör
				        </button>";
                ?>
            </div><br><br>
            

            <label class="form-label" for="category-selection">Kategori</label><br>
            <div class="form-floating">
                <input type="text" class="form-control mb-3" id="category-filter-input" placeholder="Filtrera kategorier ...">
                <label for="category-filter-input">Sök och filtrera kategorier ...</label>
            </div>

            <div  class="d-flex flex-wrap row-gap-3" id="category-selection">
                <?php
                    foreach ($allCategoriesArray as $category) {
                      $checked = "";
                      $orderFirst = "";
                        if (isset($bookData['category_id']) && $category['category_id'] == $bookData['category_id']) {
                          $checked =  " checked";
                          $orderFirst = " order-first";
                        }
                        echo "
                        <div class='form-check form-check-inline ps-2" . $orderFirst . "'>
                            <input class='btn-check' type='radio' id='category-{$category['category_id']}' name='category' value='{$category['category_id']}' autocomplete='off' required='required'" . $checked . ">
                            <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='category-{$category['category_id']}'>{$category['category_name']}</label>
                        </div>";
                    }
                ?>
            </div><br><br>



            <label class="form-label" for="genre-selection">Genre(r)</label><br>
            <div class="form-floating">
                <input type="text" class="form-control mb-3" id="genre-filter-input" placeholder="Filtrera genrer ...">
                <label for="genre-filter-input">Sök och filtrera genrer ...</label>
            </div>

            <p id="checked-genres-counter">Valda genrer: 0</p>

            <div class="d-flex flex-wrap row-gap-3" id="genre-selection" style='overflow: auto; max-height: 350px;'>
                <?php
                    $firstLetter = null;
                    foreach ($allGenresArray as $genre) {
                      $checked = "";
                      $orderFirst = "";
                      if (isset($bookData['genres']) && in_array($genre['genre_id'], $bookData['genres'])) {
                        $checked =  " checked";
                        $orderFirst = " order-first";
                      }
                        /*if ($genre['genre_id'] !== 1 && $firstLetter === null || $genre['genre_id'] !== 1 && $firstLetter !==  mb_substr($genre['genre_name'], 0, 1)) {
                            $firstLetter = mb_substr($genre['genre_name'], 0, 1);
                            echo "<div class='w-100 d-block d-sm-none'></div> <div class='ms-0 ms-sm-2 px-2 fs-5 capitalize-first-letter fw-bold border rounded-circle bg-dark-subtle'>{$firstLetter}</div>";
                        }*/
                        echo "<div class='form-check form-check-inline ps-2" . $orderFirst . "'>
                                <input class='btn-check' type='checkbox' id='genre-{$genre['genre_id']}' name='genres[]' value='{$genre['genre_id']}' autocomplete='off'" . $checked . ">
                                <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='genre-{$genre['genre_id']}'>{$genre['genre_name']}</label>
                            </div>";
                    }
                    echo "<button type='button' class='btn btn-dark p-1 px-3 ms-2 rounded-pill' data-bs-toggle='modal' data-bs-target='#newGenreModal'>
					        + Ny genre
				        </button>";
                ?>
            </div><br><br>


            <label class="form-label" for="series-selection">Serie</label><br>
            <div class="form-floating">
                <input type="text" class="form-control mb-3" id="series-filter-input" placeholder="Filtrera serier ...">
                <label for="series-filter-input">Sök och filtrera serier ...</label>
            </div>

            <div class="d-flex flex-wrap row-gap-3" id="series-selection">
                <?php
                    foreach ($allSeriesArray as $series) {
                      $checked = "";
                      $orderFirst = "";
                      if (isset($bookData['series_id']) && $series['series_id'] == $bookData['series_id']) {
                        $checked =  " checked";
                        $orderFirst = " order-first";
                      }
                        echo "<div class='form-check form-check-inline ps-2" . $orderFirst . "'>
                                <input class='btn-check' type='radio' id='series-{$series['series_id']}' name='series' value='{$series['series_id']}' autocomplete='off' required='required'" . $checked . ">
                                <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='series-{$series['series_id']}'>{$series['series_name']}</label>
                            </div>";
                    }
                    echo "<button type='button' class='btn btn-dark p-1 px-3 ms-2 rounded-pill' data-bs-toggle='modal' data-bs-target='#newSeriesModal'>
                            + Ny serie
                        </button>";
                ?>
            </div><br><br>
            

            <label class="form-label" for="language-selection">Språk</label><br>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="language-filter-input" placeholder="Sök och filtrera språk ...">
                <label for="language-filter-input">Sök och filtrera språk ...</label>
            </div>
            <p id="checked-languages-counter">Valda språk: 0</p>

            <div class="d-flex flex-wrap row-gap-3" id="language-selection" style='overflow: auto; max-height: 350px;'>
                <?php
                    foreach ($allLanguagesArray as $language) {
                      $checked = "";
                      $orderFirst = "";
                      if (isset($bookData['languages']) && in_array($language['language_id'], $bookData['languages'])) {
                        $checked =  " checked";
                        $orderFirst = " order-first";
                      }
                        echo "<div class='form-check form-check-inline ps-2" . $orderFirst . "'>
                            <input class='btn-check' type='checkbox' id='language-{$language['language_id']}' name='languages[]' value='{$language['language_id']}' autocomplete='off'" . $checked . ">
                            <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='language-{$language['language_id']}'>{$language['language_name']}</label>
                        </div>";
                    }
                    echo "<button type='button' class='btn btn-dark p-1 px-3 ms-2 rounded-pill' data-bs-toggle='modal' data-bs-target='#newLanguageModal'>
					        + Nytt språk
				        </button>";
                ?>
            </div><br><br>


            <label class="form-label" for="publisher-selection">Förlag</label><br>
            <div class="form-floating mb-3">
                <input type="text" class="form-control mb-3" id="publisher-filter-input" placeholder="Filtrera förlag ...">
                <label for="publisher-filter-input">Sök och filtrera förlag ...</label>
            </div>
            <div class="d-flex flex-wrap row-gap-3" id="publisher-selection">
                <?php
                    foreach ($allPublishersArray as $publisher) {
                      $checked = "";
                      $orderFirst = "";
                      if (isset($bookData['publisher_id']) && $publisher['publisher_id'] == $bookData['publisher_id']) {
                        $checked =  " checked";
                        $orderFirst = " order-first";
                      }
                        echo "<div class='form-check form-check-inline ps-2" . $orderFirst . "'>
                                <input class='btn-check' type='radio' id='publisher-{$publisher['publisher_id']}' name='publisher' value='{$publisher['publisher_id']}' autocomplete='off'" . $checked . ">
                                <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='publisher-{$publisher['publisher_id']}'>{$publisher['publisher_name']}</label>
                            </div>";
                    }
                    echo "<button type='button' class='btn btn-dark p-1 px-3 ms-2 rounded-pill' data-bs-toggle='modal' data-bs-target='#newPublisherModal'>
                            + Nytt förlag
                        </button>";
                ?>
            </div><br><br>


            <label class="form-label" for="age-range-selection">Åldersrekommendation</label><br>
            
            <div class="d-flex flex-wrap row-gap-3" id="age-range-selection">
                <!--<div class='form-check form-check-inline ps-2'>
                    <input class='btn-check' type='radio' id='age-range-0' name='age-range' value='' autocomplete='off' checked>
                    <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='age-range-0'>Ingen åldersrekommendation</label>
                </div>-->
                <?php
                    foreach ($allAgeRangesArray as $ageRange) {
                      $checked = "";
                      $orderFirst = "";
                      if (isset($bookData['age_range_id']) && $ageRange['age_range_id'] == $bookData['age_range_id']) {
                        $checked =  " checked";
                        $orderFirst = " order-first";
                      }
                        echo "<div class='form-check form-check-inline ps-2" . $orderFirst . "'>
                                <input class='btn-check' type='radio' id='age-range-{$ageRange['age_range_id']}' name='age-range' value='{$ageRange['age_range_id']}' autocomplete='off'" . $checked . ">
                                <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='age-range-{$ageRange['age_range_id']}'>{$ageRange['age_range_name']}</label>
                            </div>";
                    }
                ?>
            </div><br><br>


            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="visibility" id="visibility" <?php if ($bookData['visibility'] == 1) {echo "checked";} ?>>
                <label class="form-check-label" for="visibility">
                    Synlig
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="displayed" id="displayed" <?php if ($bookData['display'] == 1) {echo "checked";} ?>>
                <label class="form-check-label" for="displayed">
                    Visa upp på framsidan
                </label>
            </div><br>

			<input class="btn btn-primary py-2" type="submit" name="update-book-submit" value="Spara ändringar">
		</form>
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


<div class="modal fade" id="newIllustratorModal" tabindex="-1" aria-labelledby="newIllustratorModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="newIllustratorModalLabel">Ny formgivare eller illustratör</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
            <form id="new-illustrator-form" action="" method="post">
                <div class="modal-body">
                    <label class="form-label" for="new-illustrator-name">Formgivarens eller illustratörens namn</label><br>
                    <input type="text" class="form-control" id="new-illustrator-name" name="new-illustrator-name" placeholder="För- och efternamn">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
                    <input type="submit" name="new-illustrator-submit" class="btn btn-primary" value="Lägg till">
                </div>
            </form>
		</div>
	</div>
</div>


<div class="modal fade" id="newGenreModal" tabindex="-1" aria-labelledby="newGenreModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="newGenreModalLabel">Ny genre</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
            <form action="" method="post" enctype="multipart/form-data" id="new-genre-form">
                <div class="modal-body">
                    <label class="form-label" for="new-genre-name">Genrens namn</label><br>
                    <input type="text" class="form-control" id="new-genre-name" name="new-genre-name" placeholder="" required="required"><br>

                    <label class="form-label" for="new-genre-img">Genrebild</label><br>
                    <input class="form-control" type="file" id="new-genre-img" name="new-genre-img" required="required"><br>
                    <input type="hidden" name="new-genre-img-field-name" value="new-genre-img">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
                    <input type="submit" name="new-genre-submit" class="btn btn-primary" value="Lägg till">
                </div>
            </form>
		</div>
	</div>
</div>


<div class="modal fade" id="newSeriesModal" tabindex="-1" aria-labelledby="newSeriesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="newSeriesModalLabel">Ny serie</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
            <form action="" method="post" enctype="multipart/form-data" id="new-series-form">
                <div class="modal-body">
                    <label class="form-label" for="new-series-name">Serienamn</label><br>
                    <input type="text" class="form-control" id="new-series-name" name="new-series-name" placeholder="" required="required"><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
                    <input type="submit" name="new-series-submit" class="btn btn-primary" value="Lägg till">
                </div>
            </form>
		</div>
	</div>
</div>


<div class="modal fade" id="newLanguageModal" tabindex="-1" aria-labelledby="newLanguageModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="newLanguageModalLabel">Nytt språk</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
            <form id="new-language-form" action="" method="post">
                <div class="modal-body">
                    <label class="form-label" for="new-language-name">Språknamn</label><br>
                    <input type="text" class="form-control" id="new-language-name" name="new-language-name" placeholder="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
                    <input type="submit" name="new-language-submit" class="btn btn-primary" value="Lägg till">
                </div>
            </form>
		</div>
	</div>
</div>


<div class="modal fade" id="newPublisherModal" tabindex="-1" aria-labelledby="newPublisherModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="newPublisherModalLabel">Nytt förlag</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
            <form id="new-publisher-form" action="" method="post">
                <div class="modal-body">
                    <label class="form-label" for="new-publisher-name">Förlagets namn</label><br>
                    <input type="text" class="form-control" id="new-publisher-name" name="new-publisher-name" placeholder="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
                    <input type="submit" name="new-publisher-submit" class="btn btn-primary" value="Lägg till">
                </div>
            </form>
		</div>
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
// Function to filter authors
function filterAuthors() {
    const input = document.getElementById('author-filter-input');
    const authorSelection = document.getElementById('author-selection');
    const formChecks = authorSelection.querySelectorAll('.form-check');
    const counter = document.getElementById('checked-authors-counter');

    // Function to update the counter
    function updateCounter() {
        const checkedCount = authorSelection.querySelectorAll('.btn-check:checked').length;
        counter.textContent = `Valda författare: ${checkedCount}`;
    }
    
    input.addEventListener('input', () => {
        const searchText = input.value.toLowerCase();

        formChecks.forEach(formCheck => {
            const label = formCheck.querySelector('label');
            if (label.textContent.toLowerCase().includes(searchText)) {
                formCheck.classList.remove('d-none');
            } else {
                formCheck.classList.add('d-none');
            }
        });
    });
    // Listen for checkbox changes to update the counter
    authorSelection.addEventListener('change', updateCounter);

    // Initialize the counter at page load
    updateCounter();
}

document.addEventListener('DOMContentLoaded', () => {
  const authorSelection = document.getElementById('author-selection');
  const checkboxes = authorSelection.querySelectorAll('.btn-check');

  // Add an event listener to each checkbox
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      const parent = checkbox.closest('.form-check');

      if (checkbox.checked) {
        parent.classList.add('order-first'); // Move to the top
      } else {
        parent.classList.remove('order-first'); // Move back to original position
      }
    });
  });
});


// Function to filter illustrators
function filterIllustrators() {
    const input = document.getElementById('illustrator-filter-input');
    const illustratorSelection = document.getElementById('illustrator-selection');
    const formChecks = illustratorSelection.querySelectorAll('.form-check');
    const counter = document.getElementById('checked-illustrators-counter');

    // Function to update the counter
    function updateCounter() {
        const checkedCount = illustratorSelection.querySelectorAll('.btn-check:checked').length;
        counter.textContent = `Valda författare: ${checkedCount}`;
    }
    
    input.addEventListener('input', () => {
        const searchText = input.value.toLowerCase();

        formChecks.forEach(formCheck => {
            const label = formCheck.querySelector('label');
            if (label.textContent.toLowerCase().includes(searchText)) {
                formCheck.classList.remove('d-none');
            } else {
                formCheck.classList.add('d-none');
            }
        });
    });
    // Listen for checkbox changes to update the counter
    illustratorSelection.addEventListener('change', updateCounter);

    // Initialize the counter at page load
    updateCounter();
}

document.addEventListener('DOMContentLoaded', () => {
  const illustratorSelection = document.getElementById('illustrator-selection');
  const checkboxes = illustratorSelection.querySelectorAll('.btn-check');

  // Add an event listener to each checkbox
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      const parent = checkbox.closest('.form-check');

      if (checkbox.checked) {
        parent.classList.add('order-first'); // Move to the top
      } else {
        parent.classList.remove('order-first'); // Move back to original position
      }
    });
  });
});


// Function to filter categories
function filterCategories() {
    const input = document.getElementById('category-filter-input');
    const categorySelection = document.getElementById('category-selection');
    const formChecks = categorySelection.querySelectorAll('.form-check');
    const counter = document.getElementById('checked-categories-counter');

    // Function to update the counter
    function updateCounter() {
        const checkedCount = categorySelection.querySelectorAll('.btn-check:checked').length;
        counter.textContent = `Valda kategorier: ${checkedCount}`;
    }

    input.addEventListener('input', () => {
        const searchText = input.value.toLowerCase();

        formChecks.forEach(formCheck => {
            const label = formCheck.querySelector('label');
            if (label.textContent.toLowerCase().includes(searchText)) {
                formCheck.classList.remove('d-none');
            } else {
                formCheck.classList.add('d-none');
            }
        });
    });/*
    // Listen for checkbox changes to update the counter
    categorySelection.addEventListener('change', updateCounter);

    // Initialize the counter at page load
    updateCounter();*/
}

/*document.addEventListener('DOMContentLoaded', () => {
  const categorySelection = document.getElementById('category-selection');
  const checkboxes = categorySelection.querySelectorAll('.btn-check');

  // Add an event listener to each checkbox
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      const parent = checkbox.closest('.form-check');

      if (checkbox.checked) {
        parent.classList.add('order-first'); // Move to the top
      } else {
        parent.classList.remove('order-first'); // Move back to original position
      }
    });
  });
});*/


// Function to filter genres
function filterGenres() {
    const input = document.getElementById('genre-filter-input');
    const genreSelection = document.getElementById('genre-selection');
    const formChecks = genreSelection.querySelectorAll('.form-check');
    const counter = document.getElementById('checked-genres-counter');

    // Function to update the counter
    function updateCounter() {
        const checkedCount = genreSelection.querySelectorAll('.btn-check:checked').length;
        counter.textContent = `Valda genrer: ${checkedCount}`;
    }
    
    input.addEventListener('input', () => {
        const searchText = input.value.toLowerCase();

        formChecks.forEach(formCheck => {
            const label = formCheck.querySelector('label');
            if (label.textContent.toLowerCase().includes(searchText)) {
                formCheck.classList.remove('d-none');
            } else {
                formCheck.classList.add('d-none');
            }
        });
    });
    // Listen for checkbox changes to update the counter
    genreSelection.addEventListener('change', updateCounter);

    // Initialize the counter at page load
    updateCounter();
}

document.addEventListener('DOMContentLoaded', () => {
  const genreSelection = document.getElementById('genre-selection');
  const checkboxes = genreSelection.querySelectorAll('.btn-check');

  // Add an event listener to each checkbox
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      const parent = checkbox.closest('.form-check');

      if (checkbox.checked) {
        parent.classList.add('order-first'); // Move to the top
      } else {
        parent.classList.remove('order-first'); // Move back to original position
      }
    });
  });
});


function filterSeries() {
    const input = document.getElementById('series-filter-input');
    const seriesSelection = document.getElementById('series-selection');
    const formChecks = seriesSelection.querySelectorAll('.form-check');
    
    input.addEventListener('input', () => {
        const searchText = input.value.toLowerCase();

        formChecks.forEach(formCheck => {
            const label = formCheck.querySelector('label');
            if (label.textContent.toLowerCase().includes(searchText)) {
                formCheck.classList.remove('d-none');
            } else {
                formCheck.classList.add('d-none');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
  const seriesSelection = document.getElementById('series-selection');
  const radios = seriesSelection.querySelectorAll('.btn-check');

  // Add an event listener to each radio
  radios.forEach(radio => {
    radio.addEventListener('change', () => {
      const parent = radio.closest('.form-check');

      radios.forEach(radio => {
        radio.closest('.form-check').classList.remove('order-first');
      });

      if (radio.checked) {
        parent.classList.add('order-first'); // Move to the top
      }
    });
  });
});


// Function to filter languages
function filterLanguages() {
    const input = document.getElementById('language-filter-input');
    const languageSelection = document.getElementById('language-selection');
    const formChecks = languageSelection.querySelectorAll('.form-check');
    const counter = document.getElementById('checked-languages-counter');

    // Function to update the counter
    function updateCounter() {
        const checkedCount = languageSelection.querySelectorAll('.btn-check:checked').length;
        counter.textContent = `Valda språk: ${checkedCount}`;
    }
    
    input.addEventListener('input', () => {
        const searchText = input.value.toLowerCase();
        formChecks.forEach(formCheck => {
            const label = formCheck.querySelector('label');
            if (label.textContent.toLowerCase().includes(searchText)) {
                formCheck.classList.remove('d-none');
            } else {
                formCheck.classList.add('d-none');
            }
        });
    });
    // Listen for checkbox changes to update the counter
    languageSelection.addEventListener('change', updateCounter);

    // Initialize the counter at page load
    updateCounter();
}

document.addEventListener('DOMContentLoaded', () => {
  const languageSelection = document.getElementById('language-selection');
  const checkboxes = languageSelection.querySelectorAll('.btn-check');

  // Add an event listener to each checkbox
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
      const parent = checkbox.closest('.form-check');

      if (checkbox.checked) {
        parent.classList.add('order-first'); // Move to the top
      } else {
        parent.classList.remove('order-first'); // Move back to original position
      }
    });
  });
});


function filterPublishers() {
    const input = document.getElementById('publisher-filter-input');
    const publisherSelection = document.getElementById('publisher-selection');
    const formChecks = publisherSelection.querySelectorAll('.form-check');
    
    input.addEventListener('input', () => {
        const searchText = input.value.toLowerCase();

        formChecks.forEach(formCheck => {
            const label = formCheck.querySelector('label');
            if (label.textContent.toLowerCase().includes(searchText)) {
                formCheck.classList.remove('d-none');
            } else {
                formCheck.classList.add('d-none');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
  const publisherSelection = document.getElementById('publisher-selection');
  const radios = publisherSelection.querySelectorAll('.btn-check');

  // Add an event listener to each radio
  radios.forEach(radio => {
    radio.addEventListener('change', () => {
      const parent = radio.closest('.form-check');

      radios.forEach(radio => {
        radio.closest('.form-check').classList.remove('order-first');
      });

      if (radio.checked) {
        parent.classList.add('order-first'); // Move to the top
      }
    });
  });
});


// Initialize the filter functions
filterAuthors();
filterIllustrators();
filterCategories();
filterGenres();
filterSeries();
filterLanguages();
filterPublishers();
</script>

<?php
include_once 'includes/footer.php';
?>
