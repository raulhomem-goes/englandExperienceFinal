
<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../dashboard.php");
    exit;
}
$csrfSessao = $_SESSION['csrf'] ?? '';
$csrfPost = $_POST['csrf'] ?? '';
if ($csrfSessao === '' || !is_string($csrfPost) || !hash_equals($csrfSessao, $csrfPost)) {
    header("Location: ../../dashboard.php?msg=surfista_csrf");
    exit;
}
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
$imagem = trim($_POST['imagemSurfista'] ?? '');
$esquema = strtolower(parse_url($imagem, PHP_URL_SCHEME) ?? '');
if (!$id || $imagem === '' || strlen($imagem) > 2048 || !filter_var($imagem, FILTER_VALIDATE_URL) || !in_array($esquema, ['http', 'https'], true)) {
    header("Location: ../../dashboard.php?msg=surfista_img_invalida");
    exit;
}
try {
    $sql = 'UPDATE public.surfistas SET "imagemSurfista" = :imagem WHERE id = :id';
    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        ':imagem' => $imagem,
        ':id' => $id
    ]);
    header("Location: ../../dashboard.php?msg=surfista_img_ok");
    exit;
} catch (PDOException $e) {
    error_log("Erro ao atualizar imagem do surfista: " . $e->getMessage());
    header("Location: ../../dashboard.php?msg=surfista_img_erro");
    exit;
}
