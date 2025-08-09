<?php
// Ensure the path to your database connection file is correct.
// It should be one directory up from the 'admin' folder.
include'db.php';

session_start();
$message = [];

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use a prepared statement with mysqli for security
   // Use a prepared statement with PDO for security
// Use a prepared statement with PDO for security
$stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
$stmt->execute([$email]);

// Fetch the result directly. fetch() returns false if no rows are found.
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Now you can proceed with password and role verification
    if(password_verify($password, $user['password'])){
        if($user['role'] == 'admin'){
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_id'] = $user['id'];
            header('location:dashboard.php');
        } elseif($user['role'] == 'user'){
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];
            header('location:home.php');
        }
    } else {
        $message[] = 'Incorrect email or password!';
    }
} else {
    $message[] = 'Incorrect email or password!';
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        .error-message {
            text-align: center;
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php
            if (!empty($message)) {
                foreach ($message as $msg) {
                    echo '<p class="error-message">' . htmlspecialchars($msg) . '</p>';
                }
            }
        ?>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>