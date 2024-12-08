<?php
include_once 'includes/header.php';

$displayedExclusivesArray = $book->getDisplayedExclusives();
var_dump($displayedExclusivesArray);
?>

<script src=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js "></script>
<link href=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css " rel="stylesheet">

<div class="text-center py-5 mb-3 search-background w-100">
    <div class="search-container mw-800 mx-auto py-5 my-4 mx-3 px-3 px-md-5 px-lg-0">
        <label for="searchField" class="display-5 mb-4 text-white fw-normal font-taviraj">Letar du efter något?</label>
        <div class="search-container">
        <input type="text" class="form-control py-3 px-4 mx-auto mt-4 mw-800" id="searchField" placeholder="Sök här ...">
          <div id="searchResults" class="search-results text-start d-none flex-column row-gap-1 py-2 mb-4"></div>
        </div>
    </div>
</div>

<div class="container-fluid w-100">
    <div class="mx-auto mw-1240">

      <h2 class="font-taviraj text-center mt-5 mb-4">Sällsynt och värdefullt</h2>
        <div id="my-slider" class="splide w-100">
            <div class="splide__track">
                <ul class="splide__list">
                  <?php
                    foreach ($displayedExclusivesArray as $exclusive) {
                      echo "<li class='splide__slide font-taviraj'>
                            <div class='card p-2 p-sm-3 position-relative'>
                              <img src='img/{$exclusive['cover_image']}' class='card-img-top card-img mb-3' alt='...'>
                              <div class='d-flex flex-column card-body p-0'>
                                <h5 class='card-title wordbreak-hyphen mb-1'>{$exclusive['title']}</h5>
                                <p class='card-text card-auth-name mb-2'>{$exclusive['authors']}</p>
                                <span class='h5 ms-auto mb-0 fw-semibold'>{$exclusive['price']} €</span>
                                <a href='products.php?id={$exclusive['book_id']}' class='stretched-link'><span></span></a>
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
                  <li class="splide__slide">
                  <div class="card text-bg-dark">
                    <img src="..." class="card-img" alt="...">
                    <div class="card-img-overlay">
                      <h5 class="card-title">Card title</h5>
                      <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                      <p class="card-text"><small>Last updated 3 mins ago</small></p>
                    </div>
                  </div>
                  </li>
                  <li class="splide__slide">Slide 1</li>
                  <li class="splide__slide">Slide 2</li>
                  <li class="splide__slide">Slide 3</li>
                </ul>
            </div>
        </div>      
      <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-light ms-auto" data-bs-toggle="modal" data-bs-target="#carModal">
					Redigera Sällsynt och värdefullt
				</button>
      </div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var splide = new Splide('#my-slider', {
      type   : 'loop',     // Carousel type: 'slide', 'loop', or 'fade'
      perPage: 5,          // Number of slides to show per page
      perMove: 1,          // Number of slides to move per navigation
      autoplay: false,      // Auto-play the slider
      gap    : '2rem',     // Gap between slides
      breakpoints: {
        992: {
            perPage: 4,
        },
        767: {
            perPage: 3,
        },
        575: {
            perPage: 2.2,
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
