<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
if ($dbConnect->connect_error) { exit("Connection Failed"); }

echo "<html><head><title>TechForge Admin - Hub</title><style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; margin: 20px; color: #333; }
    .container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 25px; }
    header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; border-bottom: 5px solid #e74c3c; margin: -20px -20px 30px -20px; }
    header h1 { color: #e74c3c; margin: 0; text-transform: uppercase; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #34495e; color: white; }
    
    .id-badge { background: #f1c40f; font-weight: bold; padding: 3px 7px; border-radius: 4px; color: #000; }
    .btn { padding: 10px 20px; text-decoration: none; background: #e74c3c; color: white; border-radius: 5px; font-weight: bold; border: none; cursor: pointer; display: inline-block; }
    .btn-secondary { background: #7f8c8d; }
    .btn-logout { background: #ffff00; color: #000; border: 2px solid #333; box-shadow: 4px 4px 0px #333; }
    .btn-logout:hover { background: #e6e600; }
    
    .order-card { border: 2px solid #e74c3c; padding: 20px; margin-top: 20px; border-radius: 8px; background: #fffafb; }
    .input-box { width: 70px; padding: 8px; border: 2px solid #e74c3c; border-radius: 4px; text-align: center; font-weight: bold; }
</style></head><body>";

echo "<header><h1>TechForge Admin Command Center</h1></header>";

// --- 1. LOGOUT (Bright Yellow & Top Position) ---
echo "<div class='container' style='background: transparent; box-shadow: none; padding-left: 0;'>
        <a href='tech_register.html' class='btn btn-logout'>&larr; LOGOUT / SWITCH ACCOUNT</a>
      </div>";

// --- 2. SYSTEM CONTROL PANEL (User & Order Management Only) ---
echo "<div class='container'>
        <h3>System Control Panel</h3>
        <div style='display: flex; gap: 20px; flex-wrap: wrap; background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd; max-width: 700px;'>
            <div style='flex: 1; border-right: 1px solid #eee; padding-right: 20px;'>
                <h4 style='margin-top:0;'>Delete User</h4>
                <form action='deleteuser.php' method='post'>
                    User ID: <input type='text' name='userid' class='input-box' required style='width:50px;'> 
                    <input type='submit' class='btn' value='Delete User'>
                </form>
            </div>
            <div style='flex: 1;'>
                <h4 style='margin-top:0;'>Delete Order</h4>
                <form action='deleteorder.php' method='post'>
                    Order ID: <input type='text' name='orderid' class='input-box' required style='width:50px;'> 
                    <input type='submit' class='btn' value='Delete Order'>
                </form>
            </div>
        </div>
      </div>";

// --- 3. REGISTERED USERS ---
echo "<div class='container'>
        <h3>Registered Users</h3>";
$userResult = $dbConnect->query("SELECT userid, firstname, lastname, email, phone FROM tbluser");
echo "<table><tr><th>User ID</th><th>Full Name</th><th>Email (Login)</th><th>Phone</th></tr>";
while($u = $userResult->fetch_assoc()) {
    echo "<tr><td><strong>{$u['userid']}</strong></td><td>{$u['firstname']} {$u['lastname']}</td><td>{$u['email']}</td><td>{$u['phone']}</td></tr>";
}
echo "</table></div>";

// --- 4. INVENTORY OVERRIDE & REFERENCE TABLE ---
echo "<div class='container' style='border-left: 5px solid #27ae60;'>
        <h3>Inventory Override</h3>
        <p style='font-size: 0.9em; color: #666;'>Reference the table below for IDs, then update quantities here:</p>
        <form action='update_stock.php' method='post'>
            Component ID: <input type='text' name='compid' class='input-box' required style='width:50px;'> 
            New Quantity: <input type='number' name='new_qty' class='input-box' required style='width:80px;'> 
            <input type='submit' class='btn' style='background:#27ae60;' value='Update Stock'>
        </form>
      </div>";

echo "<div class='container'>
        <h3>Component Inventory & Reference</h3>";
$compQuery = "SELECT compid, category, compdesc, tier, price, stock_qty FROM tblcomponents ORDER BY compid ASC";
$compResult = $dbConnect->query($compQuery);
echo "<table><tr><th>ID</th><th>Category</th><th>Description</th><th>Tier</th><th>Price</th><th>In Stock</th></tr>";
while($c = $compResult->fetch_assoc()) {
    $stockStyle = ($c['stock_qty'] < 5) ? "style='color:red; font-weight:bold;'" : "";
    echo "<tr>
            <td><span class='id-badge'>{$c['compid']}</span></td>
            <td>{$c['category']}</td>
            <td>{$c['compdesc']}</td>
            <td>{$c['tier']}</td>
            <td>\${$c['price']}</td>
            <td $stockStyle>{$c['stock_qty']} units</td>
          </tr>";
}
echo "</table></div>";

// --- 5. PENDING BUILD REQUESTS ---
echo "<div class='container'>
        <h3>Pending Build Requests</h3>";
$orderQuery = "SELECT o.orderid, u.firstname, u.lastname, o.servicetype FROM tblorder o JOIN tbluser u ON o.userid = u.userid WHERE o.status = 'Pending'";
$orderResult = $dbConnect->query($orderQuery);

if ($orderResult->num_rows > 0) {
    while($o = $orderResult->fetch_assoc()) {
        $oid = $o['orderid'];
        echo "<div class='order-card'>
                <h4>Order #$oid - Customer: <span style='color:#e74c3c'>{$o['firstname']} {$o['lastname']}</span></h4>
                <form action='admin_finalize_logic.php' method='post'>
                    <input type='hidden' name='orderid' value='$oid'>
                    <table><tr><th>Category</th><th>Requested Part</th><th>Enter Final Component ID</th></tr>";
        $items = $dbConnect->query("SELECT c.category, c.compdesc FROM tblcomponents c JOIN tblorder_items i ON c.compid = i.compid WHERE i.orderid = '$oid'");
        while($item = $items->fetch_assoc()) {
            echo "<tr><td>{$item['category']}</td><td>{$item['compdesc']}</td><td><input type='text' name='part_ids[]' class='input-box' required></td></tr>";
        }
        echo "</table><br><input type='submit' class='btn' value='Finalize Order #$oid'></form></div>";
    }
} else { echo "<p style='color:#7f8c8d;'>No pending orders.</p>"; }
echo "</div>";

// --- 6. FINALIZED ORDER HISTORY ---
echo "<div class='container'>
        <h3 style='color: #27ae60;'>Finalized Order History</h3>";
$historyQuery = "SELECT o.orderid, u.userid, u.firstname, u.lastname, o.customerprice FROM tblorder o JOIN tbluser u ON o.userid = u.userid WHERE o.status = 'Finalized' ORDER BY o.orderid DESC";
$historyResult = $dbConnect->query($historyQuery);

if ($historyResult->num_rows > 0) {
    echo "<table><tr><th>Order/User ID</th><th>Customer Name</th><th>Components</th><th>Total Price</th></tr>";
    while($h = $historyResult->fetch_assoc()) {
        $oid = $h['orderid'];
        echo "<tr>
                <td>Order #$oid<br><small>User #{$h['userid']}</small></td>
                <td>{$h['firstname']} {$h['lastname']}</td>
                <td>";
        $parts = $dbConnect->query("SELECT c.compid, c.category, c.compdesc FROM tblcomponents c JOIN tblorder_items i ON c.compid = i.compid WHERE i.orderid = '$oid'");
        while($p = $parts->fetch_assoc()) {
            echo "<span class='id-badge'>{$p['compid']}</span> {$p['category']}: {$p['compdesc']}<br>";
        }
        echo "</td><td style='color:#27ae60; font-weight:bold;'>\$" . number_format($h['customerprice'], 2) . "</td></tr>";
    }
    echo "</table>";
} else { echo "<p style='color:#7f8c8d;'>No finalized history.</p>"; }
echo "</div></body></html>";
?>