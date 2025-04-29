<?php
require_once 'db.php';
// 清除所有session数据
$_SESSION = array();

// 如果使用了 session cookie，也清除
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 销毁session
session_destroy();

// 跳转到首页
header('Location: index.php');
exit();
?>