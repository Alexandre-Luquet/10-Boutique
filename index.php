<?php

require_once('inc/init.php');

// 1. afficher les catégories

$categories = execRequete("SELECT DISTINCT categorie FROM produit ORDER BY categorie");

$activeall = ( !isset($_GET['categ']) || (isset($_GET['categ']) && $_GET['categ'] == '*')  ) ? 'active' : '';

$contenu_gauche .= '<p class="lead pt-3">Catégories</p>
            <div class="list-group">
               <a class="list-group-item '.$activeall.'" href="?categ=*">Toutes</a>';

while($cat = $categories->fetch() ){
    $active = ( isset($_GET['categ']) && $_GET['categ'] == $cat['categorie']) ? 'active' : '';
    $contenu_gauche .= '<a class="list-group-item '.$active.'" href="?categ='.$cat['categorie'].'">'.ucfirst($cat['categorie']).'</a>';
}

$contenu_gauche .='</div>';

// 2. afficher les produits en tenant compte d'un eventuel choix de catégorie
$whereclause = '';
$arg = array();

if ( isset($_GET['categ']) && $_GET['categ'] != '*')
{
    $whereclause = ' WHERE categorie=:categorie';
    $arg['categorie'] = $_GET['categ'];
}

$produits = execRequete("SELECT * FROM produit $whereclause",$arg);
$contenu_droite .= '<div class="row">';
while($prd = $produits->fetch())
{
    $contenu_droite .= '<div class="col-sm-6 col-md-4 p-1">
                            <div class="border">
                                <div class="thumbnail">
                                    <a href="fiche_produit.php?id_produit='.$prd['id_produit'].'">
                                    <img src="'.URL.'photo/'.$prd['photo'].'" alt="'.$prd['titre'].'" class="img-fluid">
                                    </a>
                                </div>
                                <div class="caption mx-2">
                                    <h4 class="float-right">'.$prd['prix'].'€</h4>
                                    <h4>
                                        <a href="fiche_produit.php?id_produit='.$prd['id_produit'].'">'.$prd['titre'].'</a>
                                    </h4>
                                </div>
                            </div>
                        </div>';
}
$contenu_droite .= '</div>';


require_once('inc/header.php');
?>

<div class="row">
    <div class="col-3">
        <?= $contenu_gauche ?>
    </div>
    <div class="col-9">
        <?= $contenu_droite ?>
    </div>
</div>

<?php
require_once('inc/footer.php');
