<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
if ($dbConnect->connect_error) { die("Connection failed"); }

$uid = $_GET['userid'];

// Fetch the latest order for this user
$orderQuery = "SELECT * FROM tblorder WHERE userid = '$uid' ORDER BY orderid DESC LIMIT 1";
$orderRes = $dbConnect->query($orderQuery);
$order = $orderRes->fetch_assoc();
$oid = $order['orderid'];
$status = $order['status'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>TechForge - My Receipt</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background-color: #f4f4f4; padding: 50px; color: #333; }
        
        /* Receipt Container Styling */
        .receipt-container { 
            width: 550px; margin: auto; background: white; padding: 30px; 
            border: 2px solid #333; box-shadow: 12px 12px 0px #2c3e50; 
        }

        .receipt-header { border-bottom: 2px dashed #333; padding-bottom: 20px; text-align: center; margin-bottom: 20px; }
        .receipt-header h1 { color: #e74c3c; margin: 0; letter-spacing: 2px; }
        
        /* Disclaimer Box */
        .disclaimer { 
            background: #fff3cd; color: #856404; padding: 15px; 
            border: 1px solid #ffeeba; margin-bottom: 25px; 
            font-size: 0.9em; text-align: center; 
        }

        .item-row { display: flex; justify-content: space-between; margin: 8px 0; border-bottom: 1px dotted #eee; }
        .item-label { font-weight: bold; }
        
        .total-section { border-top: 2px solid #333; margin-top: 25px; padding-top: 15px; }
        .price-display { font-size: 1.8em; font-weight: bold; text-align: right; margin: 10px 0; }
        
        /* Status Stamp Logic */
        .status-stamp { 
            display: inline-block; border: 3px solid; padding: 5px 15px; 
            transform: rotate(-5deg); font-weight: bold; text-transform: uppercase; 
            margin-top: 10px;
        }
        .status-pending { color: #d39e00; border-color: #d39e00; }
        .status-finalized { color: #28a745; border-color: #28a745; }

        .actions { text-align: center; margin-top: 40px; }
        .btn { text-decoration: none; color: #e74c3c; font-weight: bold; border: 1px solid #e74c3c; padding: 10px 15px; }
        .btn:hover { background: #e74c3c; color: white; }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="receipt-header">
        <h1>TECHFORGE</h1>
        <?php
        $uRes = $dbConnect->query("SELECT * FROM tbluser WHERE userid = '$uid'");
        $u = $uRes->fetch_assoc();
        ?>
        <p>CUSTOMER: <?php echo $u['firstname'] . " " . $u['lastname']; ?></p>
        <p>CONTACT: <?php echo $u['email']; ?> | <?php echo $u['phone']; ?></p>
        <p>ORDER ID: #<?php echo $oid; ?></p>
    </div>

    <?php if($status == 'Pending'): ?>
    <div class="disclaimer">
        <strong>STILL PROCESSING:</strong> Your order is currently under review. The total below reflects the base component price ($0.00) until an admin finalizes the service fee.
    </div>
    <?php endif; ?>

    <h3>BUILD SPECIFICATIONS:</h3>
    <?php
    // Fetch all parts linked to this order
    $itemQuery = "SELECT c.category, c.compdesc, c.tier 
                  FROM tblcomponents c 
                  JOIN tblorder_items i ON c.compid = i.compid 
                  WHERE i.orderid = '$oid'";
    $parts = $dbConnect->query($itemQuery);
    
    while($row = $parts->fetch_assoc()) {
        echo "<div class='item-row'>";
        echo "<span class='item-label'>" . $row['category'] . ":</span>";
        echo "<span>" . $row['compdesc'] . " [" . $row['tier'] . "]</span>";
        echo "</div>";
    }
    ?>

    <div class="total-section">
        <div class="item-row">
            <span>SERVICE TYPE:</span>
            <span><?php echo $order['servicetype']; ?></span>
        </div>
        
        <div class="price-display">
            TOTAL: $<?php echo number_format($order['customerprice'], 2); ?>
        </div>

        <div style="text-align: center;">
            <div class="status-stamp <?php echo ($status == 'Finalized') ? 'status-finalized' : 'status-pending'; ?>">
                <?php echo $status; ?>
            </div>
        </div>
    </div>

    <div class="actions">
        <a href="tech_configurator.php?userid=<?php echo $uid; ?>" class="btn">NEW BUILD</a>
        &nbsp;&nbsp;
        <a href="index.htm" class="btn" style="border-color:#333; color:#333;">LOGOUT</a>
    </div>
</div>

</body>
</html>
<?php $dbConnect->close(); ?>