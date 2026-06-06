<?php
// 1. Unganisha na database yako
include 'db_connect.php';
$message = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Kuficha password kiusalama
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kuangalia kama username tayari ipo
    $check_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $check_user->store_result();

    if ($check_user->num_rows > 0) {
        $message = "<div style='color:red; font-weight:bold; margin-bottom:15px;'>Username already exists!</div>";
    } else {
        // Kuingiza mtumiaji mpya
        $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insert->bind_param("ss", $username, $hashed_password);
        
        if ($insert->execute()) {
            $message = "<div style='color:green; font-weight:bold; margin-bottom:15px;'>Account created successfully! <a href='login.php'>Login here</a></div>";
        } else {
            $message = "<div style='color:red; font-weight:bold; margin-bottom:15px;'>Something went wrong. Try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PetroGlow</title>
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
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 360px;
            border-top: 5px solid #ffcc00;
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
        p.login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        p.login-link a {
            color: #27ae60;
            text-decoration: none;
            font-weight: bold;
        }
        p.login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Create Account</h2>
    <p class="subtitle">Join PetroGlow Petroleum Trading Platform</p>
    
    <?php echo $message; ?>
    
    <form action="register.php" method="POST">
        <label for="username">Choose Username</label>
        <input type="text" name="username" id="username" placeholder="e.g., john_doe" required>
        
        <label for="password">Choose Password</label>
        <input type="password" name="password" id="password" placeholder="Enter secure password" required>
        
        <button type="submit" name="register">Sign Up</button>
    </form>
    
    <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>