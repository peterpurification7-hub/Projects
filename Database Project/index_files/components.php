<?php
@$dbConnect = new mysqli('localhost', 'root', '', 'techforge');
if ($dbConnect->connect_error) { die("Connection failed"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TechForge - Component Showroom</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; margin: 0; color: #333; }
        header { background-color: #2c3e50; color: white; padding: 40px; text-align: center; border-bottom: 5px solid #e74c3c; }
        
        .main-container { width: 90%; max-width: 1200px; margin: 40px auto; }
        
        /* Category Section */
        .category-section { margin-bottom: 80px; }
        .category-title { 
            font-size: 2em; text-transform: uppercase; letter-spacing: 2px;
            color: #2c3e50; border-left: 8px solid #e74c3c; padding-left: 15px; margin-bottom: 30px; 
        }

        /* The 3-Column Grid (Budget, Pro, Ultra) */
        .tier-grid { 
            display: flex; justify-content: space-between; gap: 20px; 
        }

        /* The Component Card (The Hovering Box) */
        .comp-card { 
            background: white; flex: 1; border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: all 0.3s ease;
            text-align: center; border: 1px solid #ddd; position: relative;
        }
        .comp-card:hover { 
            transform: translateY(-15px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.2); 
            border-color: #e74c3c; 
        }

        /* Tier Labels inside the cards */
        .tier-tag { 
            position: absolute; top: 10px; right: 10px; padding: 4px 10px; 
            border-radius: 20px; font-size: 0.75em; font-weight: bold; text-transform: uppercase;
        }
        .tag-Budget { background: #bdc3c7; color: #2c3e50; }
        .tag-Pro { background: #3498db; color: white; }
        .tag-Ultra { background: #f1c40f; color: #000; }

        .comp-img { width: 100%; height: 200px; object-fit: cover; background: #eee; }
        
        .comp-info { padding: 20px; }
        .comp-name { font-weight: bold; font-size: 1.1em; color: #2c3e50; display: block; margin-bottom: 10px; }
        .comp-price { color: #e74c3c; font-size: 1.3em; font-weight: bold; display: block; }

        .back-link { display: block; text-align: center; margin: 50px 0; font-weight: bold; color: #2c3e50; text-decoration: none; }
    </style>
</head>
<body>

<header>
    <h1>The TechForge Showroom</h1>
    <p>Compare our Precision-Selected Component Tiers</p>
</header>

<div class="main-container">

    <?php
    // 1. Get each unique category (CPU, GPU, etc.)
    $catQuery = $dbConnect->query("SELECT DISTINCT category FROM tblcomponents ORDER BY category ASC");
    
    while ($catRow = $catQuery->fetch_assoc()) {
        $category = $catRow['category'];
        echo "<div class='category-section'>";
        echo "<h2 class='category-title'>$category</h2>";
        echo "<div class='tier-grid'>";

        // 2. For this category, pull exactly 3 items: Budget, Pro, and Ultra
        // We order them so Budget is always left, Pro is middle, Ultra is right
        $tiers = ['Budget', 'Pro', 'Ultra'];
        foreach ($tiers as $tier) {
            $compQuery = $dbConnect->query("SELECT * FROM tblcomponents WHERE category = '$category' AND tier = '$tier' LIMIT 1");
            
            if ($comp = $compQuery->fetch_assoc()) {
                $imgName = "comp_" . $comp['compid'] . ".jpg";
                
                echo "
                <div class='comp-card'>
                    <div class='tier-tag tag-{$tier}'>{$tier}</div>
                    <img src='$imgName' alt='{$comp['compdesc']}' class='comp-img'>
                    <div class='comp-info'>
                        <span class='comp-name'>{$comp['compdesc']}</span>
                        <span class='comp-price'>$" . number_format($comp['price'], 2) . "</span>
                    </div>
                </div>";
            } else {
                // If a tier is missing for a category, show a placeholder
                echo "<div class='comp-card' style='opacity:0.5;'><div class='comp-info'>N/A</div></div>";
            }
        }
        
        echo "</div></div>"; // End tier-grid and category-section
    }
    ?>

    <a href="../index.htm" class="back-link">&larr; Return to Home Page</a>
</div>

</body>
</html>