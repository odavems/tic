<?php
session_start();

$servername = "db_server22_ot"; // Use the service name defined in docker-compose.yml
$username = "root";
$password = "root";
$dbname = "micro_ot";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//$query = "SELECT created_by_uuid, COUNT(*) as count FROM tickets GROUP BY created_by_uuid";
//$result = $conn->query($query);

$query = "SELECT assigned_by_uuid, COUNT(*) as count FROM tickets GROUP BY assigned_by_uuid";
$result = $conn->query($query);


// Initialize the data structure properly for chart.js
$data = array(
    'labels' => array(),
    'datasets' => array(
        array(
            'data' => array(),
            'backgroundColor' => array(), // Optional: add colors
            'borderColor' => array(),     // Optional: add border colors
            'borderWidth' => 1
        )
    )
);

function fetchUserName($uuid) {
    if (isset($_SESSION['users_names_and_uuids'])) {
        foreach ($_SESSION['users_names_and_uuids'] as $user) {
            if ($user['uuid'] === $uuid) {
                return $user['name'] ?? 'N/A';
            }
        }
        return 'N/A'; // Return 'N/A' if the uuid is not found
    } else {
        return 'N/A'; // Return 'N/A' if the session variable is not set
    }
}

// Optional: Define some colors for the pie chart
$colors = array(
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
    '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
);

$colorIndex = 0;

if ($result && $result->num_rows > 0) {
    foreach ($result as $row) {
        // Fetch the user name using the UUID from the query
        //$user_name = fetchUserName($row['created_by_uuid']);
        $user_name = fetchUserName($row['assigned_by_uuid']);
        
        // Add to chart data
        $data['labels'][] = $user_name;
        $data['datasets'][0]['data'][] = intval($row['count']);
        
        // Optional: Add colors
        $data['datasets'][0]['backgroundColor'][] = $colors[$colorIndex % count($colors)];
        $data['datasets'][0]['borderColor'][] = '#fff';
        $colorIndex++;
    }
}

// Set content type to JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>