/*global $*/

$(document).ready(function(){
    //toggle display-hide password
	$('#display-password').click(function(event){
		if($('#password').attr('type') == "password")
			$('#password').attr('type','text');
		else
			$('#password').attr('type','password');
	});
})