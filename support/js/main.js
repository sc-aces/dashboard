/*global $*/
//****** Globals ******//

/* Keeps track of what was the last element that was selected for displaying
 * the admin sections on button click
 */ var lastDisplayedAdminElement = false;
	


//****** END Globals ******//

function resetOnClick(){
	//remove alerts
	$('.alert-close').click(function(event) {
		$(this).parent().slideUp("slow",function(){
			$(this).parent().remove();
		});
	});
	
	
	$('.btn').click(function(event){
		var id = $(this).attr('id');
		if(id == "createElection-button" || id == "editElection-button"
			|| id == "restartElection-button" || id == "deleteElection-button"
			|| id == "startElection-button" || id == "pauseElection-button"
			|| id == "stopElection-button"){
				id = id.split('-');
				id = id[0];
				
				if(lastDisplayedAdminElement){
					$('#'+lastDisplayedAdminElement).slideUp('slow',function(){});
				}
				
				if(id ==  lastDisplayedAdminElement){
					$('#'+id).slideUp('slow',function(){});
					lastDisplayedAdminElement = false;
				}
				else{
					$('#'+id).slideDown('slow',function(){});
					lastDisplayedAdminElement = id;
				}
			}
	});
	
	$('.more-parent').click(function(event){
		$('#more-'+$(this).parent().attr('id')).slideToggle( "slow", function() {});
		var parentId = $(this).parent().attr('id').split('-');
		log(parentId);
		if(parentId[0] == "election"){
			log($(this).parent().attr('id'));
			$('.progress-bar').animate({width:$('.process-bar').attr('aria-valuenow')},1000);
			// $(".progress-bar").hide().show("slide", { direction: "left" }, 1500);
			// $('.progress-bar').css('width',$('.progress-bar').attr('aria-valuenow')+'%');
		}
	});
	$('.more').click(function(event){
		$('#more-'+$(this).attr('id')).slideToggle("slow", function() {});
	});
	
	
	// $('.election-form').change(function(event){
	// 	var inputId = $(this).find('input:checked').val();
	// 	$('#'+inputId).parent().parent().addClass('selected-canidate');
	// });
	
	
	///////Reset Input
	$('.reset-input').click(function(event) {
		 $(this).siblings().val($(this).siblings().attr("default"));
		 $(this).removeClass('reset-input-active');
	});
	$('.reset-input').siblings().keyup(function(event){
		$(this).siblings().addClass('reset-input-active');
	});
}

$(document).ready(function(){
	//remove alerts
	$('.alert-close').click(function(event) {
		$(this).parent().slideUp("slow",function(){
			$(this).parent().remove();
		});
	});
	
	
	$('.btn').click(function(event){
		var id = $(this).attr('id');
		if(id == "createElection-button" || id == "editElection-button"
			|| id == "restartElection-button" || id == "deleteElection-button"
			|| id == "startElection-button" || id == "pauseElection-button"
			|| id == "stopElection-button"){
				id = id.split('-');
				id = id[0];
				
				if(lastDisplayedAdminElement){
					$('#'+lastDisplayedAdminElement).slideUp('slow',function(){});
				}
				
				if(id ==  lastDisplayedAdminElement){
					$('#'+id).slideUp('slow',function(){});
					lastDisplayedAdminElement = false;
				}
				else{
					$('#'+id).slideDown('slow',function(){});
					lastDisplayedAdminElement = id;
				}
			}
	});
	
	$('.more-parent').click(function(event){
		$('#more-'+$(this).parent().attr('id')).slideToggle( "slow", function() {});
		var parentId = $(this).parent().attr('id').split('-');
		log(parentId);
		if(parentId[0] == "election"){
			log($(this).parent().attr('id'));
			$('.progress-bar').animate({width:$('.process-bar').attr('aria-valuenow')},1000);
			// $(".progress-bar").hide().show("slide", { direction: "left" }, 1500);
			// $('.progress-bar').css('width',$('.progress-bar').attr('aria-valuenow')+'%');
		}
	});
	$('.more').click(function(event){
		$('#more-'+$(this).attr('id')).slideToggle("slow", function() {});
	});
	
	
	// $('.election-form').change(function(event){
	// 	var inputId = $(this).find('input:checked').val();
	// 	$('#'+inputId).parent().parent().addClass('selected-canidate');
	// });
	
	
	///////Reset Input
	$('.reset-input').click(function(event) {
		 $(this).siblings().val($(this).siblings().attr("default"));
		 $(this).removeClass('reset-input-active');
	});
	$('.reset-input').siblings().keyup(function(event){
		$(this).siblings().addClass('reset-input-active');
	});

	function z(n){return (n < 10? '0' : '') + n;}
	var date = new Date();
	date = new Date(date.getUTCFullYear() + '-' + z(date.getUTCMonth() + 1) + '-' + 
			z(date.getUTCDate()) + ' ' + z(date.getUTCHours()) + ':' +
			z(date.getUTCMinutes()) + ':' + z(date.getUTCSeconds()));
			
	var clock = $('.clock').FlipClock(date,{  
					clockFace: 'TwentyFourHourClock'
				});
	date = new Date();
	log(date);
	date = date.toUTCString();
	log(date);
	var clock2 = $('.clock2').FlipClock(date,{  
					clockFace: 'TwentyFourHourClock'
				});
	
	$('#election-type').change(function(event) {
		log($(this).val());
		var type = $(this).val();
		if(type == "General Election"){
			$('#squadron-vote-inputs').slideUp("slow",function(){});
			$('#special-election-inputs').slideUp("slow",function(){});
			$('#election-tags-input').slideUp("slow",function(){});
			$('#squadron-id').attr("required",false);
		}else if(type == "Special Election"){
			$('#election-tags-input').slideUp("slow",function(){});
			$('#squadron-vote-inputs').slideUp("slow",function(){});
			$('#special-election-inputs').slideDown("slow",function(){});
			$('#squadron-id').attr("required",false);
		}else if(type == "Squadron Vote"){
			$('#squadron-vote-inputs').slideDown("slow",function(){});
			$('#election-tags-input').slideDown("slow",function(){});
			$('#special-election-inputs').slideUp("slow",function(){});
			$('#squadron-id').attr("required",true);
		}else if(type == "Generic Election"){
			$('#election-tags-input').slideDown("slow",function(){});
			$('#squadron-vote-inputs').slideUp("slow",function(){});
			$('#special-election-inputs').slideUp("slow",function(){});
			$('#squadron-id').attr("required",false);
		}else{
			console.error("Undefined election type: "+type);
		}
	})
	
	
	
	//Colorboxes
	$('.lightbox-btn').colorbox({inline:true, maxWidth:"95%", maxHeight:"95%"});
});


function getTimeRemaining(endtime) {
	var t = Date.parse(endtime) - Date.parse(new Date());
	var seconds = Math.floor((t / 1000) % 60);
	var minutes = Math.floor((t / 1000 / 60) % 60);
	var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
	var days = Math.floor(t / (1000 * 60 * 60 * 24));
	return {
		'total': t,
		'days': days,
		'hours': hours,
		'minutes': minutes,
		'seconds': seconds
	};
}

function initializeClock(id, endtime) {
	var clock = document.getElementById(id);
	var daysSpan = clock.querySelector('.days');
	var hoursSpan = clock.querySelector('.hours');
	var minutesSpan = clock.querySelector('.minutes');
	var secondsSpan = clock.querySelector('.seconds');

	function updateClock() {
		var t = getTimeRemaining(endtime);

		daysSpan.innerHTML = t.days;
		hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
		minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
		secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

		if (t.total <= 0) {
			clearInterval(timeinterval);
		}
	}

	updateClock();
	var timeinterval = setInterval(updateClock, 1000);
}


function createAlert(type, message){
	return "<div class=\"alert alert-"+type+"\">"+message+"<span class=\"alert-close\"><i class=\"fa fa-times fa-lg\"></i></span></div>\n";
}


function echo(obj){
	alert(obj);
}
function log(obj){
	console.log(obj);
}
function err(obj){
	console.error(obj);
}