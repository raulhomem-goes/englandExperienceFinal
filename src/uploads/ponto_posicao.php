<?php
session_start();
require_once('../../config/database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    header("Location: ../../files.php?msg=ponto_csrf");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$novaPosicao = filter_input(INPUT_POST, 'nova_posicao', FILTER_VALIDATE_INT);

if (!$id || !$novaPosicao || $novaPosicao < 1) {
    header("Location: ../../files.php?msg=ponto_pos_erro");
    exit;
}

try {
    $conexao->beginTransaction();

    // Busca a posição atual do item selecionado
    $stmtAtual = $conexao->prepare("SELECT posicao FROM pontosturisticos WHERE id = :id FOR UPDATE");
    $stmtAtual->execute([':id' => $id]);
    $itemAtual = $stmtAtual->fetch(PDO::FETCH_ASSOC);

    if (!$itemAtual) {
        $conexao->rollBack();
        header("Location: ../../files.php?msg=ponto_pos_erro");
        exit;
    }

    $posicaoAntiga = (int) $itemAtual['posicao'];

    if ($posicaoAntiga !== $novaPosicao) {
        // Verifica se já existe outro registro ocupando a nova posição
        $stmtDestino = $conexao->prepare("SELECT id FROM pontosturisticos WHERE posicao = :pos FOR UPDATE");
        $stmtDestino->execute([':pos' => $novaPosicao]);
        $itemDestino = $stmtDestino->fetch(PDO::FETCH_ASSOC);

        if ($itemDestino) {
            // Move o item atual para posição temporária 0 para não violar a UNIQUE KEY
            $conexao->prepare("UPDATE pontosturisticos SET posicao = 0 WHERE id = :id")
                    ->execute([':id' => $id]);

            // Move o item que estava na posição alvo para a posição antiga
            $conexao->prepare("UPDATE pontosturisticos SET posicao = :posAntiga WHERE id = :idDestino")
                    ->execute([':posAntiga' => $posicaoAntiga, ':idDestino' => $itemDestino['id']]);
        }

        // Coloca o item atual na nova posição desejada
        $conexao->prepare("UPDATE pontosturisticos SET posicao = :novaPos WHERE id = :id")
                ->execute([':novaPos' => $novaPosicao, ':id' => $id]);
    }

    $conexao->commit();
    header("Location: ../../files.php?msg=ponto_pos_ok");
    exit;

} catch (PDOException $e) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }
    header("Location: ../../files.php?msg=ponto_pos_erro");
    exit;
}