<?php
require_once('../inc/init.php');

// 1 . verifier qu'on est admin
if( !isAdmin() ){
    header('location:' . URL . 'connexion.php');
    exit();
}

// 6 . Suppression d'un produit
if( isset($_GET['action']) && $_GET['action'] == 'suppr' && isset($_GET['id_produit'])){

    $result = execRequete("SELECT photo FROM produit WHERE id_produit=:id_produit",array('id_produit' => $_GET['id_produit']));
    if ( $result->rowCount() == 1 ){
        $produit = $result->fetch();
        $fichier_a_supp = $_SERVER['DOCUMENT_ROOT'] . URL . 'photo/' . $produit['photo'];
        if( !empty($produit['photo']) && file_exists($fichier_a_supp) ){
            // suppression de la photo
            unlink($fichier_a_supp);
        }
        // suppression en BDD
        execRequete("DELETE FROM produit WHERE id_produit=:id_produit",array('id_produit' => $_GET['id_produit']));
        $contenu .= '<div class="alert alert-success">Le produit a été supprimé</div>';
        $_GET['action'] = 'affichage';

    }

}


// 4 . Enregistrement d'un produit
if( !empty($_POST) ){
    
    $photo_bdd = $_POST['photo_courante'] ?? '';
    $errors = array();

    // on s'occupe de copier la photo sur le serveur
    if(  !empty($_FILES['photo']['name']) ){
        $photo_bdd = $_POST['reference'] . '-' . $_FILES['photo']['name'];
        // dossier physique
        $dossier_photo = $_SERVER['DOCUMENT_ROOT'] . URL . 'photo/';
        $ext_auto = array('image/jpeg','image/png','image/gif');
        if( in_array($_FILES['photo']['type'], $ext_auto)){
            move_uploaded_file($_FILES['photo']['tmp_name'], $dossier_photo . $photo_bdd);
        }
        else
        {
            $errors[] = 'La photo n\'a pas été enregistrée. Formats acceptés : jpg, png et gif';
        }
    }
    $nb_champs_vides = 0;
    foreach($_POST as $value){
        if( trim($value) == '' ) $nb_champs_vides++;
    }
    if ( $nb_champs_vides > 0 ){
        $errors[] = 'Merci de remplir ' . $nb_champs_vides . ' champ(s) manquant(s)';
    }
    // Controles à imaginer
    if( !empty($errors)){
        $contenu .= '<div class="alert alert-danger">'.implode('<br>',$errors).'</div>';
    }
    else
    {
        extract($_POST);
        // on procède à l'enregistrement du produit
        execRequete("REPLACE INTO produit VALUES (:id_produit,:reference,:categorie,:titre,:description,:couleur,:taille,:public,:photo,:prix,:stock)",array(
            'id_produit'    =>  $id_produit,
            'reference'     => $reference,
            'categorie'     => $categorie,
            'titre'         => $titre,
            'description'   => $description,
            'couleur'       => $couleur,
            'taille'        => $taille,
            'public'        => $public,
            'photo'         => $photo_bdd,
            'prix'          => $prix,
            'stock'         => $stock
        ));
        $contenu .= '<div class="alert alert-success">Le produit a été enregistré</div>';
        $_GET['action'] = 'affichage'; // forcer l'onglet affichage
        }
}


// 2 . Onglets Affichage et Ajout/Modif

$active_affichage= (!isset($_GET['action']) || ( isset($_GET['action']) && $_GET['action'] == 'affichage') ) ? 'active' : '';
$active_ajout= ( isset($_GET['action']) && ( $_GET['action'] == 'ajout' || $_GET['action'] == 'modification' )) ? 'active' : '';

$contenu .= '<ul class="nav nav-tabs nav-justified">
                <li class="nav-item">
                    <a class="nav-link '.$active_affichage.'" href="?action=affichage">Affichage des produits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link '.$active_ajout.'" href="?action=ajout">Ajout d\'un produit</a>
                </li>
</ul>';


// 5. Affichage des produits
if( !isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'affichage') ){

    $result= execRequete("SELECT * FROM produit");
    if( $result->rowCount()  == 0){
        $contenu .= '<div class="alert alert-warning">Il n\'y a pas encore de produits enregistrés</div>';
    }
    else
    {
        $contenu .= '<p>Il y a '.$result->rowCount().' produit(s) dans la boutique</p>
        <table class="table table-bordered table-striped">
            <tr>';
        // entetes
        for($i=0; $i < $result->columnCount(); $i++){
            $colonne = $result->getColumnMeta($i);
            $contenu .= '<th>'.ucfirst($colonne['name']).'</th>';
        }    
        
        $contenu .= '<th colspan="2">Actions</th></tr>';
        // données
        while( $ligne = $result->fetch() ){

            $contenu .='<tr>';
                foreach($ligne as $index => $information){
                    if($index == 'photo'){
                        $information = '<img class="img-fluid" src="'.URL . 'photo/' . $information .'" alt="'.$ligne['titre'].'">';
                    }                    
                    $contenu .= '<td>'.$information.'</td>';
                }     
                 $contenu .= '<td><a href="?action=modification&id_produit='.$ligne['id_produit'].'"><i class="fas fa-pencil-alt"></i></a></td>
                              <td><a class="confsup" href="?action=suppr&id_produit='.$ligne['id_produit'].'"><i class="fas fa-trash-alt"></i></a></td>';     
            $contenu .= '</tr>';
        }


        $contenu .='</table>';
    }

}


require_once('../inc/header.php');
echo $contenu;
// 3 .Formulaire d'ajout/modif de produit
if ( isset($_GET['action']) && ( $_GET['action'] == 'ajout' || $_GET['action'] == 'modification') ):
    // 7 . Cas du chargement du produit en modif
    if ( !empty($_GET['id_produit']) ){
        $result = execRequete("SELECT * FROM produit WHERE id_produit=:id_produit",array('id_produit' => $_GET['id_produit']));
        if($result->rowCount() == 1) $produit_courant = $result->fetch();
    }

    ?>
    <h3>Formulaire d'ajout/modification de produit</h3>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="id_produit" value="<?= $_POST['id_produit'] ?? $produit_courant['id_produit'] ?? 0 ?>">

        <div class="form-row">
            <div class="form-group col-6">
                <label for="reference">Référence</label>
                <input type="text" name="reference" id="reference" class="form-control" value="<?= $_POST['reference'] ?? $produit_courant['reference'] ?? '' ?>">
            </div>
            <div class="form-group col-6">
                <label for="categorie">Catégorie</label>
                <input type="text" name="categorie" id="categorie" class="form-control" value="<?= $_POST['categorie'] ?? $produit_courant['categorie'] ?? '' ?>">
            </div>
        </div>
        <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" name="titre" id="titre" class="form-control" value="<?= $_POST['titre'] ?? $produit_courant['titre'] ?? '' ?>">
        </div>
        <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"><?= $_POST['description'] ?? $produit_courant['description'] ?? '' ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group col">
                <label for="couleur">Couleur</label>
                <input type="text" name="couleur" id="couleur" class="form-control" value="<?= $_POST['couleur'] ?? $produit_courant['couleur'] ?? '' ?>">
            </div>
            <div class="form-group col">
                <label for="taille">Taille</label>
                <select name="taille" id="taille" class="form-control">
                    <?php
                        $tailles = array('S','M','L','XL');
                        foreach($tailles as $value):
                            ?>
                            <option value="<?= $value ?>" <?= ( (isset($_POST['taille']) && $_POST['taille'] == $value) || ( isset($produit_courant['taille']) && $produit_courant['taille'] == $value) ) ? 'selected' : '' ?>><?= $value ?></option>
                            <?php
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="form-group col">
                <label for="public">Public</label>
                <select name="public" id="public" class="form-control">
                    <?php
                        $publics = array(
                            'm' => 'Homme',
                            'f' => 'Femme',
                            'mixte' => 'Mixte'
                        );
                        foreach($publics as $key => $value):
                            ?>
                            <option value="<?= $key ?>"  <?= ( (isset($_POST['public']) && $_POST['public'] == $key ) || ( isset($produit_courant['public']) && $produit_courant['public'] == $key ) ) ? 'selected' : '' ?>><?= $value ?></option>
                            <?php
                        endforeach;
                    ?> 
                </select>
            </div>        
        </div>
        <div class="form-group">
            <label for="photo">Photo</label>
            <input type="file" name="photo" id="photo" class="form-control">
            <?php
                if ( !empty($produit_courant['photo']) ){
                    ?>
                    <em>Vous pouvez uploader une nouvelle photo</em>
                    <input type="hidden" name="photo_courante" value="<?= $produit_courant['photo'] ?>">
                    <img src="<?= URL . 'photo/' . $produit_courant['photo'] ?>" alt="<?= $produit_courant['titre'] ?>" class="vignette">
                    <?php
                }
            ?>
        </div>
        <div class="form-row">
            <div class="form-group col-6">
                <label for="prix">Prix</label>
                <div class="input-group md-3">
                    <input type="number" name="prix" id="prix" class="form-control" value="<?= $_POST['prix'] ?? $produit_courant['prix'] ?? '' ?>">
                    <div class="input-group-append">
                        <span class="input-group-text">€</span>
                    </div>
                </div>
            </div>
            <div class="form-group col-6">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" value="<?= $_POST['stock'] ?? $produit_courant['stock'] ?? '' ?>">
            </div>
        </div>
        <input type="submit" value="Enregistrer" class="btn btn-primary">
    </form>
    <?php
endif;

require_once('../inc/footer.php');