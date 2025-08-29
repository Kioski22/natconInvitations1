<?php
// Set content type to JSON
header('Content-Type: application/json');

// Prevent any HTML output
ob_start();

try {
    require 'db.php';

    $company_id = $_POST['company_id'] ?? '';
    $names = [];

    if (!empty($company_id)) {
        // Check if delegates table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'delegates'");
        if ($check_table->num_rows == 0) {
            throw new Exception("Table 'delegates' does not exist");
        }

        // Check table structure
        $check_columns = $conn->query("SHOW COLUMNS FROM delegates");
        $columns = [];
        while ($col = $check_columns->fetch_assoc()) {
            $columns[] = $col['Field'];
        }

        // Use the correct column names based on actual table structure
        if (in_array('firstname', $columns)) {
            // Correct query for your table structure
            $sql = "SELECT firstname, middle, lastname, suffix 
                    FROM delegates 
                    WHERE company_id = ?";
        } else if (in_array('delegates_fname', $columns)) {
            // Original query (fallback)
            $sql = "SELECT delegates_fname, delegates_mname, delegates_lname, delegates_suffix 
                    FROM delegates 
                    WHERE company_id = ?";
        } else if (in_array('name', $columns)) {
            // Alternative: if there's just a 'name' column
            $sql = "SELECT name FROM delegates WHERE company_id = ?";
        } else if (in_array('full_name', $columns)) {
            // Alternative: if there's a 'full_name' column
            $sql = "SELECT full_name FROM delegates WHERE company_id = ?";
        } else {
            throw new Exception("No recognized name columns found in delegates table. Available columns: " . implode(', ', $columns));
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if (isset($row['firstname'])) {
                // Build full name from parts (your actual table structure)
                $full_name = $row['firstname'];
                if (!empty($row['middle'])) {
                    $full_name .= ' ' . $row['middle'];
                }
                $full_name .= ' ' . $row['lastname'];
                if (!empty($row['suffix'])) {
                    $full_name .= ', ' . $row['suffix'];
                }
                $names[] = $full_name;
            } else if (isset($row['delegates_fname'])) {
                // Build full name from parts (fallback)
                $full_name = $row['delegates_fname'];
                if (!empty($row['delegates_mname'])) {
                    $full_name .= ' ' . $row['delegates_mname'];
                }
                $full_name .= ' ' . $row['delegates_lname'];
                if (!empty($row['delegates_suffix'])) {
                    $full_name .= ', ' . $row['delegates_suffix'];
                }
                $names[] = $full_name;
            } else if (isset($row['name'])) {
                $names[] = $row['name'];
            } else if (isset($row['full_name'])) {
                $names[] = $row['full_name'];
            }
        }
        $stmt->close();
    }

    // Clean output buffer
    ob_clean();
    echo json_encode($names);

} catch (Exception $e) {
    // Clean output buffer
    ob_clean();
    
    // Return error as JSON
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'company_id' => $company_id ?? 'not provided'
    ]);
} catch (Error $e) {
    // Clean output buffer
    ob_clean();
    
    // Return error as JSON
    echo json_encode([
        'error' => true,
        'message' => 'Fatal error: ' . $e->getMessage(),
        'company_id' => $company_id ?? 'not provided'
    ]);
}

if (isset($conn)) {
    $conn->close();
}
?>
