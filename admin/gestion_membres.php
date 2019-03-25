<?php

require_once('../inc/init.php');
// 1 . verifier qu'on est admin
if( !isAdmin() ){
    header('location:' . URL . 'connexion.php');
    exit();
}

// 3 . Changement de statut
if( isset($_GET['action']) && $_GET['action'] == 'changestatut' && isset($_GET['id_membre']) && $_GET['id_membre'] != $_SESSION['membre']['id_membre']){

    $result = execRequete("SELECT statut FROM membre WHERE id_membre=:id_membre",array(
        'id_membre' => $_GET['id_membre']
    ));
    if($result->rowCount() > 0 ){
        $membre = $result->fetch();
        $newstatut = ( $membre['statut'] == 0 ) ?  1 : 0;
        execRequete("UPDATE membre SET statut=:newstatut WHERE id_membre=:id_membre",array(
            'newstatut' => $newstatut,
            'id_membre' => $_GET['id_membre']
        ));
        header('location:'.URL.'admin/gestion_membres.php');
        exit();
    }
}


// 2 . affichage des membres
$result = execRequete("SELECT * FROM membre ORDER BY nom");
$contenu .= '<table class="table table-bordered table-striped"><tr>';
// entêtes
for($i=0; $i < $result->columnCount(); $i++){
    $colonne = $result->getColumnMeta($i);
    if( $colonne['name'] != 'mdp' ){
        $contenu .= '<th>'.ucfirst($colonne['name']).'</th>';
    }
  }
  $contenu .= '<th>Action</th></tr>';
// données
while($membre = $result->fetch()){
    $contenu .= '<tr>';
        foreach($membre as $indice => $valeur){
            if( $indice != 'mdp' ){
                if($indice == 'statut'){
                    $valeur = ($valeur == 1) ? 'Administrateur' : 'Membre';
                }
                $contenu .= '<td>'.$valeur.'</td>';
            }
        }
        if( $membre['id_membre'] != $_SESSION['membre']['id_membre']){
            $contenu .= '<td class="text-center"><a href="?action=changestatut&id_membre='.$membre['id_membre'].'"><i class="fas fa-user-edit"></i></a></td>';
        }
        else{
            $contenu .= '<td class="text-center">*</td>';
        }
    $contenu .= '</tr>';
}
$contenu .= '</table>';

require_once('../inc/header.php');
?>
<h2>Gestion des Membres</h2>
<?= $contenu ?>
<?php
require_once('../inc/footer.php');