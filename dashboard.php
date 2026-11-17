<?php
session_start();
require_once __DIR__ . '/config/database.php';
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$surfistas = [];
try {
    $stmtSurfistas = $conexao->query('SELECT id, "imagemSurfista", nome, descricao, posicao FROM public.surfistas ORDER BY posicao ASC, id ASC');
    $surfistas = $stmtSurfistas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar surfistas: " . $e->getMessage());
}
$mensagens = [
    'surfista_img_ok' => ['Imagem do surfista atualizada com sucesso.', 'text-green-400'],
    'surfista_img_erro' => ['Não foi possível atualizar a imagem.', 'text-rose-500'],
    'surfista_img_invalida' => ['Informe um endereço de imagem válido.', 'text-rose-500'],
    'surfista_csrf' => ['Requisição inválida. Recarregue a página.', 'text-rose-500']
];
$msgAtual = $mensagens[$_GET['msg'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/output.css">
    <title>Dashboard</title>
</head>

<body class="min-h-screen bg-black text-white">
    <main class="relative min-h-screen w-full flex flex-col items-center overflow-hidden py-16">
        <img src="./src/img/emfrentepraia.jpg" class="fixed inset-0 w-full h-full object-cover" alt="">
        <div class="fixed inset-0 bg-black/75 backdrop-blur-sm"></div>
        <a href="./index.php" class="absolute top-6 left-6 z-20 font-impact text-2xl text-white hover:text-rose-600 transition-colors">
            INGLATERRA
        </a>
        <div class="relative z-10 w-full max-w-7xl px-5">
            <div class="max-w-xl mx-auto bg-black/60 backdrop-blur-md border border-white/20 p-8 md:p-12">
                <div class="mb-10 text-center">
                    <h1 class="font-impact text-6xl md:text-7xl text-white mb-3">DASHBOARD</h1>
                    <p class="text-gray-300 text-sm md:text-base">Publique novas imagens para o seu site.</p>
                    <div class="w-16 h-1 bg-rose-600 mx-auto mt-5"></div>
                </div>
                <form action="./src/uploads/upload.php" method="post" enctype="multipart/form-data" class="flex flex-col gap-6">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                    <div>
                        <label for="fileToUpload" class="block text-sm font-medium text-white mb-3">Selecione uma imagem para fazer upload:</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" class="block w-full text-sm text-gray-300 border border-white/30 bg-white/10 cursor-pointer focus:outline-none file:mr-4 file:border-0 file:bg-rose-600 file:px-5 file:py-3 file:text-sm file:font-bold file:uppercase file:tracking-wider file:text-white hover:file:bg-rose-700 transition-colors" required>
                    </div>
                    <button type="submit" class="group relative flex items-center justify-center overflow-hidden bg-rose-600 px-6 py-4 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:-translate-y-1 hover:bg-rose-700">
                        <span class="relative z-10 transition-transform duration-300 group-hover:scale-105">Postar imagem</span>
                        <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700 group-hover:translate-x-full"></span>
                    </button>
                    <a href="./files.php" class="group flex items-center justify-center border border-white/40 bg-white/10 px-6 py-4 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:-translate-y-1 hover:bg-white hover:text-black">
                        Ver imagens
                    </a>
                    <a href="index.php" class="text-center text-sm text-gray-400 hover:text-white transition-colors mt-2">
                        Voltar ao início
                    </a>
                </form>
            </div>
            <section class="mt-12 bg-black/60 backdrop-blur-md border border-white/20 p-6 md:p-10 rounded-sm">
                <div class="flex items-center justify-between mb-8 border-b border-white/20 pb-5">
                    <div>
                        <h2 class="font-impact text-3xl md:text-4xl text-white">IMAGENS DOS SURFISTAS</h2>
                        <p class="text-gray-400 text-sm mt-2">Gerencie as fotografias exibidas na página de surfe.</p>
                    </div>
                    <span class="text-rose-500 font-impact text-3xl">01</span>
                </div>
                <?php if ($msgAtual) { ?>
                    <p class="text-center text-sm mb-6 <?= $msgAtual[1] ?>">
                        <?= htmlspecialchars($msgAtual[0], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php } ?>
                <?php if (!empty($surfistas)) { ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($surfistas as $surfista) {
                            $imagemAtual = trim($surfista['imagemSurfista'] ?? '');
                        ?>
                            <div class="group flex flex-col border border-white/10 bg-white/5 overflow-hidden">
                                <?php if ($imagemAtual !== '') { ?>
                                    <img src="<?= htmlspecialchars($imagemAtual, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($surfista['nome'], ENT_QUOTES, 'UTF-8') ?>" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-105">
                                <?php } else { ?>
                                    <div class="w-full h-64 flex items-center justify-center bg-white/10 text-gray-400 text-sm uppercase tracking-wider">
                                        Sem imagem cadastrada
                                    </div>
                                <?php } ?>
                                <div class="p-5 flex flex-col gap-4">
                                    <div>
                                        <h3 class="font-impact text-2xl text-white uppercase">
                                            <?= htmlspecialchars($surfista['nome'], ENT_QUOTES, 'UTF-8') ?>
                                        </h3>
                                        <p class="text-gray-400 text-sm mt-2 line-clamp-2">
                                            <?= htmlspecialchars($surfista['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>
                                    <form action="./src/uploads/surfista_imagem.php" method="POST" class="flex flex-col gap-3">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="id" value="<?= (int) $surfista['id'] ?>">
                                        <div class="flex flex-col gap-2">
                                            <label class="text-xs font-bold uppercase tracking-wider text-gray-300">
                                                Endereço da imagem
                                            </label>
                                            <input type="url" name="imagemSurfista" value="<?= htmlspecialchars($imagemAtual, ENT_QUOTES, 'UTF-8') ?>" maxlength="2048" required placeholder="https://..." class="w-full bg-white/10 border border-white/30 px-4 py-3 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-rose-600">
                                        </div>
                                        <button type="submit" class="flex items-center justify-center bg-rose-600 px-5 py-3 text-sm font-bold uppercase tracking-wider text-white transition-all duration-300 hover:bg-rose-700 hover:-translate-y-1">
                                            Atualizar imagem
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-400 text-center py-10">
                        Nenhum surfista cadastrado no banco de dados.
                    </p>
                <?php } ?>
            </section>
        </div>
    </main>
</body>

</html>