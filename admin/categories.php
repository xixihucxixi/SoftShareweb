<?php
require '../db.php';
if (empty($_SESSION['user_id']) || $_SESSION['is_admin']!=1) exit('无权限');

// 新增
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['name'])) {
    $name=trim($_POST['name']);
    if ($name) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
    }
    header('Location: categories.php');
    exit();
}
// 删除
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    header('Location: categories.php');
    exit();
}
$list = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title>分类管理</title>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">
    <a href="index.php" class="btn btn-secondary btn-sm">⬅ 返回后台</a>
    <h4>分类管理</h4>
    <form method="post" class="form-inline mb-2">
        <input type="text" name="name" placeholder="新分类名" class="form-control mr-2" required>
        <button class="btn btn-success" type="submit">添加</button>
    </form>
    <table class="table">
        <tr><th>ID</th><th>名称</th><th>操作</th></tr>
        <?php foreach ($list as $r): ?>
            <tr>
                <td><?=$r['id']?></td>
                <td><?=htmlspecialchars($r['name'])?></td>
                <td><a href="?delete=<?=$r['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('删除该分类？')">删除</a></td>
            </tr>
        <?php endforeach;?>
    </table>
</div>
</body>
</html>