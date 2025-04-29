<?php
require_once 'db.php';

$login = !empty($_SESSION['user_id']);

if (isset($_GET['q']) && trim($_GET['q'])!=='') {
    // 有关键词搜索，全局模糊（仅审核通过的）
    $kw = trim($_GET['q']);
    $stmt = $pdo->prepare(
        "SELECT s.*, m.name modelname FROM softwares s 
         LEFT JOIN models m ON s.model_id=m.id
         WHERE (s.name LIKE ? OR s.description LIKE ?) AND s.is_audited=1
         ORDER BY s.uploaded_at DESC"
    );
    $stmt->execute(['%'.$kw.'%', '%'.$kw.'%']);
} else {
    $cid = intval($_GET['catid']??0);
    $stmt = $pdo->prepare(
        "SELECT s.*, m.name modelname FROM softwares s 
         LEFT JOIN models m ON s.model_id=m.id 
         WHERE s.category_id=? AND s.is_audited=1
         ORDER BY s.uploaded_at DESC"
    );
    $stmt->execute([$cid]);
}
$softs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php if(!$softs):?>
    <div class="alert alert-warning">暂无匹配的软件数据。</div>
<?php else:?>
    <div class="row">
    <?php foreach($softs as $sw):?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?=htmlspecialchars($sw['name'])?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        型号：<?=htmlspecialchars($sw['modelname'])?>&nbsp; 更新时间：<?=htmlspecialchars($sw['uploaded_at'])?>
                    </h6>
                    <p class="card-text" style="min-height:38px"><?=htmlspecialchars($sw['description'])?></p>
                    <?php if($login): ?>
                        <a class="btn btn-success" href="download.php?id=<?=$sw['id']?>">下载</a>
                    <?php else: ?>
                        <a class="btn btn-warning" href="login.php" onclick="alert('请先登录/注册，再下载。')">登录后下载</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach;?>
    </div>
<?php endif;?>