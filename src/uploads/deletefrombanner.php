
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
$stmt = $conexao->prepare("DELETE FROM imagemPrincipal WHERE id = ?");
$stmt->execute([$id]);
$basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
header('Location: ../../files.php');
exit;
