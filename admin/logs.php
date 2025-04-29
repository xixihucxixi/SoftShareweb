<?php
require '../db.php';
if (empty($_SESSION['user_id']) || $_SESSION['is_admin']!=1) exit('无权限');
$uplog = $pdo->query("SELECT l.*, u.username, s.name soft 
       FROM upload_logs l LEFT JOIN users u ON l.user_id=u.id LEFT JOIN softwares s ON l.software_id=s.id 
       ORDER BY l.upload_time DESC LIMIT 200")->fetchAll();
$downlog = $pdo->query("SELECT l.*, u.username, s.name soft 
       FROM download_logs l LEFT JOIN users u ON l.user_id=u.id LEFT JOIN softwares s ON l.software_id=s.id 
       ORDER BY l.download_time DESC LIMIT 200")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>上传/下载日志</title>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">
    <a href="index.php" class="btn btn-secondary btn-sm">⬅ 返回后台</a>
    <h4>最近上传日志</h4>
    <table class="table">
        <tr><th>用户</th><th>软件</th><th>上传时间</th></tr>
        <?php foreach ($uplog as $r):?>
            <tr><td><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['soft'])?></td><td><?=$r['upload_time']?></td></tr>
        <?php endforeach;?>
    </table>
    <h4>最近下载日志</h4>
    <table class="table">
        <tr><th>用户</th><th>软件</th><th>下载时间</th></tr>
        <?php foreach ($downlog as $r):?>
            <tr><td><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['soft'])?></td><td><?=$r['download_time']?></td></tr>
        <?php endforeach;?>
    </table>
</div>
</body>
</html>