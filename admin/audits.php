<?php
require '../db.php';
if (empty($_SESSION['user_id']) || $_SESSION['is_admin']!=1) exit('无权限');

// 审核通过
if (isset($_GET['pass'])) {
    $id = intval($_GET['pass']);
    $pdo->prepare("UPDATE softwares SET is_audited=1 WHERE id=?")->execute([$id]);
    header('Location: audits.php');
    exit();
}
// 驳回(删除)
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $stmt = $pdo->prepare("SELECT filename FROM softwares WHERE id=?");
    $stmt->execute([$id]);
    $sw = $stmt->fetch();
    if ($sw) @unlink("../downloads/".$sw['filename']);
    $pdo->prepare("DELETE FROM softwares WHERE id=?")->execute([$id]);
    header('Location: audits.php');
    exit();
}
// 查询未审核
$list = $pdo->query("SELECT s.*, c.name categ, m.name model
                     FROM softwares s
                     LEFT JOIN categories c ON s.category_id=c.id
                     LEFT JOIN models m ON s.model_id=m.id
                     WHERE s.is_audited=0 ORDER BY s.uploaded_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>软件审核</title>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">
    <a href="index.php" class="btn btn-secondary btn-sm">⬅ 返回后台</a>
    <h4>待审核软件</h4>
    <table class="table table-bordered">
        <tr><th>名称</th><th>分类</th><th>型号</th><th>描述</th><th>上传时间</th><th>操作</th></tr>
        <?php foreach($list as $r): ?>
            <tr>
                <td><?=htmlspecialchars($r['name'])?></td>
                <td><?=htmlspecialchars($r['categ'])?></td>
                <td><?=htmlspecialchars($r['model'])?></td>
                <td><?=htmlspecialchars($r['description'])?></td>
                <td><?=$r['uploaded_at']?></td>
                <td>
                    <a href="?pass=<?=$r['id']?>" class="btn btn-sm btn-success">通过</a>
                    <a href="?reject=<?=$r['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('确认删除？')">驳回</a>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
</div>
</body>
</html>