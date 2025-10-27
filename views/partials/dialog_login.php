

<dialog id="loginDialog">

    <iframe id="dialogIframe" src="\e-commece-pronto-saudavel-todos-os-dias\views\pages\auth\login.php" style="width:100%; height:100%; border:none;"></iframe>

    <button type="button" id="closeDialog">Fechar</button>

</dialog>

<script>

    const dialog = document.getElementById('loginDialog');
    const openBtn = document.getElementById('openDialog');
    const closeBtn = document.getElementById('closeDialog');

    openBtn.addEventListener('click', () => dialog.showModal());
    closeBtn.addEventListener('click', () => dialog.close());

</script>

<!-- icone pra lista em Nav -->

<!-- 

    <li>
            <a href="#" id="openDialog"><div id="usuario" ></div></a>
    </li>

-->

<!-- Pra colocar no index -->

<!-- 

            Dialog de Login
     require_once "../views/partials/dialog_login.php"; 

-->