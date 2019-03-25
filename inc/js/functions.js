$(document).ready(function() {

    $('.confsup').on('click', function() {
        return (confirm('Etes vous certain de vouloir supprimer ce produit?'));
    });

    if ($('#maModale').length == 1) { // tester la présence de ma div sur la page
        $('#maModale').modal('show');
    }


}); // FIN du Document Ready