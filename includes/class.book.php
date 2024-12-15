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
                                <a href='product.php?id={$book['book_id']}' class='stretched-link'><span></span></a>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<div class=''>Inga resultat hittades.</div>";
                }
            }
        }
    }

    public function searchExclusives() {
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
                        b.display,
                        GROUP_CONCAT(DISTINCT a.author_name SEPARATOR ', ') AS authors
                    FROM 
                        t_books b
                    LEFT JOIN 
                        t_book_authors ba ON b.book_id = ba.book_id_fk
                    LEFT JOIN 
                        t_authors a ON ba.author_id_fk = a.author_id
                    LEFT JOIN 
                        t_book_genres bg ON b.book_id = bg.book_id_fk
                    WHERE 
                        b.visibility = '1' 
                    AND 
                        bg.genre_id_fk = 1
                    AND (
                        LOWER(REPLACE(REPLACE(REPLACE(b.title, '.', ''), ' ', ''), '-', '')) LIKE :title 
                        OR 
                        LOWER(REPLACE(REPLACE(REPLACE(a.author_name, '.', ''), ' ', ''), '-', '')) LIKE :author_name
                    )
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
                        $buttonString = "";
                        $buttonColor = "";
                        $displayValue;
                        if ($book['display'] == 0) {
                            $buttonString = "Lägg till";
                            $buttonColor = "btn-success";
                            $displayValue = 1;
                        } else {
                            $buttonString = "Ta bort";
                            $buttonColor = "btn-danger";
                            $displayValue = 0;
                        }
                        echo "
                        <div class='d-flex search-result py-2 px-4 position-relative'>
                            <div class='d-none d-md-block me-3'>
                                <img src='img/{$book['cover_image']}' alt='...'>
                            </div>
                            <div class='d-flex flex-column font-taviraj'>
                                <h5 class='search-title mb-0 mb-md-2'>{$book['title']}</h5>
                                <h6 class='search-auth-name fw-normal d-none d-md-block'>{$book['authors']}</h6>
                                <h6 class='h6 d-none d-md-block'>{$book['price']} €</h6>
                            </div>
                            <div class='d-flex align-items-center font-taviraj ms-auto'>
                            <form action='' method='post'>
                                <input type='hidden' name='displayed-exclusive-id' class='btn btn-primary' value='{$book['book_id']}'>
                                <input type='hidden' name='change-displayed-exclusive-to' class='btn btn-primary' value='" . $displayValue . "'>
                                <button type='submit' class='ms-auto btn " . $buttonColor . "' value='0' name='update-displayed-exclusive-submit'>" . $buttonString . "</button>
                            </form>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<div class=''>Inga resultat hittades.</div>";
                }
            }
        }
    }


    public function getBooks() {
        // Get the page number from the AJAX request
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

        // Define how many results per page
        $resultsPerPage = 20;

        // Calculate the offset
        $offset = ($page - 1) * $resultsPerPage;

        // Fetch the results with limit and offset
        $stmt = $pdo->prepare("
            SELECT 
                b.book_id, b.title, b.price, b.cover_image
            FROM 
                t_books b
            WHERE 
                b.visibility = '1'
            ORDER BY 
                b.title ASC
            LIMIT :limit OFFSET :offset
        ");

        // Bind limit and offset
        $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the results
        $books = $stmt->fetchAll();

        // Return JSON response
        echo json_encode($books);
    }


    public function insertNewBook(string $title, string $description, string $price, string $publishingDate, string $coverImageField, int $pageAmount, array $authors, ?array $illustrators, int $category, array $genres, int $series, int $publisher, int $ageRange, int $visibility, int $displayed, int $userId) {

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
        
        if (!empty($authors)) {
            $stmt_insertNewBookGenres = $this->pdo->prepare('INSERT INTO t_book_authors (book_id_fk, author_id_fk) VALUES (:book_id, :author_id)');
            foreach ($authors as $authorId) {
                if (!$stmt_insertNewBookGenres->execute([':book_id' => $bookId, ':author_id' => $authorId])) {
                    return "Lyckades inte koppla författare till boken.";
                }
            }
        }
        if (!empty($illustrators)) {
            $stmt_insertNewBookGenres = $this->pdo->prepare('INSERT INTO t_book_illustrators (book_id_fk, illustrator_id_fk) VALUES (:book_id, :illustrator_id)');
            foreach ($illustrators as $illustratorId) {
                if (!$stmt_insertNewBookGenres->execute([':book_id' => $bookId, ':illustrator_id' => $illustratorId])) {
                    return "Lyckades inte koppla illustratörer till boken.";
                }
            }
        }
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


    public function updateExistingBook(
        int $bookId,
        string $title,
        string $description,
        string $price,
        string $publishingDate,
        string $coverImageField,
        int $pageAmount,
        array $authors,
        ?array $illustrators,
        int $category,
        array $genres,
        int $series,
        int $publisher,
        int $ageRange,
        int $visibility,
        int $displayed,
        int $userId
    ) {
        $bookId = cleanInput($bookId);
        $title = cleanInput($title);
        $description = cleanInput($description);
        $price = cleanInput($price);
        $publishingDate = cleanInput($publishingDate);
        $coverImageField = cleanInput($coverImageField);
        $pageAmount = cleanInput($pageAmount);
        $category = cleanInput($category);
        $series = cleanInput($series);
        $publisher = cleanInput($publisher);
        $ageRange = cleanInput($ageRange);
        $visibility = cleanInput($visibility);
        $displayed = cleanInput($displayed);
        $userId = cleanInput($userId);

        // Validate image upload if a new image is provided
        if (isset($_FILES[$coverImageField]["name"]) && !empty($_FILES[$coverImageField]["name"])) {
            $imageError = $this->validateImageUpload($coverImageField);
            if (!empty($imageError)) {
                return $imageError;
            }
            $coverImage = basename($_FILES[$coverImageField]["name"]);
        }
    
        // Update main book details
        $stmt_updateBook = $this->pdo->prepare('
            UPDATE t_books
            SET 
                title = :title,
                description = :description,
                date_published = :date_published,
                page_amount = :page_amount,
                publisher_id_fk = :publisher_id,
                series_id_fk = :series_id,
                age_range_id_fk = :age_range_id,
                category_id_fk = :category_id,
                price = :price,
                visibility = :visibility,
                display = :display,
                user_id_fk = :user_id'
            . (isset($coverImage) ? ', cover_image = :cover_image' : '') .
            ' WHERE book_id = :book_id'
        );
        $stmt_updateBook->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt_updateBook->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt_updateBook->bindParam(':date_published', $publishingDate, PDO::PARAM_STR);
        $stmt_updateBook->bindParam(':page_amount', $pageAmount, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':publisher_id', $publisher, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':series_id', $series, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':age_range_id', $ageRange, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':category_id', $category, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt_updateBook->bindParam(':visibility', $visibility, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':display', $displayed, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt_updateBook->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        if (isset($coverImage)) {
            $stmt_updateBook->bindParam(':cover_image', $coverImage, PDO::PARAM_STR);
        }
    
        if (!$stmt_updateBook->execute()) {
            return "Failed to update book details.";
        }
    
        // Update authors
        $stmt_clearAuthors = $this->pdo->prepare('DELETE FROM t_book_authors WHERE book_id_fk = :book_id');
        $stmt_clearAuthors->execute([':book_id' => $bookId]);
    
        if (!empty($authors)) {
            $stmt_insertAuthors = $this->pdo->prepare('INSERT INTO t_book_authors (book_id_fk, author_id_fk) VALUES (:book_id, :author_id)');
            foreach ($authors as $authorId) {
                if (!$stmt_insertAuthors->execute([':book_id' => $bookId, ':author_id' => $authorId])) {
                    return "Failed to update authors for the book.";
                }
            }
        }
    
        // Update illustrators
        $stmt_clearIllustrators = $this->pdo->prepare('DELETE FROM t_book_illustrators WHERE book_id_fk = :book_id');
        $stmt_clearIllustrators->execute([':book_id' => $bookId]);
    
        if (!empty($illustrators)) {
            $stmt_insertIllustrators = $this->pdo->prepare('INSERT INTO t_book_illustrators (book_id_fk, illustrator_id_fk) VALUES (:book_id, :illustrator_id)');
            foreach ($illustrators as $illustratorId) {
                if (!$stmt_insertIllustrators->execute([':book_id' => $bookId, ':illustrator_id' => $illustratorId])) {
                    return "Failed to update illustrators for the book.";
                }
            }
        }
    
        // Update genres
        $stmt_clearGenres = $this->pdo->prepare('DELETE FROM t_book_genres WHERE book_id_fk = :book_id');
        $stmt_clearGenres->execute([':book_id' => $bookId]);
    
        if (!empty($genres)) {
            $stmt_insertGenres = $this->pdo->prepare('INSERT INTO t_book_genres (book_id_fk, genre_id_fk) VALUES (:book_id, :genre_id)');
            foreach ($genres as $genreId) {
                if (!$stmt_insertGenres->execute([':book_id' => $bookId, ':genre_id' => $genreId])) {
                    return "Failed to update genres for the book.";
                }
            }
        }
    
        return true;
    }


    public function getBookData($bookId) {
        $stmt_getBookData = $this->pdo->prepare("
            SELECT 
                b.book_id,
                b.title,
                b.description,
                b.date_published,
                b.page_amount,
                b.price,
                b.cover_image,
                b.user_id_fk,
                p.publisher_name,
                ar.age_range_name,
                s.series_name,
                c.category_name,
                GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name ASC SEPARATOR ', ') AS authors,
                GROUP_CONCAT(DISTINCT g.genre_name ORDER BY g.genre_name ASC SEPARATOR ', ') AS genres,
                GROUP_CONCAT(DISTINCT l.language_name ORDER BY l.language_name ASC SEPARATOR ', ') AS languages,
                GROUP_CONCAT(DISTINCT il.illustrator_name ORDER BY il.illustrator_name ASC SEPARATOR ', ') AS illustrators
            FROM 
                t_books b
            LEFT JOIN 
                t_publishers p ON b.publisher_id_fk = p.publisher_id
            LEFT JOIN 
                t_age_ranges ar ON b.age_range_id_fk = ar.age_range_id
            LEFT JOIN 
                t_series s ON b.series_id_fk = s.series_id
            LEFT JOIN 
                t_book_authors ba ON b.book_id = ba.book_id_fk
            LEFT JOIN 
                t_authors a ON ba.author_id_fk = a.author_id
            LEFT JOIN 
                t_book_genres bg ON b.book_id = bg.book_id_fk
            LEFT JOIN 
                t_genres g ON bg.genre_id_fk = g.genre_id
            LEFT JOIN 
                t_categories c ON b.category_id_fk = c.category_id
            LEFT JOIN 
                t_book_languages bl ON b.book_id = bl.book_id_fk
            LEFT JOIN 
                t_languages l ON bl.language_id_fk = l.language_id
            LEFT JOIN 
                t_book_illustrators bi ON b.book_id = bi.book_id_fk
            LEFT JOIN 
                t_illustrators il ON bi.illustrator_id_fk = il.illustrator_id
            WHERE
                b.book_id = :book_id 
            GROUP BY 
                b.book_id;
        ");
        $stmt_getBookData->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $stmt_getBookData->execute();

        return $stmt_getBookData->fetch();
    }

    public function getBookDataEdit($bookId) {
        $stmt_getBookData = $this->pdo->prepare("
            SELECT 
                b.book_id,
                b.title,
                b.description,
                b.date_published,
                b.page_amount,
                b.price,
                b.cover_image,
                b.visibility,
                b.display,
                p.publisher_id,
                ar.age_range_id,
                s.series_id,
                c.category_id,
                GROUP_CONCAT(DISTINCT a.author_id ORDER BY a.author_id ASC SEPARATOR ', ') AS authors,
                GROUP_CONCAT(DISTINCT g.genre_id ORDER BY g.genre_id ASC SEPARATOR ', ') AS genres,
                GROUP_CONCAT(DISTINCT l.language_id ORDER BY l.language_id ASC SEPARATOR ', ') AS languages,
                GROUP_CONCAT(DISTINCT il.illustrator_id ORDER BY il.illustrator_id ASC SEPARATOR ', ') AS illustrators
            FROM 
                t_books b
            LEFT JOIN 
                t_publishers p ON b.publisher_id_fk = p.publisher_id
            LEFT JOIN 
                t_age_ranges ar ON b.age_range_id_fk = ar.age_range_id
            LEFT JOIN 
                t_series s ON b.series_id_fk = s.series_id
            LEFT JOIN 
                t_book_authors ba ON b.book_id = ba.book_id_fk
            LEFT JOIN 
                t_authors a ON ba.author_id_fk = a.author_id
            LEFT JOIN 
                t_book_genres bg ON b.book_id = bg.book_id_fk
            LEFT JOIN 
                t_genres g ON bg.genre_id_fk = g.genre_id
            LEFT JOIN 
                t_categories c ON b.category_id_fk = c.category_id
            LEFT JOIN 
                t_book_languages bl ON b.book_id = bl.book_id_fk
            LEFT JOIN 
                t_languages l ON bl.language_id_fk = l.language_id
            LEFT JOIN 
                t_book_illustrators bi ON b.book_id = bi.book_id_fk
            LEFT JOIN 
                t_illustrators il ON bi.illustrator_id_fk = il.illustrator_id
            WHERE
                b.book_id = :book_id 
            GROUP BY 
                b.book_id;
        ");
        $stmt_getBookData->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $stmt_getBookData->execute();

        $bookData = $stmt_getBookData->fetch();

        // Convert the comma-separated strings into arrays
        $bookData['authors'] = explode(',', $bookData['authors']);
        $bookData['genres'] = explode(',', $bookData['genres']);
        $bookData['languages'] = explode(',', $bookData['languages']);
        $bookData['illustrators'] = explode(',', $bookData['illustrators']);

        return $bookData;
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
            WHERE (
                SELECT 1 
                FROM t_book_genres bg 
                WHERE bg.book_id_fk = b.book_id 
                AND bg.genre_id_fk = 1
            )
            AND b.visibility = '1' AND b.display = '1'
            GROUP BY 
                b.book_id
        ");
        $allDisplayedExclusivesArray = $stmt_getAllDisplayedExclusives->fetchAll();
        return $allDisplayedExclusivesArray;
    }

    public function getDisplayedBooks() {
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
                NOT EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                    AND bg.genre_id_fk = 1
                )
            AND b.visibility = '1' AND b.display = '1'
            GROUP BY 
                b.book_id
        ");
        $allDisplayedExclusivesArray = $stmt_getAllDisplayedExclusives->fetchAll();
        return $allDisplayedExclusivesArray;
    }

    public function getDisplayedGenres() {
        $stmt_getAllDisplayedGenres = $this->pdo->query("
            SELECT DISTINCT g.*
            FROM t_genres g
            JOIN t_book_genres bg ON g.genre_id = bg.genre_id_fk
            JOIN t_books b ON bg.book_id_fk = b.book_id
            WHERE g.display = '1'
            AND b.visibility = '1'
        ");
        $allDisplayedGenresArray = $stmt_getAllDisplayedGenres->fetchAll();
        return $allDisplayedGenresArray;
    }
    

    public function getDisplayedGenresWithNoAvailableBooks() {
        $stmt_getGenresSelectedForAndEligibleForDisplay = $this->pdo->query("
            SELECT DISTINCT g.*
            FROM t_genres g
            LEFT JOIN t_book_genres bg ON g.genre_id = bg.genre_id_fk
            LEFT JOIN t_books b ON bg.book_id_fk = b.book_id AND b.visibility = '1'
            WHERE g.display = '1'
            AND b.book_id IS NULL
        ");
        $allNotAvailableGenresArray = $stmt_getGenresSelectedForAndEligibleForDisplay->fetchAll();
        return $allNotAvailableGenresArray;
    }

    public function updateDisplayedGenres(array $genres) {
        $checkedGenres = $genres ?? []; // Get checked genres
        $placeholders = implode(',', array_fill(0, count($checkedGenres), '?')); // Create placeholders for the query
        
        // Update query
        $stmt = $this->pdo->prepare("
            UPDATE t_genres
            SET display = CASE
                WHEN genre_id IN ($placeholders) THEN 1
                ELSE 0
            END
        ");
        // Execute the query
        if (!$stmt->execute($checkedGenres)) {
            return "Lyckades inte uppdatera Populära genrer.";
        }
        return true;
    }

    public function updateDisplayedExclusive(int $exclusiveId, int $value) {
        $checkedGenres = $genres ?? []; // Get checked genres
        $placeholders = implode(',', array_fill(0, count($checkedGenres), '?')); // Create placeholders for the query
        
        // Update query
        $stmt_updateDisplayedExclusive = $this->pdo->prepare("
            UPDATE 
                t_books 
            SET 
                display = :display 
            WHERE 
                book_id = :book_id
        ");
        $stmt_updateDisplayedExclusive->bindParam(':display', $value, PDO::PARAM_INT);
        $stmt_updateDisplayedExclusive->bindParam(':book_id', $exclusiveId, PDO::PARAM_INT);

        // Execute the query
        if (!$stmt_updateDisplayedExclusive->execute()) {
            return "Lyckades inte uppdatera Sällsynt och värdefullt.";
        }
        return true;
    }



    public function getNewestBookPublishingYear() {
        $newestPublishingYear = $this->pdo->query("    
            SELECT YEAR(MAX(b.date_published)) AS newest_year
            FROM t_books b
            WHERE b.visibility = 1
                AND NOT EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )")->fetch();
        return $newestPublishingYear;
    }

    public function getOldestBookPublishingYear() {
        $oldestPublishingYear = $this->pdo->query("    
            SELECT YEAR(MIN(b.date_published)) AS oldest_year
            FROM t_books b
            WHERE b.visibility = 1
                AND NOT EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )")->fetch();
        return $oldestPublishingYear;
    }

    public function getAllBookPublishingYears() {
        $getBookPublishingYears = $this->pdo->query("    
            SELECT YEAR(b.date_published) AS published_year
            FROM t_books b
            WHERE b.visibility = 1
                AND NOT EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )
            ORDER BY b.date_published DESC")->fetchAll();
        return $getBookPublishingYears;
    }


    public function getNewestExclusivePublishingYear() {
        $newestPublishingYear = $this->pdo->query("    
            SELECT YEAR(MAX(b.date_published)) AS newest_year
            FROM t_books b
            WHERE b.visibility = 1
                AND EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )")->fetch();
        return $newestPublishingYear;
    }

    public function getOldestExclusivePublishingYear() {
        $oldestPublishingYear = $this->pdo->query("    
            SELECT YEAR(MIN(b.date_published)) AS oldest_year
            FROM t_books b
            WHERE b.visibility = 1
                AND EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )")->fetch();
        return $oldestPublishingYear;
    }

    public function getAllExclusivePublishingYears() {
        $getExclusivePublishingYears = $this->pdo->query("    
            SELECT YEAR(b.date_published) AS published_year
            FROM t_books b
            WHERE b.visibility = 1
                AND EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )
            ORDER BY b.date_published DESC")->fetchAll();
        return $getExclusivePublishingYears;
    }


    public function getAllBooks() {
        $stmt_getAllBooks = $this->pdo->query("
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
            WHERE b.visibility = 1
                AND NOT EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )
            AND 
                b.visibility = '1'
            GROUP BY 
                b.book_id
        ");
        $allBooksArray = $stmt_getAllBooks->fetchAll();
        return $allBooksArray;
    }

    public function getAllExclusives() {
        $stmt_getAllExclusives = $this->pdo->query("
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
            WHERE b.visibility = 1
                AND EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
            )
            AND 
                b.visibility = '1'
            GROUP BY 
                b.book_id
        ");
        $allExclusivesArray = $stmt_getAllExclusives->fetchAll();
        return $allExclusivesArray;
    }


    public function filterBooks(array $categories, array $genres, array $languages, array $series, array $age_ranges, array $publishers, ?string $fromDate, ?string $toDate) {
        // Start the base query
        $query = "
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
                b.visibility = 1
                AND NOT EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
                )
        ";
    
        // Check for categories filter
        if (isset($categories) && !empty($categories)) {
            $categoriesString = implode(',', $categories);
            $query .= " AND b.category_id_fk IN ($categoriesString)";
        }
    
        // Check for genres filter
        if (isset($genres) && !empty($genres)) {
            $genresString = implode(',', $genres);
            $query .= " AND EXISTS (
                SELECT 1 
                FROM t_book_genres bg 
                WHERE bg.book_id_fk = b.book_id 
                AND bg.genre_id_fk IN ($genresString)
            )";
        }
    
        // Check for languages filter
        if (isset($languages) && !empty($languages)) {
            $languagesString = implode(',', $languages);
            $query .= " AND EXISTS (
                SELECT 1 
                FROM t_book_languages bl
                WHERE bl.book_id_fk = b.book_id 
                AND bl.language_id_fk IN ($languagesString)
            )";
        }
    
        // Check for series filter
        if (isset($series) && !empty($series)) {
            $seriesString = implode(',', $series);
            $query .= " AND b.series_id_fk IN ($seriesString)";
        }
    
        // Check for age ranges filter
        if (isset($age_ranges) && !empty($age_ranges)) {
            $ageRangesString = implode(',', $age_ranges);
            $query .= " AND b.age_range_id_fk IN ($ageRangesString)";
        }
    
        // Check for publishers filter
        if (isset($publishers) && !empty($publishers)) {
            $publishersString = implode(',', $publishers);
            $query .= " AND b.publisher_id_fk IN ($publishersString)";
        }
    
        // Check for date range filter (fromDate and toDate)
        if (isset($fromDate) && isset($toDate)) {
            $query .= " AND YEAR(b.date_published) BETWEEN $fromDate AND $toDate";
        }
    
        // Group by book_id (same as in your original query)
        $query .= " GROUP BY b.book_id";
    
        // Execute the query
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    
        // Fetch and return the results
        $filteredBooks = $stmt->fetchAll();
        return $filteredBooks;
    }

    public function filterExclusives(array $categories, array $genres, array $languages, array $series, array $age_ranges, array $publishers, ?string $fromDate, ?string $toDate) {
        // Start the base query
        $query = "
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
                b.visibility = 1
                AND EXISTS (
                    SELECT 1 
                    FROM t_book_genres bg 
                    WHERE bg.book_id_fk = b.book_id 
                        AND bg.genre_id_fk = 1
                )
        ";
    
        // Check for categories filter
        if (isset($categories) && !empty($categories)) {
            $categoriesString = implode(',', $categories);
            $query .= " AND b.category_id_fk IN ($categoriesString)";
        }
    
        // Check for genres filter
        if (isset($genres) && !empty($genres)) {
            $genresString = implode(',', $genres);
            $query .= " AND EXISTS (
                SELECT 1 
                FROM t_book_genres bg 
                WHERE bg.book_id_fk = b.book_id 
                AND bg.genre_id_fk IN ($genresString)
            )";
        }
    
        // Check for languages filter
        if (isset($languages) && !empty($languages)) {
            $languagesString = implode(',', $languages);
            $query .= " AND EXISTS (
                SELECT 1 
                FROM t_book_languages bl
                WHERE bl.book_id_fk = b.book_id 
                AND bl.language_id_fk IN ($languagesString)
            )";
        }
    
        // Check for series filter
        if (isset($series) && !empty($series)) {
            $seriesString = implode(',', $series);
            $query .= " AND b.series_id_fk IN ($seriesString)";
        }
    
        // Check for age ranges filter
        if (isset($age_ranges) && !empty($age_ranges)) {
            $ageRangesString = implode(',', $age_ranges);
            $query .= " AND b.age_range_id_fk IN ($ageRangesString)";
        }
    
        // Check for publishers filter
        if (isset($publishers) && !empty($publishers)) {
            $publishersString = implode(',', $publishers);
            $query .= " AND b.publisher_id_fk IN ($publishersString)";
        }
    
        // Check for date range filter (fromDate and toDate)
        if (isset($fromDate) && isset($toDate)) {
            $query .= " AND YEAR(b.date_published) BETWEEN $fromDate AND $toDate";
        }
    
        // Group by book_id (same as in your original query)
        $query .= " GROUP BY b.book_id";
    
        // Execute the query
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    
        // Fetch and return the results
        $filteredBooks = $stmt->fetchAll();
        return $filteredBooks;
    }


    public function getFilteredGenres(array $categories) {
        // Ensure the input array is not empty
        if (empty($categories)) {
            return [];
        }

        // Prepare the placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($categories), '?'));

        // Prepare the statement
        $stmt_getFilteredGenres = $this->pdo->prepare("
            SELECT DISTINCT g.genre_id, g.genre_name
            FROM t_genres g
            INNER JOIN t_book_genres bg ON g.genre_id = bg.genre_id_fk
            INNER JOIN t_books b ON bg.book_id_fk = b.book_id
            INNER JOIN t_categories c ON b.category_id_fk = c.category_id
            WHERE c.category_id IN ($placeholders)
        ");

        // Execute with the categories array
        $stmt_getFilteredGenres->execute($categories);

        // Fetch and return results
        return $stmt_getFilteredGenres->fetchAll();
    }


    public function getAllGenresWithAvailableProducts() {
        $allGenresArray = $this->pdo->query("
            SELECT DISTINCT g.*
            FROM t_genres g
            JOIN t_book_genres bg ON g.genre_id = bg.genre_id_fk
            JOIN t_books b ON bg.book_id_fk = b.book_id
            WHERE b.visibility = 1
            ORDER BY (g.genre_id = 0) DESC, g.genre_name ASC
        ")->fetchAll();
        return $allGenresArray;
    }


    public function getAllCategoriesWithAvailableBooks() {
        
        $allCategoriesArray = $this->pdo->query("    
            SELECT DISTINCT c.*
            FROM t_categories c
            JOIN t_books b ON c.category_id = b.category_id_fk
            LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk
            WHERE b.visibility = 1
            AND NOT EXISTS (
                SELECT 1
                FROM t_book_genres bg_sub
                WHERE bg_sub.book_id_fk = b.book_id
                AND bg_sub.genre_id_fk = 1
            )
            ORDER BY (c.category_id = 0) DESC, c.category_name ASC
        ")->fetchAll();
        return $allCategoriesArray;
    }

    public function getAllGenresWithAvailableBooks() {
        $allGenresArray = $this->pdo->query("
            SELECT DISTINCT g.*
            FROM t_genres g
            JOIN t_book_genres bg ON g.genre_id = bg.genre_id_fk
            JOIN t_books b ON bg.book_id_fk = b.book_id
            WHERE b.visibility = 1
            AND g.genre_id != 1
            AND b.book_id NOT IN (
                SELECT bg2.book_id_fk
                FROM t_book_genres bg2
                WHERE bg2.genre_id_fk = 1
            )
            ORDER BY (g.genre_id = 0) DESC, g.genre_name ASC
        ")->fetchAll();
        return $allGenresArray;
    }

    public function getAllSeriesWithAvailableBooks() {
        $allSeriesArray = $this->pdo->query("
            SELECT DISTINCT s.*
            FROM t_series s
            JOIN t_books b ON s.series_id = b.series_id_fk
            LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk
            WHERE b.visibility = 1
            AND NOT EXISTS (
                SELECT 1
                FROM t_book_genres bg_sub
                WHERE bg_sub.book_id_fk = b.book_id
                AND bg_sub.genre_id_fk = 1
            )
            ORDER BY (s.series_id = 0) DESC, s.series_name ASC
        ")->fetchAll();
        return $allSeriesArray;
    }

    public function getAllLanguagesWithAvailableBooks() {
        $allLanguagesArray = $this->pdo->query("
            SELECT DISTINCT l.*
            FROM t_languages l
            JOIN t_book_languages bl ON l.language_id = bl.language_id_fk
            JOIN t_books b ON bl.book_id_fk = b.book_id
            WHERE b.visibility = 1
            AND b.book_id NOT IN (
                SELECT bg2.book_id_fk
                FROM t_book_genres bg2
                WHERE bg2.genre_id_fk = 1
            )
            ORDER BY (l.language_id = 0) DESC, l.language_name ASC
        ")->fetchAll();
        /*$allLanguagesArray = $this->pdo->query("
        SELECT DISTINCT l.*
        FROM t_languages l
        INNER JOIN t_book_languages bl ON l.language_id = bl.language_id_fk
        INNER JOIN t_books b ON bl.book_id_fk = b.book_id
        LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk AND bg.genre_id_fk = 1
        WHERE b.visibility = 1 AND bg.genre_id_fk IS NULL
        ORDER BY (l.language_id = 0) DESC, l.language_name ASC
        ")->fetchAll();*/
        return $allLanguagesArray;
    }

    public function getAllPublishersWithAvailableBooks() {
        $allPublishersArray = $this->pdo->query("
            SELECT DISTINCT p.*
            FROM t_publishers p
            JOIN t_books b ON p.publisher_id = b.publisher_id_fk
            LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk
            WHERE b.visibility = 1
            AND NOT EXISTS (
                SELECT 1
                FROM t_book_genres bg_sub
                WHERE bg_sub.book_id_fk = b.book_id
                AND bg_sub.genre_id_fk = 1
            )
            ORDER BY (p.publisher_id = 0) DESC, p.publisher_name ASC        
        ")->fetchAll();
        return $allPublishersArray;
    }

    public function getAllAgeRangesWithAvailableBooks() {
        $allAgeRangesArray = $this->pdo->query("SELECT * FROM t_age_ranges")->fetchAll();
        return $allAgeRangesArray;
    }


    public function getAllCategoriesWithAvailableExclusives() {
        
        $allCategoriesArray = $this->pdo->query("    
            SELECT DISTINCT c.*
            FROM t_categories c
            JOIN t_books b ON c.category_id = b.category_id_fk
            LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk
            WHERE b.visibility = 1
            AND EXISTS (
                SELECT 1
                FROM t_book_genres bg_sub
                WHERE bg_sub.book_id_fk = b.book_id
                AND bg_sub.genre_id_fk = 1
            )
            ORDER BY (c.category_id = 0) DESC, c.category_name ASC
        ")->fetchAll();
        return $allCategoriesArray;
    }

    public function getAllGenresWithAvailableExclusives() {
        $allGenresArray = $this->pdo->query("
            SELECT DISTINCT g.*
            FROM t_genres g
            JOIN t_book_genres bg ON g.genre_id = bg.genre_id_fk
            JOIN t_books b ON bg.book_id_fk = b.book_id
            WHERE b.visibility = 1
            AND g.genre_id != 1
            AND b.book_id IN (
                SELECT bg2.book_id_fk
                FROM t_book_genres bg2
                WHERE bg2.genre_id_fk = 1
            )
            ORDER BY (g.genre_id = 0) DESC, g.genre_name ASC
        ")->fetchAll();
        return $allGenresArray;
    }

    public function getAllSeriesWithAvailableExclusives() {
        $allSeriesArray = $this->pdo->query("
            SELECT DISTINCT s.*
            FROM t_series s
            JOIN t_books b ON s.series_id = b.series_id_fk
            LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk
            WHERE b.visibility = 1
            AND EXISTS (
                SELECT 1
                FROM t_book_genres bg_sub
                WHERE bg_sub.book_id_fk = b.book_id
                AND bg_sub.genre_id_fk = 1
            )
            ORDER BY (s.series_id = 0) DESC, s.series_name ASC
        ")->fetchAll();
        return $allSeriesArray;
    }

    public function getAllLanguagesWithAvailableExclusives() {
        $allLanguagesArray = $this->pdo->query("
            SELECT DISTINCT l.*
            FROM t_languages l
            JOIN t_book_languages bl ON l.language_id = bl.language_id_fk
            JOIN t_books b ON bl.book_id_fk = b.book_id
            WHERE b.visibility = 1
            AND b.book_id IN (
                SELECT bg2.book_id_fk
                FROM t_book_genres bg2
                WHERE bg2.genre_id_fk = 1
            )
            ORDER BY (l.language_id = 0) DESC, l.language_name ASC
        ")->fetchAll();
        /*$allLanguagesArray = $this->pdo->query("
        SELECT DISTINCT l.*
        FROM t_languages l
        INNER JOIN t_book_languages bl ON l.language_id = bl.language_id_fk
        INNER JOIN t_books b ON bl.book_id_fk = b.book_id
        LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk AND bg.genre_id_fk = 1
        WHERE b.visibility = 1 AND bg.genre_id_fk IS NULL
        ORDER BY (l.language_id = 0) DESC, l.language_name ASC
        ")->fetchAll();*/
        return $allLanguagesArray;
    }

    public function getAllPublishersWithAvailableExclusives() {
        $allPublishersArray = $this->pdo->query("
            SELECT DISTINCT p.*
            FROM t_publishers p
            JOIN t_books b ON p.publisher_id = b.publisher_id_fk
            LEFT JOIN t_book_genres bg ON b.book_id = bg.book_id_fk
            WHERE b.visibility = 1
            AND EXISTS (
                SELECT 1
                FROM t_book_genres bg_sub
                WHERE bg_sub.book_id_fk = b.book_id
                AND bg_sub.genre_id_fk = 1
            )
            ORDER BY (p.publisher_id = 0) DESC, p.publisher_name ASC        
        ")->fetchAll();
        return $allPublishersArray;
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