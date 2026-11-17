
<?php
session_start();
require_once('../../config/database.php');
$target_dir = __DIR__ . DIRECTORY_SEPARATOR;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método não permitido.');
}
if (empty($_SESSION['csrf']) || !isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    exit('Requisição inválida. Atualize a página e tente novamente.');
}
if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
    exit('Nenhum arquivo válido foi enviado.');
}
$arquivo = $_FILES['fileToUpload'];
if ($arquivo['size'] > 5 * 1024 * 1024) {
    exit('O tamanho máximo permitido é 5 MB.');
}
$info = getimagesize($arquivo['tmp_name']);
if ($info === false) {
    exit('O arquivo enviado não é uma imagem válida.');
}
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($arquivo['tmp_name']);
$formatos = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif'
];
if (!isset($formatos[$mime])) {
    exit('Formato não permitido. Utilize JPG, PNG ou GIF.');
}
$extensao = $formatos[$mime];
$novoNome = 'img_' . bin2hex(random_bytes(16)) . '.' . $extensao;
$caminho = $target_dir . $novoNome;
if (!move_uploaded_file($arquivo['tmp_name'], $caminho)) {
    exit('Não foi possível salvar a imagem. Verifique as permissões da pasta.');
}
try {
    $stmt = $conexao->prepare("INSERT INTO imagensenviadas (nome) VALUES (:nome)");
    $stmt->execute([':nome' => $novoNome]);
    header('Location: ../../files.php');
    exit;
} catch (PDOException $e) {
    if (is_file($caminho)) {
        unlink($caminho);
    }
    error_log($e->getMessage());
    exit('Erro ao registrar a imagem no banco de dados.');
}
