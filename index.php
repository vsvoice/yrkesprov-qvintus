<?php
include_once 'includes/header.php';

$user->checkLoginStatus();

$displayedExclusivesArray = $book->getDisplayedExclusives();
$displayedBooksArray = $book->getDisplayedBooks();
$displayedGenresArray = $book->getDisplayedGenres();
$displayedGenresWithNoAvailableBooksArray = $book->getDisplayedGenresWithNoAvailableBooks();
//var_dump($displayedExclusivesArray);
//var_dump($displayedGenresArray);

$allAvailableGenresArray = $book->getAllGenresWithAvailableProducts();
//var_dump($allAvailableGenresArray);


if(isset($_POST['edit-popular-genres-submit'])) {
  $errorMessage = $book->updateDisplayedGenres($_POST['genres']);
  if(isset($errorMessage) && $errorMessage === true) {
    $_SESSION['success_message'] = "Populära genrer har uppdaterats.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  }
}

if(isset($_POST['update-displayed-exclusive-submit'])) {
  $errorMessage = $book->updateDisplayedExclusive($_POST['displayed-exclusive-id'], $_POST['change-displayed-exclusive-to']);
  if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "Sällsynt och värdefullt har uppdaterats.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
  }
}

if(isset($_POST['update-displayed-book-submit'])) {
  $errorMessage = $book->updateDisplayedBook($_POST['displayed-book-id'], $_POST['change-displayed-book-to']);
  if(isset($errorMessage) && $errorMessage === true) {
      $_SESSION['success_message'] = "Populärt just nu har uppdaterats.";
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
  }
}
?>

<script src=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css " rel="stylesheet">

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
    </div>
<div class="text-center py-5 mb-3 search-background w-100">
    <div class="search-container mw-800 mx-auto py-4 my-4 mx-3 px-3 px-md-5 px-lg-0">
        <label for="searchField" class="display-5 mb-3 mt-lg-4 text-white fw-normal font-taviraj">Letar du efter något?</label>
        <div class="search-container mb-lg-3 mb-xl-4">
        <input type="text" class="form-control py-3 px-4 mx-auto mt-3 mt-lg-4 mw-800" id="searchField" placeholder="Sök här ...">
          <div id="searchResults" class="search-results text-start d-none flex-column row-gap-1 py-2 mb-4"></div>
        </div>
        <div class="pb-3"></div>
    </div>
</div>

<div class="container-fluid w-100">
    <div class="mx-auto mw-1240">

      <h2 class="font-taviraj text-center pt-3 mt-5 mb-4">Sällsynt och värdefullt</h2>
        <div id="my-slider" class="splide w-100">
            <div class="splide__track pt-2 pb-5 border border-top-0 border-bottom-0">
                <ul class="splide__list">
                  <?php
                    foreach ($displayedExclusivesArray as $exclusive) {
                      $exclusiveBadge = "";
                      if ($exclusive['is_exclusive'] == 1) {
                        $exclusiveBadge = "<span class='badge rounded-pill text-bg-warning me-auto fw-semibold'>Exklusivt</span>";
                      }
                      echo "<li class='splide__slide font-taviraj d-flex align-items-stretch'>
                            <div class='card book-card w-100 p-3 rounded-0 border-0 shadow position-relative font-taviraj'>
                              <div class='mx-auto'>
                                <img src='img/{$exclusive['cover_image']}' class='card-img-top card-img mb-3' alt='...'>
                              </div>
                              <div class='d-flex flex-column card-body p-0'>
                                <h5 class='card-title wordbreak-hyphen mb-1'>{$exclusive['title']}</h5>
                                <p class='card-text card-auth-name mb-2'>{$exclusive['authors']}</p>" . 
                                $exclusiveBadge
                                . "<span class='h5 ms-auto mt-auto mb-0 fw-semibold'>" . number_format($exclusive['price'], 2, ',', ' ') . " €</span>
                                <a href='product.php?id={$exclusive['book_id']}' class='stretched-link'><span></span></a>
                              </div>
                            </div>
                            </li>";
                    }
                  ?>
                  <!--<li class='splide__slide font-taviraj'>
                    <div class='card bg-body-secondary'>
                      <img src='img/{$exclusive['cover_image']}' class='card-img mb-4 mb-sm-0' alt='...'>
                      <div class='d-flex card-img-overlay p-0 align-items-end'>
                        <div class='d-flex flex-column card-body bg-white w-100 p-2 p-md-3' style='--bs-bg-opacity: .85;'>
                          <h5 class='card-title mb-1'>{$exclusive['title']}</h5>
                          <p class='card-text card-auth-name mb-2'>Förafattarens namn</p>
                          <span class='h5 ms-auto mb-0 fw-semibold'>{$exclusive['price']} €</span>
                        </div>
                      </div>
                    </div>
                  </li>-->
                </ul>
            </div>
        </div>      
      <div class="d-flex justify-content-end mt-3">
        <?php
        if (isset($_SESSION['user_id'])) {
          if ($user->checkUserRole(50)) {
            echo "
            <button type='button' class='btn btn-light ms-auto' data-bs-toggle='modal' data-bs-target='#exclusivesModal'>
              Redigera Sällsynt och värdefullt
            </button>";
          }
        }
        ?>
        <a href="exclusives.php" class="btn btn-light ms-3">
					Se mer ...
        </a>
      </div>

      <h2 class="font-taviraj text-center pt-2 mt-5 mb-4 mt-5">Populära genrer</h2>
      <div class="row px-sm-4 g-3 g-sm-4">
        <?php
        foreach ($displayedGenresArray as $genre) {
          $genreImage = "";
          if(isset($genre['genre_image']) && empty($genre['genre_image'])) {
            $genreImage = "genre-placeholder.png";
          } else {
            $genreImage = $genre['genre_image'];
          }
          echo "
          <div class='col-6 col-md-4 col-lg-3 d-flex align-items-stretch'>
            <div class='card genre-card bg-body-secondary rounded-0 border-0 shadow position-relative font-taviraj'>
              <img src='img/{$genreImage}' class='card-img h-100 rounded-0 mb-0' alt='...'>
              <div class='d-flex card-img-overlay p-0 align-items-end'>
                <div class='d-flex flex-row card-body align-items-center bg-white w-100 ps-3 ps-sm-4 py-2 py-sm-3' style='--bs-bg-opacity: .85;'>
                  <h3 class='h4 mb-1'>{$genre['genre_name']}</h3>
                  <a href='products.php?genres[]={$genre['genre_id']}' class='stretched-link'><span></span></a>
                </div>
              </div>
            </div>
          </div>";
        }
        ?>
      </div>
      <?php
        if (isset($_SESSION['user_id'])) {
          if ($user->checkUserRole(50)) {
            echo "
            <div class='d-flex justify-content-end mt-3'>
              <button type='button' class='btn btn-light ms-auto' data-bs-toggle='modal' data-bs-target='#genresModal'>
                Redigera Populära genrer
              </button>
            </div>";
          }
        }
        ?>
      

      <h2 class="font-taviraj text-center pt-5 mt-5 mb-2 mt-5">Populärt just nu</h2>
      <div class="row px-sm-4">
        <?php
        foreach ($displayedBooksArray as $book) {
          echo "
          <div class='col-6 col-sm-4 col-md-3 col-xl-2 g-3 d-flex align-items-stretch'>
            <div class='card book-card w-100 p-3 rounded-0 border-0 shadow position-relative font-taviraj'>
              <div class='mx-auto'>
                <img src='img/{$book['cover_image']}' class='card-img-top card-img mb-3' alt='...'>
              </div>
              <div class='d-flex flex-column card-body p-0'>
                <h5 class='card-title wordbreak-hyphen mb-1'>{$book['title']}</h5>
                <p class='card-text card-auth-name mb-2'>{$book['authors']}</p>
                <span class='h5 ms-auto mb-0 mt-auto fw-semibold'>" . number_format($book['price'], 2, ',', ' ') . " €</span>
                <a href='product.php?id={$book['book_id']}' class='stretched-link'><span></span></a>
              </div>
            </div>
          </div>";
        }
        ?>
      </div>
      <div class="d-flex justify-content-end mt-3 pb-2 pb-lg-4">
        <?php
        if (isset($_SESSION['user_id'])) {
          if ($user->checkUserRole(50)) {
            echo "
            <button type='button' class='btn btn-light ms-auto' data-bs-toggle='modal' data-bs-target='#booksModal'>
              Redigera Populärt just nu
            </button>";
          }
        }
        ?>
        <a href="books.php" class="btn btn-light ms-3">
					Se mer ...
        </a>
      </div>
    </div>
</div>

<div class="text-center py-4 py-lg-5 mt-5 mb-3 search-background w-100">
  <div class="mw-800 mx-auto py-4 my-4 mx-3 px-3 px-md-5 px-lg-0">
    <h2 for="searchField" class="display-6 mb-4 text-white fw-normal font-taviraj">Hittar inte det du söker?</h2>
    <p class="text-white font-taviraj fs-4">Inga problem, vi fixar de flesta önskemål, stora som små.</p>
    <a href="contact.php" class="btn btn-warning fs-5 fw-semibold rounded-pill px-4 py-3 mt-3 font-taviraj">Gör ett önskemål</a>
  </div>
</div>

<div class="container pt-4 pb-5">
  <div class="mx-auto mw-1240">
    <div class="row d-flex mx-2 mt-5 shadow">

      <div class="order-2 order-lg-1 col-lg-6 gx-0 d-flex align-items-stretch">

        <div class='card w-100 p-2 p-sm-3 pt-4 rounded-0 border-0 position-relative font-taviraj'>
          <div class='d-flex flex-column card-body align-items-center'>
            <h2 class='wordbreak-hyphen mb-3 text-center'>Hälsning från Qvintus</h5>
            <p class='text-center align-self-center mt-4'>Det gläder mig att du har hittat hit! Hos oss är varje bok en berättelse, inte bara i sitt innehåll, utan också genom sin resa genom tid och händer. Oavsett om du letar efter en sällsynt skatt, en älskad klassiker eller bara vill njuta av atmosfären bland böcker, hoppas jag att du ska trivas här.</p>
            <p class='text-center align-self-center mt-2'>Varmt välkommen in – både här på webben och i vårt antikvariat i Tammerfors.</p>
            <p class='fs-5 fst-italic ms-auto mt-4 mb-0'>- Magnus Qvintus</p>
          </div>
        </div>

      </div>


      <div class="order-1 order-lg-2 col-lg-6 gx-0 d-flex align-items-stretch">

        <div class='card w-100 image-card rounded-0 border-0 position-relative font-taviraj'>
          <img src="assets/tolkein.webp" class="card-img h-100 rounded-0">
        </div>

      </div>

    </div>
  </div>
</div>


<?php
if (isset($_SESSION['user_id'])) {
  if ($user->checkUserRole(50)) {
    echo <<<HTML
    <div class="modal fade" id="exclusivesModal" tabindex="-1" aria-labelledby="exclusivesModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exclusivesModalLabel">Populära genrer</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
            <div class="p-3">

            <div class="search-container">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="searchExclusivesField" placeholder="Sök efter exklusivt ...">
                <label for="searchExclusivesField">Sök efter exklusivt ...</label>
                <div id="searchExclusivesResults" class="search-results text-start d-none flex-column row-gap-1 py-2 mb-4"></div>
              </div>
            </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
            </div>

        </div>
      </div>
    </div>

    <div class="modal fade" id="genresModal" tabindex="-1" aria-labelledby="genresModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header">
            <h1 class="modal-title fs-5" id="genresModalLabel">Populära genrer</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        
          <form id="new-publisher-form" action="" method="post">

            <div class="p-3">
              <p>Genrer med synliga böcker: </p>

              <div class="d-flex flex-wrap row-gap-3" id="genre-selection" style='overflow: auto; max-height: 350px;'>
    HTML;

    // PHP Code to dynamically populate genres
    $displayedGenreIds = array_column($displayedGenresArray, 'genre_id');
    foreach ($allAvailableGenresArray as $genre) {
      $checked = "";
      $orderFirst = "";
      if (in_array($genre['genre_id'], $displayedGenreIds)) {
        $checked = " checked";
        $orderFirst = " order-first";
      }
      echo "
      <div class='form-check form-check-inline ps-1$orderFirst'>
        <input class='btn-check' type='checkbox' id='genre-{$genre['genre_id']}' name='genres[]' value='{$genre['genre_id']}' autocomplete='off'$checked>
        <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='genre-{$genre['genre_id']}'>{$genre['genre_name']}</label>
      </div>";
    }

    echo <<<HTML
              </div>

              <p class="mt-4">Markerade genrer utan synliga böcker:</p>

              <div class="d-flex flex-wrap row-gap-3" id="genre-selection2" style='overflow: auto; max-height: 350px;'>
    HTML;

    // PHP Code for genres with no available books
    foreach ($displayedGenresWithNoAvailableBooksArray as $notAvailableGenre) {
      echo "
      <div class='form-check form-check-inline ps-2'>
        <input class='btn-check' type='checkbox' id='genre-{$notAvailableGenre['genre_id']}' name='genres[]' value='{$notAvailableGenre['genre_id']}' autocomplete='off' checked>
        <label class='btn btn-outline-dark capitalize-first-letter p-1 px-3 rounded-pill' for='genre-{$notAvailableGenre['genre_id']}'>{$notAvailableGenre['genre_name']}</label>
      </div>";
    }

    echo <<<HTML
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
              <input type="submit" name="edit-popular-genres-submit" class="btn btn-primary" value="Spara">
            </div>

          </form>

        </div>
      </div>
    </div>
    
    <div class="modal fade" id="booksModal" tabindex="-1" aria-labelledby="booksModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header">
            <h1 class="modal-title fs-5" id="booksModalLabel">Populärt just nu</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
            <div class="p-3">

            <div class="search-container">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="searchBooksField" placeholder="Sök efter böcker ...">
                <label for="searchBooksField">Sök efter böcker ...</label>
                <div id="searchBooksResults" class="search-results text-start d-none flex-column row-gap-1 py-2 mb-4"></div>
              </div>
            </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary me-auto py-2" data-bs-dismiss="modal">Tillbaka</button>
            </div>

        </div>
      </div>
    </div>
    HTML;
  }
}

?>






<script>
const refreshButton = document.getElementById('refresh-page-btn');
if (refreshButton) {
    refreshButton.addEventListener('click', function () {
        // Reload the page cleanly (GET request only)
        window.location.href = window.location.href.split('?')[0];
    });
}


  document.addEventListener('DOMContentLoaded', function () {
    var splide = new Splide('#my-slider', {
      type   : 'loop',     // Carousel type: 'slide', 'loop', or 'fade'
      perPage: 5,          // Number of slides to show per page
      perMove: 1,          // Number of slides to move per navigation
      autoplay: false,      // Auto-play the slider
      gap    : '1rem',     // Gap between slides
      padding: '2rem',
      breakpoints: {
        992: {
            perPage: 4,
        },
        767: {
            perPage: 3,
        },
        575: {
            perPage: 2,
            padding: '1rem',
        },
      }
    });

    splide.mount(); // Mount the Splide slider
  });
  

  document.getElementById('searchField').addEventListener('input', function () {
    const query = this.value;
    const resultsDiv = document.getElementById('searchResults');

    if (query.trim() === "") {
        resultsDiv.innerHTML = ""; // Clear results if query is empty
        resultsDiv.classList.add('d-none');
        resultsDiv.classList.remove('d-flex');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/search_products.php', true); // Replace with the path to your PHP file
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (this.status === 200) {
            resultsDiv.innerHTML = this.responseText; // Display results
            resultsDiv.classList.remove('d-none');
            resultsDiv.classList.add('d-flex');
        } else {
            resultsDiv.innerHTML = "<p class='text-danger'>Error loading results.</p>";
            resultsDiv.classList.remove('d-none');
            resultsDiv.classList.add('d-flex');
        }
    };

    xhr.send('query=' + encodeURIComponent(query));
});

document.getElementById('searchExclusivesField').addEventListener('input', function () {
    const query = this.value;
    const resultsDiv = document.getElementById('searchExclusivesResults');

    if (query.trim() === "") {
        resultsDiv.innerHTML = ""; // Clear results if query is empty
        resultsDiv.classList.add('d-none');
        resultsDiv.classList.remove('d-flex');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/search_exclusives.php', true); // Replace with the path to your PHP file
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (this.status === 200) {
            resultsDiv.innerHTML = this.responseText; // Display results
            resultsDiv.classList.remove('d-none');
            resultsDiv.classList.add('d-flex');
        } else {
            resultsDiv.innerHTML = "<p class='text-danger'>Error loading results.</p>";
            resultsDiv.classList.remove('d-none');
            resultsDiv.classList.add('d-flex');
        }
    };

    xhr.send('query=' + encodeURIComponent(query));
});


document.addEventListener('DOMContentLoaded', () => {
  const languageSelection = document.getElementById('genre-selection');
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


document.getElementById('searchBooksField').addEventListener('input', function () {
    const query = this.value;
    const resultsDiv = document.getElementById('searchBooksResults');

    if (query.trim() === "") {
        resultsDiv.innerHTML = ""; // Clear results if query is empty
        resultsDiv.classList.add('d-none');
        resultsDiv.classList.remove('d-flex');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/search_books.php', true); // Replace with the path to your PHP file
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (this.status === 200) {
            resultsDiv.innerHTML = this.responseText; // Display results
            resultsDiv.classList.remove('d-none');
            resultsDiv.classList.add('d-flex');
        } else {
            resultsDiv.innerHTML = "<p class='text-danger'>Error loading results.</p>";
            resultsDiv.classList.remove('d-none');
            resultsDiv.classList.add('d-flex');
        }
    };

    xhr.send('query=' + encodeURIComponent(query));
});
</script>

<?php
include_once 'includes/footer.php';
?>
