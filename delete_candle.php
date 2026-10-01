<?php include 'config.php';
if (isset($_GET['id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $stmt = $pdo->prepare("DELETE FROM velas_artigos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
header("Location: dashboard.php"); ?>