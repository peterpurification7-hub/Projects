<?php
$url_userid = isset($_GET['userid']) ? $_GET['userid'] : "0";
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>TechForge - Configurator</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; margin: 0; }
        header { background-color: #2c3e50; color: white; padding: 25px; text-align: center; border-bottom: 5px solid #e74c3c; }
        header h1 { color: #e74c3c; margin: 0; font-size: 2.5em; text-transform: uppercase; }
        
        .config-container { 
            width: 70%; max-width: 900px; margin: 50px auto; background: white; 
            padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
        }
        
        h2 { text-align: center; color: #34495e; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        .label { font-weight: bold; color: #2c3e50; width: 35%; font-size: 1.1em; }
        
        select { 
            width: 100%; padding: 12px; border: 2px solid #dcdde1; 
            border-radius: 6px; background-color: #f9f9f9;
        }
        
        /* Special styling for the greyed-out option */
        option:disabled { color: #aaa; background-color: #f0f0f0; }

        .submit-btn { 
            display: block; width: 100%; background: #e74c3c; color: white; 
            padding: 18px; border: none; border-radius: 8px; 
            font-size: 20px; font-weight: bold; cursor: pointer; margin-top: 30px;
        }
        .submit-btn:hover { background: #c0392b; }
    </style>

    <script>
        function checkBuildRequirements() {
            // These IDs must match the category names used in the loop
            const reqs = ['CPU', 'Motherboard', 'RAM', 'Storage', 'PSU', 'Case'];
            let met = true;

            reqs.forEach(id => {
                let dropdown = document.getElementById(id);
                if (dropdown && dropdown.value == "0") {
                    met = false;
                }
            });

            const fullBuildOption = document.getElementById('fullBuildOption');
            const serviceSelect = document.getElementById('serviceSelect');

            if (met) {
                fullBuildOption.disabled = false;
                fullBuildOption.innerText = "Full Build (Professional Assembly)";
                fullBuildOption.style.color = "black";
            } else {
                fullBuildOption.disabled = true;
                fullBuildOption.innerText = "Full Build (Requires CPU, Motherboard, RAM, Storage, PSU, and Case)";
                // If they previously selected Full Build then unselected a part, force back to Parts Only
                if (serviceSelect.value == "Full Build") {
                    serviceSelect.value = "Parts Only";
                }
            }
        }
    </script>
</head>
<body>

<header>
    <h1>TechForge</h1>
    <p>Logged in as: <strong>Customer #<?php echo $url_userid; ?></strong></p>
</header>

<div class="config-container">
    <h2>Configure Your Performance System</h2>
    <form action="tech_order_submit.php" method="post">
        <input type="hidden" name="userid" value="<?php echo $url_userid; ?>">
        
        <table>
            <?php
            // Added 'Case' to the array
            $categories = ['CPU', 'GPU', 'CPU Cooler', 'Motherboard', 'RAM', 'Storage', 'PSU', 'Case', 'OS', 'Monitor'];

            foreach ($categories as $cat) {
                echo "<tr>";
                echo "<td class='label'>$cat:</td>";
                // ID added here so JavaScript can "find" the dropdown
                echo "<td><select name='parts[]' id='$cat' onchange='checkBuildRequirements()'>";
                echo "<option value='0'>-- Select Option (None) --</option>";
                
                $query = "SELECT compid, compdesc, tier, price FROM tblcomponents WHERE category='$cat' ORDER BY price ASC";
                $result = $dbConnect->query($query);
                
                while($row = $result->fetch_assoc()) {
                    echo "<option value='".$row['compid']."'>";
                    echo $row['compdesc'] . " [" . $row['tier'] . "] - $" . number_format($row['price'], 2);
                    echo "</option>";
                }
                echo "</select></td></tr>";
            }
            ?>
            
            <tr>
                <td class="label">Service Level:</td>
                <td>
                    <select name="servicetype" id="serviceSelect">
                        <option value="Parts Only">Parts Only (DIY Kit)</option>
                        <option value="Full Build" id="fullBuildOption" disabled>
                            Full Build (Requires CPU, Motherboard, RAM, Storage, PSU, and Case)
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        
        <input type="submit" class="submit-btn" value="Finalize & View Receipt">
    </form>
</div>

</body>
</html>