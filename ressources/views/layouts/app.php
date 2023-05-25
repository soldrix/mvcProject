<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>app</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script src="../js/all.min.js" defer></script>
    <script src="../js/jquery.min.js" defer></script>
    <script src="../js/bootstrap.bundle.min.min.js" defer></script>
    <meta name="csrf-token" content="<?=\App\lscore\Application::$app->csrfToken->generateToken()?>">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">logo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/contact">Contact</a>
                </li>

            </ul>
        </div>
        <ul class="navbar-nav d-flex">

            <li class="dropdown ">

                <a id="navbarDropdown" class="text-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user fa-xl"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-s" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item color-white" href="/profil" role="button">Profil</a>
                    <a class="dropdown-item color-white" data-bs-toggle="modal" data-bs-target="#logoutModal" href="#">
                        <span>Déconnexion</span>
                    </a>

                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    {{content}}
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Êtes-vous sûr de vouloir vous déconnecter ?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-pink" onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">Continuer</button>
                <form id="logout-form" action="/logout" method="POST" class="d-none">
                    <?= \App\lscore\Application::$app->csrfToken->loadToken() ?>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
