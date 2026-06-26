<?php
// 1. Turn on errors so we can see what's wrong
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. Connect to database
$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
if ($dbConnect->connect_error) {
    die("Database Connection Failed: " . $dbConnect->connect_error);
}

// 3. Get the data from the form
$userid = isset($_POST['userid']) ? $_POST['userid'] : null;
$service = isset($_POST['servicetype']) ? $_POST['servicetype'] : 'Not Specified';
$selected_parts = isset($_POST['parts']) ? $_POST['parts'] : [];

if (!$userid) {
    die("Error: No User ID found. Please log in again.");
}

// 4. Insert the main order into tblorder
$sqlOrder = "INSERT INTO tblorder (userid, servicetype, status) VALUES ('$userid', '$service', 'Pending')";
if ($dbConnect->query($sqlOrder)) {
    $newOrderID = $dbConnect->insert_id;

    // 5. Loop through selected parts and insert into tblorder_items
    foreach ($selected_parts as $compid) {
        if ($compid != "0") {
            $dbConnect->query("INSERT INTO tblorder_items (orderid, compid) VALUES ('$newOrderID', '$compid')");
        }
    }
} else {
    die("Error inserting order: " . $dbConnect->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TechForge - Receipt</title>
    <style>
        body { font-family: 'Courier New', monospace; background: #f4f4f4; padding: 20px; }
        .receipt { 
            width: 500px; margin: auto; background: white; padding: 30px; 
            border: 2px solid #333; box-shadow: 10px 10px 0px #2c3e50; 
        }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 10px; }
        .item-row { display: flex; justify-content: space-between; margin: 8px 0; }
        .disclaimer { background: #eee; padding: 10px; font-size: 0.8em; border: 1px solid #ccc; margin: 15px 0; }
        .total-row { border-top: 2px solid #333; margin-top: 15px; padding-top: 10px; font-weight: bold; }
    </style>
</head>
<body>

<div class="receipt">
    <div class="header">
        <h1 style="color: #e74c3c; margin:0;">TECHFORGE</h1>
        <p>ORDER #<?php echo $newOrderID; ?></p>
        <p>Service: <?php echo $service; ?></p>
    </div>

    <div class="disclaimer">
        <strong>NOTICE:</strong> Total is $0.00 until an admin reviews your components and confirms your order.
    </div>

    <h3>BUILD SPECIFICATIONS:</h3>
    <?php
    // 6. Join query to show names instead of IDs
    $query = "SELECT c.category, c.compdesc, c.tier 
              FROM tblcomponents c 
              JOIN tblorder_items i ON c.compid = i.compid 
              WHERE i.orderid = '$newOrderID'";
    
    $partsResult = $dbConnect->query($query);
    if ($partsResult->num_rows > 0) {
        while($row = $partsResult->fetch_assoc()) {
            echo "<div class='item-row'>";
            echo "<span>" . $row['category'] . ": " . $row['compdesc'] . "</span>";
            echo "<span>[" . $row['tier'] . "]</span>";
            echo "</div>";
        }
    } else {
        echo "<p>No parts selected.</p>";
    }
    ?>

    <div class="total-row">
        <div class="item-row"><span>CURRENT TOTAL:</span><span>$0.00</span></div>
        <div class="item-row"><span>STATUS:</span><span>PENDING REVIEW</span></div>
    </div>

    <p style="text-align: center; margin-top: 30px;">
        <a href="index.htm" style="color: #e74c3c; font-weight: bold;">Return to Home</a>
    </p>
</div>

</body>
</html>
<?php $dbConnect->close(); ?>