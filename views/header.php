<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo VIEW_PATH ?>css/custom.css">

    <!-- AOS -> sweet display on scrolldown https://michalsnik.github.io/aos/ -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    <title>LeXique</title>
</head>

<body>
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <section class="navbar-collapse collapse w-100 order-1 order-lg-0 dual-collapse2">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php?action=display&scope=words">Les mots</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/index.php?action=display&scope=lex">Les champs lexicaux</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/index.php?action=display&scope=per">Les périodes</a>
                </li>
            </ul>
        </section>

        <section class="mx-auto order-0">
            <a class="navbar-brand mx-auto" href="/index.php"><h1>leXique</h1></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
                <span class="navbar-toggler-icon"></span>
            </button>
        </section>

        <section class="navbar-collapse collapse w-100 order-3 dual-collapse2">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Menu
                    </a>
                    <section class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown2">

                        <?php if (!empty($_SESSION['admin']) && $_SESSION['admin']) { ?>
                            <a class="disabled">- Ajout au lexique -</a>
                            <a class="dropdown-item"
                               href="/index.php?action=adminData&scope=words&subAction=add">Mot</a>
                            <a class="disabled">- Information -</a>
                            <a class="dropdown-item" href="/index.php?action=adminList&scope=words">Mots</a>
                            <a class="dropdown-item" href="/index.php?action=adminList&scope=lex">Champs lexicaux</a>
                            <a class="dropdown-item" href="/index.php?action=adminList&scope=per">Périodes</a>
                            <a class="disabled">- Administration -</a>
                            <a class="dropdown-item" href="/index.php?action=adminMembers">Membres</a>

                            <section class="dropdown-divider"></section>

                        <?php }
                        if (empty($_SESSION['auth'])) { ?>
                            <a class="dropdown-item" href="/index.php?action=login">Se connecter</a>
                        <?php } else { ?>
                            <a class="dropdown-item" href="/index.php?action=logout">Se déconnecter</a>
                        <?php } ?>

                    </section>
                </li>
            </ul>
        </section>
    </nav>
</header>