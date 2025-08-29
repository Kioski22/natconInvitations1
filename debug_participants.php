<?php
require 'db.php';

echo "<h3>Debug: Participants Data</h3>";

// Check if companies table exists and has data
echo "<h4>1. Companies Table:</h4>";
$sql_companies = "SELECT company_id, company_name FROM companies LIMIT 5";
$result = $conn->query($sql_companies);
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Company ID</th><th>Company Name</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['company_id']."</td><td>".$row['company_name']."</td></tr>";
    }
    echo "</table>";
} else {
    echo "No companies found or table doesn't exist.";
}

// Check if delegates table exists and has data
echo "<h4>2. Delegates Table:</h4>";
$sql_delegates = "SELECT company_id, delegates_fname, delegates_lname FROM delegates LIMIT 10";
$result = $conn->query($sql_delegates);
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Company ID</th><th>First Name</th><th>Last Name</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row['company_id']."</td><td>".$row['delegates_fname']."</td><td>".$row['delegates_lname']."</td></tr>";
    }
    echo "</table>";
} else {
    echo "No delegates found or table doesn't exist.";
}

// Test specific company (based on your screenshot showing "DOLE 9")
echo "<h4>3. Test for 'DOLE 9' Company:</h4>";
$sql_dole = "SELECT * FROM companies WHERE company_name LIKE '%DOLE%'";
$result = $conn->query($sql_dole);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $company_id = $row['company_id'];
        echo "Found company: ".$row['company_name']." (ID: $company_id)<br>";
        
        // Check for delegates in this company
        $sql_delegates_test = "SELECT delegates_fname, delegates_mname, delegates_lname, delegates_suffix 
                              FROM delegates WHERE company_id = $company_id";
        $delegates_result = $conn->query($sql_delegates_test);
        if ($delegates_result && $delegates_result->num_rows > 0) {
            echo "Delegates found: ".$delegates_result->num_rows."<br>";
            while($delegate = $delegates_result->fetch_assoc()) {
                echo "- ".$delegate['delegates_fname']." ".$delegate['delegates_lname']."<br>";
            }
        } else {
            echo "No delegates found for this company.<br>";
        }
    }
} else {
    echo "No DOLE company found.";
}

// Test the exact same query as get_participants.php
echo "<h4>4. Test get_participants.php Logic:</h4>";
if (isset($_GET['test_company_id'])) {
    $test_company_id = $_GET['test_company_id'];
    $names = [];
    
    $sql = "SELECT delegates_fname, delegates_mname, delegates_lname, delegates_suffix 
            FROM delegates 
            WHERE company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $test_company_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $full_name = $row['delegates_fname'];
        if (!empty($row['delegates_mname'])) {
            $full_name .= ' ' . $row['delegates_mname'];
        }
        $full_name .= ' ' . $row['delegates_lname'];
        if (!empty($row['delegates_suffix'])) {
            $full_name .= ', ' . $row['delegates_suffix'];
        }
        $names[] = $full_name;
    }
    
    echo "JSON Response: " . json_encode($names);
} else {
    echo "Add ?test_company_id=X to URL to test specific company";
}

$conn->close();
?>
