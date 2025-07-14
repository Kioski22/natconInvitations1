<?php
require 'db.php';

$company = $_POST['company'];
$sql = "SELECT full_name FROM invitations WHERE company = '$company'";
$result = $conn->query($sql);

$names = [];
while($row = $result->fetch_assoc()){
    $names[] = $row['full_name'];
}
echo json_encode($names);
?>
