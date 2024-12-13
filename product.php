<?php  
include_once 'includes/header.php';

$bookDataArray = $book->getBookData($_GET['id']);
//var_dump($bookDataArray);
?>

<div class="container-fluid w-100">
    <div class="mx-auto mw-1240">
        <div class="container mt-4">
            <div class="row gap-2">
                <div class="col-12 col-lg-4 d-flex justify-content-center align-items-start pt-5 mb-3 mb-lg-0">
                    <img src="img/<?php echo $bookDataArray['cover_image'] ?>" class="bookpage-cover-img     shadow" alt="...">
                </div>
                <div class="col-12 col-lg px-4 px-xl-5 py-5 me-5 shadow">
                    <h1 class="font-taviraj"><?php echo $bookDataArray['title']; ?></h1>
                    <p class="h5 fw-normal mb-4 font-taviraj"><?php echo $bookDataArray['authors']; ?></p>
                    <p class="h2 mb-5 font-taviraj"> <?php echo $bookDataArray['price']; ?> €</p>
                    <p class="h4 fw-normal font-taviraj mb-3">Beskrivning:</p>
                    <p class="ms-2"><?php echo $bookDataArray['description']; ?></p>

                    <div>
                        <h2 class="h4 fw-normal font-taviraj mt-5 mb-3">Produktinformation:</h2>
                        <div class="bookpage-product-info flex-wrap border p-4 ms-2">
                            <?php if ($bookDataArray['authors'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Författare:</span>  {$bookDataArray['authors']}</p>";};?>
                            <?php if ($bookDataArray['illustrators'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Formgivare eller illustratör:</span>  {$bookDataArray['illustrators']}</p>";};?>
                            <?php if ($bookDataArray['age_range_name'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Åldersrekommendation:</span>  {$bookDataArray['age_range_name']}</p>";};?>
                            <?php if ($bookDataArray['category_name'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Kategori:</span> {$bookDataArray['category_name']}</p>";};?>
                            <?php if ($bookDataArray['genres'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Genrer:</span> {$bookDataArray['genres']}</p>";};?>
                            <?php if ($bookDataArray['series_name'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Serie:</span> {$bookDataArray['series_name']}</p>";};?>
                            <?php if ($bookDataArray['languages'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Språk:</span> {$bookDataArray['languages']}</p>";};?>
                            <?php if ($bookDataArray['date_published'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Utgiven:</span> {$bookDataArray['date_published']}</p>";};?>
                            <?php if ($bookDataArray['publisher_name'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Förlag:</span> {$bookDataArray['publisher_name']}</p>";};?>
                            <?php if ($bookDataArray['page_amount'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Antal sidor:</span> {$bookDataArray['page_amount']}</p>";};?>
                            <?php if ($bookDataArray['price'] !== NULL) {echo "<p class='mb-1 pe-4'><span class='fw-semibold'>Pris:</span> {$bookDataArray['price']} €</p>";};?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
