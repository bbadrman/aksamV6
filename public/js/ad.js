

// START Fetching Users Active //
window.onload = () => {

	let activer = document.querySelectorAll("[type=checkbox]")
	for (let bouton of activer) {
		bouton.addEventListener("click", function () {
			let xmlhttp = new XMLHttpRequest;

			xmlhttp.open("get", `/utilisateurs/activer/${this.dataset.id}`)
			xmlhttp.send();

		})
	}
}



// START Fetching Groups //
function fetchGroups() {
	$.ajax({
		type: "GET",
		url: "/team",
		dataType: "json",
		success: function (response) {
			let listGroups = '';

			$.each(response.team, function (key, item) {
				listGroups += `<tr>
											<td>${item.id
					}</td>
											<td>${item.name
					}</td>
											<td>${item.description
					}</td>
											<td class="btn-toolbar">
											<button href="#"  value="${item.id
					}" class="show_groupe btn btn-success btn-xs"> <i class="fa fa-eye"></i></button>
											<button href="#"  value="${item.id
					}" class="edit_groupe btn btn-primary btn-xs"> <i class="fa fa-pencil"></i></button>
											<button href="#"  value="${item.id
					}" class="delete_groupe btn btn-danger btn-xs"> <i class="fa fa-trash"></i></button>
											</td>
										</tr>`;
			});
			groupList.html(listGroups);
		}
	});
}
// END Fetching Groups //

// START Adding a Team //

$(document).on('click', '.team_new', function (e) {
	e.preventDefault();
	const data = {
		'name': $('#team_name').val(),

		'description': $('.description').val(),
	};

	let AddGroupModal = $('#AddGroupeModal');

	$.ajax({
		type: "POST",
		url: "/team/new-team",
		data: data,
		dataType: "json",
		success: function (response) {

			if (response.status === 400) {
				$('#saveform_errList').html("").addClass('alert alert-danger');
				$.each(response.errors, function (key, err_values) {
					$('#saveform_errList').append('<li>' + err_values + '</li>');


				});
			} else {
				$('#saveform_errList').html("").removeClass('alert alert-danger');
				$('#success_message').addClass('alert alert-success').text(response.message);
				$('#user_teams').append('<option value="1" selected="selected">' + $('#team_name').val() + '</option>');
				AddGroupModal.modal('hide').find('#team_name').val("").find('#team_description').val("");

				fetchGroups();
			}
			// window.location.reload();
		}
	})
});

// END Adding a Team //


// START adding a Product //

$(document).on('click', '.product_new', function (e) {
	e.preventDefault();

	const data = {
		'name': $('#product_name').val(),

		'descrption': $('.descrption').val()
	};

	let AddProductModal = $('#AddProductModal');

	$.ajax({
		type: "POST",
		url: "/product/new-product",
		data: data,
		dataType: "json",
		success: function (response) {
			if (response.status === 400) {
				$('#saveform_errList').html("").addClass('alert alert-danger');
				$.each(response.errors, function (key, err_values) {
					$('#saveform_errList').append('<li>' + err_values + '</li>');
				});
			} else {
				$('#saveform_errList').html("").removeClass('alert alert-danger');
				$('#success_message').addClass('alert alert-success').text(response.message);
				$('#user_products').append('<option value="1" selected="selected">' + $('#product_name').val() + '</option>');
				AddProductModal.modal('hide').find('#product_name').val("").find('#product_descrption').val("");

			}
		}
	})
});

// END adding a Product //

// START adding a Client //

// $(document).on('click', '.clients_add', function (e) {
// 	e.preventDefault();

// 	const data = {
// 		'firstname': $('#client_firstname').val(),

// 		'lastname': $('.client_lastname').val(),
// 		'phone': $('.client_phone').val(),
// 		'email': $('.client_email').val(),
// 		'raisonSociale': $('.client_raisonSociale').val()
// 	};

// 	let AddClientModal = $('#AddClientModal');

// 	$.ajax({
// 		type: "POST",
// 		url: "/client/new-client",
// 		data: data,
// 		dataType: "json",
// 		success: function (response) {
// 			if (response.status === 400) {
// 				$('#saveform_errList').html("").addClass('alert alert-danger');
// 				$.each(response.errors, function (key, err_values) {
// 					$('#saveform_errList').append('<li>' + err_values + '</li>');
// 				});
// 			} else {
// 				$('#saveform_errList').html("").removeClass('alert alert-danger');
// 				$('#success_message').addClass('alert alert-success').text(response.message);

// 				AddClientModal.modal('hide').find('#client_firstname').val("").find('#client_lastname').find('#client_phone').find('#client_email').find('#client_raisonSociale').val("");

// 			}
// 		}
// 	})
// });

// START adding a Fonction //

$(document).on('click', '.fonction_new', function (e) {
	e.preventDefault();

	const data = {

		'name': $('#fonction_name').val(),
		'description': $('.description').val()
		// 'description': $('.description').val()
	};
	// console.log(data);

	let AddFonctionModal = $('#AddFonctionModal');

	$.ajax({
		type: "POST",
		url: "/fonctions/new-fonction",
		data: data,
		dataType: "json",
		success: function (response) {
			// console.log(response);
			if (response.status === 400) {
				$('#saveform_errList').html("").addClass('alert alert-danger');
				$.each(response.errors, function (key, err_values) {
					console.log(response);
					$('#saveform_errList').append('<li>' + err_values + '</li>');
				});
			} else {
				$('#saveform_errList').html("").removeClass('alert alert-danger');
				$('#success_message').addClass('alert alert-success').text(response.message);
				$('#user_fonctions').append('<option value="1" selected="selected">' + $('#fonction_name').val() + '</option>');
				AddFonctionModal.modal('hide').find('#fonction_name').val("").find('#fonction_description').val("");

			}
		}
	})
});

// END adding a Fonction //









// $(document).ready(function() {
// 	var $locationSelector = $('user_teams');
// 	var $specificLocationTarget = $('user_products');

// 	$locationSelector.on('change', function(e){
// 		$.ajax({
// 			url: $locationSelector.data('specification'),
// 			data: {
// 				location: $locationSelect.val()
// 			},
// 			success: function(html) {
// 				if(!html){
// 					$specificLocationTarget.find('select').remove();
// 					$specificLocationTarget.addClass('d-none');

// 					return;
// 				}

// 				$specificLocationTarget
// 				.html(html)
// 				.removeClass('d-none');
// 			}
// 		});
// 	});
// });


// pour cache le tableau prospect
$(document).ready(function () {
	$("#shrch").hide();  // masquer la div au chargement de la page
	$("#searchBtn").click(function () {  // ajouter un écouteur d'événements sur le bouton de recherche
		$("#shrch").show();  // afficher la div lorsque le bouton de recherche est cliqué
	});
});

$('document').ready(function () {
	$("#shrch").hide()

});


//Select Dynamique with API


$(document).ready(function () {
	handleTeamChange('#prospect_team', '#prospect_comrcl');
	handleTeamChange('#prospect_affect_team', '#prospect_affect_comrcl');
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

//select dynamique


// document.addEventListener('DOMContentLoaded', function () {
// 	const teamSelectEl = document.getElementById('prospect_team');
// 	teamSelectEl.addEventListener('change', function (e) {
// 		// console.log('okok');
// 		const formEl = teamSelectEl.closest('form');
// 		// console.log("formEl: " + formEl);

// 		fetch(formEl.action, {
// 			method: formEl.method,
// 			body: new FormData(formEl)
// 		})
// 			.then(response => response.text())
// 			.then(html => {


// 				const parser = new DOMParser();
// 				const doc = parser.parseFromString(html, 'text/html');
// 				const newComrclFormFieldEl = doc.getElementById('prospect_comrcl');


// 				newComrclFormFieldEl.addEventListener('change', function (e) {
// 					e.target.classList.remove('is-invalid');
// 				});
// 				document.getElementById('prospect_comrcl').replaceWith(newComrclFormFieldEl);

// 			})
// 			.catch(function (err) {
// 				console.warn('Something went wrong.', err);
// 			});
// 	});
// });



// form prospect  select 
function setupDisplayControl(fieldId, containerId, targetValue) {
	var field = document.getElementById(fieldId);
	var container = document.getElementById(containerId);
	if (field !== null && container !== null) {
		field.addEventListener('change', function () {
			container.style.display = field.value === targetValue ? 'block' : 'none';
		});
	}
}

// Configuration des différents champs de formulaire
setupDisplayControl('prospect_typeProspect', 'subcategory-container', '2');
setupDisplayControl('prospect_lastAssure', 'subResil-container', 'Oui');





//select motiveRelanced pour afficher la ajouter client quand on click sur pasage contart
//select motiveRelanced pour afficher la calandrie et comment quand on click sur rndv
var resilField = document.getElementById('relanced_motifRelanced');
var commentField = document.getElementById('MotivRelcoment-container'); // Changer l'ID ici


var subresilContainer1 = document.getElementById('subMotivRelc-container');
var subresilContainer2 = document.getElementById('subMotivContrat-container');

if (resilField !== null) {
	resilField.addEventListener('change', function () {
		if (resilField.value === '1' || resilField.value === '2' || resilField.value === '4' || resilField.value === '5' || resilField.value === '6') {
			subresilContainer1.style.display = 'block';     // Afficher le champ date
			subresilContainer2.style.display = 'none';     // cacher le champ client
			commentField.style.display = 'block';          // Afficher le champ comment
			//click sur passage en contart il faut affichier les champ des contart et comment cache du date 
		} else if (resilField.value === '3' || resilField.value === '7' || resilField.value === '8' || resilField.value === '9' || resilField.value === '11') {
			//pour rederct to rout 
			//window.location.href = "/client/new-client";
			// pour manipilie les champs
			subresilContainer1.style.display = 'none';   // cacher date
			subresilContainer2.style.display = 'none';
			commentField.style.display = 'block'; // Cacher le champ comment

		} else if (resilField.value === '10') {
			//pour rederct to rout 
			//window.location.href = "/client/new-client";
			// pour manipilie les champs
			subresilContainer1.style.display = 'none';   // cacher date
			subresilContainer2.style.display = 'block';
			commentField.style.display = 'block'; // Cacher le champ comment

		}
		else {
			subresilContainer1.style.display = 'none';
			subresilContainer2.style.display = 'none';
			commentField.style.display = 'none'; // Cacher le champ comment

		}
	});

}

//select motiveRelanced pour afficher la ajouter client quand on click sur pasage contart
//select motiveRelanced pour afficher la calandrie et comment quand on click sur rndv
// var resilField = document.getElementById('relanced_motifRelanced');
// var commentField = document.getElementById('MotivRelcoment-container'); // Changer l'ID ici  champ comment

// var subresilContainer1 = document.getElementById('subMotivRelc-container'); //champ date 

// if (resilField !== null) {
// 	resilField.addEventListener('change', function () {
// 		if (resilField.value === '1' || resilField.value === '2' || resilField.value === '4' || resilField.value === '5' || resilField.value === '6') {
// 			subresilContainer1.style.display = 'block';     // Afficher le champ date 
// 			commentField.style.display = 'block';          // Afficher le champ comment
// 			//click sur passage en contart il faut affichier les champ des contart et comment cache du date 
// 		}
// 		else {
// 			subresilContainer1.style.display = 'none'; // Cacher le champ date 
// 			commentField.style.display = 'none'; // Cacher le champ comment 
// 		}
// 	});
// }

//select cloture

// var resilField2 = document.getElementById('cloture_motifCloture');  // form cloture 
// var commentField2 = document.getElementById('MotivClotrcoment-container'); // Changer l'ID ici   comment

// var subresilContainer11 = document.getElementById('subMotivCltr-container');  //champ date cloture
// var subresilContainer22 = document.getElementById('subMotivContrat-container'); //champ contrat cloture

// resilField2.addEventListener('change', function () {
// 	if (resilField2.value === 'faux' || resilField2.value === 'doublon' || resilField2.value === 'concurrent' || resilField2.value === 'test' || resilField2.value === 'souscrit') {
// 		subresilContainer11.style.display = 'none';     // cacher le champ date 
// 		commentField2.style.display = 'block';            // affichier le champ comment
// 		subresilContainer22.style.display = 'none';       // cacher le champ contrat
// 		//click sur passage en contart il faut affichier les champ des contart et comment cache du date 
// 	}
// 	else if (resilField2.value === 'contrat') {

// 		subresilContainer11.style.display = 'none';   // cacher date
// 		subresilContainer22.style.display = 'block';  // affichier form contart
// 		commentField2.style.display = 'block'; // affichier le champ comment

// 	}
// 	else {
// 		subresilContainer11.style.display = 'none';
// 		subresilContainer22.style.display = 'none';
// 		commentField2.style.display = 'none'; // Cacher le champ comment

// 	}
// });


// var resilField = document.getElementById('relanced_motifRelanced');
// var subresilContainer1 = document.getElementById('subMotivRelc-container');
// // var subresilContainer2 = document.getElementById('subMotivContrat-container');

// if (resilField !== null) {
// 	resilField.addEventListener('change', function () {
// 		if (resilField.value === '1') {
// 			subresilContainer1.style.display = 'block';
// 		} else if (resilField.value === '10') {
// 			subresilContainer1.style.display = 'none';
// 		} else {
// 			subresilContainer1.style.display = 'none';
// 		}
// 	});
// }



$(document).ready(function () {
	$("#relance-div").click(function () {
		$("#relanceChoix").animate({
			height: 'toggle'
		});
	});
});


//pour afficher comment 	
function showFullText(element) {
	// Récupère l'élément contenant le texte complet
	var fullText = element.querySelector('.fullText');
	// Change la visibilité du texte complet au clic
	if (fullText.style.display === 'none') {
		fullText.style.display = 'inline'; // Montre le texte complet
		element.querySelector('.shortened').style.display = 'none'; // Cache le texte raccourci
	} else {
		fullText.style.display = 'none'; // Cache le texte complet
		element.querySelector('.shortened').style.display = 'inline'; // Montre le texte raccourci
	}
}

// submut deux button en meme temps
document.addEventListener('DOMContentLoaded', function () {
	const relanceSubmitButton = document.querySelector('#relance-submit');
	const clientSubmitButton = document.querySelector('#client-submit');
	const clientForm = document.querySelector('#client-form');

	clientSubmitButton.addEventListener('click', async (event) => {
		event.preventDefault(); // Empêche la soumission par défaut du formulaire client
		await submitForm(clientForm); // Soumet le formulaire client manuellement
		await delay(100); // Attente de 100 ms pour laisser le temps à la soumission de se terminer
		relanceSubmitButton.click(); // Déclenche la soumission du formulaire de relance
	});

	async function submitForm(form) {
		try {
			await fetch(form.action, {
				method: form.method,
				body: new FormData(form)
			});
		} catch (error) {
			console.error('Une erreur s\'est produite :', error);
		}
	}

	function delay(ms) {
		return new Promise(resolve => setTimeout(resolve, ms));
	}
});

// filter table
$(document).ready(function () {
	var table = $('#example').DataTable({
		searchPanes: true
	});
	table.searchPanes.container().prependTo(table.table().container());
	table.searchPanes.resizePanes();
});


//persist le champs activite
// document.addEventListener("DOMContentLoaded", function () {
// 	var typeProspectField = document.getElementById("{{ form.typeProspect.vars.id }}");
// 	var activitesField = document.getElementById("{{ form.activites.vars.id }}");
// 	var subcategoryContainer = document.getElementById("subcategory-container");

// 	typeProspectField.addEventListener("change", function () {
// 		if (typeProspectField.value === '2') {
// 			subcategoryContainer.style.display = "block";
// 			activitesField.setAttribute('required', 'required');
// 		} else {
// 			subcategoryContainer.style.display = "none";
// 			activitesField.removeAttribute('required');
// 		}
// 	});
// });

// pour passer au path client quen on click sur passage en contart
// document.addEventListener('DOMContentLoaded', function () {
// 	const motifRelanced = document.getElementById('relanced_motifRelanced'); // Assurez-vous de remplacer 'relanced_motifRelanced' par l'ID réel de votre champ de formulaire

// 	motifRelanced.addEventListener('change', function () {
// 		const selectedValue = this.value;
// 		const prospectId = document.getElementById('relance-submit').dataset.prospectId; // Récupérer l'ID du prospect à partir du bouton

// 		if (selectedValue === '10') {
// 			window.location.href = '/client/new/'; // Redirection vers la route client avec l'ID du prospect
// 		}
// 	});
// });



//button dashbord
// function togglePanel() {
// 	var panel = document.getElementById('panell');
// 	if (panel.style.display === 'none') {
// 	  panel.style.display = 'table-row'; // ou 'block' si le parent est un tbody
// 	} else {
// 	  panel.style.display = 'none';
// 	}
// 	 var panel = document.getElementById('panel1');
// 	if (panel.style.display === 'none') {
// 	  panel.style.display = 'table-row'; // ou 'block' si le parent est un tbody
// 	} else {
// 	  panel.style.display = 'none';
// 	}
//   }



// $(document).ready(function(){
// $("#flipp").click(function(){
// 	$("#panell").slideToggle("slow");
// 	$("#panel1").slideToggle("slow");
// });
// });
// // select typeProspect

// var categoryField = document.getElementById('prospect_typeProspect');
// var subcategoryContainer = document.getElementById('subcategory-container');

// if (categoryField !== null) {
// 	categoryField.addEventListener('change', function () {
// 		if (categoryField.value === 'Professionnels') {

// 			subcategoryContainer.style.display = 'block';
// 		} else {
// 			subcategoryContainer.style.display = 'none';
// 		}
// 	});
// }



// //select motiveProspect
// var motifField = document.getElementById('prospect_source');
// var submotifContainer = document.getElementById('subMotiv-container');
// if (motifField !== null) {
// 	motifField.addEventListener('change', function () {
// 		if (motifField.value === 'Saisie manuelle') {

// 			submotifContainer.style.display = 'block';
// 		} else {
// 			submotifContainer.style.display = 'none';
// 		}
// 	});
// }

// //select motiveResil
// var resilField = document.getElementById('prospect_lastAssure');
// var subresilContainer = document.getElementById('subResil-container');
// if (resilField !== null) {
// 	resilField.addEventListener('change', function () {
// 		if (resilField.value === 'Oui') {

// 			subresilContainer.style.display = 'block';
// 		} else {
// 			subresilContainer.style.display = 'none';
// 		}
// 	});
// }

// function playNotificationSound() {
// 	const audio = new Audio('/assets/sounds/notification-soundtone360.mp3');
// 	audio.play();
// }

$.ajax({
	url: 'http://localhost:92/api/prospects',
	success: function (data) {
		// Process prospect data
		playNotificationSound();
	}
});

