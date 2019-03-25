<?php


$pdo->query("SELECT * FROM employes WHERE nom='laborde' AND prenom='Jean-Pierre' ");

$result = $pdo->prepare("SELECT * FROM employes WHERE nom=:nom AND prenom=:prenom' ");
$result->execute(array(
    'nom' => 'Laborde',
    'prenom' => 'Jean-Pierre'
));

