<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
$uid = $_GET['userid'];

// 1. Get User Info for the Welcome Message
$userRes = $dbConnect->query("SELECT firstname FROM tbluser WHERE userid = '$uid'");
$user = $userRes->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>TechForge - My Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding-bottom: 50px; }
        header { background-color: #2c3e50; color: white; padding: 25px; text-align: center; border-bottom: 5px solid #e74c3c; }
        .container { width: 800px; margin: auto; }
        
        .welcome-sec { text-align: center; margin: 30px 0; }
        .new-build-btn { 
            display: inline-block; padding: 15px 30px; background: #e74c3c; color: white; 
            text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 1.2em;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
        }
        .new-build-btn:hover { background: #c0392b; }

        h2 { border-bottom: 2px solid #2c3e50; padding-bottom: 10px; color: #2c3e50; margin-top: 50px; }

        /* Receipt Styling */
        .receipt { 
            font-family: 'Courier New', monospace; width: 550px; margin: 20px auto; 
            padding: 25px; border: 2px solid #333; position: relative;
        }
        .receipt-pending { background-color: #fff9c4; border-color: #fbc02d; } /* Yellow */
        .receipt-completed { background-color: #c8e6c9; border-color: #2e7d32; } /* Green */

        .receipt-head { text-align: center; border-bottom: 1px dashed #333; margin-bottom: 15px; }
        .item-row { display: flex; justify-content: space-between; margin: 5px 0; font-size: 0.9em; }
        .status-text { font-weight: bold; text-transform: uppercase; text-align: center; margin-top: 15px; display: block; }
        
        .logout { text-align: center; margin-top: 50px; }
    </style>
</head>
<body>

<header>
    <h1>TECHFORGE CUSTOMER PORTAL</h1>
</header>

<div class="container">
    <div class="welcome-sec">
        <h1>Welcome, <?php echo $user['firstname']; ?>!</h1>
        <p>Manage your builds and track assembly progress below.</p>
        <a href="tech_configurator.php?userid=<?php echo $uid; ?>" class="new-build-btn">+ START A NEW BUILD</a>
    </div>

    <h2>Pending Builds (Awaiting Admin Review)</h2>
    <?php
    $pending = $dbConnect->query("SELECT * FROM tblorder WHERE userid = '$uid' AND status = 'Pending' ORDER BY orderid DESC");
    if ($pending->num_rows > 0) {
        while($order = $pending->fetch_assoc()) {
            renderReceipt($order, $dbConnect, 'receipt-pending');
        }
    } else { echo "<p style='text-align:center; color:#7f8c8d;'>No builds currently in the queue.</p>"; }
    ?>

    <h2>Completed Builds (Ready)</h2>
    <?php
    $finalized = $dbConnect->query("SELECT * FROM tblorder WHERE userid = '$uid' AND status = 'Finalized' ORDER BY orderid DESC");
    if ($finalized->num_rows > 0) {
        while($order = $finalized->fetch_assoc()) {
            renderReceipt($order, $dbConnect, 'receipt-completed');
        }
    } else { echo "<p style='text-align:center; color:#7f8c8d;'>No finalized builds yet.</p>"; }
    ?>

    <div class="logout">
        <a href="index.htm" style="color: #7f8c8d; text-decoration: none;">&larr; Logout and Return to Home</a>
    </div>
</div>

</body>
</html>

<?php
// Function to draw the receipt to keep the code clean
function renderReceipt($order, $db, $class) {
    $oid = $order['orderid'];
    echo "<div class='receipt $class'>";
    echo "<div class='receipt-head'><strong>ORDER #$oid</strong><br>Service: {$order['servicetype']}</div>";
    
    $parts = $db->query("SELECT c.category, c.compdesc FROM tblcomponents c JOIN tblorder_items i ON c.compid = i.compid WHERE i.orderid = '$oid'");
    while($p = $parts->fetch_assoc()) {
        echo "<div class='item-row'><span>{$p['category']}</span><span>{$p['compdesc']}</span></div>";
    }
    
    echo "<div style='border-top: 1px solid #333; margin-top:10px; padding-top:5px;'>";
    echo "<div class='item-row'><strong>TOTAL PRICE:</strong><strong>$" . number_format($order['customerprice'], 2) . "</strong></div>";
    echo "<span class='status-text'>STATUS: {$order['status']}</span>";
    echo "</div></div>";
}
?>