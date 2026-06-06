<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

// Ulinzi: Kama mteja hajajisajili au hajalogin, anafukuzwa kwenda login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_points = 0;

// Kuvuta points za huyu mteja kutoka kwenye database
$stmt = $conn->prepare("SELECT points FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $user_points = $user['points'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rewards & Points | PetroGlow</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
        }
        .navbar {
            background-color: #1a1a1a;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo { font-size: 22px; font-weight: bold; }
        .navbar .logo span { color: #ffcc00; }
        .navbar nav a { color: white; text-decoration: none; margin-left: 20px; font-weight: bold; }
        .navbar nav a.logout { background: #e74c3c; padding: 5px 10px; border-radius: 4px; }
        
        .container {
            padding: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        /* CARD YA POINTS */
        .points-display-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .points-display-card::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 150px; height: 150px;
            background: rgba(255, 204, 0, 0.1);
            border-radius: 50%;
        }
        .points-display-card h2 { margin: 0 0 10px 0; font-size: 20px; color: #ffcc00; text-transform: uppercase; }
        .points-display-card .points-number { font-size: 64px; font-weight: bold; margin: 10px 0; color: #fff; }
        .points-display-card p { margin: 0; color: #ccc; font-size: 16px; }

        /* SEHEMU YA REWARDS */
        .section-title { color: #1a1a1a; margin-bottom: 20px; font-size: 24px; border-bottom: 3px solid #ffcc00; padding-bottom: 5px; display: inline-block; }
        
        .rewards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        .reward-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            padding: 25px;
            border-top: 4px solid #27ae60;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .reward-card:hover { transform: translateY(-5px); }
        .reward-card.discount { border-top-color: #e67e22; }
        
        .reward-card h3 { margin: 0 0 10px 0; color: #1a1a1a; font-size: 20px; }
        .reward-card p { color: #666; font-size: 14px; margin: 0 0 20px 0; line-height: 1.5; }
        
        .reward-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cost-tag { background: #f1f2f6; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 14px; color: #333; }
        .cost-tag span { color: #27ae60; }
        
        .btn-claim {
            background: #1a1a1a; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: background 0.2s;
        }
        .btn-claim:hover { background: #ffcc00; color: #1a1a1a; }
        .btn-disabled { background: #ccc; color: #666; cursor: not-allowed; }
        .btn-disabled:hover { background: #ccc; color: #666; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">Petro<span>Glow</span> Client</div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="rewards.php" style="color: #ffcc00;">My Rewards</a>
            <a href="logout.php" class="logout">Sign Out</a>
        </nav>
    </div>

    <div class="container">
        <div class="points-display-card">
            <h2>PetroGlow Loyalty Points</h2>
            <div class="points-number"><?php echo number_format($user_points); ?></div>
            <p>Habari <strong><?php echo htmlspecialchars($username); ?></strong>, tumia points zako hapa chini kupata punguzo la bei ya mafuta!</p>
        </div>

        <div class="section-title">Available Rewards & Discounts</div>
        
        <div class="rewards-grid">
            
            <div class="reward-card discount">
                <div>
                    <h3>5% Fuel Discount</h3>
                    <p>Get a 5% discount on your next Petrol (PMS) purchase of over 100 Liters.</p>
                </div>
                <div class="reward-footer">
                    <div class="cost-tag">Gharama: <span>100 Pts</span></div>
                    <?php if($user_points >= 100): ?>
                        <button class="btn-claim" onclick="alert('Hongera! Umeomba punguzo la 5%. Code yako ya siri ni: PETRO5')">Claim Now</button>
                    <?php else: ?>
                        <button class="btn-claim btn-disabled" disabled>Points Hazitoshi</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="reward-card">
                <div>
                    <h3>Free 10 Liters Diesel</h3>
                    <p>Redeem your points to get completely free 10 Liters of high-quality Diesel (AGO).</p>
                </div>
                <div class="reward-footer">
                    <div class="cost-tag">Gharama: <span>300 Pts</span></div>
                    <?php if($user_points >= 300): ?>
                        <button class="btn-claim" onclick="alert('Hongera! Umefanikiwa kudai lita 10 za Diesel Bure. Code: FREEDIESEL')">Claim Now</button>
                    <?php else: ?>
                        <button class="btn-claim btn-disabled" disabled>Points Hazitoshi</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="reward-card discount">
                <div>
                    <h3>Tsh 20,000 Cashback</h3>
                    <p>Get Tsh 20,000 cash back instantly deposited to your trading wallet profile.</p>
                </div>
                <div class="reward-footer">
                    <div class="cost-tag">Gharama: <span>500 Pts</span></div>
                    <?php if($user_points >= 500): ?>
                        <button class="btn-claim" onclick="alert('Hongera! Tsh 20,000 zimeongezwa kwenye wallet yako.')">Claim Now</button>
                    <?php else: ?>
                        <button class="btn-claim btn-disabled" disabled>Points Hazitoshi</button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</body>
</html>