<?php

require_once('../inc/init.php');
// 1 . verifier qu'on est admin
if( !isAdmin() ){
    header('location:' . URL . 'connexion.php');
    exit();
}

// 3. Gestion du changement d'état


// 2. Afficher les commandes
$commandes = execRequete("SELECT *, c.id_commande as nbcmd , p.id_produit as idprod, m.id_membre as idmembre 
FROM commande c, details_commande d, produit p, membre m
WHERE d.id_commande = c.id_commande
AND d.id_produit = p.id_produit
AND c.id_membre = m.id_membre
ORDER BY c.id_membre, c.etat ASC, c.date_enregistrement DESC, c.id_commande");

if( $commandes->rowCount() > 0){
    $contenu .= '<table class="table table-bordered table-striped">';
    
    $oldusr = 0;
    $oldcmd = 0;
    while( $cmd = $commandes->fetch() ){

        // Entete du client à n'écrire qu'une fois
        if( $oldusr != $cmd['idmembre']){
            $civilite = ( $cmd['civilite'] == 'm') ? 'Monsieur' : 'Madame';
            $contenu .= '<tr>
                <th colspan="3"><i class="fas fa-user"></i> '.$civilite.' '.strtoupper($cmd['nom']).' '.ucfirst($cmd['prenom']).'</th>
                <th colspan="3"><i class="far fa-address-card"></i> '.$cmd['adresse'].' '.$cmd['code_postal'].' '.$cmd['ville'].'</th>
                <th colspan="3"><i class="far fa-envelope"></i> <a href="mailto:'.$cmd['email'].'">'.$cmd['email'].'</a></th>
            </tr>';
        }
        // objet date pour la reformater en mode FR
        $datecmd = new DateTime($cmd['date_enregistrement']);
        //  entete de commande à n'écrire qu'une seule fois par commande
        if ($oldcmd != $cmd['nbcmd']){
            $contenu .= '<tr class="thead-dark">
                <th>Commande n°'.$cmd['nbcmd'].'</th>
                <th colspan="2">Date : '.$datecmd->format('d/m/Y à H:i:s').'</th>
                <th colspan="3">
                    <form action="" method="post" class="form-inline">
                        <input type="hidden" name="id_commande" value="'.$cmd['nbcmd'].'">
                        <label for="etat">Etat</label>
                        <select name="newetat" class="form-control mx-3">
                            <option>en cours de traitement</option>
                            <option>envoyé</option>
                            <option>livré</option>
                        </select>
                        <input type="submit" value="valider" class="btn btn-primary mx-3">
                    </form>                
                </th>
                <th colspan="3">Montant : '.$cmd['montant'].' €</th>
            </tr>';
        }
        // Ligne de détail
        $contenu .= '<tr>
            <td>'.$cmd['reference'].'</td>
            <td>'.$cmd['titre'].'</td>
            <td>'.$cmd['description'].'</td>
            <td>Taille : '.$cmd['taille'].'</td>
            <td>'.$cmd['categorie'].'</td>
            <td><img src="'.URL . 'photo/' . $cmd['photo'].'" alt="'.$cmd['titre'].'" class="vignettecommande"></td>
            <td>'.$cmd['prix'].' €</td>
            <td>'.$cmd['quantite'].'</td>
            <td>'.$cmd['prix']*$cmd['quantite'].' €</td>
        </tr>';

        $oldusr = $cmd['idmembre'];
        $oldcmd = $cmd['nbcmd'];

    }
    $contenu .= '</table>';

}
else{
    $contenu .= '<div class="alert alert-info">Il n\'y a pas encore de commandes</div>';
}


require_once('../inc/header.php');
?>
<h2>Gestion des commandes</h2>
<?= $contenu ?>
<?php
require_once('../inc/footer.php');