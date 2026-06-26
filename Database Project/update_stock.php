<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');

$id = $_POST['compid'];
$qty = $_POST['new_qty'];

// Update the stock_qty for the specific ID
$sql = "UPDATE tblcomponents SET stock_qty = $qty WHERE compid = $id";

if ($dbConnect->query($sql)) {
    // Redirect back to admin page to see the update
    header("Location: adminreview.php");
} else {
    echo "Error updating stock: " . $dbConnect->error;
}

$dbConnect->close();
?>