<?php 
//Security risk with alax folder accessible via browser?
include_once '../includes/config.php';

$pid = $_GET["pid"];
$uid = $_GET["uid"];
$date = $_GET["date"];

$stmt_selectWorkingHoursByDate = $pdo->prepare('SELECT * FROM table_hours WHERE u_id_fk = :uid AND h_date = :hdate AND p_id_fk = :pid');
$stmt_selectWorkingHoursByDate->bindParam(':uid', $uid, PDO::PARAM_INT);
$stmt_selectWorkingHoursByDate->bindParam(':hdate', $date, PDO::PARAM_STR);
$stmt_selectWorkingHoursByDate->bindParam(':pid', $pid, PDO::PARAM_INT);
$stmt_selectWorkingHoursByDate->execute();

$workingHours = $stmt_selectWorkingHoursByDate->fetch();

if ($workingHours) {
    echo $workingHours['h_amount'];
} else {
    echo '';
}

?>