<?php
// 返回分类下所有型号数组
function get_models_by_category($pdo, $cat_id) {
    $stmt = $pdo->prepare("SELECT * FROM models WHERE category_id=? ORDER BY id");
    $stmt->execute([$cat_id]);
    return $stmt->fetchAll();
}

// 获取所有分类
function get_all_categories($pdo) {
    return $pdo->query("SELECT * FROM categories")->fetchAll();
}

// 安全输出
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}
?>