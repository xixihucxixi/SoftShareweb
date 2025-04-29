<?php
require '../db.php';
if (empty($_SESSION['user_id']) || $_SESSION['is_admin']!=1) {
    header('Location: ../login.php');
    exit();
}
?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>后台管理中心</title>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">后台管理</a>
    <span class="navbar-text"> 管理员：<?=htmlspecialchars($_SESSION['username'])?> </span>
    <a class="nav-link text-light" href="../index.php">回前台</a>
</nav>
<div class="container my-4">
    <h3>管理功能区</h3>
    <ul class="list-group">
        <li class="list-group-item"><a href="softwares.php">软件管理</a></li>
        <li class="list-group-item"><a href="audits.php">待审核软件</a></li>
        <li class="list-group-item"><a href="categories.php">分类管理</a></li>
        <li class="list-group-item"><a href="models.php">型号管理</a></li>
        <li class="list-group-item"><a href="logs.php">上传/下载日志</a></li>
    </ul>
</div>
</body>
</html>
