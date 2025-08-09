<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .success-container {
            text-align: center;
            margin-top: 100px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .success-container h2 {
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="success-container">
            <h2>🎉 Thank You for Your Order!</h2>
            <?php if ($order_id) : ?>
                <p>Your order has been successfully placed.</p>
                <p><strong>Order ID: #<?php echo htmlspecialchars($order_id); ?></strong></p>
            <?php else : ?>
                <p>Thank you for your order!</p>
            <?php endif; ?>
            <p><a href="../index.php">Continue shopping</a></p>
        </div>
    </div>
</body>
</html>