<?php  
include_once 'includes/header.php';

$bookDataArray = $book->getBookData($_GET['id']);
$recommendedBooksArray = $book->getBooksByRandomGenre($_GET['id']);
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
                    <p class="h2 mb-5 font-taviraj"> <?php echo number_format($bookDataArray['price'], 2, ',', ' '); ?> €</p>
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        if ($bookDataArray['user_id_fk'] == $_SESSION['user_id'] || $user->checkUserRole(50)) {
                            echo "<a href='editbook.php?id=" . $_GET['id'] . "' class='btn btn-warning mb-4'>Redigera bok</a>";
                        }
                    }
                    ?>

                    <p class="h4 fw-normal font-taviraj mb-3">Beskrivning:</p>
                    <p class="ms-2 font-taviraj"><?php echo nl2br($bookDataArray['description']); ?></p>

                    <div class="font-taviraj">
                        <h2 class="h4 fw-normal font-taviraj mt-5 mb-3">Produktinformation:</h2>
                        <div class="bookpage-product-info flex-wrap border p-4 ms-2">
                            <?php if ($bookDataArray['authors'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Författare:</span>  {$bookDataArray['authors']}</p>";};?>
                            <?php if ($bookDataArray['illustrators'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Formgivare eller illustratör:</span>  {$bookDataArray['illustrators']}</p>";};?>
                            <?php if ($bookDataArray['age_range_name'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Åldersrekommendation:</span>  {$bookDataArray['age_range_name']}</p>";};?>
                            <?php if ($bookDataArray['category_name'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Kategori:</span> {$bookDataArray['category_name']}</p>";};?>
                            <?php if ($bookDataArray['genres'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Genrer:</span> {$bookDataArray['genres']}</p>";};?>
                            <?php if ($bookDataArray['series_name'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Serie:</span> {$bookDataArray['series_name']}</p>";};?>
                            <?php if ($bookDataArray['languages'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Språk:</span> {$bookDataArray['languages']}</p>";};?>
                            <?php if ($bookDataArray['date_published'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Utgiven:</span> {$bookDataArray['date_published']}</p>";};?>
                            <?php if ($bookDataArray['publisher_name'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Förlag:</span> {$bookDataArray['publisher_name']}</p>";};?>
                            <?php if ($bookDataArray['page_amount'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Antal sidor:</span> {$bookDataArray['page_amount']}</p>";};?>
                            <?php if ($bookDataArray['price'] !== NULL) {echo "<p class='mb-2 pe-4'><span class='fw-semibold'>Pris:</span> " . number_format($bookDataArray['price'], 2, ',', ' ')  . " €</p>";};?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if (isset($recommendedBooksArray) && !empty($recommendedBooksArray)) {
            ?>
            <h2 class="font-taviraj text-center pt-5 mt-5 mb-4 mt-5">Du kanske gillar</h2>
            <div class="row">
                <?php
                foreach ($recommendedBooksArray as $book) {
                echo "
                <div class='col-6 col-sm-4 col-md-3 col-xl-2 g-3 d-flex align-items-stretch'>
                    <div class='card book-card w-100 p-3 rounded-0 border-0 shadow position-relative font-taviraj'>
                    <div class='mx-auto'>
                        <img src='img/{$book['cover_image']}' class='card-img-top card-img mb-3' alt='...'>
                    </div>
                    <div class='d-flex flex-column card-body p-0'>
                        <h5 class='card-title wordbreak-hyphen mb-1'>{$book['title']}</h5>
                        <p class='card-text card-auth-name mb-2'>{$book['authors']}</p>
                        <span class='h5 ms-auto mb-0 mt-auto fw-semibold'>{$book['price']} €</span>
                        <a href='product.php?id={$book['book_id']}' class='stretched-link'><span></span></a>
                    </div>
                    </div>
                </div>";
                }
                ?>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
