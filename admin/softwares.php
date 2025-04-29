<?php
require '../db.php';
if (empty($_SESSION['user_id']) || $_SESSION['is_admin']!=1) {
    header('Location: ../login.php');
    exit();
}

// 查询分类、型号数据
$cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();
$mdl_map = [];
foreach ($cats as $cat) {
    $mdl_map[$cat['id']] = $pdo->query("SELECT * FROM models WHERE category_id={$cat['id']}")->fetchAll();
}

// 上传功能
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['op']) && $_POST['op']=='add') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $model_id = intval($_POST['model_id']);
    $file = $_FILES['file'];
    if ($file['error'] > 0 || !$file['name']) $err = "请选择上传文件";
    else {
        $tmp = $file['tmp_name'];
        $filename = date('YmdHis').'_'.uniqid().strrchr($file['name'], '.');
        $hash = hash_file('sha256', $tmp);
        $cstmt = $pdo->prepare("SELECT id FROM softwares WHERE category_id=? AND model_id=? AND (filehash=?)");
        $cstmt->execute([$category_id, $model_id, $hash]);
        if ($cstmt->fetch()) $err = "该文件已存在";
        else {
            move_uploaded_file($tmp, "../downloads/$filename");
            $stmt = $pdo->prepare("INSERT INTO softwares (category_id, model_id, name, description, filename, filehash, uploader_id, is_audited) VALUES (?,?,?,?,?,?,?,1)");
            $stmt->execute([$category_id, $model_id, $name, $description, $filename, $hash, $_SESSION['user_id']]);
            $succ = "上传成功";
        }
    }
    // 如果是AJAX上传，直接简明输出
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if (!empty($succ)) echo '<span class="text-success">'.$succ.'</span>';
        else echo '<span class="text-danger">'.$err.'</span>';
        exit;
    }
}

// 删除功能
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT filename FROM softwares WHERE id=?");
    $stmt->execute([$id]);
    $sw = $stmt->fetch();
    if ($sw) @unlink("../downloads/".$sw['filename']);
    $stmt = $pdo->prepare("DELETE FROM softwares WHERE id=?");
    $stmt->execute([$id]);
    header('Location: softwares.php');
    exit();
}

// 编辑功能
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['op']) && $_POST['op']=='edit') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $model_id = intval($_POST['model_id']);
    $stmt = $pdo->prepare("UPDATE softwares SET name=?,description=?, category_id=?, model_id=? WHERE id=?");
    $stmt->execute([$name, $description, $category_id, $model_id, $id]);
    header("Location: softwares.php");
    exit();
}

// 列表展示
$list = $pdo->query("SELECT s.*, c.name categ, m.name model
                     FROM softwares s
                     LEFT JOIN categories c ON s.category_id=c.id
                     LEFT JOIN models m ON s.model_id=m.id
                     ORDER BY s.uploaded_at DESC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>软件管理与上传</title>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        #addform {display:none;}
        .progress {margin-top:8px;display:none;}
        .progress-bar {transition:width 0.2s;}
    </style>
</head>
<body>
<div class="container my-4">
    <nav>
        <a href="index.php" class="btn btn-secondary btn-sm">⬅ 返回后台首页</a>
    </nav>
    <h3 class="mb-3">软件管理
        <button class="btn btn-success btn-sm float-right" onclick="document.getElementById('addform').style.display='block'">+上传</button>
    </h3>

    <!-- 软件列表 -->
    <table class="table table-bordered table-hover">
        <thead><tr>
            <th>ID</th><th>名称</th><th>分类</th><th>型号</th><th>描述</th><th>上传时间</th><th>操作</th>
        </tr></thead>
        <tbody>
        <?php foreach($list as $r):?>
        <tr>
            <td><?=$r['id']?></td>
            <td><?=htmlspecialchars($r['name'])?></td>
            <td><?=htmlspecialchars($r['categ'])?></td>
            <td><?=htmlspecialchars($r['model'])?></td>
            <td><?=htmlspecialchars($r['description'])?></td>
            <td><?=$r['uploaded_at']?></td>
            <td>
                <a href="?edit=<?=$r['id']?>" class="btn btn-warning btn-sm">编辑</a>
                <a href="?delete=<?=$r['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('真的要删除？')">删除</a>
            </td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>

    <!-- 新增上传表单（弹窗效果） -->
    <div id="addform" class="card mb-4" style="position:fixed;z-index:1000;top:60px;left:50%;width:450px;max-width:95%;margin-left:-225px;">
        <div class="card-header bg-success text-white">
            <b>上传软件</b>
            <button type="button" class="close text-white float-right" onclick="document.getElementById('addform').style.display='none'">&times;</button>
        </div>
        <div class="card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <input type="hidden" name="op" value="add">
                <div class="form-group"><label>软件名称</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group"><label>软件描述</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label>分类</label>
                    <select name="category_id" class="form-control" id="f_category" required>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?=$c['id']?>"><?=$c['name']?></option><?php endforeach;?>
                    </select>
                </div>
                <div class="form-group">
                    <label>型号</label>
                    <select name="model_id" class="form-control" id="f_model" required></select>
                </div>
                <div class="form-group"><label>软件包文件</label>
                    <input type="file" name="file" required>
                    <!-- 上传进度条 -->
                    <div class="progress my-2" id="progbar">
                        <div class="progress-bar" role="progressbar" style="width:0%">0%</div>
                    </div>
                </div>
                <button class="btn btn-success" type="submit">上传</button>
                <button class="btn btn-secondary" type="button" onclick="document.getElementById('addform').style.display='none'">取消</button>
            </form>
            <div id="up_result"></div>
        </div>
    </div>

    <!-- 编辑软件弹窗 -->
    <?php if (isset($_GET['edit'])):
        $eid = intval($_GET['edit']);
        $sw=$pdo->query("SELECT * FROM softwares WHERE id=$eid")->fetch();
    ?>
    <div class="modal" style="display:block;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.2)">
        <div class="card" style="margin:80px auto;width:500px;max-width:95%;">
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="op" value="edit">
                <input type="hidden" name="id" value="<?=$eid?>">
                <div class="form-group"><label>软件名称</label>
                    <input type="text" name="name" class="form-control" required value="<?=htmlspecialchars($sw['name'])?>">
                </div>
                <div class="form-group"><label>软件描述</label>
                    <textarea name="description" class="form-control"><?=htmlspecialchars($sw['description'])?></textarea>
                </div>
                <div class="form-group">
                    <label>分类</label>
                    <select name="category_id" class="form-control" id="edit_cat" required>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?=$c['id']?>" <?=$c['id']==$sw['category_id']?'selected':''?>><?=$c['name']?></option><?php endforeach;?>
                    </select>
                </div>
                <div class="form-group">
                    <label>型号</label>
                    <select name="model_id" class="form-control" id="edit_mod" required>
                        <?php foreach ($mdl_map[$sw['category_id']] as $mod): ?>
                        <option value="<?=$mod['id']?>" <?=$mod['id']==$sw['model_id']?'selected':''?>><?=$mod['name']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit">保存</button>
                <a href="softwares.php" class="btn btn-secondary">取消</a>
            </form>
        </div>
        </div>
    </div>
    <?php endif;?>
</div>

<script>
    // 分类选择动态加载型号
    const mdl_map = <?=json_encode($mdl_map)?>;
    function reloadModel(cat, selectId) {
        var mid = selectId||'f_model';
        var sel = document.getElementById(mid); sel.innerHTML='';
        var arr = mdl_map[cat]||[];
        arr.forEach(function(m){sel.innerHTML+='<option value="'+m.id+'">'+m.name+'</option>';});
    }
    if(document.getElementById('f_category')){
        document.getElementById('f_category').onchange = function(){reloadModel(this.value);};
        reloadModel(document.getElementById('f_category').value);  // 初始加载
    }

    // 上传进度条AJAX
    var form = document.getElementById('uploadForm');
    if(form){
        form.onsubmit = function(e){
            e.preventDefault();
            var formData = new FormData(form);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'softwares.php', true);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    var percent = Math.round(e.loaded * 100 / e.total);
                    var progbar = form.querySelector('.progress');
                    var bar = form.querySelector('.progress-bar');
                    progbar.style.display = 'block';
                    bar.style.width = percent + '%';
                    bar.innerText = percent + '%';
                }
            };
            xhr.onload = function() {
                if (xhr.status == 200) {
                    var resp = xhr.responseText;
                    form.querySelector('.progress-bar').innerText = '100%';
                    setTimeout(function(){
                        form.querySelector('.progress').style.display='none';
                        form.querySelector('.progress-bar').style.width = '0%';
                        form.querySelector('.progress-bar').innerText = '0%';
                    }, 900);
                    document.getElementById('up_result').innerHTML = resp;
                    if (resp.indexOf('成功') >= 0) {
                        form.reset();
                        reloadModel(document.getElementById('f_category').value);
                    }
                } else {
                    alert('上传失败，请重试');
                }
            };
            xhr.send(formData);
        };
    }
</script>
</body>
</html>