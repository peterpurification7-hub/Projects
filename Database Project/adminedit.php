<?php
$target_order = isset($_GET['orderid']) ? $_GET['orderid'] : "";
?>
<!DOCTYPE html>
<html>
<body>
<h3>Finalize Build - Manual ID Entry</h3>
<p>Use the Admin Dashboard as a reference for Component IDs.</p>

<form action="admin_finalize_logic.php" method="post">
    <table>
        <tr><td>Order ID to Finalize:</td><td><input type="text" name="orderid" value="<?php echo $target_order; ?>" required></td></tr>
        <tr><td>CPU ID:</td><td><input type="text" name="part_ids[]" required></td></tr>
        <tr><td>GPU ID:</td><td><input type="text" name="part_ids[]" required></td></tr>
        <tr><td>Motherboard ID:</td><td><input type="text" name="part_ids[]" required></td></tr>
        <tr><td>RAM ID:</td><td><input type="text" name="part_ids[]" required></td></tr>
        <tr><td>PSU ID:</td><td><input type="text" name="part_ids[]" required></td></tr>
        <tr><td>Case ID:</td><td><input type="text" name="part_ids[]" required></td></tr>
    </table>
    <br>
    <input type="submit" value="Calculate Prices & Finalize">
</form>
</body>
</html>