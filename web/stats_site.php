<?php
//4.  Tickets by Site
//SELECT s.site_name, COUNT(t.ticket_id) as count
//FROM sites s
//JOIN tickets t ON s.site_id = t.site_id
//GROUP BY s.site_name;

$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "micro_ots";

$conn = new mysqli($servername, $username, $password, $dbname);

$query = "SELECT s.site_name, COUNT(t.ticket_id) as count FROM sites s JOIN tickets t ON s.site_id = t.site_id GROUP BY s.site_name";
$result = $conn->query($query);

$data = array();
foreach ($result as $row) {
    $data['labels'][] = $row['site_name'];
    $data['datasets'][0]['data'][] = $row['count'];
}

echo json_encode($data);
$conn->close();

?>