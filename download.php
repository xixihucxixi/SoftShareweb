<?php
require_once 'db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 获取ID参数
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die('请求异常，未指定ID！');
}

// 检查软件信息，只允许下载已审核的软件
$stmt = $pdo->prepare("SELECT * FROM softwares WHERE id=? AND is_audited=1");
$stmt->execute([$id]);
$sw = $stmt->fetch();
if (!$sw) {
    die('该软件不存在或未通过审核！');
}

$filepath = __DIR__ . '/downloads/' . $sw['filename'];
if (!is_file($filepath)) {
    die('文件已被删除或找不到！');
}

// 记录日志
$stmt = $pdo->prepare("INSERT INTO download_logs (user_id, software_id) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $id]);

// 输出下载
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . rawurlencode($sw['filename']) . '"');
header('Content-Length: ' . filesize($filepath));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
readfile($filepath);
exit;