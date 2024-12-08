<?php
    function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function successMessage($feedbackMessage) {
        return "
        <div class='d-flex align-items-center alert alert-success alert-dismissible fade show fw-semibold mt-4' role='alert'>
            {$feedbackMessage} <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }

    function errorMessage($feedbackMessage) {
        return "
        <div class='d-flex align-items-center alert alert-danger alert-dismissible fade show fw-semibold mt-4' role='alert'>
            {$feedbackMessage}<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
?>