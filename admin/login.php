<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    
     
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit();
    } else {
        $error = "Invalid credentials!";
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            background-image: radial-gradient(#ff660011 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: #ffffff;
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 440px;
            position: relative;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #ff6600, #ff9933);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header img {
            max-width: 180px;
            margin-bottom: 1.5rem;
        }
        
        .login-header h1 {
            color: #1a1a1a;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: #fdfdfd;
            border: 2px solid #eee;
            border-radius: 12px;
            color: #1a1a1a;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }
        
        .form-group input:focus {
            border-color: #ff6600;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255, 102, 0, 0.1);
        }
        
        .login-btn {
            width: 100%;
            background: #ff6600;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 1rem;
            box-shadow: 0 10px 20px rgba(255, 102, 0, 0.2);
        }
        
        .login-btn:hover {
            background: #e65c00;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(255, 102, 0, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }
        
        .error {
            background: #fff5f5;
            color: #e53e3e;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid #feb2b2;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../images/logo.png" alt="Netcoder Technology">
            <h1>Admin Portal</h1>
            <p>Welcome back! Please login to continue.</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Sign In</button>
        </form>
    </div>
</body>
</html>
