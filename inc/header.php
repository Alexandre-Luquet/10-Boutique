<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Awes'Mode</title>

    <!-- feuilles de styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= URL . 'inc/css/style.css' ?>">

    <!-- scripts JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="<?= URL . 'inc/js/functions.js' ?>"></script>

</head>
<body>
    <header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <a class="navbar-brand" href="<?= URL ?>">AWES'MODE</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">

          <li class="nav-item">
            <a class="nav-link" href="<?= URL ?>"> <i class="fas fa-home"></i> Boutique <span class="sr-only">(current)</span></a>
          </li>
          <?php

          if( !isConnected() ) :
            ?>
            <li class="nav-item">
            <a class="nav-link" href="<?= URL . 'inscription.php' ?>"><i class="fab fa-wpforms"></i> Inscription</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="<?= URL . 'connexion.php' ?>"><i class="fas fa-sign-in-alt"></i> Connexion</a>
            </li>
            <?php
          else :
            ?>
            <li class="nav-item">
            <a class="nav-link" href="<?= URL . 'compte.php' ?>"><i class="fas fa-user-circle"></i> Compte</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="<?= URL . 'commandes.php' ?>"><i class="fas fa-clipboard-list"></i> Commandes</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="<?= URL . 'connexion.php?action=deconnexion' ?>"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </li>
            <?php
          endif;

          if ( isAdmin() ) :
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="menuadmin" role="button" data-toggle="dropdown"><i class="fas fa-tools"></i> Admin</a>
                <div class="dropdown-menu" aria-labelledby="menuadmin">      
                        <a class="dropdown-item" href="<?= URL . 'admin/gestion_produits.php' ?>"> Gestion des produits</a>
                        <a class="dropdown-item" href="<?= URL . 'admin/gestion_membres.php' ?>"> Gestion des membres</a>
                        <a class="dropdown-item" href="<?= URL . 'admin/gestion_commandes.php' ?>"> Gestion des commandes</a>
                </div>
            </li>
            <?php
          endif;
          ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= URL . 'panier.php' ?>"><i class="fas fa-shopping-cart"></i> <?= nbArticles() ?></a>
          </li>

        </ul>
        <form class="form-inline mt-2 mt-md-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>
    </header>
    <main class="container maincontainer">
    
    