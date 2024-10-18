
// Select du Contrat
var contratypeField = document.getElementById('contrat_type');
var produitField = document.getElementById('contrat_products');
var typeCondField = document.getElementById('contrat_typeConducteur');
var CondField = document.getElementById('contrat_conducteur');
var datePermField = document.getElementById('contrat_datePermis');
var activtField = document.getElementById('contrat_activite');
var raisonField = document.getElementById('contrat_raisonSociale');
var imatricltField = document.getElementById('contrat_imatriclt');

function updateFields() {
    var contratType = contratypeField ? contratypeField.value : null;
    var produitType = produitField ? produitField.value : null;

    if (contratType === 'Particulier' && (produitType === '1' || produitType === '10' || produitType === '12')) {
        CondField.style.display = 'block';
        datePermField.style.display = 'block';
        activtField.style.display = 'none';
        raisonField.style.display = 'none';
        imatricltField.style.display = 'block';
        typeCondField.style.display = 'block';
    } else if (contratType === 'Particulier' && (produitType === '2' || produitType === '7' || produitType === '11')) {
        CondField.style.display = 'none';
        datePermField.style.display = 'none';
        activtField.style.display = 'none';
        raisonField.style.display = 'none';
        imatricltField.style.display = 'none';
        typeCondField.style.display = 'none';
    } else if (contratType === 'Particulier') {
        CondField.style.display = 'block';
        datePermField.style.display = 'block';
        activtField.style.display = 'none';
        raisonField.style.display = 'block';
        imatricltField.style.display = 'block';
        typeCondField.style.display = 'block';
    }
    else if (contratType === 'Professionnel' && (produitType === '1' || produitType === '10' || produitType === '12')) {

        activtField.style.display = 'block';
        raisonField.style.display = 'block';
        CondField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'block';
        datePermField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'block';
        imatricltField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'block';
        typeCondField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'block';

    }
    else if (contratType === 'Professionnel' && (produitType === '2' || produitType === '7' || produitType === '11')) {

        activtField.style.display = 'block';
        raisonField.style.display = 'block';
        CondField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'none';
        datePermField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'none';
        imatricltField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'none';
        typeCondField.style.display = (produitType === '1' || produitType === '10' || produitType === '12') ? 'block' : 'none';

    }

    else if (contratType === 'Professionnel') {

        CondField.style.display = 'block';
        datePermField.style.display = 'block';
        activtField.style.display = 'block';
        raisonField.style.display = 'block';
        imatricltField.style.display = 'block';
        typeCondField.style.display = 'block';

    } else {
        // Default case if neither specific conditions are met
        CondField.style.display = 'block';
        datePermField.style.display = 'block';
        activtField.style.display = 'block';
        raisonField.style.display = 'block';
        imatricltField.style.display = 'block';
        typeCondField.style.display = 'block';
    }
}

if (contratypeField !== null) {
    contratypeField.addEventListener('change', updateFields);
}

if (produitField !== null) {
    produitField.addEventListener('change', updateFields);
}



if (typeCondField !== null) {
    typeCondField.addEventListener('change', function () {
        if (typeCondField.value === 'Désigné') {
            CondField.style.display = 'block';
            datePermField.style.display = 'block';

        } else if (typeCondField.value === 'Multiconducteur') {
            CondField.style.display = 'none';
            datePermField.style.display = 'none';

        } else if (typeCondField.value !== 'Désigné' && typeCondField.value !== 'Multiconducteur') {
            CondField.style.display = 'block';
            datePermField.style.display = 'block';
        }
    });

}

