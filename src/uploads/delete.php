
<?php
session_start();
require_once('../../config/database.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método não permitido.');
}
if (empty($_SESSION['csrf']) || !isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    exit('Requisição inválida. Atualize a página e tente novamente.');
}
$id = filter_input(INPUT_POST, 'delete', FILTER_VALIDATE_INT);
if (!$id) {
    exit('ID inválido.');
}
$stmt = $conexao->prepare("SELECT nome FROM imagensenviadas WHERE id = ?");
$stmt->execute([$id]);
$arquivo = $stmt->fetchColumn();
if (!$arquivo) {
    exit('Imagem não encontrada.');
}
$nomeSeguro = basename($arquivo);
$caminho = __DIR__ . DIRECTORY_SEPARATOR . $nomeSeguro;
if (is_file($caminho) && !unlink($caminho)) {
    error_log("Falha ao excluir imagem: " . $caminho);
    exit('Não foi possível excluir o arquivo. Verifique as permissões da pasta.');
}
try {
    $conexao->beginTransaction();
    $stmt = $conexao->prepare("DELETE FROM imagemPrincipal WHERE nome = ?");
    $stmt->execute([$arquivo]);
    $stmt = $conexao->prepare("DELETE FROM imagensenviadas WHERE id = ?");
    $stmt->execute([$id]);
    $conexao->commit();
    $basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
    header('Location: ../../files.php');
    exit;
} catch (PDOException $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }
    error_log($e->getMessage());
    exit('Erro ao excluir a imagem do banco de dados.');
}
