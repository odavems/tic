<?php
//3.  Tickets by Customer
//SELECT c.customer_name, COUNT(t.ticket_id) as count
//FROM customers c
//JOIN tickets t ON c.customer_id = t.customer_id
//GROUP BY c.customer_name;

// ... (DB connection code)

$servername = "db_server22_ot"; // Use the service name defined in docker-compose.yml
$username = "root";
$password = "root";
$dbname = "micro_ot";

$conn = new mysqli($servername, $username, $password, $dbname);

$query = "SELECT c.customer_name, COUNT(t.ticket_id) as count FROM customers c JOIN tickets t ON c.customer_id = t.customer_id GROUP BY c.customer_name";
$result = $conn->query($query);

$data = array();
foreach ($result as $row) {
    $data['labels'][] = $row['customer_name'];
    $data['datasets'][0]['data'][] = $row['count'];
}

echo json_encode($data);
$conn->close();

?>