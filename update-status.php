<?php
include_once 'includes/header.php';

if ($user->checkLoginStatus()) {
    if(!$user->checkUserRole(200)) {
        header("Location: home.php");
    }
}

if (isset($_POST['project_id']) && isset($_POST['status_id'])) {
    $projectId = $_POST['project_id'];
    $statusId = $_POST['status_id'];

    // Ensure $pdo is set before proceeding
    if (isset($pdo)) {
        // Update project status in the database
        $stmt = $pdo->prepare('UPDATE table_projects SET status_id_fk = :status_id WHERE project_id = :project_id');
        $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Status updated successfully
            header("Location: project.php?project_id=" . $projectId);  // Redirect back to project page
            exit;
        } else {
            // Error updating status
            echo "<div class='container'>
                    <div class='alert alert-danger text-center' role='alert'>
                        Lyckades inte uppdatera status. Försök igen.
                    </div>
                </div>";
        }
    } else {
        echo "<div class='container'>
                <div class='alert alert-danger text-center' role='alert'>
                    Databasanslutning är inte tillgänglig.
                </div>
            </div>";
    }
} else {
    echo "<div class='container'>
            <div class='alert alert-danger text-center' role='alert'>
                Inte giltig förfrågan.
            </div>
        </div>";
}
?>