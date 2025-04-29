<?php
// 数据库连接配置
$db_host = 'localhost';      // 数据库服务器地址
$db_name = 'down';    // 数据库名（请根据实际填写）
$db_user = 'down';    // 用户名
$db_pass = 'm4998735';// 密码

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    exit('数据库连接失败: ' . $e->getMessage());
}

// 启动 session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 如果有登录用户，便捷补充用户名 session 字段
if (!empty($_SESSION['user_id']) && empty($_SESSION['username'])) {
    // 下方代码可选，用于在登录后刷新页面时 $_SESSION['username'] 始终存在
    $stmt = $pdo->prepare('SELECT username FROM users WHERE id=?');
    $stmt->execute([$_SESSION['user_id']]);
    if ($row = $stmt->fetch()) {
        $_SESSION['username'] = $row['username'];
    }
}
?>