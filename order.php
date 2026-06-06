<?php 
// Include the database connection we created earlier
include 'db.php'; 

// Fetch products from the database to populate our dropdown list
$products_result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - PetroGlow</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 50px; }
        .form-container { background: white; padding: 30px; max-width: 50px; margin: 0 auto; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); max-width: 500px; }
        h2 { color: #111; text-align: center; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        select, input[type="number"], button { width: 100%; padding: 10px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background-color: #ffcc00; border: none; font-weight: bold; cursor: pointer; margin-top: 20px; font-size: 16px; }
        button:hover { background-color: #e6b800; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Petroleum Order Form</h2>
    
    <form action="process_order.php" method="POST">
        
        <label for="product">Select Petroleum Product:</label>
        <select name="product_id" id="product" required>
            <option value="">-- Select Product --</option>
            <?php while($row = $products_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo $row['name']; ?> ($<?php echo $row['price_per_liter']; ?>/L)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="liters">Quantity (in Liters):</label>
        <input type="number" name="liters" id="liters" min="1" placeholder="e.g. 500" required>

        <button type="submit" name="submit_order">Calculate Total & Order</button>
    </form>
</div>

</body>
</html>