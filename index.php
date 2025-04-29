<?php
require_once 'db.php';
// 分类列表
$categories = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$first_cid = $categories ? $categories[0]['id'] : 0;
$login = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>软件下载中心</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .category-active { background: #007bff !important; color:#fff;}
    </style>
</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">软件下载平台</a>
    <div class="navbar-nav ml-auto">
        <?php if($login): ?>
            <span class="nav-link">你好，<?=htmlspecialchars($_SESSION['username']);?></span>
            <a class="nav-link" href="logout.php">退出</a>
        <?php else:?>
            <a class="nav-link" href="login.php">登录</a>
            <a class="nav-link" href="register.php">注册</a>
        <?php endif;?>
    </div>
</nav>
<div class="container-fluid mt-3">
    <div class="row">
        <!-- 左侧分类 -->
        <div class="col-md-3 sidebar">
            <h6><strong>软件分类</strong></h6>
            <ul class="list-group mb-3" id="catList">
                <?php foreach ($categories as $cat): ?>
                <li class="list-group-item category-li <?=$cat['id']==$first_cid?'category-active':''?>" 
                    data-cid="<?=$cat['id']?>" style="cursor:pointer;">
                    <?=htmlspecialchars($cat['name'])?>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
        <!-- 右侧主内容 -->
        <div class="col-md-9">
            <!-- 搜索区域 -->
            <form class="form-inline mb-3" onsubmit="event.preventDefault(); submitSearch();">
                <input id="kw" class="form-control mr-2" style="width:240px;" type="text" name="q" placeholder="搜索软件名称/描述">
                <button class="btn btn-primary" type="submit">搜索</button>
                <button class="btn btn-link" type="button" onclick="resetSearch();" style="display:none;" id="btnReset">清空</button>
            </form>
            <h4 class="mb-3" id="main-title">软件下载列表</h4>
            <div id="soft-list">
                <div class="text-center text-muted">加载中...</div>
            </div>
        </div>
    </div>
</div>

<script>
// 分类切换与ajax加载
var currCat = <?=$first_cid?>;
function loadSoftList(catid) {
    document.getElementById('main-title').innerText = '软件下载列表';
    document.getElementById('btnReset').style.display = 'none';
    document.getElementById('kw').value = '';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax_softwares.php?catid='+catid, true);
    xhr.onreadystatechange = function() {
        if(xhr.readyState==4 && xhr.status==200) {
            document.getElementById('soft-list').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
var listItems = document.querySelectorAll('.category-li');
listItems.forEach(function(li){
    li.onclick = function(){
        listItems.forEach(function(el){el.classList.remove('category-active');});
        this.classList.add('category-active');
        currCat = this.getAttribute('data-cid');
        loadSoftList(currCat);
    }
});

// 页面初次加载默认显示第一个分类
loadSoftList(currCat);

// 全局搜索
function submitSearch(){
    var kw = document.getElementById('kw').value.trim();
    if(kw.length==0) { resetSearch(); return; }
    document.getElementById('main-title').innerText = '“'+kw+'” 的搜索结果';
    document.getElementById('btnReset').style.display = '';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax_softwares.php?q='+encodeURIComponent(kw), true);
    xhr.onreadystatechange = function() {
        if(xhr.readyState==4 && xhr.status==200) {
            document.getElementById('soft-list').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
// 清空搜索，恢复分类
function resetSearch(){
    document.getElementById('kw').value = '';
    document.getElementById('main-title').innerText = '软件下载列表';
    document.getElementById('btnReset').style.display = 'none';
    loadSoftList(currCat);
}
</script>
</body>
</html>