<?php
// Salvar em: src/uploads/ponto_delete.php
session_start();
require_once __DIR__ . '/../../config/database.php';

$voltar = '../../files.php?msg=';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    header('Location: ' . $voltar . 'ponto_csrf');
    exit;
}

$id = (int) ($_POST['delete'] ?? 0);

$sql = $conexao->prepare("SELECT imagem FROM pontosturisticos WHERE id = :id");
$sql->execute([':id' => $id]);
$ponto = $sql->fetch(PDO::FETCH_ASSOC);

if ($ponto) {
    $caminho = __DIR__ . '/pontos/' . basename($ponto['imagem']);
    if (is_file($caminho)) {
        unlink($caminho);
    }
    $del = $conexao->prepare("DELETE FROM pontosturisticos WHERE id = :id");
    $del->execute([':id' => $id]);
}

header('Location: ' . $voltar . 'ponto_del');
exit;
