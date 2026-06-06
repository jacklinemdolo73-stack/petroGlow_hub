<?php
// 1. Unganisha na database yako
include 'db_connect.php';
session_start(); // Inaruhusu mfumo kumkumbuka mtumiaji akishaingia

$message = "";

// 2. Angalia kama mtumiaji amebonyeza kitufe cha Log In
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Tafuta kama huyu mtumiaji yupo kwenye database
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Linganisha password aliyoandika na ile iliyopo kwenye DB (iliyofichwa)
        if (password_verify($password, $user['password'])) {
            // Hapa mfumo unamtambua rasmi mtumiaji
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            
            // Mfumo unampeleka mtumiaji kwenye ukurasa wa ndani (Dashboard)
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "<div class='alert error'>Incorrect password!</div>";
        }
    } else {
        $message = "<div class='alert error'>Username not found!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PetroGlow</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 360px;
            border-top: 5px solid #ffcc00; /* Mstari wa rangi ya dhahabu/mafuta */
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #1a1a1a;
        }
        p.subtitle {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 30px;
        }
        label {
            font-weight: bold;
            font-size: 14px;
            color: #333;
            display: block;
            margin-top: 15px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #ffcc00;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #1a1a1a;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 25px;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #ffcc00;
            color: #1a1a1a;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
            margin-bottom: 15px;
        }
        .error {
            background-color: #fce4e4;
            color: #cc0000;
            border: 1px solid #fccdcd;
        }
        p.signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        p.signup-link a {
            color: #27ae60;
            text-decoration: none;
            font-weight: bold;
        }
        p.signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login to PetroGlow</h2>
    <p class="subtitle">Enter your credentials to manage petroleum orders</p>
    
    <?php echo $message; ?>
    
    <form action="login.php" method="POST">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" placeholder="Enter username" required>
        
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>
        
        <button type="submit" name="login">Sign In</button>
    </form>
    
    <p class="signup-link">Don't have an account? <a href="register.php">Create Account</a></p>
</div>

</body>
</html>