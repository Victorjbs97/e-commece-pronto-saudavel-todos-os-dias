<header id="desktop">
<link rel="stylesheet" href="css/header.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap">

<!-- Começo do menu -->
<div id="menu">

<div id="logo_busca" >

<!-- Logo -->
<div id="logo"></div>

<!-- Barra de pesquisa -->
<div id="busca" >
    <input type="text" placeholder=" Busque aqui..." >
</div>

</div>

<!-- Icones -->
<nav id="icons">

    <ul id="lista_icons">

        <li>
            <a href=""> <div id="chefinho" ></div> </a>
        </li>

        <li>
            <a href=""> <div id="entrega" ></div> </a>
        </li>
        
        <li>
            <a href=""> <div id="compras" ></div> </a>
        </li>

        <li>
            <a href="/e-commece-pronto-saudavel-todos-os-dias/views/pages/auth/login.php"> <div id="usuario" ></div> </a>
        </li>

    </ul>

    

</nav>

<!-- Fim do menu -->
</div>

<!-- Navegador -->
<nav id="nav">

    <ul id="lista_nav">

        <li>Inicio</li>
        <li>Personal Chefe</li>
        <li>Marmitas</li>
        <li>Outros Produtos</li>
        <li>Quem Somos</li>

    </ul>

</nav>

</header>


<!-- Mobile -->


<header id="mobile">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

<div id="menu">

    <div id="logo_busca" >

        <!-- Logo -->
        <div id="logo"></div>

        <!-- Barra de pesquisa -->
        <div id="busca" >
            <input type="text" placeholder=" Busque aqui..." >
        </div>

    </div>

    <div id="hamburguer">

        <button class="hamburger-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" aria-label="Abrir menu">
            
            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 -960 960 960"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
        
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            
        <div class="offcanvas-header">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <!-- Itens do menu -->
        <div class="offcanvas-body">

            <ul class="lista_hamburguer">

                <li><a href="/e-commece-pronto-saudavel-todos-os-dias/views/pages/auth/login.php">Entrar</a></li>
                <li><a href="/e-commece-pronto-saudavel-todos-os-dias/views/pages/auth/register.php">Cadastrar</a></li>
                
            </ul>
            <hr>
            <ul class="lista_hamburguer">

                <li><a href="">Inicio</a></li>
                <li><a href="">Personal Chefe</a></li>
                <li><a href="">Entregas</a></li>
                <li><a href="">Carrinho de Compras</a></li>
                <li><a href="">Marmitas</a></li>
                <li><a href="">Outros Produtos</a></li>
                <li><a href="">Quem Somos</a></li>

            </ul>

        </div>

        </div>

    </div>        
  
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</header>