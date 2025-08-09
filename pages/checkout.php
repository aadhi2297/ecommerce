<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = [];

if (isset($_POST['place_order'])) {
    $cart_stmt = $conn->prepare("SELECT SUM(p.price * c.quantity) AS total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $cart_stmt->execute([$user_id]);
    $cart_total = $cart_stmt->fetchColumn();

    if ($cart_total > 0) {
        $conn->beginTransaction();

        try {
            $order_stmt = $conn->prepare("INSERT INTO `orders` (user_id, total_amount) VALUES (?, ?)");
            $order_stmt->execute([$user_id, $cart_total]);
            $order_id = $conn->lastInsertId();

            $clear_cart_stmt = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $clear_cart_stmt->execute([$user_id]);

            $conn->commit();
            
            header('Location: order_success.php?order_id=' . $order_id);
            exit();

        } catch (PDOException $e) {
            $conn->rollBack();
            $message[] = "Order placement failed: " . $e->getMessage();
        }
    } else {
        $message[] = "Your cart is empty.";
    }
}

$stmt = $conn->prepare("SELECT p.name, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="checkout-page-container">
        <header>
            <div class="header-container">
                <h1>Checkout</h1>
                <nav>
                    <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                </nav>
            </div>
        </header>

        <div class="checkout-container">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($message[0]); ?></div>
            <?php endif; ?>

            <?php if (empty($cart_items)): ?>
                <div class="alert alert-info">
                    <p>Your cart is empty. <a href="../index.php">Continue shopping</a>.</p>
                </div>
            <?php else: ?>
                <div class="order-summary-card">
                    <h3>Order Summary</h3>
                    <ul class="order-item-list">
                        <?php foreach ($cart_items as $item): ?>
                            <li>
                                <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                <span class="item-details">
                                    x<?php echo htmlspecialchars($item['quantity']); ?> 
                                    - $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <h4 class="order-total">Total: $<?php echo number_format($total_amount, 2); ?></h4>
                </div>

                <div class="shipping-card">
                    <h3>Shipping Information</h3>
                    <form method="POST">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required>
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" required>
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" required>
                        <label for="postal_code">Postal Code:</label>
                        <input type="text" id="postal_code" name="postal_code" required>
                        
                        <button type="submit" name="place_order" class="checkout-button">Place Order</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>