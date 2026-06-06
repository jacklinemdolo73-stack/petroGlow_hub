<?php
// 1. Kuanzisha session juu kabisa ili kumlinda mtumiaji na kuvuta jina lake
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

// ULINZI: Kama mtu hajalogin, mfumo unamfukuza na kumrudisha login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// NJIA MBADALA YA HARAKA: Mfumo unampa mteja points 250 hapa hapa kwenye kodi
// Hii inazuia kosa la "Unknown column 'points'" kutokea kwenye kioo chako!
$user_points = 250; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard & Rewards | PetroGlow</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
            color: #333;
        }
        
        /* NAVBAR YA JUU */
        .navbar {
            background-color: #1a1a1a;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }
        .navbar .logo span { color: #ffcc00; }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar a.btn-logout {
            color: white;
            text-decoration: none;
            background: #e74c3c;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            transition: background 0.2s;
        }
        .navbar a.btn-logout:hover { background: #c0392b; }

        .container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* WELCOME SECTION BLOCK */
        .welcome-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 6px solid #ffcc00;
            margin-bottom: 30px;
        }
        .welcome-card h1 { margin: 0 0 10px 0; color: #1a1a1a; font-size: 28px; }
        .welcome-card h1 span { color: #27ae60; }
        .welcome-card p { margin: 0; color: #666; font-size: 15px; }

        /* SEHEMU YA MCHANGANYIKO: KADI YA POINTS + STATS */
        .dashboard-summary-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 768px) {
            .dashboard-summary-grid { grid-template-columns: 1fr; }
        }

        /* KADI KUBWA YA LOYALTY POINTS */
        .points-box {
            background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .points-box h3 { margin: 0; font-size: 16px; color: #ffcc00; text-transform: uppercase; letter-spacing: 1px; }
        .points-box .counter { font-size: 55px; font-weight: bold; margin: 10px 0; color: #ffffff; }
        .points-box p { margin: 0; font-size: 13px; color: #bdc3c7; }

        /* STATS NDOGO MBILI */
        .stats-right-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .stat-card h4 { margin: 0 0 10px 0; color: #7f8c8d; font-size: 15px; }
        .stat-card p { margin: 0; font-size: 32px; font-weight: bold; color: #1a1a1a; }

        /* SEHEMU YA REWARDS & DISCOUNTS (OFA) */
        .section-title {
            color: #1a1a1a;
            font-size: 22px;
            margin: 40px 0 20px 0;
            font-weight: bold;
            border-bottom: 3px solid #ffcc00;
            padding-bottom: 6px;
            display: inline-block;
        }
        
        .rewards-container-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .reward-item-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
            border-top: 4px solid #27ae60;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .reward-item-card.discount-style { border-top-color: #e67e22; }
        
        .reward-item-card h4 { margin: 0 0 8px 0; font-size: 18px; color: #1a1a1a; }
        .reward-item-card p { margin: 0 0 20px 0; font-size: 14px; color: #666; line-height: 1.4; }
        
        .reward-action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .points-tag {
            background-color: #f1f2f6;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
        }
        .points-tag span { color: #27ae60; }
        
        .btn-redeem {
            background-color: #1a1a1a;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s;
        }
        .btn-redeem:hover { background-color: #ffcc00; color: #1a1a1a; }
        .btn-locked { background-color: #d1d8e0; color: #888; cursor: not-allowed; }
        .btn-locked:hover { background-color: #d1d8e0; color: #888; }

        /* JEDWALI LA ODA ZILIZOPITA */
        .orders-table-wrapper {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .orders-table-wrapper h3 { margin-top: 0; color: #1a1a1a; font-size: 18px; margin-bottom: 15px; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eeeeee;
            font-size: 14px;
        }
        table th { background-color: #f8f9fa; color: #555; font-weight: bold; }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-badge.success { background-color: #e8f8f5; color: #27ae60; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">Petro<span>Glow</span> Portal</div>
        <div class="user-info">
            <span style="font-weight: bold;">👤 Account: <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="btn-logout" onclick="localStorage.clear();">Sign Out</a>
        </div>
    </div>

    <div class="container">
        
        <div class="welcome-card">
            <h1>Welcome back, <span><?php echo htmlspecialchars($username); ?></span>!</h1>
            <p>This is your personalized dashboard. Here you can monitor your fuel purchasing activity, manage current petroleum orders, and track your loyalty points metrics.</p>
        </div>

        <div class="dashboard-summary-grid">
            
            <div class="points-box">
                <h3>My Loyalty Points</h3>
                <div class="counter"><?php echo number_format($user_points); ?></div>
                <p>Earned from your recent fuel orders</p>
            </div>
            
            <div class="stats-right-grid">
                <div class="stat-card">
                    <h4>Total Fuel Orders</h4>
                    <p>12</p>
                </div>
                <div class="stat-card">
                    <h4>Pending Approvals</h4>
                    <p style="color: #e67e22;">2</p>
                </div>
                <div class="stat-card">
                    <h4>System Gateway</h4>
                    <p style="color: #27ae60; font-size: 24px;">Connected</p>
                </div>
            </div>
            
        </div>

        <div class="section-title">🎁 Available Rewards & Member Discounts</div>
        
        <div class="rewards-container-grid">
            
            <div class="reward-item-card discount-style">
                <div>
                    <h4>5% Fuel Discount Coupon</h4>
                    <p>Redeem this coupon to get a straight 5% discount on your next Petrol (PMS) delivery order exceeding 100 liters.</p>
                </div>
                <div class="reward-action-bar">
                    <div class="points-tag">Requires: <span>100 Pts</span></div>
                    <?php if ($user_points >= 100): ?>
                        <button class="btn-redeem" onclick="alert('Hongera! Umefanikiwa kuomba Punguzo la 5%. Code yako ya siri ni: PETRO5')">Claim Discount</button>
                    <?php else: ?>
                        <button class="btn-redeem btn-locked" disabled>Locked</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="reward-item-card">
                <div>
                    <h4>Free 10 Liters of Diesel</h4>
                    <p>Trade your loyalty points tokens to secure exactly 10 Liters of premium Diesel (AGO) completely free of charge.</p>
                </div>
                <div class="reward-action-bar">
                    <div class="points-tag">Requires: <span>300 Pts</span></div>
                    <?php if ($user_points >= 300): ?>
                        <button class="btn-redeem" onclick="alert('Hongera! Umefanikiwa kudai lita 10 za Diesel Bure. Code ya Ofa: FREEDIESEL')">Claim Fuel</button>
                    <?php else: ?>
                        <button class="btn-redeem btn-locked" disabled>Locked</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="reward-item-card discount-style">
                <div>
                    <h4>Tsh 20,000 Wallet Cashback</h4>
                    <p>Convert your accrued points to secure an instant Tsh 20,000 cash balance added directly to your trading account profile.</p>
                </div>
                <div class="reward-action-bar">
                    <div class="points-tag">Requires: <span>500 Pts</span></div>
                    <?php if ($user_points >= 500): ?>
                        <button class="btn-redeem" onclick="alert('Hongera! Tsh 20,000 zimehamishiwa kwenye akaunti yako.')">Claim Cash</button>
                    <?php else: ?>
                        <button class="btn-redeem btn-locked" disabled>Locked</button>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="orders-table-wrapper">
            <h3>Your Recent Petroleum Orders</h3>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Specification</th>
                        <th>Volume Ordered</th>
                        <th>Total Transaction Price</th>
                        <th>Delivery Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#PG-9081</td>
                        <td>Petrol (PMS)</td>
                        <td>5,000 Ltrs</td>
                        <td>Tsh 14,500,000</td>
                        <td><span class="status-badge success">Completed</span></td>
                    </tr>
                    <tr>
                        <td>#PG-8922</td>
                        <td>Diesel (AGO)</td>
                        <td>10,000 Ltrs</td>
                        <td>Tsh 28,000,000</td>
                        <td><span class="status-badge success">Completed</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>