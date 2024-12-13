<?php  
include_once 'includes/header.php';


$currentYear = date("Y");   

$previousYear = $currentYear - 1;
$twoYearsAgo = $currentYear - 2;

$allBookPublishingYears = $book->getAllExclusivePublishingYears();
$newestExclusivePublishingYear = $book->getNewestExclusivePublishingYear();
$oldestExclusivePublishingYear = $book->getOldestExclusivePublishingYear();

$allCategoriesArray = $book->getAllCategories();

/*if (isset($_GET['categories'])) {
    $allGenresArray = $book->getFilteredGenres($_GET['categories']);
} else {}*/
    $allGenresArray = $book->getAllGenres();

$allLanguagesArray = $book->getAllLanguages();
$allSeriesArray = $book->getAllSeries();
$allAgeRangesArray = $book->getAllAgeRanges();
$allPublishersArray = $book->getAllPublishers();


//var_dump($booksArray);

if (
    isset($_GET['categories']) || 
    isset($_GET['genres']) || 
    isset($_GET['languages']) || 
    isset($_GET['series']) || 
    isset($_GET['age_ranges']) || 
    isset($_GET['publishers']) || 
    isset($_GET['oldest']) || 
    isset($_GET['newest'])
) {
    $booksArray = $book->filterExclusives(
        $_GET['categories'] ?? [], 
        $_GET['genres'] ?? [], 
        $_GET['languages'] ?? [], 
        $_GET['series'] ?? [], 
        $_GET['age_ranges'] ?? [],
        $_GET['publishers'] ?? [],
        $_GET['oldest'] ?? null,
        $_GET['newest'] ?? null
    );
} else {
    $booksArray = $book->getAllExclusives();
}
?>

<div class="container">
    <div class="mx-auto mw-1240">
    <h1 class="my-5 font-taviraj">Böcker</h1>
        <div class="container mt-4">
            <div class="row column-gap-4">
            <div class="col-12 col-lg-3 col-xl-2 p-0 mb-5">

            <div id="category-field" class="shadow font-taviraj">
                <form action="" method="get">
                    <div class="accordion accordion-flush" id="accordion-flush-1">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-1" aria-expanded="false" aria-controls="flush-collapse-1">
                                Kategorier
                            </button>
                            </h2>
                            <div id="flush-collapse-1" class="accordion-collapse collapse">
                                <div class="accordion-body p-2 pb-3 ps-3">
                                    <?php
                                        foreach ($allCategoriesArray as $category) {
                                            echo "
                                            <div class='form-check my-2 pt-2'>
                                                <input class='form-check-input border-dark-subtle rounded-0' type='checkbox' id='category-{$category['category_id']}' name='categories[]' value='{$category['category_id']}' autocomplete='off' onchange='this.form.submit();'";
                                            if (isset($_GET['categories']) && in_array($category['category_id'], $_GET['categories'])) {
                                                echo "checked";
                                            }   
                                            echo ">
                                                <label class='form-check-label capitalize-first-letter ps-1' for='category-{$category['category_id']}'>{$category['category_name']}</label>
                                            </div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>



                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-2" aria-expanded="false" aria-controls="flush-collapse-2">
                                Genrer
                            </button>
                            </h2>
                            <div id="flush-collapse-2" class="accordion-collapse collapse">
                                <div class="accordion-body p-2 pb-3 ps-3">
                                    <?php
                                        foreach ($allGenresArray as $genre) {
                                            if ($genre['genre_id'] == 1) {
                                                continue;
                                            }
                                            echo "
                                            <div class='form-check my-2 pt-2'>
                                                <input class='form-check-input border-dark-subtle rounded-0' type='checkbox' id='genre-{$genre['genre_id']}' name='genres[]' value='{$genre['genre_id']}' autocomplete='off' onchange='this.form.submit();'";
                                            if (isset($_GET['genres']) && in_array($genre['genre_id'], $_GET['genres'])) {
                                                echo "checked";
                                            }           
                                            echo ">
                                                <label class='form-check-label capitalize-first-letter ps-1' for='genre-{$genre['genre_id']}'>{$genre['genre_name']}</label>
                                            </div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>



                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-3" aria-expanded="false" aria-controls="flush-collapse-3">
                                Språk
                            </button>
                            </h2>
                            <div id="flush-collapse-3" class="accordion-collapse collapse">
                                <div class="accordion-body p-2 pb-3 ps-3">
                                    <?php
                                        foreach ($allLanguagesArray as $language) {
                                            echo "
                                            <div class='form-check my-2 pt-2'>
                                                <input class='form-check-input border-dark-subtle rounded-0' type='checkbox' id='language-{$language['language_id']}' name='languages[]' value='{$language['language_id']}' autocomplete='off' onchange='this.form.submit();'";
                                            if (isset($_GET['languages']) && in_array($language['language_id'], $_GET['languages'])) {
                                                echo "checked";
                                            }    
                                            echo ">
                                                <label class='form-check-label capitalize-first-letter ps-1' for='language-{$language['language_id']}'>{$language['language_name']}</label>
                                            </div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>



                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-4" aria-expanded="false" aria-controls="flush-collapse-4">
                                Serie
                            </button>
                            </h2>
                            <div id="flush-collapse-4" class="accordion-collapse collapse">
                                <div class="accordion-body p-2 pb-3 ps-3">
                                    <?php
                                        foreach ($allSeriesArray as $series) {
                                            echo "
                                            <div class='form-check my-2 pt-2'>
                                                <input class='form-check-input border-dark-subtle rounded-0' type='checkbox' id='series-{$series['series_id']}' name='series[]' value='{$series['series_id']}' autocomplete='off' onchange='this.form.submit();'";
                                            if (isset($_GET['series']) && in_array($series['series_id'], $_GET['series'])) {
                                                echo "checked";
                                            }                                     
                                            echo ">
                                                <label class='form-check-label capitalize-first-letter ps-1' for='series-{$series['series_id']}'>{$series['series_name']}</label>
                                            </div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>



                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-5" aria-expanded="false" aria-controls="flush-collapse-5">
                                Ålder
                            </button>
                            </h2>
                            <div id="flush-collapse-5" class="accordion-collapse collapse">
                                <div class="accordion-body p-2 pb-3 ps-3">
                                    <?php
                                        foreach ($allAgeRangesArray as $ageRange) {
                                            echo "
                                            <div class='form-check my-2 pt-2'>
                                                <input class='form-check-input border-dark-subtle rounded-0' type='checkbox' id='age-range-{$ageRange['age_range_id']}' name='age_ranges[]' value='{$ageRange['age_range_id']}' autocomplete='off' onchange='this.form.submit();'";
                                            if (isset($_GET['age_ranges']) && in_array($ageRange['age_range_id'], $_GET['age_ranges'])) {
                                                echo "checked";
                                            }
                                            echo ">
                                                <label class='form-check-label capitalize-first-letter ps-1' for='age-range-{$ageRange['age_range_id']}'>{$ageRange['age_range_name']}</label>
                                            </div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>



                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-6" aria-expanded="false" aria-controls="flush-collapse-6">
                                Utgiven
                            </button>
                            </h2>
                            <div id="flush-collapse-6" class="accordion-collapse collapse">
                                <div class="accordion-body p-3">

                                    <label class="mt-2" for="newest">Nyaste:</label>
                                    <select class="form-select mb-3" id="newest" name="newest" aria-label="Size 3 select example"  onchange='this.form.submit();'>
                                        <?php
                                        
                                        foreach ($allBookPublishingYears as $publishedYear) {
                                            $year = $publishedYear['published_year'];
                                            $selectAttribute = "";
                                            if (isset($_GET['oldest']) && $_GET['oldest'] > $year) {
                                                $selectAttribute = "disabled";
                                            }
                                            echo "<option value='{$year}' "; 
                                            if(!isset($_GET['newest']) && $year == $newestExclusivePublishingYear['newest_year'] || isset($_GET['newest']) && $_GET['newest'] == $year) {
                                                $selectAttribute = "selected";
                                            }
                                            echo "{$selectAttribute}>{$year}</option>";
                                        }   
                                        ?>                                    
                                    </select>
                                    
                                    <label for="oldest">Äldsta:</label>
                                    <select class="form-select mb-2" id="oldest" name="oldest" aria-label="Size 3 select example"  onchange='this.form.submit();'>
                                        <?php
                                        
                                        foreach ($allBookPublishingYears as $publishedYear) {     
                                            $year = $publishedYear['published_year'];
                                            $selectAttribute = "";
                                            if (isset($_GET['newest']) && $_GET['newest'] < $year) {
                                                $selectAttribute = "disabled";
                                            }
                                            echo "<option value='{$year}' "; 
                                            if(!isset($_GET['oldest']) && $year == $oldestExclusivePublishingYear['oldest_year'] || isset($_GET['oldest']) && $_GET['oldest'] == $year) {
                                                $selectAttribute = "selected";
                                            }
                                            echo "{$selectAttribute}>{$year}</option>";
                                        }   
                                        ?>                                    
                                    </select>

                                </div>
                            </div>
                        </div>



                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-7" aria-expanded="false" aria-controls="flush-collapse-7">
                                Förlag
                            </button>
                            </h2>
                            <div id="flush-collapse-7" class="accordion-collapse collapse">
                                <div class="accordion-body p-2 pb-3 ps-3">
                                    <?php
                                        foreach ($allPublishersArray as $publisher) {
                                            echo "
                                            <div class='form-check my-2 pt-2'>
                                                <input class='form-check-input border-dark-subtle rounded-0' type='checkbox' id='publisher-{$publisher['publisher_id']}' name='publishers[]' value='{$publisher['publisher_id']}' autocomplete='off' onchange='this.form.submit();'";
                                            if (isset($_GET['publishers']) && in_array($publisher['publisher_id'], $_GET['publishers'])) {
                                                echo "checked";
                                            }
                                            echo ">
                                                <label class='form-check-label capitalize-first-letter ps-1' for='publisher-{$publisher['publisher_id']}'>{$publisher['publisher_name']}</label>
                                            </div>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        </div>


                </form>
            </div>

            </div>

            <div class="col-12 col-lg p-0 ms-lg-2">
                <div id="bookContainer font-taviraj">
                <div class="row g-3">
                <?php
                    foreach ($booksArray as $book) {
                      echo "<div class='col-6 col-md-4 col-xl-3 d-flex align-items-stretch'>
                            <div class='card book-card w-100 p-3 rounded-0 border-0 shadow position-relative font-taviraj'>
                              <img src='img/{$book['cover_image']}' class='card-img-top card-img mb-3' alt='...'>
                              <div class='d-flex flex-column card-body p-0 px-xxl-1'>
                                <h5 class='card-title wordbreak-hyphen mb-1' lang='sv'>{$book['title']}</h5>
                                <p class='card-text card-auth-name mb-2'>{$book['authors']}</p>
                                <span class='h5 ms-auto mt-auto mb-0 fw-semibold'>{$book['price']} €</span>
                                <a href='product.php?id={$book['book_id']}' class='stretched-link'><span></span></a>
                              </div>
                            </div>
                            </div>
                            ";
                    }
                  ?>
                </div>
                </div>
                <button id="loadMore" class="btn btn-primary mt-3">Läs in fler</button>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Get all elements with the class 'accordion-collapse'
    const accordionDivs = document.querySelectorAll('.accordion-collapse');

    // Iterate through each div
    accordionDivs.forEach(div => {
      // Check if there is a checked checkbox inside the div
      const checkedCheckbox = div.querySelector('input[type="checkbox"]:checked');
      if (checkedCheckbox) {
        console.log("Checked");
        // Add the 'show' class if a checked checkbox is found
        div.classList.add('show');

        let accordionButton = div.closest('.accordion-item').querySelector('.accordion-button');

        if (accordionButton) {
            // Remove the 'collapsed' class
            accordionButton.classList.remove('collapsed');
            // Set 'aria-expanded' attribute to 'true'
            accordionButton.setAttribute('aria-expanded', 'true');
        }
      }
    });
  </script>


<?php
include_once 'includes/footer.php';
?>
