<?php

//1.  Tickets by Status
//SELECT status, COUNT(*) as count FROM tickets GROUP BY status;

header('Content-Type: application/json'); // Set header for JSON response

$servername = "db_server22_ot"; // Use the service name defined in docker-compose.yml
$username = "root";
$password = "root";
$dbname = "micro_ot";

$conn = new mysqli($servername, $username, $password, $dbname);

$query = "SELECT status, COUNT(*) as count FROM tickets GROUP BY status";
$result = $conn->query($query);

$data = array();
foreach ($result as $row) {
    $data['labels'][] = $row['status'];
    $data['datasets'][0]['data'][] = $row['count'];
}

echo json_encode($data);
$conn->close();

?>
