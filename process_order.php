<?php
include 'db.php';

// Check if the form was actually submitted
if (isset($_POST['submit_order'])) {
    
    // Get the data safely from the form
    $product_id = intval($_POST['product_id']);
    $liters_ordered = intval($_POST['liters']);

    // Fetch the price of the specific product selected from the database
    $query = "SELECT name, price_per_liter, stock_available FROM products WHERE id = $product_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        $product_name = $product['name'];
        $price_per_liter = $product['price_per_liter'];
        $current_stock = $product['stock_available'];

        // Business Logic Validation: Check if we have enough stock
        if ($liters_ordered > $current_stock) {
            echo "<div style='color: red; font-family: Arial; text-align: center; margin-top: 50px;'>";
            echo "<h2>Order Failed!</h2>";
            echo "<p>Sorry, we only have " . $current_stock . " liters of " . $product_name . " available.</p>";
            echo "<a href='order.php'>Go Back</a>";
            echo "</div>";
        } else {
            // Calculation
            $total_cost = $liters_ordered * $price_per_liter;
            
            // Deduct stock from database (Update operation)
            $new_stock = $current_stock - $liters_ordered;
            $conn->query("UPDATE products SET stock_available = $new_stock WHERE id = $product_id");

            // Display Invoice/Receipt
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Order Receipt</title>
                <style>
                    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 50px; text-align: center; }
                    .receipt { background: white; max-width: 400px; margin: 0 auto; padding: 30px; border: 2px dashed #111; border-radius: 8px; }
                    h2 { color: green; }
                    .details { text-align: left; margin-top: 20px; line-height: 2; }
                    .total { font-size: 20px; font-weight: bold; color: #111; border-top: 2px solid #ccc; padding-top: 10px; }
                    .btn { display: inline-block; background: #111; color: white; padding: 10px 20px; text-decoration: none; margin-top: 20px; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class="receipt">
                    <h2>Order Successful!</h2>
                    <p>Thank you for trading with PetroGlow.</p>
                    <hr>
                    <div class="details">
                        <strong>Product:</strong> <?php echo $product_name; ?><br>
                        <strong>Price per Liter:</strong> $<?php echo number_format($price_per_liter, 2); ?><br>
                        <strong>Quantity Ordered:</strong> <?php echo number_format($liters_ordered); ?> Liters<br>
                        <div class="total">
                            Total Cost: $<?php echo number_format($total_cost, 2); ?>
                        </div>
                    </div>
                    <a href="order.php" class="btn">Place Another Order</a>
                </div>
            </body>
            </html>
            <?php
        }
    } else {
        echo "Invalid Product Selected.";
    }
} else {
    // Redirect back to order form if they try to access this page directly without submitting
    header("Location: order.php");
    exit();
}
?>