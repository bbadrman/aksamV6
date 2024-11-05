
//Select Dynamique with API  

$(document).ready(function () {
	handleTeamChange('#prospect_team, #prospect_affect_team', '#prospect_comrcl, #prospect_affect_comrcl');

	function handleTeamChange(teamId, comercialId) {
		const prospectTeam = $(teamId);
		const prospectCommercial = $(comercialId);
		if (prospectTeam.length && prospectCommercial.length) {
			if (!prospectTeam.val().length) {
				prospectCommercial.parent().hide();

			} else {
				loadCommercials();
			}
			function loadCommercials() {
				const currentValue = prospectTeam.val();
				const commercialvalue = prospectCommercial.val();

				if (!currentValue.length) {
					return;
				}
				$.ajax({
					url: "/team/teams-api", success: function (result) {
						prospectCommercial.empty()
						const options = result.find(function (item) {
							return item.id == currentValue;
						});
						prospectCommercial.append(new Option());

						options?.commercials?.map(function (item) {
							prospectCommercial.append(new Option(item.username, item.id));
						})
						prospectCommercial.val(commercialvalue).change();
						prospectCommercial.parent().show();
						console.log('RESULT', options);
					}
				});
			}
			prospectTeam.change(function () {
				loadCommercials();
			})
			console.log('HEre im 2')

		}
	}
})


//table statistique des team comrcl sous du affectation
$(document).ready(function () {
	$('.expandable-row').click(function () {
		$('.expanded-content').not($(this).nextAll('.expanded-content')).hide();
		$(this).nextUntil('.expandable-row').toggle();
	});
});

//--------------------------------------------------------------------------//
// Select du Contrat //----
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

	// Hide `activite` if `contrat_type` is "Particulier"
	if (contratType === 'Particulier') {
		activtField.style.display = 'none';
	} else {
		// Reset and populate `activite` based on `produitType`
		var activiteChoices = {
			'1': ['TPM', 'VTC', 'Société', 'Négociant', 'Professionnel auto', 'Garage', 'AUTO ECOLE', 'BTP', 'BATIMENT', 'DEMENAGEMENT', 'DEPANNAGE', 'LOCATION', 'TAXI', 'TPPC', 'TPV'],
			'2': ['TPM', 'VTC', 'Société', 'Négociant', 'Professionnel auto', 'Garage', 'AUTO ECOLE', 'BTP', 'BATIMENT', 'DEMENAGEMENT', 'DEPANNAGE', 'LOCATION', 'TAXI', 'TPPC', 'TPV'],
			'5': ['TPM', 'VTC', 'Société', 'Négociant', 'Professionnel auto', 'Garage', 'AUTO ECOLE', 'BTP', 'BATIMENT', 'DEMENAGEMENT', 'DEPANNAGE', 'LOCATION', 'TAXI', 'TPPC', 'TPV'],
			'7': ['TPM', 'VTC', 'Société', 'Négociant', 'Professionnel auto', 'Garage', 'AUTO ECOLE', 'BTP', 'BATIMENT', 'DEMENAGEMENT', 'DEPANNAGE', 'LOCATION', 'TAXI', 'TPPC', 'TPV'],
			'21': ['TPM', 'VTC', 'Société', 'Négociant', 'Professionnel auto', 'Garage', 'AUTO ECOLE', 'BTP', 'BATIMENT', 'DEMENAGEMENT', 'DEPANNAGE', 'LOCATION', 'TAXI', 'TPPC', 'TPV'],
			'20': ['TPM', 'VTC', 'Société', 'Négociant', 'Professionnel auto', 'Garage', 'AUTO ECOLE', 'BTP', 'BATIMENT', 'DEMENAGEMENT', 'DEPANNAGE', 'LOCATION', 'TAXI', 'TPPC', 'TPV'],
			'19': [''],
			'18': ['TPM', 'Société', 'Professionnel auto', 'Garage', 'AUTO ECOLE', 'DEMENAGEMENT', 'DEPANNAGE', 'LOCATION', 'TAXI', 'TPPC', 'TPV'],
			'16': ['AUTO ECOLE', 'LOCATION', 'TPPC', 'TPV'],
			'17': ['BTP'],
			'18': ['TPM', 'Société', 'Garage', 'TAXI'],
		};

		// Clear previous options and populate new options for `activite`
		activtField.innerHTML = '';
		if (produitType in activiteChoices) {
			activiteChoices[produitType].forEach(function (activity) {
				var option = document.createElement('option');
				option.value = activity;
				option.textContent = activity;
				activtField.appendChild(option);
			});
			activtField.style.display = 'block';
		} else {
			activtField.style.display = 'none';
		}
	}

	// Additional display logic for other fields...
}

if (contratypeField !== null) {
	contratypeField.addEventListener('change', updateFields);
}

if (produitField !== null) {
	produitField.addEventListener('change', updateFields);
}

// Type conducteur change event
if (typeCondField !== null) {
	typeCondField.addEventListener('change', function () {
		if (typeCondField.value === 'Désigné') {
			CondField.style.display = 'block';
			datePermField.style.display = 'block';
		} else if (typeCondField.value === 'Multiconducteur') {
			CondField.style.display = 'none';
			datePermField.style.display = 'none';
		} else {
			CondField.style.display = 'block';
			datePermField.style.display = 'block';
		}
	});
}

// Le reste de votre logique pour les autres champs...
