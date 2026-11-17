<?php
// Salvar em: src/uploads/ponto_add.php
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

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if ($nome === '' || $descricao === '' || mb_strlen($nome) > 100) {
    header('Location: ' . $voltar . 'ponto_erro');
    exit;
}

$arquivo = $_FILES['imagem'] ?? null;
if (!$arquivo || $arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] > 5 * 1024 * 1024) {
    header('Location: ' . $voltar . 'ponto_img');
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($arquivo['tmp_name']);
$extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if (!isset($extensoes[$mime])) {
    header('Location: ' . $voltar . 'ponto_img');
    exit;
}

$pasta = __DIR__ . '/pontos/';
if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
}

$nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensoes[$mime];
if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $nomeArquivo)) {
    header('Location: ' . $voltar . 'ponto_erro');
    exit;
}

$posicao = filter_input(INPUT_POST, 'posicao', FILTER_VALIDATE_INT);
if (!$posicao || $posicao < 1) {
    $maxPos = (int) $conexao->query("SELECT COALESCE(MAX(posicao), 0) FROM pontosturisticos")->fetchColumn();
    $posicao = $maxPos + 1;
}

$stmtExiste = $conexao->prepare("SELECT COUNT(*) FROM pontosturisticos WHERE posicao = :pos");
$stmtExiste->execute([':pos' => $posicao]);

if ($stmtExiste->fetchColumn() > 0) {
    $conexao->prepare("UPDATE pontosturisticos SET posicao = posicao + 1 WHERE posicao >= :pos ORDER BY posicao DESC")
        ->execute([':pos' => $posicao]);
}

$sql = $conexao->prepare("INSERT INTO pontosturisticos (nome, descricao, imagem, posicao) VALUES (:nome, :descricao, :imagem, :posicao)");
$sql->execute([
    ':nome'      => $nome,
    ':descricao' => $descricao,
    ':imagem'    => $nomeArquivo,
    ':posicao'   => $posicao
]);

header('Location: ' . $voltar . 'ponto_ok');
exit;
