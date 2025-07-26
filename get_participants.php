<?php
require 'db.php';

$company_id = $_POST['company_id'] ?? '';

$names = [];

if (!empty($company_id)) {
    $sql = "SELECT delegates_fname, delegates_mname, delegates_lname, delegates_suffix 
            FROM delegates 
            WHERE company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $company_id);
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
}

echo json_encode($names);
?>
