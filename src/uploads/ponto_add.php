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
$caminho = $pasta . $nomeArquivo;
if (!move_uploaded_file($arquivo['tmp_name'], $caminho)) {
    header('Location: ' . $voltar . 'ponto_erro');
    exit;
}

try {
    $conexao->beginTransaction();

    $posicao = filter_input(INPUT_POST, 'posicao', FILTER_VALIDATE_INT);
    if (!$posicao || $posicao < 1) {
        $maxPos = (int) $conexao->query("SELECT COALESCE(MAX(posicao), 0) FROM pontosturisticos")->fetchColumn();
        $posicao = $maxPos + 1;
    }

    $stmtExiste = $conexao->prepare("SELECT COUNT(*) FROM pontosturisticos WHERE posicao = :pos");
    $stmtExiste->execute([':pos' => $posicao]);

    if ((int) $stmtExiste->fetchColumn() > 0) {
        // Postgres não aceita ORDER BY no UPDATE e confere a UNIQUE linha a linha.
        // Por isso o deslocamento é feito em duas etapas, passando por valores negativos:
        // 1) posicao vira -(posicao + 1)   2) posicao vira o valor positivo (posicao + 1)
        $conexao->prepare("UPDATE pontosturisticos SET posicao = -(posicao + 1) WHERE posicao >= :pos")
            ->execute([':pos' => $posicao]);
        $conexao->exec("UPDATE pontosturisticos SET posicao = -posicao WHERE posicao < 0");
    }

    $sql = $conexao->prepare("INSERT INTO pontosturisticos (nome, descricao, imagem, posicao) VALUES (:nome, :descricao, :imagem, :posicao)");
    $sql->execute([
        ':nome'      => $nome,
        ':descricao' => $descricao,
        ':imagem'    => $nomeArquivo,
        ':posicao'   => $posicao
    ]);

    $conexao->commit();
} catch (PDOException $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }
    if (is_file($caminho)) {
        unlink($caminho);
    }
    error_log($e->getMessage());
    header('Location: ' . $voltar . 'ponto_erro');
    exit;
}

header('Location: ' . $voltar . 'ponto_ok');
exit;
