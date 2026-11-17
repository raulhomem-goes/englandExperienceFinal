<?php
require_once __DIR__ . '/config/database.php';
try {
    $sql = 'SELECT id, "imagemSurfista", nome, descricao, posicao FROM public.surfistas ORDER BY posicao ASC';
    $stmt = $conexao->query($sql);
    $surfistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar surfistas: " . $e->getMessage());
    $surfistas = [];
}

$spots = [
    ["Sennen Cove", 50.0789, -5.6990, "Oeste", "Praia de ondas variadas, ideal para diferentes níveis de surfe."],
    ["Gwenver", 50.0870, -5.7010, "Oeste", "Praia mais isolada, com ondas fortes e mar aberto."],
    ["Porthmeor", 50.2145, -5.4850, "St Ives", "Praia famosa pelo surfe e pela vista de St Ives."],
    ["Carbis Bay", 50.2070, -5.4650, "St Ives", "Baía abrigada, com águas tranquilas em boas condições."],
    ["Hayle Rivermouth", 50.2010, -5.4270, "St Ives", "Encontro do rio Hayle com o mar, com ondas potentes."],
    ["Godrevy / Gwithian", 50.2350, -5.3980, "St Ives", "Extensa faixa de areia com vários pontos de surfe."],
    ["Portreath", 50.2610, -5.2900, "Norte", "Vila costeira conhecida por suas ondas rápidas."],
    ["Porthtowan", 50.2880, -5.2480, "Norte", "Praia popular entre os surfistas da região."],
    ["Chapel Porth", 50.3040, -5.2280, "Norte", "Praia cercada por falésias e paisagens naturais."],
    ["Droskyn / Main Beach", 50.3420, -5.1570, "Perranporth", "Praia extensa, com ondas e areia dourada."],
    ["Penhale", 50.3650, -5.1620, "Perranporth", "Trecho de praia cercado por dunas naturais."],
    ["Holywell Bay", 50.3930, -5.1520, "Newquay", "Praia ampla, com dunas e ondas consistentes."],
    ["Crantock", 50.4075, -5.1030, "Newquay", "Praia de areia próxima ao estuário do rio Gannel."],
    ["Fistral South", 50.4100, -5.0990, "Newquay", "Setor sul de Fistral, conhecido pelas ondas."],
    ["Fistral North", 50.4160, -5.1000, "Newquay", "Área famosa por receber competições de surfe."],
    ["Little Fistral", 50.4190, -5.0980, "Newquay", "Pequena praia rochosa próxima a Fistral."],
    ["The Cribber", 50.4150, -5.1260, "Newquay", "Pico de ondas grandes, indicado para experientes."],
    ["Towan", 50.4200, -5.0850, "Newquay", "Praia urbana próxima ao centro de Newquay."],
    ["Tolcarne", 50.4220, -5.0780, "Newquay", "Praia popular, cercada por falésias."],
    ["Porth Beach", 50.4240, -5.0610, "Newquay", "Praia ampla e familiar ao norte de Newquay."]
];
$cities = [
    ["Newquay", 50.4130, -5.0760],
    ["St Ives", 50.2110, -5.4800],
    ["Penzance", 50.1180, -5.5370],
    ["Camborne", 50.2130, -5.2930],
    ["Truro", 50.2630, -5.0510],
    ["Falmouth", 50.1540, -5.0700],
    ["Perranporth", 50.3430, -5.1550]
];
$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.typekit.net/ghm2efo.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/surfe.css">
    <link rel="stylesheet" href="./src/output.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <title>Surfe</title>
    <style>
        .surf-marker {
            width: 18px;
            height: 18px;
            background: #e11d48;
            border: 3px solid #fff;
            border-radius: 9999px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .4);
            transition: transform .15s;
        }

        .surf-marker.active {
            transform: scale(1.5);
            background: #0f766e;
        }

        .city-label {
            background: transparent;
            border: 0;
            box-shadow: none;
            font-weight: 700;
            font-size: 12px;
            color: #1f2937;
            text-shadow: 0 0 3px #fff, 0 0 3px #fff;
            white-space: nowrap;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="px-4 lg:px-6 py-2.5">
            <div class="fixed right-10 flex flex-wrap justify-end items-center w-full max-w-screen-xl z-10">
                <div class="flex items-center lg:order-2">
                    <button data-collapse-toggle="mobile-menu-2" type="button" class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="mobile-menu-2" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1">
                    <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                        <li>
                            <a href="./index.php" class="block py-2 pr-4 pl-3 bg-gradient-to-r from-blue-950 to-blue-950 bg-no-repeat bg-[length:0%_100%] !text-gray-700 transition-all duration-700 hover:bg-[length:100%_100%] hover:!text-white">
                                <span class="transition-colors duration-700 group-hover:text-white">Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block py-2 pr-4 pl-3 bg-gradient-to-r from-blue-950 to-blue-950 bg-no-repeat bg-[length:0%_100%] !text-gray-700 transition-all duration-700 hover:bg-[length:100%_100%] hover:!text-white">
                                <span class="transition-colors duration-700 group-hover:text-white">Surfe</span>
                            </a>
                        </li>
                        <li>
                            <a href="./capsulaTempo.php" class="block py-2 pr-4 pl-3 bg-gradient-to-r from-blue-950 to-blue-950 bg-no-repeat bg-[length:0%_100%] !text-gray-700 transition-all duration-700 hover:bg-[length:100%_100%] hover:!text-white">
                                <span class="transition-colors duration-700 group-hover:text-white">Capsula do Tempo</span>
                            </a>
                        </li>
                        <li>
                            <a href="./identidadeVisual.php" class="block py-2 pr-4 pl-3 bg-gradient-to-r from-blue-950 to-blue-950 bg-no-repeat bg-[length:0%_100%] !text-gray-700 transition-all duration-700 hover:bg-[length:100%_100%] hover:!text-white">
                                <span class="transition-colors duration-700 group-hover:text-white">Identidade Visual</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="fixed inset-x-0 top-0 h-[55svh] md:inset-0 md:h-auto -z-10">
        <img src="./assets/img/surfeBG.jpg" class="w-full h-full object-cover object-[35%_center] md:object-center" alt="">
    </div>
    <div class="relative min-h-[55svh] md:min-h-[100svh] overflow-hidden">
        <h1 class="font-impact text-white text-left leading-[0.9] md:leading-none text-[10rem] md:text-[13rem] p-4 md:p-10 mt-8 md:mt-20 md:ml-10 emptyText">
            SURFE
        </h1>
        <h1 class="font-impact text-white text-left leading-[0.9] md:leading-none text-[7rem] md:text-[10rem] p-4 md:p-10 md:ml-10 -mt-16">
            na inglaterra
        </h1>
    </div>
    <?php
    $layouts = [
        [
            'grid' => 'md:col-span-7 md:col-start-1 h-[350px] md:h-[450px]',
            'bg' => 'bg-[#24445a]'
        ],
        [
            'grid' => 'md:col-span-5 md:col-start-8 md:mt-14 h-[300px] md:h-[380px]',
            'bg' => 'bg-[#31566a]'
        ],
        [
            'grid' => 'md:col-span-5 md:col-start-1 md:mt-[18px] h-[300px] md:h-[350px]',
            'bg' => 'bg-[#806d4a]'
        ],
        [
            'grid' => 'md:col-span-7 md:col-start-6 md:-mt-8 h-[350px] md:h-[400px]',
            'bg' => 'bg-[#31566a]'
        ]
    ];
    ?>
    <section id="surfistas" class="bg-[#0c203a] px-5 py-10 md:px-10 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-0">
            <?php if (!empty($surfistas)): ?>
                <?php foreach ($surfistas as $index => $surfista): ?>
                    <?php $layout = $layouts[$index % count($layouts)]; ?>
                    <div class="group relative col-span-1 <?= $layout['grid'] ?> overflow-hidden rounded-sm <?= $layout['bg'] ?>">
                        <?php if (!empty($surfista['imagemSurfista'])): ?>
                            <img
                                src="<?= htmlspecialchars($surfista['imagemSurfista'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($surfista['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#07182c] via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 md:p-8">
                            <p class="text-xs tracking-[0.2em] uppercase text-gray-300">Surfista local</p>
                            <h2 class="font-impact text-5xl md:text-7xl text-white uppercase leading-none">
                                <?= htmlspecialchars($surfista['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </h2>
                            <p class="max-w-xs text-sm text-white mt-2">
                                <?= htmlspecialchars($surfista['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-span-12 text-center text-white">
                    Nenhum surfista cadastrado no momento.
                </p>
            <?php endif; ?>
        </div>
    </section>
    <section id="mapa" class="bg-[#0c203a] px-5 py-12 md:px-10 md:py-16">
        <div class="max-w-screen-xl mx-auto">
            <p class="font-impact text-white text-left text-7xl sm:text-8xl md:text-[8rem] p-4 md:p-10 mt-10 md:mt-20 md:ml-10 mb-10 cursor-pointer flex flex-col">
                <span class="text-lg tracking-[.3em] uppercase text-blue-200">Descubra os principais</span>
                <span class="bg-gradient-to-r from-white to-white bg-no-repeat bg-[length:0%_100%] !text-white transition-all duration-700 hover:bg-[length:100%_100%] hover:!text-blue-950 hover:emptyText">
                    <span class="transition-colors duration-700 group-hover:text-blue-950 group-hover:emptyText">Pontos de Surfe</span>
                </span>
            </p>
            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-4">
                <!-- MAPA -->
                <div id="surf-map" class="relative z-0 w-full h-[450px] md:h-[620px] rounded-sm overflow-hidden border border-white/10"></div>
                <!-- LISTA DE PRAIAS -->
                <aside class="bg-white rounded-sm border border-slate-200 flex flex-col max-h-[620px] overflow-hidden">
                    <div class="p-4 border-b border-slate-100">
                        <p class="text-xs tracking-[.2em] uppercase text-slate-400 mb-3">Localizar praia</p>
                        <input id="search" type="search" placeholder="Buscar praia..." class="w-full rounded-sm border border-slate-200 px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <ul id="spot-list" class="overflow-y-auto divide-y divide-slate-100 flex-1"></ul>
                </aside>
            </div>
            <footer class="flex flex-wrap justify-between gap-2 mt-5 text-xs text-blue-200">
                <span>
                    <span class="inline-block w-2 h-2 rounded-full bg-rose-600 mr-1"></span>
                    Pontos de surfe (<?= count($spots) ?>)
                </span>
                <span>Coordenadas aproximadas · Mapa © OpenStreetMap</span>
            </footer>
        </div>
    </section>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const spots = <?= json_encode($spots, $jsonFlags) ?>;
        const cities = <?= json_encode($cities, $jsonFlags) ?>;
        const map = L.map('surf-map', {
            scrollWheelZoom: false
        }).setView([50.30, -5.25], 9);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
        // Ativa o zoom com scroll somente depois de clicar no mapa
        map.on('click', () => map.scrollWheelZoom.enable());
        map.on('mouseout', () => map.scrollWheelZoom.disable());
        // Escapa caracteres especiais para evitar problemas no HTML dos pop-ups
        const esc = value => String(value).replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [character]));
        // Marcadores das cidades
        cities.forEach(city => {
            L.marker([city[1], city[2]], {
                interactive: false,
                icon: L.divIcon({
                    className: 'city-label',
                    html: esc(city[0]),
                    iconSize: [100, 16],
                    iconAnchor: [0, 8]
                })
            }).addTo(map);
        });
        // Ícone dos pontos de surfe
        const icon = () => L.divIcon({
            className: '',
            html: '<div class="surf-marker"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });
        const markers = [];
        const list = document.getElementById('spot-list');
        // Criação dos marcadores e da lista de praias
        spots.forEach((spot, index) => {
            const [name, lat, lng, region, desc] = spot;
            const marker = L.marker([lat, lng], {
                icon: icon()
            }).addTo(map);
            marker.bindTooltip(name, {
                direction: 'top',
                offset: [0, -8]
            });
            marker.bindPopup(`
                <div class="min-w-[180px]">
                    <span class="text-[10px] uppercase tracking-widest text-teal-600">${esc(region)}</span>
                    <h3 class="text-base font-bold text-teal-950 mb-1">${esc(name)}</h3>
                    <p class="text-sm text-slate-600 leading-snug">${esc(desc)}</p>
                    <a class="inline-block mt-2 text-xs font-semibold text-rose-600 hover:underline" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}">Abrir no Google Maps →</a>
                </div>
            `);
            marker.on('click', () => setActive(index, false));
            markers.push(marker);
            const li = document.createElement('li');
            li.innerHTML = `
                <button type="button" data-i="${index}" class="w-full text-left px-4 py-3 hover:bg-teal-50 transition">
                    <span class="block text-sm font-semibold text-slate-800">${esc(name)}</span>
                    <span class="block text-xs text-slate-400">${esc(region)}</span>
                </button>
            `;
            li.dataset.name = name.toLowerCase();
            li.querySelector('button').addEventListener('click', () => setActive(index, true));
            list.appendChild(li);
        });
        let current = null;
        // Seleção de uma praia
        function setActive(index, fly) {
            if (current !== null) {
                markers[current].getElement()?.querySelector('.surf-marker')?.classList.remove('active');
            }
            current = index;
            markers[index].getElement()?.querySelector('.surf-marker')?.classList.add('active');
            if (fly) {
                map.flyTo(markers[index].getLatLng(), 13, {
                    duration: .8
                });
                markers[index].openPopup();
            }
            document.querySelectorAll('#spot-list button').forEach(button => {
                button.classList.toggle('bg-teal-50', Number(button.dataset.i) === index);
            });
        }
        // Busca de praias
        document.getElementById('search').addEventListener('input', event => {
            const query = event.target.value.trim().toLowerCase();
            list.querySelectorAll('li').forEach(li => {
                li.classList.toggle('hidden', query && !li.dataset.name.includes(query));
            });
        });
        // Ajusta o enquadramento para mostrar todos os pontos
        map.fitBounds(
            L.latLngBounds(spots.map(spot => [spot[1], spot[2]])).pad(0.1)
        );
    </script>
</body>

</html>