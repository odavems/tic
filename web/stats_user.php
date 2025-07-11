<?php
//2.  Tickets by User
// SELECT u.created_by_uuid, COUNT(*) as count
// FROM tickets u
// GROUP BY u.created_by_uuid;

// ... (DB connection code as above)

$servername = "db_server22_ot"; // Use the service name defined in docker-compose.yml
$username = "root";
$password = "root";
$dbname = "micro_ot";

$conn = new mysqli($servername, $username, $password, $dbname);

$query = "SELECT created_by_uuid, COUNT(*) as count FROM tickets GROUP BY created_by_uuid";
$result = $conn->query($query);

$data = array();
foreach ($result as $row) {
    $data['labels'][] = $row['created_by_uuid']; // Or fetch user names if available
    $data['datasets'][0]['data'][] = $row['count'];
}

echo json_encode($data);
$conn->close();

?>