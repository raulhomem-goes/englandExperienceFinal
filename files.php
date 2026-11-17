<?php
session_start();
require_once('./config/database.php');
require_once('./includes/data/data.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Pontos turísticos (tabela própria, independente da imagem principal)
$pontos = [];
try {
    $stmtPontos = $conexao->query("SELECT id, nome, descricao, imagem, posicao FROM pontosturisticos ORDER BY posicao ASC, id ASC");
    $pontos = $stmtPontos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pontos = [];
}

// Mensagens de retorno dos scripts de adicionar/deletar ponto
$mensagens = [
    'ponto_ok'       => ['Ponto turístico adicionado com sucesso.', 'text-green-400'],
    'ponto_del'      => ['Ponto turístico removido.', 'text-green-400'],
    'ponto_pos_ok'   => ['Posição atualizada com sucesso.', 'text-green-400'],
    'ponto_pos_erro' => ['Essa posição já está em uso ou é inválida.', 'text-rose-500'],
    'ponto_erro'     => ['Não foi possível salvar. Verifique os campos.', 'text-rose-500'],
    'ponto_img'      => ['Imagem inválida. Use JPG, PNG ou WEBP de até 5 MB.', 'text-rose-500'],
    'ponto_csrf'     => ['Requisição inválida. Recarregue a página e tente de novo.', 'text-rose-500'],
];
$msgKey = $_GET['msg'] ?? '';
$msgAtual = $mensagens[$msgKey] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/output.css">
    <title>Configurar</title>
</head>

<body class="min-h-screen bg-black text-white">
    <main class="relative min-h-screen w-full flex flex-col items-center justify-center overflow-hidden py-16">
        <img src="./src/img/emfrentepraia.jpg" class="fixed inset-0 w-full h-full object-cover" alt="">
        <div class="fixed inset-0 bg-black/75 backdrop-blur-sm"></div>
        <a href="./index.php" class="absolute top-6 left-6 z-20 font-impact text-2xl text-white hover:text-rose-600 transition-colors">
            INGLATERRA
        </a>
        <div class="relative z-10 w-full max-w-7xl px-5">
            <div class="mb-12 text-center">
                <h1 class="font-impact text-6xl md:text-8xl text-white mb-3">CONFIGURAÇÕES</h1>
                <p class="text-gray-300 text-sm md:text-base tracking-wide">Gerencie as imagens e o conteúdo do seu site.</p>
                <div class="w-20 h-1 bg-rose-600 mx-auto mt-5"></div>
            </div>

            <?php if ($msgAtual) { ?>
                <p class="text-center text-sm mb-8 <?= $msgAtual[1] ?>"><?= htmlspecialchars($msgAtual[0], ENT_QUOTES, 'UTF-8') ?></p>
            <?php } ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <section class="bg-black/60 backdrop-blur-md border border-white/20 p-6 md:p-10 rounded-sm">
                    <div class="flex items-center justify-between mb-8 border-b border-white/20 pb-5">
                        <div>
                            <h2 class="font-impact text-3xl md:text-4xl text-white">IMAGENS DO BANCO</h2>
                            <p class="text-gray-400 text-sm mt-2">Imagens disponíveis para publicação.</p>
                        </div>
                        <span class="text-rose-500 font-impact text-3xl">01</span>
                    </div>
                    <?php if (!empty($imagens)) {
                        foreach ($imagens as $imagem) { ?>
                            <div class="flex flex-col sm:flex-row items-center gap-5 border-b border-white/10 py-6 last:border-0">
                                <img src="./src/uploads/<?= htmlspecialchars($imagem['nome'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($imagem['nome'], ENT_QUOTES, 'UTF-8') ?>" class="w-full sm:w-2/5 h-48 object-cover rounded-sm">
                                <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                                    <form action="./src/uploads/delete.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta imagem? Ela também será removida do banner, caso esteja sendo utilizada.');">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" name="delete" value="<?= (int) $imagem['id'] ?>" class="group relative flex items-center justify-center overflow-hidden bg-rose-600 px-5 py-3 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-rose-700 hover:-translate-y-1">
                                            <span class="relative z-10">Deletar</span>
                                        </button>
                                    </form>
                                    <form action="./src/uploads/tobanner.php" method="POST">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" name="adicionar" value="<?= (int) $imagem['id'] ?>" class="group relative flex items-center justify-center overflow-hidden border border-white/40 bg-white/10 px-5 py-3 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-white hover:text-black hover:-translate-y-1">
                                            <span class="relative z-10">Adicionar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <p class="text-gray-400 text-center py-10">Nenhuma imagem no banco de dados.</p>
                    <?php } ?>
                </section>
                <section class="bg-black/60 backdrop-blur-md border border-white/20 p-6 md:p-10 rounded-sm h-fit">
                    <div class="flex items-center justify-between mb-8 border-b border-white/20 pb-5">
                        <div>
                            <h2 class="font-impact text-3xl md:text-4xl text-white">IMAGENS DO BANNER</h2>
                            <p class="text-gray-400 text-sm mt-2">Imagens atualmente em destaque.</p>
                        </div>
                        <span class="text-rose-500 font-impact text-3xl">02</span>
                    </div>
                    <?php if (!empty($imagemPrincipal)) {
                        foreach ($imagemPrincipal as $principal) { ?>
                            <div class="flex flex-col sm:flex-row items-center gap-5 border-b border-white/10 py-6 last:border-0">
                                <img src="./src/uploads/<?= htmlspecialchars($principal['nome'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($principal['nome'], ENT_QUOTES, 'UTF-8') ?>" class="w-full sm:w-2/5 h-48 object-cover rounded-sm">
                                <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                                    <form action="./src/uploads/deletefrombanner.php" method="POST">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" name="delete" value="<?= (int) $principal['id'] ?>" class="group relative flex items-center justify-center overflow-hidden bg-rose-600 px-5 py-3 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-rose-700 hover:-translate-y-1">
                                            <span class="relative z-10">Tirar do banner</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <p class="text-gray-400 text-center py-10">Nenhuma imagem no banner.</p>
                    <?php } ?>
                </section>
            </div>

            <!-- NOVA SECTION: PONTOS TURÍSTICOS (não mexe no banner/imagem principal) -->
            <!-- SECTION: PONTOS TURÍSTICOS -->
            <section class="mt-8 bg-black/60 backdrop-blur-md border border-white/20 p-6 md:p-10 rounded-sm">
                <div class="flex items-center justify-between mb-8 border-b border-white/20 pb-5">
                    <div>
                        <h2 class="font-impact text-3xl md:text-4xl text-white">PONTOS TURÍSTICOS</h2>
                        <p class="text-gray-400 text-sm mt-2">Imagens exibidas na galeria de pontos turísticos do site.</p>
                    </div>
                    <span class="text-rose-500 font-impact text-3xl">03</span>
                </div>

                <!-- Formulário de Adição com Input de Posição -->
                <form action="./src/uploads/ponto_add.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-10">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="flex flex-col gap-2 md:col-span-5">
                        <label for="ponto-nome" class="text-xs font-bold uppercase tracking-wider text-gray-300">Nome</label>
                        <input id="ponto-nome" type="text" name="nome" maxlength="100" required class="bg-white/10 border border-white/30 px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-rose-600" placeholder="Ex: Stonehenge">
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label for="ponto-posicao" class="text-xs font-bold uppercase tracking-wider text-gray-300">Posição</label>
                        <input id="ponto-posicao" type="number" name="posicao" min="1" value="<?= count($pontos) + 1 ?>" required class="bg-white/10 border border-white/30 px-4 py-3 text-white focus:outline-none focus:border-rose-600">
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-5">
                        <label for="ponto-imagem" class="text-xs font-bold uppercase tracking-wider text-gray-300">Imagem (JPG, PNG ou WEBP, até 5 MB)</label>
                        <input id="ponto-imagem" type="file" name="imagem" accept="image/jpeg,image/png,image/webp" required class="bg-white/10 border border-white/30 px-4 py-2.5 text-gray-300 file:mr-4 file:border-0 file:bg-rose-600 file:px-4 file:py-1.5 file:text-xs file:font-bold file:uppercase file:text-white">
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-12">
                        <label for="ponto-descricao" class="text-xs font-bold uppercase tracking-wider text-gray-300">Descrição</label>
                        <textarea id="ponto-descricao" name="descricao" rows="3" required class="bg-white/10 border border-white/30 px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-rose-600" placeholder="Texto que aparece no modal do ponto turístico"></textarea>
                    </div>

                    <div class="md:col-span-12">
                        <button type="submit" class="flex items-center justify-center bg-rose-600 px-8 py-4 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-rose-700 hover:-translate-y-1">
                            Adicionar ponto turístico
                        </button>
                    </div>
                </form>

                <?php if (!empty($pontos)) {
                    $totalPontos = count($pontos);
                ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($pontos as $ponto) { ?>
                            <div class="flex flex-col border border-white/10 bg-white/5 relative">
                                <span class="absolute top-3 left-3 z-10 bg-black/80 border border-white/20 px-3 py-1 font-impact text-lg text-rose-500">
                                    #<?= (int) ($ponto['posicao'] ?? 0) ?>
                                </span>

                                <img src="./src/uploads/pontos/<?= htmlspecialchars($ponto['imagem'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($ponto['nome'], ENT_QUOTES, 'UTF-8') ?>" class="w-full h-48 object-cover">
                                <div class="p-4 flex flex-col gap-3 grow">
                                    <h3 class="font-impact text-2xl"><?= htmlspecialchars($ponto['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="text-gray-400 text-sm line-clamp-3"><?= htmlspecialchars($ponto['descricao'], ENT_QUOTES, 'UTF-8') ?></p>

                                    <div class="mt-auto pt-3 border-t border-white/10 flex items-center justify-between gap-3">
                                        <form action="./src/uploads/ponto_posicao.php" method="POST" class="flex items-center gap-2">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="id" value="<?= (int) $ponto['id'] ?>">
                                            <label for="pos-<?= (int) $ponto['id'] ?>" class="text-xs font-bold uppercase tracking-wider text-gray-400">Posição:</label>
                                            <select id="pos-<?= (int) $ponto['id'] ?>" name="nova_posicao" onchange="this.form.submit()" class="bg-black/80 border border-white/30 px-3 py-2 text-sm text-white focus:outline-none focus:border-rose-600 cursor-pointer">
                                                <?php for ($i = 1; $i <= $totalPontos; $i++) { ?>
                                                    <option value="<?= $i ?>" <?= ((int) ($ponto['posicao'] ?? 0) === $i) ? 'selected' : '' ?>>
                                                        <?= $i ?>º
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </form>

                                        <form action="./src/uploads/ponto_delete.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este ponto turístico?');">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" name="delete" value="<?= (int) $ponto['id'] ?>" class="flex items-center justify-center bg-rose-600 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-rose-700">
                                                Deletar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-400 text-center py-10">Nenhum ponto turístico cadastrado. O site exibe os pontos padrão enquanto a lista estiver vazia.</p>
                <?php } ?>
            </section>

            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10">
                <a href="dashboard.php" class="group flex items-center justify-center bg-rose-600 px-8 py-4 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-rose-700 hover:-translate-y-1">
                    Publicar imagem
                </a>
                <a href="index.php" class="group flex items-center justify-center border border-white/40 bg-white/10 px-8 py-4 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-white hover:text-black hover:-translate-y-1">
                    Voltar ao início
                </a>
            </div>
        </div>
    </main>
</body>

</html>