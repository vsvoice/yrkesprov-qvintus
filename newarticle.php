<?php  
include_once 'includes/header.php';

if(isset($_POST['insert-article-submit'])) {
    $publishingDate = date("Y-m-d");
    $visibility = isset($_POST['visibility']) ? 1 : 0;
    $displayed = isset($_POST['displayed']) ? 1 : 0;
    $errorMessage = $book->insertNewArticle($_POST['heading'], $_POST['article-img-field-name'], $_POST['body-text'], $publishingDate, $visibility, $displayed, $_SESSION['user_id']);
    if ($errorMessage !== true) {
        echo $errorMessage;
    }
}


?>

<div class="container-fluid">
    <div class="mx-auto mw-1240 px-2 px-sm-4">
    <h1 class="my-5 font-taviraj">Lägg till ny artikel</h1>

        <form action="" method="post" enctype="multipart/form-data" class="">
			<label class="form-label" for="heading">Rubrik</label><br>
			<input class="form-control" type="text" name="heading" id="heading" required="required"><br>

            <label class="form-label" for="article-img">Bild</label><br>
            <input class="form-control" type="file" id="article-img" name="article-img" required="required"><br>

            <input type="hidden" name="article-img-field-name" value="article-img">

			<label class="form-label" for="body-text">Text</label><br>
			<textarea class="form-control" name="body-text" id="body-text" rows="12" required="required"></textarea><br>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="visibility" id="visibility">
                <label class="form-check-label" for="visibility">
                    Synlig
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="displayed" id="displayed">
                <label class="form-check-label" for="displayed">
                    Visa upp på framsidan
                </label>
            </div><br>

			<input class="btn btn-primary py-2" type="submit" name="insert-article-submit" value="Lägg till artikel">
		</form>


    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
