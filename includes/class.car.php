<?php
include_once 'functions.php';

class Car {

    private $pdo;
    private $errorMessages = [];
    private $errorState = 0;


    function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insertNewCar(string $brand, string $model, string $license) {

        // Check if car_license already exists in the database
        $stmt_checkLicense = $this->pdo->prepare('SELECT COUNT(*) FROM table_cars WHERE car_license = :license');
        $stmt_checkLicense->bindParam(':license', $license, PDO::PARAM_STR);
        $stmt_checkLicense->execute();

        if ($stmt_checkLicense->fetchColumn() > 0) {
            // License already exists, return error message
            array_push($this->errorMessages, "Bilens registernummer finns redan ");
            $this->errorState = 1;
            return $this->errorMessages;
        }
        
        // If no existing license, proceed to insert new car
        $stmt_insertNewCar = $this->pdo->prepare('INSERT INTO table_cars (car_brand, car_model, car_license)
            VALUES 
            (:brand, :model, :license)');
            $stmt_insertNewCar->bindParam(':brand', $brand, PDO::PARAM_STR);
            $stmt_insertNewCar->bindParam(':model', $model, PDO::PARAM_STR);
            $stmt_insertNewCar->bindParam(':license', $license, PDO::PARAM_STR);

            if(!$stmt_insertNewCar->execute()) {
                array_push($this->errorMessages, "Lyckades inte skapa bilen ");
                $this->errorState = 1;
            }

            // Check if query returns any result
            if($stmt_insertNewCar->rowCount() > 0) {
                array_push($this->errorMessages, "Bilen finns redan ");
                $this->errorState = 1;
            }

            if ($this->errorState == 1) {
                return $this->errorMessages;
            } else {
                return 1;    
            }
    }

    public function selectAllCars() {
        $allCarsArray = $this->pdo->query("SELECT * FROM table_cars ORDER BY car_id DESC")->fetchAll();
        return $allCarsArray;
    }

    public function populateCarField(array $carsArray) {

        echo "<div class='list-group list-group-flush table-responsive'>";

        foreach ($carsArray as $car) {
			echo "<button type='button' class='list-group-item list-group-item-action px-4' aria-current='true' data-bs-dismiss='modal' value='{$car['car_id']}' onclick='selectProjectCar(this.value)'>
                <div class='row'>
                    <div class='col-5'>{$car['car_brand']} {$car['car_model']}</div>
                    <div class='col-5 text-truncate'>{$car['car_license']}</div>
                </div>
            </button>";
        }

        echo "</div>";
    }

    public function getCarDataById(int $id) {
        // Prepare and execute the query to fetch user data by ID
        $carData = $this->pdo->query("SELECT * FROM table_cars WHERE car_id = $id")->fetch();
        
        echo "<span id='car-brand'>{$carData['car_brand']}</span> <span id='car-model'>{$carData['car_model']}</span> <span class='ms-4' id='car-license'>{$carData['car_license']}</span>";
    }

    public function searchCars(string $input) {
        $input = cleanInput($input);

        // Replace all whitespace characters with % wildcards
        $input = preg_replace('/\s+/', '%', $input);

        $inputJoker = "%".$input."%";
        
        $stmt_searchCars = $this->pdo->prepare('SELECT * FROM table_cars WHERE car_brand LIKE :brand OR car_model LIKE :model OR car_license LIKE :license OR CONCAT(car_brand, car_model, car_license) LIKE :fullcar');
        $stmt_searchCars->bindParam(':brand', $inputJoker, PDO::PARAM_STR);
        $stmt_searchCars->bindParam(':model', $inputJoker, PDO::PARAM_STR);
        $stmt_searchCars->bindParam(':fullcar', $inputJoker, PDO::PARAM_STR);
        $stmt_searchCars->bindParam(':license', $inputJoker, PDO::PARAM_STR);
        $stmt_searchCars->execute();
        $carsList = $stmt_searchCars->fetchAll();
        
        return $carsList;
    }

    public function populateCarSearchField(array $carsArray) {
        foreach ($carsArray as $car) {
            echo "
            <tr data-bs-toggle='modal' data-bs-target='#carModal' data-id='{$car['car_id']}' onclick=\"selectCarProjects(this.getAttribute('data-id'))\">
                <td>{$car['car_brand']}</td>
                <td>{$car['car_model']}</td>
                <td>{$car['car_license']}</td>
            </tr>";
        }
    }

    public function selectCarProjects(int $carId) {
        $stmt_selectCarProjects = $this->pdo->prepare('SELECT *,
                c.*,
                s.s_name
            FROM 
                table_projects p
            JOIN 
                table_customers c ON p.customer_id_fk = c.customer_id
            JOIN 
                table_statuses s ON p.status_id_fk = s.s_id
            WHERE 
                p.car_id_fk = :cid');
        $stmt_selectCarProjects->bindParam(':cid', $carId, PDO::PARAM_INT);
        $stmt_selectCarProjects->execute();
        $carProjects = $stmt_selectCarProjects->fetchAll();
        return $carProjects;
    }

    public function populateCarProjectsField(array $carProjectsArray) {
        if (empty($carProjectsArray)){
             echo "<tr class='text-center fst-italic'><td colspan='2'>Inga projekt hittades för denna bil ...</td></tr>";
        }
        foreach ($carProjectsArray as $project) {
            echo "
                <tr onclick=\"window.location.href='project.php?project_id={$project['project_id']}';\" style=\"cursor: pointer;\">
                    <td>{$project['customer_fname']} {$project['customer_lname']}</td>
                    <td>{$project['s_name']}</td>
                </tr>";
        }
    }
}

?>