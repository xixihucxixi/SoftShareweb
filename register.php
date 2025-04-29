<?php
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // 表单验证
    if ($username === '' || $password === '' || $password2 === '') {
        $error = '所有字段均不能为空！';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = '用户名长度需在3到20字符之间。';
    } elseif ($password !== $password2) {
        $error = '两次输入的密码不一致！';
    } elseif (strlen($password) < 6) {
        $error = '密码长度不能少于6位。';
    } else {
        // 检查用户是否存在
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = '该用户名已被注册！';
        } else {
            // 写入新用户
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 0)");
            $stmt->execute([$username, $hash]);
            header('Location: login.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>用户注册</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        body {background: #f7f7fa;}
        .register-box {max-width: 380px; margin: auto; margin-top: 100px;}
    </style>
</head>
<body>
<div class="register-box">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">用户注册</h4>
        </div>
        <div class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
            <?php endif;?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input class="form-control" type="text" name="username" id="username"
                        required minlength="3" maxlength="20" value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input class="form-control" type="password" name="password" id="password"
                        required minlength="6">
                </div>
                <div class="form-group">
                    <label for="password2">重复密码</label>
                    <input class="form-control" type="password" name="password2" id="password2"
                        required minlength="6">
                </div>
                <button class="btn btn-success btn-block" type="submit">注册</button>
            </form>
            <hr>
            <p class="text-center mb-0">已有账号？<a href="login.php">立即登录</a></p>
        </div>
    </div>
</div>
</body>
</html>