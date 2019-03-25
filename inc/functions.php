<?php

// tester si un membre est  connecté
function isConnected(){
    if( isset($_SESSION['membre']) ){
        return true;
    }
    else{
        return false;
    }
}

// tester si un membre est Admin
function isAdmin(){
    if ( isConnected() && $_SESSION['membre']['statut'] == 1 )
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Fonction de requete SQL
function execRequete($req,$params = array()){

    // Sanitize
    if ( !empty($params)){
        foreach($params as $key => $value){
            $params[$key] = trim(strip_tags($value));
        }
    }
    global $pdo; // globalisation de $pdo

    $r = $pdo->prepare($req);
    $r->execute($params);
    if( !empty($r->errorInfo()[2]) ){
        die('Erreur rencontrée lors de la requête : '.$r->errorInfo()[2]);
    }

    return $r;
}

// Fonctions liées au Panier
function createPanier(){
    if(!isset($_SESSION['panier'])){
        $_SESSION['panier'] = array(
            'id_produit' => array(),
            'quantite' => array(),
            'prix' => array()
        );
    }
}

function addPanier($id_produit,$quantite,$prix){
    createPanier();

    $position_produit = array_search($id_produit,$_SESSION['panier']['id_produit']);
    if( $position_produit === false ){
        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;
    }
    else
    {
        $_SESSION['panier']['quantite'][$position_produit] += $quantite;
    }
}

function nbArticles(){
    $nb='';
    if( isset($_SESSION['panier']['id_produit']) )
    {
        $nb = array_sum($_SESSION['panier']['quantite']);
        if ($nb !=0 ){
            $nb='<span class="badge badge-primary">'.$nb.'</span>';
        } 
        else {
            $nb = '';
        }
    }
    return $nb;
}

function retraitDuPanier($id_produit){
    $position_produit = array_search($id_produit,$_SESSION['panier']['id_produit']);
    if( $position_produit !== false){
        // supprime et réarrange les index
        array_splice($_SESSION['panier']['id_produit'],$position_produit,1);
        array_splice($_SESSION['panier']['quantite'],$position_produit,1);
        array_splice($_SESSION['panier']['prix'],$position_produit,1);
    }
}

function montantTotal(){
    $total = 0;
    for($i=0;$i<count($_SESSION['panier']['id_produit']);$i++){
        $total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
    }
    return $total;
}