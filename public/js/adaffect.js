
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

