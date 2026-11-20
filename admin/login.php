<?php
session_start();

// Basit şifre koruması (gerçek projede hash + veritabanı önerilir)
$admin_password = getenv('ADMIN_PASSWORD') ?: 'CHANGE_ME';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Yanlış şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - AFL MUN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #0f0f1f;
            color: #c9c9d1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: rgba(16, 15, 74, 0.3);
            border: 1px solid rgba(201, 201, 209, 0.1);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #f9fff0;
            font-size: 1.8rem;
            margin-bottom: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .login-header p {
            color: #a1a1aa;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #f9fff0;
            font-weight: 500;
        }

        .form-group input[type="password"] {
            width: 100%;
            padding: 14px;
            background: rgba(16, 15, 74, 0.4);
            border: 1px solid rgba(201, 201, 209, 0.1);
            border-radius: 8px;
            color: #c9c9d1;
            font-size: 1rem;
        }

        .form-group input[type="password"]:focus {
            outline: none;
            border-color: rgba(139, 92, 246, 0.5);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: #8b5cf6;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn:hover {
            background: #7c3aed;
            transform: translateY(-2px);
        }

        .error {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #fca5a5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
            color: #8b5cf6;
            font-size: 2.2rem;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-lock"></i>
        </div>
        <div class="login-header">
            <h1><i class="fas fa-user-shield"></i> Admin Giriş</h1>
            <p>AFL MUN Yönetim Paneli</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="off">
            </div>
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </button>
        </form>
    </div>
</body>
</html>