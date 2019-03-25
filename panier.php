<?php

require_once('inc/init.php');

// Ajout d'un produit dans le panier
if( isset($_POST['ajout_panier']) ){
    $result = execRequete("SELECT * FROM produit WHERE id_produit=:id_produit",array('id_produit' => $_POST['id_produit']));
    if ( $result->rowCount() > 0 ){
        $produit = $result->fetch();
        addPanier($_POST['id_produit'],$_POST['quantite'],$produit['prix']);
        header('location:' . URL . 'fiche_produit.php?id_produit=' . $_POST['id_produit'] . '&statut_produit=ajoute');
        exit();
    }
}

// vider le panier
if ( isset($_GET['action']) && $_GET['action']=='vider'){
    unset($_SESSION['panier']);
}
// supprimer une ligne du panier
if ( isset($_GET['action']) && $_GET['action']=='suppr' && isset($_GET['id_produit'])){
    retraitDuPanier($_GET['id_produit']);
}

// validation du panier ( panier => commande )
if ( isset($_GET['action']) && $_GET['action'] == 'valider'){

    $feu_rouge = 0;
    // controles du panier avant commande
    for($i=0 ; $i<count($_SESSION['panier']['id_produit']); $i++){
        $result = execRequete("SELECT * FROM produit WHERE id_produit=:id_produit",array(
            'id_produit' => $_SESSION['panier']['id_produit'][$i]
        ));
        $produit = $result->fetch();
        if($_SESSION['panier']['quantite'][$i] > 5){
            $feu_rouge = 1;
        }
        if ( $produit['stock'] < $_SESSION['panier']['quantite'][$i] ){
            $feu_rouge = 1;
        }
        if( $_SESSION['panier']['prix'][$i] != $produit['prix']){
            $feu_rouge = 1;
        }
    }

    if ($feu_rouge == 0){

        // je peux procéder à la commande
        $id_membre = $_SESSION['membre']['id_membre'];
        $montant_total = montantTotal();
        execRequete("INSERT INTO commande VALUES (NULL,:id_membre,:montant,NOW(), 'en cours de traitement')",array(
            'id_membre' => $id_membre,
            'montant' => $montant_total
        ));
        $id_commande = $pdo->lastInsertId();

        // on boucle sur le panier pour insérer chaque article dans details_commande
        for($i=0; $i < count($_SESSION['panier']['id_produit']); $i++){
            $id_produit = $_SESSION['panier']['id_produit'][$i];
            $quantite = $_SESSION['panier']['quantite'][$i];
            $prix = $_SESSION['panier']['prix'][$i];
            
            // on alimente la table
            execRequete("INSERT INTO details_commande VALUES (NULL,:id_commande,:id_produit,:quantite,:prix)",array(
                'id_commande' => $id_commande,
                'id_produit' => $id_produit,
                'quantite' => $quantite,
                'prix' => $prix
            ));
            // on décremente le stock
            execRequete("UPDATE produit SET stock = stock - :quantite WHERE id_produit=:id_produit",array(
                'quantite' => $quantite,
                'id_produit' => $id_produit
            ));
        }
        // detruire le panier
        unset($_SESSION['panier']);
        header('location:' . URL . 'commandes.php');
        exit();
    } 
    else
    {
        $contenu .= '<div class="alert alert-danger">La commande n\'a pas été validé en raison de modifications concernant le stock ou les prix des produits de votre panier. Merci de valider à nouveau votre panier après vérification</div>';
    }
}

require_once('inc/header.php');
echo $contenu;
?>
<h2>Voici votre panier</h2>
<?php
    if ( empty($_SESSION['panier']['id_produit']) ){
    ?>
        <div class="alert alert-info">Votre panier est vide :( </div>
    <?php   
    }
    else
    {
    ?>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Référence</th>
            <th>Titre</th>
            <th>Quantité</th>
            <th>Prix Unitaire</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php
            // controle et réécriture eventuelle du panier
            for($i=0; $i < count($_SESSION['panier']['id_produit']) ; $i++){

                $result = execRequete("SELECT * FROM produit WHERE id_produit=:id_produit",array('id_produit' => $_SESSION['panier']['id_produit'][$i]));
                $message='';
                $produit = $result->fetch();

                if( $_SESSION['panier']['quantite'][$i] > 5 ){
                    $_SESSION['panier']['quantite'][$i] = 5;
                }
                if( $produit['stock'] <  $_SESSION['panier']['quantite'][$i] ){
                    $_SESSION['panier']['quantite'][$i] =  $produit['stock'];
                    $message .= '<div class="alert alert-info">La quantité a été réajustée en fonction du stock et dans la limite de 5 maximum par article</div>';
                }
                if( $_SESSION['panier']['prix'][$i] != $produit['prix'] ){
                    $_SESSION['panier']['prix'][$i]  = $produit['prix'];
                    $message .= '<div class="alert alert-info">Le prix a été réactualisé</div>';
                }
                ?>
                <tr>
                    <td><?= $produit['reference'] ?></td>
                    <td><?= $produit['titre'] . $message ?></td>
                    <td><?= $_SESSION['panier']['quantite'][$i] ?></td>
                    <td><?= $_SESSION['panier']['prix'][$i]  ?> €</td>
                    <td><?= $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i] ?> €</td>
                    <td><a href="?action=suppr&id_produit=<?= $produit['id_produit'] ?>"><i class="fas fa-trash-alt"></i></a></td>
                </tr>
                <?php
            }
        ?>
        <tr class="bg-info text-light">
            <td colspan="4" class="text-right">Total</td>
            <td colspan="2"><?= montantTotal() ?> €</td>
        </tr>
        <?php
            if ( isConnected()){
                ?>
                <tr>
                    <td colspan="6" class="text-center">
                        <a href="?action=valider" class="btn btn-primary">Valider le panier</a>
                    </td>
                </tr>
                <?php
            }
            else{
                ?>
                <tr>
                    <td colspan="6" class="text-center">
                    Veuillez vous <a href="<?= URL . 'connexion.php'?>">connecter</a> ou vous <a href="<?= URL . 'inscription.php' ?>">inscrire</a> afin de valider votre panier
                    </td>
                </tr>
                <?php
            }
        ?>
        <tr>
            <td colspan="6" class="text-center">
                <a href="?action=vider" class="btn btn-warning">Vider le panier</a>
            </td>
        </tr>
    </table>
    <?php
    }
require_once('inc/footer.php');
