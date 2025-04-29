<?php
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = '用户名和密码不能为空！';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // 写入Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = '用户名或密码错误！';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>用户登录</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        body {background: #f7f7fa;}
        .login-box {max-width: 350px; margin: auto; margin-top: 100px;}
    </style>
</head>
<body>
<div class="login-box">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">用户登录</h4>
        </div>
        <div class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
            <?php endif;?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input class="form-control" type="text" name="username" id="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input class="form-control" type="password" name="password" id="password" required>
                </div>
                <button class="btn btn-primary btn-block" type="submit">登录</button>
            </form>
            <hr>
            <p class="text-center mb-0">没有账号？<a href="register.php">用户注册</a></p>
        </div>
    </div>
</div>
</body>
</html>