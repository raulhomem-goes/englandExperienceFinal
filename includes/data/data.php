
<?php
$imagens = [];
$imagemPrincipal = [];
$pontosTuristicos = [];
$sql = $conexao->prepare("SELECT id, nome FROM imagensenviadas ORDER BY id DESC");
$sql->execute();
$imagens = $sql->fetchAll(PDO::FETCH_ASSOC);
$sql = $conexao->prepare("SELECT id, nome, descricao, imagem, posicao FROM pontosTuristicos ORDER BY posicao ASC, id ASC");
$sql->execute();
$pontosTuristicos = $sql->fetchAll(PDO::FETCH_ASSOC);
$sql = $conexao->prepare("SELECT id, nome FROM imagemPrincipal ORDER BY id DESC");
$sql->execute();
$imagemPrincipal = $sql->fetchAll(PDO::FETCH_ASSOC);
