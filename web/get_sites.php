<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "micro_ots";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Get customer_id from the request
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : null;

// Validate customer_id
if (!$customer_id || !is_numeric($customer_id)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid customer ID']);
    exit;
}

// Prepare and execute the query to get sites for the given customer
$stmt = $conn->prepare("SELECT site_id, site_name FROM sites WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all sites and store in an array
$sites = [];
while ($row = $result->fetch_assoc()) {
    $sites[] = [
        'id' => $row['site_id'],
        'name' => $row['site_name']
    ];
}

// Close connection
$stmt->close();
$conn->close();

// Return sites as JSON
header('Content-Type: application/json');
echo json_encode($sites);
?>