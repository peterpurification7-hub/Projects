<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');

$orderid = $_POST['orderid'];
$part_ids = $_POST['part_ids'];

$total_admin_price = 0;
$display_items = [];

// --- THE FIX: CLEAR PREVIOUS SELECTIONS ---
// This prevents the "Double Recording" bug by removing the user's 
// initial choices before we record the admin's verified choices.
$dbConnect->query("DELETE FROM tblorder_items WHERE orderid = '$orderid'");

// Now we loop through the IDs you just manually typed in
foreach ($part_ids as $id) {
    // 1. Get component details for the summary and price calculation
    $res = $dbConnect->query("SELECT category, compdesc, price FROM tblcomponents WHERE compid = '$id'");
    $row = $res->fetch_assoc();
    
    $total_admin_price += $row['price'];
    $row['id'] = $id;
    $display_items[] = $row;

    // 2. Insert the VERIFIED part into the table
    $dbConnect->query("INSERT INTO tblorder_items (orderid, compid) VALUES ('$orderid', '$id')");

    // 3. Subtract 1 from stock since we are using this part
    $dbConnect->query("UPDATE tblcomponents SET stock_qty = stock_qty - 1 WHERE compid = '$id'");
}

$customer_price = $total_admin_price * 1.20; // 20% Markup

// 4. Update the main Order record with finalized prices and status
$dbConnect->query("UPDATE tblorder SET adminprice = '$total_admin_price', customerprice = '$customer_price', status = 'Finalized' WHERE orderid = '$orderid'");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Order #<?php echo $orderid; ?> Finalized</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; margin: 40px; }
        .summary-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .admin-price { color: #c0392b; font-weight: bold; }
        .customer-price { color: #27ae60; font-weight: bold; }
    </style>
</head>
<body>

<div class="summary-card">
    <h1>Order Finalized!</h1>
    <p>Order #<?php echo $orderid; ?> has been updated and stock has been deducted.</p>

    <table>
        <thead>
            <tr><th>Part ID</th><th>Category</th><th>Description</th><th>Cost</th></tr>
        </thead>
        <tbody>
            <?php foreach ($display_items as $item): ?>
            <tr>
                <td><strong><?php echo $item['id']; ?></strong></td>
                <td><?php echo $item['category']; ?></td>
                <td><?php echo $item['compdesc']; ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" align="right">Admin Cost:</td>
                <td class="admin-price">$<?php echo number_format($total_admin_price, 2); ?></td>
            </tr>
            <tr>
                <td colspan="3" align="right">Customer Total:</td>
                <td class="customer-price">$<?php echo number_format($customer_price, 2); ?></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <a href="adminreview.php" style="text-decoration:none; color:#e74c3c; font-weight:bold;">&larr; Return to Dashboard</a>
</div>

</body>
</html>