<?php
require '../db.php';
if (empty($_SESSION['user_id']) || $_SESSION['is_admin']!=1) exit('无权限');
$cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();
// 新增
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['name']) && isset($_POST['category_id'])) {
    $name=trim($_POST['name']);
    $cid=intval($_POST['category_id']);
    if ($name && $cid) {
        $stmt = $pdo->prepare("INSERT INTO models (category_id, name) VALUES (?,?)");
        $stmt->execute([$cid, $name]);
    }
    header('Location: models.php');
    exit();
}
// 删除
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM models WHERE id=?")->execute([$id]);
    header('Location: models.php');
    exit();
}
$list = $pdo->query("SELECT m.*, c.name cname FROM models m LEFT JOIN categories c ON m.category_id=c.id ORDER BY m.category_id, m.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title>型号管理</title>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">
    <a href="index.php" class="btn btn-secondary btn-sm">⬅ 返回后台</a>
    <h4>型号管理</h4>
    <form method="post" class="form-inline mb-2">
        <select name="category_id" class="form-control mr-2" required>
            <?php foreach($cats as $c):?><option value="<?=$c['id']?>"><?=$c['name']?></option><?php endforeach;?>
        </select>
        <input type="text" name="name" placeholder="型号名称" class="form-control mr-2" required>
        <button class="btn btn-success" type="submit">添加</button>
    </form>
    <table class="table">
        <tr><th>ID</th><th>所属分类</th><th>型号名称</th><th>操作</th></tr>
        <?php foreach ($list as $r): ?>
            <tr>
                <td><?=$r['id']?></td>
                <td><?=htmlspecialchars($r['cname'])?></td>
                <td><?=htmlspecialchars($r['name'])?></td>
                <td><a href="?delete=<?=$r['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('删除该型号？')">删除</a></td>
            </tr>
        <?php endforeach;?>
    </table>
</div>
</body>
</html>