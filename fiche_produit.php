<?php

require_once('inc/init.php');

if( !empty($_GET['id_produit'])){

    // Génération d'une fiche produit
    $result = execRequete("SELECT * FROM produit WHERE id_produit=:id_produit",array(
        'id_produit' => $_GET['id_produit']
        ));
    if( $result->rowCount() == 0 ) // pas de produit trouvé
    {
        header('location:'.URL);
        exit();
    }
    $produit = $result->fetch();
    $contenu .= '<div class="col-12">
        <h1 class="page-header text-center">'.$produit['titre'].'</h1>
        <div class="row">
            <div class="col-8">
                <img class="img-fluid" src="'.URL.'photo/'.$produit['photo'].'" alt="'.$produit['titre'].'">
            </div>
            <div class="col-4">
                <h3>Description</h3>
                <p>'.$produit['description'].'</p>
                <h3>Détails</h3>
                <ul>
                    <li>Catégorie : '.$produit['categorie'].'</li>
                    <li>Couleur : '.$produit['couleur'].'</li>
                    <li>Taille : '.$produit['taille'].'</li>
                </ul>
                <p class="lead">Prix : '.$produit['prix'].'€</p>';
// Mettre le produit au panier
if ( $produit['stock'] > 0){
    $contenu .= '<form method="post" action="panier.php">
        <input type="hidden" name="id_produit" value="'.$produit['id_produit'].'">
        <div class="form-row">
            <div class="form-group col-4">
                <select name="quantite" class="form-control">';
                for($i=1; $i<=$produit['stock'] && $i<=5 ;$i++){
                    $contenu .= '<option>'.$i.'</option>';
                }
    $contenu.= '</select>
            </div>
            <div class="form-group col-4">
                <input type="submit" name="ajout_panier" value="Ajouter au panier" class="btn btn-primary">
            </div>
        </div>
    </form>';
} 
else{
    $contenu .= '<p>Produit en cours de réapprovisionnement</p>';
}            

$contenu .= '   <div class="bottom text-center">
                    <a href="'.URL.'" class="btn btn-primary">Retour à la boutique</a>
                </div>
            </div>
        </div>
    </div>';

}
else{
    header('location:'.URL);
    exit();
}




require_once('inc/header.php');
?>
<div class="row">
    <?= $contenu ?>
</div>
<?php

if ( isset($_GET['statut_produit']) && $_GET['statut_produit']== 'ajoute'){
    ?>
    <div class="modal fade" id="maModale" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Le produit a bien été ajouté au panier !</h4>
                </div>
                <div class="modal-body">
                    <a class="btn btn-primary" href="<?= URL . 'panier.php' ?>">Voir le panier</a>
                    <a class="btn btn-primary" href="<?= URL ?>">Continuer ses achats</a>
                </div>
            </div>
        </div>
    </div>
    <?php
}


require_once('inc/footer.php');
