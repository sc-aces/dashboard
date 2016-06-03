/*
    global $
    global createAlert
*/

$(document).ready(function(){
	$('.correct-answer').click(function(event){
		var next = ($(this).attr('id')).split("-")[1];
		var parent = ($(this).parent().attr('id')).split("-")[1];
		$('#q-'+parent).slideUp("fast", function(){
			$('#q-'+next).slideDown("slow", function(){});
		});
	});
	
	$('#id-token-submit').click(function(event){
		var id = $('#enjin-id').val();
		var token = $('#voting-token').val();
		if(id != "" && token != ""){
			$.ajax({
				type: 'GET',
				url: "https://php-testbed-dlennox.c9users.io/z_test/test.php?eID="+id+"&token="+token,
				success: function (jsonString) {
					var responseObj = JSON.parse(jsonString)[0];
					if(responseObj == null)
						responseObj = {status:"incorrect"};
					if(responseObj.status == "success"){
						$('#validate-alert').html(createAlert('success','Identity successfully validated!'));
						$('#validate-alert').children().children('.alert-close').addClass('displayNone');
						
						$.ajax({
							type: 'GET',
							headers: { },
							url: "get-enjin-username.php?id="+id,
							success: function (jsonString) {
								var responseObj2 = JSON.parse(jsonString);
								if(responseObj2.status == "success"){
									$.ajax({
										type: 'GET',
										url: "get-enjin-tags.php?id="+id,
										success: function(jsonString){
											var responseObj3 = JSON.parse(jsonString);
											if(responseObj3.status == "error"){
												$('.alert-section').html(createAlert('danger','Unable to fetch tags!'));
												$('.alert-section').children().children('.alert-close').addClass('displayNone');
											}else{
												$('#tags').attr('value', responseObj3.status);
												console.log(responseObj3);
												console.log(responseObj3.status);
											}
											
											$('#enjin-username-header').html(responseObj2.username);
											$('#enjin-username').attr('value', responseObj2.username);
										
											$('#q-4').slideUp("fast", function(){
												$('#q-5').slideDown("slow", function(){});
											});
										},
										error: function(request, status, error){
											console.error(error);
										}
									});
								}else if(responseObj2.status == "error"){
									console.error("An error occured");
								}else{
									console.error("Unknown status");
								}
							},
							error: function(request, status, error){
								console.error("fetchShip - Status: "+status+", Error: "+error);
							}
						});
					}else if(responseObj.status == "incorrect"){
						$('#validate-alert').html(createAlert('danger','Incorrect ID or Token'));
						$('#validate-alert').children().children('.alert-close').addClass('displayNone');
					}else if(responseObj.status == "error"){
						$('#validate-alert').html(createAlert('danger','An error has occured. Please try again.'));
						$('#validate-alert').children().children('.alert-close').addClass('displayNone');
					}else{
						$('#validate-alert').html(createAlert('danger','An even bigger error has occured. Please contact an admin.'));
						$('#validate-alert').children().children('.alert-close').addClass('displayNone');
					}
						$.ajax({
							type: 'GET',
							url: "get-enjin-username.php?id="+id,
							success: function (jsonString) {
								var responseObj = JSON.parse(jsonString);
								if(responseObj.status == "success"){
									$('enjin-username').html(responseObj.username);
									$('enjin-username').attr('value', responseObj.username);
								}else if(responseObj.status == "error"){
									console.err("An error occured");
								}else{
									console.err("Unknown status");
								}
							},
							error: function(request, status, error){
								console.error("fetchShip - Status: "+status+", Error: "+error);
							}
						});
				},
				error: function(request, status, error){
					console.error("fetchShip - Status: "+status+", Error: "+error);
				}
			});
		}
	});
	
	$('#email').change(function(event){
		$.ajax({
			type: 'GET',
			headers: { },
			url: "check-email.php?email="+$(this).val(),
			success: function (jsonString) {
				console.log(jsonString);
				var responseObj2 = JSON.parse(jsonString);
				console.log(responseObj2.username);
				if(responseObj2.status == "free"){
					$('#create-account-button').attr("disabled",false);
					$('#email-alert').html(createAlert("success", "Email is available"));
					$('#email-alert').children().children('.alert-close').addClass('displayNone');
				}else if(responseObj2.status == "taken"){
					$('#create-account-button').attr("disabled",true);
					$('#email-alert').html(createAlert("danger", "Email is not available"));
					$('#email-alert').children().children('.alert-close').addClass('displayNone');
				}else if(responseObj2.status == "error"){
					console.error("An error occured");
				}else{
					console.error("Unknown status");
				}
			},
			error: function(request, status, error){
				console.error("fetchShip - Status: "+status+", Error: "+error);
			}
		});
	});
	
	$('#password').keyup(function(){checkPasswords()});
	$('#re-password').keyup(function(){checkPasswords()});
});

function checkPasswords(){
	console.log('t');
	var pass = $('#password').val();
	var rePass = $('#re-password').val();
	if(pass == "" && rePass == ""){}
	else if(pass != rePass){
		$('#password-alert').html(createAlert('danger','Passwords do not match!'));
		$('#password-alert').children().children('.alert-close').addClass('displayNone');
		$('#create-account-button').attr('disabled',true);
	}else{
		$('#password-alert').html(createAlert('success','Passwords match!'));
		$('#password-alert').children().children('.alert-close').addClass('displayNone');
		$('#create-account-button').attr('disabled',false);
	}
}