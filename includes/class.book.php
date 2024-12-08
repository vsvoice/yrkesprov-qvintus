<?php
include_once 'functions.php';

class Book {

    private $pdo;
    private $errorMessages = [];
    private $errorState = 0;


    function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function searchBooks() {
        // Check if a search query was sent
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
            $query = cleanInput($_POST['query']);
            $query = '%' . strtolower(str_replace(['.', ' ', '-'], '', $query)) . '%';

            if (!empty($query)) {
                // Use prepared statements to prevent SQL injection
                $stmt_searchBooks = $this->pdo->prepare("
                    SELECT 
                        b.book_id,
                        b.title, 
                        b.price, 
                        b.cover_image, 
                        GROUP_CONCAT(a.author_name SEPARATOR ', ') AS authors
                    FROM 
                        t_books b
                    LEFT JOIN 
                        t_book_authors ba ON b.book_id = ba.book_id_fk
                    LEFT JOIN 
                        t_authors a ON ba.author_id_fk = a.author_id
                    WHERE 
                        b.visibility = '1' 
                    AND 
                        LOWER(REPLACE(REPLACE(REPLACE(b.title, '.', ''), ' ', ''), '-', '')) LIKE :title 
                    OR 
                        LOWER(REPLACE(REPLACE(REPLACE(a.author_name, '.', ''), ' ', ''), '-', '')) LIKE :author_name
                    GROUP BY 
                        b.book_id
                    ORDER BY 
                        b.title ASC
                    LIMIT 10
                ");
                $stmt_searchBooks->bindParam(':title', $query, PDO::PARAM_STR);
                $stmt_searchBooks->bindParam(':author_name', $query, PDO::PARAM_STR);
                $stmt_searchBooks->execute();

                $results = $stmt_searchBooks->fetchAll();

                if ($results) {
                    foreach ($results as $book) {
                        echo "
                        <div class='d-flex search-result py-2 px-4 position-relative'>
                            <div class='d-none d-md-block me-3'>
                                <img src='img/{$book['cover_image']}' alt='...'>
                            </div>
                            <div class='d-flex flex-column font-taviraj'>
                                <h5 class='search-title mb-0 mb-md-2'>{$book['title']}</h5>
                                <h6 class='search-auth-name fw-normal d-none d-md-block'>{$book['authors']}</h6>
                                <h6 class='h6 d-none d-md-block'>{$book['price']} €</h6>
                                <a href='products.php?id={$book['book_id']}' class='stretched-link'><span></span></a>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<div class=''>Inga resultat hittades.</div>";
                }
            }
        }
    }


    public function insertNewBook(string $title, string $description, string $price, string $publishingDate, string $coverImageField, int $pageAmount, int $category, array $genres, int $series, int $publisher, int $ageRange, int $visibility, int $displayed, int $userId) {

        $imageError = $this->validateImageUpload($coverImageField);
        if (!empty($imageError)) {
            return $imageError;
        }

        $stmt_insertNewBook = $this->pdo->prepare('INSERT INTO t_books (title, description, date_published, page_amount, publisher_id_fk, series_id_fk, age_range_id_fk, category_id_fk, price, cover_image, visibility, display, user_id_fk)
            VALUES 
            (:title, :description, :date_published, :page_amount, :publisher_id, :series_id, :age_range_id, :category_id, :price, :cover_image, :visibility, :display, :user_id)');
        if (isset($_FILES[$coverImageField]["name"])) {
            $coverImage = basename($_FILES[$coverImageField]["name"]);
            $stmt_insertNewBook->bindParam(':cover_image', $coverImage, PDO::PARAM_STR);
        }
        $stmt_insertNewBook->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt_insertNewBook->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt_insertNewBook->bindParam(':page_amount', $pageAmount, PDO::PARAM_INT);
        $stmt_insertNewBook->bindParam(':date_published', $publishingDate, PDO::PARAM_STR);
        $stmt_insertNewBook->bindParam(':publisher_id', $publisher, PDO::PARAM_INT);
        $stmt_insertNewBook->bindParam(':age_range_id', $ageRange, PDO::PARAM_INT);
        $stmt_insertNewBook->bindParam(':series_id', $series, PDO::PARAM_INT);
        $stmt_insertNewBook->bindParam(':category_id', $category, PDO::PARAM_INT);
        $stmt_insertNewBook->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt_insertNewBook->bindParam(':visibility', $visibility, PDO::PARAM_INT);
        $stmt_insertNewBook->bindParam(':display', $displayed, PDO::PARAM_INT);
        $stmt_insertNewBook->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if(!$stmt_insertNewBook->execute()) {
            return "Lyckades inte lägga till boken.";
        }

        $bookId = $this->pdo->lastInsertId();
        
        if (!empty($genres)) {
            $stmt_insertNewBookGenres = $this->pdo->prepare('INSERT INTO t_book_genres (book_id_fk, genre_id_fk) VALUES (:book_id, :genre_id)');
            foreach ($genres as $genreId) {
                if (!$stmt_insertNewBookGenres->execute([':book_id' => $bookId, ':genre_id' => $genreId])) {
                    return "Lyckades inte koppla genrer till boken.";
                }
            }
        }
        
        return true;
    }

    public function validateImageUpload($file) {
        // Check if the image file exists and is uploaded successfully
        if(isset($_FILES[$file]["name"]) && !empty($_FILES[$file]["name"])) {
            $target_dir = "img/";
            $target_file = $target_dir . basename($_FILES[$file]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES[$file]["tmp_name"]);
            if($check === false) {
                return "Bildfel. Filen är inte en bild.";
            }
    
            // Check if file already exists
            if (file_exists($target_file)) {
                return "Bildfel. Filen finns redan.";
            }
    
            // Check file size
            if ($_FILES[$file]["size"] > 15000000) {
                return "Bildfel. Filen är för stor.";
            }
    
            // Allow certain file formats
            if(!in_array($imageFileType, ["jpg", "png", "jpeg", "gif", "webp"])) {
                return "Bildfel. Enbart filtyperna JPG, JPEG, PNG, WEBP & GIF är tillåtna.";
            }
    
            // Check if the file is uploaded successfully
            if (!move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
                return "Bildfel. Ett fel inträffade vid filuppladdningen.";
            }
        }/* else {
            // If no image file is uploaded, return an error
            return "Ingen bild har laddats upp.";
        }*/
    }

    public function insertNewAuthor($authorName) {
        $authorName = cleanInput($authorName);
        $stmt_insertNewAuthor = $this->pdo->prepare('INSERT INTO t_authors (author_name) VALUES (:author_name)');
        $stmt_insertNewAuthor->bindParam(':author_name', $authorName, PDO::PARAM_STR);

        if(!$stmt_insertNewAuthor->execute()) {
            return "Lyckades inte lägga till författaren.";
        }
        
        return true;
    }

    public function insertNewIllustrator($illustratorName) {
        $illustratorName = cleanInput($illustratorName);
        $stmt_insertNewIllustrator = $this->pdo->prepare('INSERT INTO t_illustrators (illustrator_name) VALUES (:illustrator_name)');
        $stmt_insertNewIllustrator->bindParam(':illustrator_name', $illustratorName, PDO::PARAM_STR);

        if(!$stmt_insertNewIllustrator->execute()) {
            return "Lyckades inte lägga till formgivaren/illustratören.";
        }
        
        return true;
    }

    public function insertNewGenre($genreName, $genreImageField) {
        $imageError = $this->validateImageUpload($genreImageField);
        if (!empty($imageError)) {
            return $imageError;
        }
        $genreName = cleanInput($genreName);     
        $stmt_insertNewGenre = $this->pdo->prepare('INSERT INTO t_genres (genre_name, genre_image, display) VALUES (:genre_name, :genre_image, 1)');
        if (isset($_FILES[$genreImageField]["name"])) {
            $genreImage = basename($_FILES[$genreImageField]["name"]);
            $stmt_insertNewGenre->bindParam(':genre_image', $genreImage, PDO::PARAM_STR);
        }
        $stmt_insertNewGenre->bindParam(':genre_name', $genreName, PDO::PARAM_STR);

        if(!$stmt_insertNewGenre->execute()) {
            //return $stmt_insertNewGenre->errorInfo();
            return "Lyckades inte lägga till genren.";
        }

        return true;
    }

    public function insertNewSeries($seriesName) {
        $seriesName = cleanInput($seriesName);
        $stmt_insertNewSeries = $this->pdo->prepare('INSERT INTO t_series (series_name) VALUES (:series_name)');
        $stmt_insertNewSeries->bindParam(':series_name', $seriesName, PDO::PARAM_STR);

        if(!$stmt_insertNewSeries->execute()) {
            return "Lyckades inte lägga till serien.";
        }
        
        return true;
    }

    public function insertNewLanguage($languageName) {
        $languageName = cleanInput($languageName);
        $stmt_insertNewLanguage = $this->pdo->prepare('INSERT INTO t_languages (language_name) VALUES (:language_name)');
        $stmt_insertNewLanguage->bindParam(':language_name', $languageName, PDO::PARAM_STR);

        if(!$stmt_insertNewLanguage->execute()) {
            return "Lyckades inte lägga till språket.";
        }
        
        return true;
    }

    public function insertNewPublisher($publisherName) {
        $publisherName = cleanInput($publisherName);

        $stmt_checkPublisher = $this->pdo->prepare('SELECT COUNT(*) FROM t_publishers WHERE publisher_name = :publisher_name');
        $stmt_checkPublisher->bindParam(':publisher_name', $publisherName, PDO::PARAM_STR);
        $stmt_checkPublisher->execute();
        
        if ($stmt_checkPublisher->fetchColumn() > 0) {
            $feedbackMessage = "Förlaget finns redan i databasen."; 
            return errorMessage($feedbackMessage);
        }

        $stmt_insertNewPublisher = $this->pdo->prepare('INSERT INTO t_publishers (publisher_name) VALUES (:publisher_name)');
        $stmt_insertNewPublisher->bindParam(':publisher_name', $publisherName, PDO::PARAM_STR);

        if(!$stmt_insertNewPublisher->execute()) {
            return "Lyckades inte lägga till förlaget."; 
        }

        return true;
    }

    public function getAllAuthors() {
        $allAuthorsArray = $this->pdo->query("SELECT * FROM t_authors ORDER BY (author_id = 0) DESC, author_name ASC")->fetchAll();
        return $allAuthorsArray;
    }

    public function getAllIllustrators() {
        $allAuthorsArray = $this->pdo->query("SELECT * FROM t_illustrators ORDER BY (illustrator_id = 0) DESC, illustrator_name ASC")->fetchAll();
        return $allAuthorsArray;
    }

    public function getAllCategories() {
        $allCategoriesArray = $this->pdo->query("SELECT * FROM t_categories ORDER BY (category_id = 0) DESC, category_name ASC")->fetchAll();
        return $allCategoriesArray;
    }

    public function getAllGenres() {
        $allGenresArray = $this->pdo->query("SELECT * FROM t_genres ORDER BY (genre_id = 1) DESC, genre_name ASC")->fetchAll();
        return $allGenresArray;
    }

    public function getAllSeries() {
        $allSeriesArray = $this->pdo->query("SELECT * FROM t_series ORDER BY (series_id = 0) DESC, series_name ASC")->fetchAll();
        return $allSeriesArray;
    }

    public function getAllLanguages() {
        $allLanguagesArray = $this->pdo->query("SELECT * FROM t_languages ORDER BY (language_id = 0) DESC, language_name ASC")->fetchAll();
        return $allLanguagesArray;
    }

    public function getAllPublishers() {
        $allPublishersArray = $this->pdo->query("SELECT * FROM t_publishers ORDER BY (publisher_id = 0) DESC, publisher_name ASC")->fetchAll();
        return $allPublishersArray;
    }

    public function getAllAgeRanges() {
        $allAgeRangesArray = $this->pdo->query("SELECT * FROM t_age_ranges")->fetchAll();
        return $allAgeRangesArray;
    }

    public function getDisplayedExclusives() {
        $stmt_getAllDisplayedExclusives = $this->pdo->query("
            SELECT 
                b.book_id,
                b.title, 
                b.price, 
                b.cover_image, 
                GROUP_CONCAT(a.author_name SEPARATOR ', ') AS authors
            FROM 
                t_books b
            LEFT JOIN 
                t_book_authors ba ON b.book_id = ba.book_id_fk
            LEFT JOIN 
                t_authors a ON ba.author_id_fk = a.author_id
            WHERE 
                b.visibility = '1' AND b.display = '1'
            GROUP BY 
                b.book_id
        ");
        $allDisplayedExclusivesArray = $stmt_getAllDisplayedExclusives->fetchAll();
        return $allDisplayedExclusivesArray;
    }

    
    public function insertNewArticle(string $heading, string $articleImageField, string $bodyText, string $publishingDate, int $visibility, int $displayed, int $userId) {
        $imageError = $this->validateImageUpload($articleImageField);

        if (!empty($imageError)) {
            return $imageError;
        }

        $stmt_insertNewArticle = $this->pdo->prepare('INSERT INTO t_articles (heading, body, date_published, article_image, visibility, display, user_id_fk)
            VALUES 
            (:heading, :body, :date_published, :article_image, :visibility, :displayed, :user_id)');
        if (isset($_FILES[$articleImageField]["name"])) {
            $coverImage = basename($_FILES[$articleImageField]["name"]);
            $stmt_insertNewArticle->bindParam(':article_image', $coverImage, PDO::PARAM_STR);
        }
        $stmt_insertNewArticle->bindParam(':heading', $heading, PDO::PARAM_STR);
        $stmt_insertNewArticle->bindParam(':body', $bodyText, PDO::PARAM_STR);
        $stmt_insertNewArticle->bindParam(':date_published', $publishingDate, PDO::PARAM_STR);
        $stmt_insertNewArticle->bindParam(':visibility', $visibility, PDO::PARAM_INT);
        $stmt_insertNewArticle->bindParam(':displayed', $displayed, PDO::PARAM_INT);
        $stmt_insertNewArticle->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if(!$stmt_insertNewArticle->execute()) {
            return "Lyckades inte lägga till artikeln.";
        }
        
        return true;
    }
}

?>