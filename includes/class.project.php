<?php

class Project {

    private $pdo;
    private $errorMessages = [];
    private $errorState = 0;

    function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Insert a new project (existing method)
    public function insertNewProject(int $carId, int $customerId, string $defectDesc, string $workDesc) {
        $stmt_insertNewProject = $this->pdo->prepare('INSERT INTO table_projects (customer_id_fk, car_id_fk, defect_desc, work_desc, status_id_fk)
            VALUES 
            (:customerId, :carId, :defectDesc, :workDesc, 1)');
        $stmt_insertNewProject->bindParam(':customerId', $customerId, PDO::PARAM_INT);
        $stmt_insertNewProject->bindParam(':carId', $carId, PDO::PARAM_INT);
        $stmt_insertNewProject->bindParam(':defectDesc', $defectDesc, PDO::PARAM_STR);
        $stmt_insertNewProject->bindParam(':workDesc', $workDesc, PDO::PARAM_STR);

        if(!$stmt_insertNewProject->execute()) {
            array_push($this->errorMessages, "Lyckades inte skapa projektet ");
            $this->errorState = 1;
        }

        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            $lastProjectId = $this->pdo->lastInsertId();
            return $lastProjectId;    
        }
    }

    public function selectSingleProject(int $projectId) {
        // Select data from single project in table_projects
        $projectDataArray = $this->pdo->query("SELECT 
            table_projects.*, 
            table_customers.*, 
            table_cars.*
        FROM 
            table_projects
        JOIN 
            table_customers ON table_projects.customer_id_fk = table_customers.customer_id
        JOIN 
            table_cars ON table_projects.car_id_fk = table_cars.car_id
        WHERE 
            table_projects.project_id = $projectId")->fetch();
                return $projectDataArray;
    }

    public function selectProjectProducts(int $projectId) {
        // Select all products linked to project in table_project_product
        $projectProductsArray = $this->pdo->query("SELECT table_products.*
            FROM table_products
            JOIN table_project_product ON table_products.product_id = table_project_product.product_id_fk
            WHERE table_project_product.project_id_fk = $projectId")->fetchAll();
        return $projectProductsArray;
    }

    public function insertNewProduct(string $prodName, string $prodPrice, string $prodNumber, int $projectId) {

        $prodPrice = str_replace(',', '.', $prodPrice);
        if (!is_numeric($prodPrice)) {
            array_push($this->errorMessages, "Det angivna produktpriset är inte ett giltigt tal ");
            $this->errorState = 1;
        }

        if ($this->errorState !== 1) {
            $stmt_insertNewProduct = $this->pdo->prepare('INSERT INTO table_products (name, price, invoice_number)
                VALUES 
                (:prodName, :prodPrice, :prodNumber)');
            $stmt_insertNewProduct->bindParam(':prodName', $prodName, PDO::PARAM_STR);
            $stmt_insertNewProduct->bindParam(':prodPrice', $prodPrice, PDO::PARAM_STR);
            $stmt_insertNewProduct->bindParam(':prodNumber', $prodNumber, PDO::PARAM_STR);
            $stmt_insertNewProduct->execute();

            $lastProductId = $this->pdo->lastInsertId();

            $stmt_insertIntoProjectProduct = $this->pdo->prepare('INSERT INTO table_project_product (project_id_fk, product_id_fk)
                VALUES 
                (:projectId, :productId)');
            $stmt_insertIntoProjectProduct->bindParam(':projectId', $projectId, PDO::PARAM_INT);
            $stmt_insertIntoProjectProduct->bindParam(':productId', $lastProductId, PDO::PARAM_STR);


            // Check if query is successful
            if(!$stmt_insertIntoProjectProduct->execute()) {
                array_push($this->errorMessages, "Lyckades inte mata in i tabellen table_project_product ");
                $this->errorState = 1;
            }
        }

        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }

    }

    public function updateProduct(string $prodName, string $prodPrice, string $prodNumber, int $prodId) {

        $prodPrice = str_replace(',', '.', $prodPrice);
        if (!is_numeric($prodPrice)) {
            array_push($this->errorMessages, "Det angivna priset är inte ett giltigt tal ");
            $this->errorState = 1;
        }

        if ($this->errorState !== 1) {
            $stmt_editProduct = $this->pdo->prepare('UPDATE table_products 
                SET name = :name, price = :price, invoice_number = :number
                WHERE product_id = :productId');
            $stmt_editProduct->bindParam(':name', $prodName, PDO::PARAM_STR);
            $stmt_editProduct->bindParam(':price', $prodPrice, PDO::PARAM_STR);
            $stmt_editProduct->bindParam(':number', $prodNumber, PDO::PARAM_STR);
            $stmt_editProduct->bindParam(':productId', $prodId, PDO::PARAM_INT);

            if(!$stmt_editProduct->execute()) {
                array_push($this->errorMessages, "Lyckades inte uppdatera produktuppgifter ");
                $this->errorState = 1;
            }
        }
        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }
    }

    public function deleteProduct(int $prodId) {
        // DELETE FROM associative table
        $stmt_deleteProductAssoc = $this->pdo->prepare('DELETE FROM table_project_product WHERE product_id_fk = :productId');
        $stmt_deleteProductAssoc->bindParam(':productId', $prodId, PDO::PARAM_INT);
        if(!$stmt_deleteProductAssoc->execute()) {
            array_push($this->errorMessages, "Lyckades inte radera produkten från projektet ");
            $this->errorState = 1;
        }
        if ($this->errorState !== 1) {
            // DELETE FROM product table
            $stmt_deleteProduct = $this->pdo->prepare('DELETE FROM table_products WHERE product_id = :productId');
            $stmt_deleteProduct->bindParam(':productId', $prodId, PDO::PARAM_INT);
            if(!$stmt_deleteProduct->execute()) {
                array_push($this->errorMessages, "Lyckades inte radera produkten ");
                $this->errorState = 1;
            }
        }

        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }
    }

    public function selectWorkingHours(string $date, int $userId, int $projectId) {

        // SELECT hours of current user on selected date AS WELL AS user total hours on project from table_hours
        $stmt_selectWorkingHours = $this->pdo->prepare('SELECT *
            FROM table_hours 
            WHERE u_id_fk = :uid AND h_date = :hdate AND p_id_fk = :pid'
        ); 

        $stmt_selectWorkingHours->bindParam(':uid', $userId, PDO::PARAM_INT);
        $stmt_selectWorkingHours->bindParam(':hdate', $date, PDO::PARAM_STR);
        $stmt_selectWorkingHours->bindParam(':pid', $projectId, PDO::PARAM_INT);
        $stmt_selectWorkingHours->execute();
        return $stmt_selectWorkingHours->fetch();
    }

    public function selectTotalWorkingHours(int $userId, int $projectId) {

        // SELECT total hours on project for current user from table_hours
        $stmt_selectWorkingHours = $this->pdo->prepare('SELECT 
            SUM(h_amount) AS total_project_hours,
            SUM(CASE WHEN u_id_fk = :uid THEN h_amount ELSE 0 END) AS user_hours 
        FROM 
            table_hours 
        WHERE 
            p_id_fk = :pid');

        $stmt_selectWorkingHours->bindParam(':uid', $userId, PDO::PARAM_INT);
        $stmt_selectWorkingHours->bindParam(':pid', $projectId, PDO::PARAM_INT);
        $stmt_selectWorkingHours->execute();
        
        return $stmt_selectWorkingHours->fetch();
    }

    public function insertWorkingHours(int $projectId, int $userId, int $hours, string $date) {

        if (!is_numeric($hours)) {
            array_push($this->errorMessages, "Den angivna arbetstiden är inte ett giltigt tal ");
            $this->errorState = 1;
        }

        if ($this->errorState !== 1) {

            if ($this->selectWorkingHours($date, $userId, $projectId) === false) 
            {
                $stmt_insertWorkingHours = $this->pdo->prepare('INSERT INTO table_hours (p_id_fk, u_id_fk, h_amount, h_date)
                VALUES 
                (:pid, :uid, :hamount, :hdate)');
                $stmt_insertWorkingHours->bindParam(':pid', $projectId, PDO::PARAM_INT);
                $stmt_insertWorkingHours->bindParam(':uid', $userId, PDO::PARAM_INT);
                $stmt_insertWorkingHours->bindParam(':hamount', $hours, PDO::PARAM_INT);
                $stmt_insertWorkingHours->bindParam(':hdate', $date, PDO::PARAM_STR);

                if(!$stmt_insertWorkingHours->execute()) {
                    array_push($this->errorMessages, "Lyckades inte lägga till arbetstiden ");
                    $this->errorState = 1;
                }
            }
            else 
            {
                $stmt_editWorkingHours = $this->pdo->prepare('
                UPDATE table_hours
                SET h_amount = :hamount
                WHERE p_id_fk = :pid AND u_id_fk = :uid AND h_date = :hdate');
                $stmt_editWorkingHours->bindParam(':hamount', $hours, PDO::PARAM_INT);
                $stmt_editWorkingHours->bindParam(':pid', $projectId, PDO::PARAM_INT);
                $stmt_editWorkingHours->bindParam(':uid', $userId, PDO::PARAM_INT);
                $stmt_editWorkingHours->bindParam(':hdate', $date, PDO::PARAM_STR);

                if(!$stmt_editWorkingHours->execute()) {
                    array_push($this->errorMessages, "Lyckades inte uppdatera arbetstiden ");
                    $this->errorState = 1;
                }
        
            }
        }

        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }
    }

    // Update project details
    public function updateProject(int $projectId, int $carId, int $customerId, string $defectDesc, string $workDesc) {
        // Use 'project_id' for identifying the project
        $stmt = $this->pdo->prepare('UPDATE table_projects 
            SET car_id_fk = :carId, customer_id_fk = :customerId, defect_desc = :defectDesc, work_desc = :workDesc
            WHERE project_id = :projectId');
        $stmt->bindParam(':carId', $carId, PDO::PARAM_INT);
        $stmt->bindParam(':customerId', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':defectDesc', $defectDesc, PDO::PARAM_STR);
        $stmt->bindParam(':workDesc', $workDesc, PDO::PARAM_STR);
        $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
    
        // Check if query is successful
        if(!$stmt->execute()) {
            array_push($this->errorMessages, "Lyckades redigera projektet ");
            $this->errorState = 1;
        }

        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }
    }

    public function updateProjectStatus(int $projectId, int $statusId) {
        // Update the status_id_fk of the project
        $stmt = $this->pdo->prepare('UPDATE table_projects 
            SET status_id_fk = :statusId
            WHERE project_id = :projectId');
        $stmt->bindParam(':statusId', $statusId, PDO::PARAM_INT);
        $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;  // Successfully updated the status
        } else {
            return false; // No rows affected or failed
        }
    }

    public function selectAllStatusData() {
        $stmt_selectStatusData = $this->pdo->prepare('SELECT * FROM table_statuses');
        $stmt_selectStatusData->execute();
        $statusInfo = $stmt_selectStatusData->fetchAll();
        return $statusInfo;
    }
}

?>
