<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.typekit.net/ghm2efo.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/surfe.css">
    <link href="./src/output.css" rel="stylesheet">
    <title>Surfe</title>
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
        <h1 class="font-impact text-white text-left leading-[0.9] md:leading-none
                        text-[10rem] md:text-[13rem]
                        p-4 md:p-10 mt-8 md:mt-20 md:ml-10 emptyText">
            SURFE
        </h1>
        <h1 class="font-impact text-white text-left leading-[0.9] md:leading-none text-[7rem] md:text-[10rem] p-4 md:p-10 md:ml-10 -mt-16">
            na inglaterra
        </h1>
    </div>
    <section id="regioes" class="bg-[#0c203a] px-5 py-10 md:px-10 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-0">
            <!-- CARD 1 -->
            <div class="group relative col-span-1 md:col-span-7 md:col-start-1 md:row-start-1 h-[350px] md:h-[450px] overflow-hidden rounded-sm bg-[#24445a]">
                <div class="absolute inset-0 bg-gradient-to-t from-[#07182c] via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 md:p-8">
                    <p class="text-xs tracking-[0.2em] uppercase text-gray-300">Categoria</p>
                    <h2 class="font-impact text-5xl md:text-7xl text-white uppercase leading-none">Título</h2>
                    <p class="max-w-xs text-sm text-white mt-2">Descrição genérica para apresentar este conteúdo.</p>
                </div>
            </div>
            <!-- CARD 2 -->
            <div class="group relative col-span-1 md:col-span-5 md:col-start-8 md:row-start-1 md:mt-14 h-[300px] md:h-[380px] overflow-hidden rounded-sm bg-[#31566a]">
                <div class="absolute inset-0 bg-gradient-to-t from-[#07182c] via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 md:p-8">
                    <p class="text-xs tracking-[0.2em] uppercase text-gray-300">Categoria</p>
                    <h2 class="font-impact text-5xl md:text-7xl text-white uppercase leading-none">Título</h2>
                    <p class="max-w-xs text-sm text-white mt-2">Descrição genérica para apresentar este conteúdo.</p>
                </div>
            </div>
            <!-- CARD 3 -->
            <div class="group relative col-span-1 md:col-span-5 md:col-start-1 md:row-start-2 md:-mt-[-18px] h-[300px] md:h-[350px] overflow-hidden rounded-sm bg-[#806d4a]">
                <div class="absolute inset-0 bg-gradient-to-t from-[#07182c] via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 md:p-8">
                    <p class="text-xs tracking-[0.2em] uppercase text-gray-300">Categoria</p>
                    <h2 class="font-impact text-5xl md:text-7xl text-white uppercase leading-none">Título</h2>
                    <p class="max-w-xs text-sm text-white mt-2">Descrição genérica para apresentar este conteúdo.</p>
                </div>
            </div>
            <!-- CARD 4 -->
            <div class="group relative col-span-1 md:col-span-7 md:col-start-6 md:row-start-2 md:-mt-8 h-[350px] md:h-[400px] overflow-hidden rounded-sm bg-[#31566a]">
                <div class="absolute inset-0 bg-gradient-to-t from-[#07182c] via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 md:p-8">
                    <p class="text-xs tracking-[0.2em] uppercase text-gray-300">Categoria</p>
                    <h2 class="font-impact text-5xl md:text-7xl text-white uppercase leading-none">Título</h2>
                    <p class="max-w-xs text-sm text-white mt-2">Descrição genérica para apresentar este conteúdo.</p>
                </div>
            </div>
        </div>
    </section>
</body>

</html>