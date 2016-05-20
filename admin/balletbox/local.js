/*
	global $
	global createAlert
	global initializeClock
	global resetOnClick
*/

$(document).ready(function(event){
	$.ajax({
		type: 'GET',
		url: 'fetch-elections.php',
		success: function (jsonString) {
			buildElections(JSON.parse(jsonString));
		},
		error: function(request, status, error){
			console.error("fetchShip - Status: "+status+", Error: "+error);
		}
	});
});

function buildElections(json){
	console.log(json);
	var html = "";
	for(var i=0; i<json.length;i++){
		
		var error = false;
		if(json[i].type == "bge"){ //basic generic election
			
		}else if(json[i].type == "squ"){ //squaron vote
			
		}else if(json[i].type == "spe"){ //special election
		
		}else if(json[i].type == "gen"){ //general election
			
		}else{ //error
			error = true;
			console.error("Undefined Election type: "+json[i].type+" (index="+i+", id="+json[i].id+")");
		}
		
		if(json[i].election_generic.length > 0){
			var voteHtml = "\t\t<h4 class=\"clear\">Results</h4>\n"
					+"\t\t<hr class=\"custom-hr increase-margins clear\">\n"
					+"\t\t<div class=\"election-result-container\">\n";
			var election_genericArray = json[i].election_generic;
			for(var j=0; j<election_genericArray.length; j++){
				voteHtml += "\t\t\t<div id=\""+election_genericArray[j].id+"-"+election_genericArray[j].election_id+"\" class=\"election-result\">\n"
						+"\t\t\t\t<h5 class=\"text-center\">"+election_genericArray[j].name+"</h5>\n";
				election_genericArray[j].candidates = JSON.parse(json[i].election_generic[j].candidates);
				console.log(election_genericArray[j]);
				var candidatesArray = election_genericArray[j].candidates.candidates;
				if(candidatesArray[0] != null){
					var totalVotes = json[i].election_generic[j].candidates.totalVotes;
					for(var k=0; k<candidatesArray.length; k++){
						voteHtml += "\t\t\t\t<div class=\"candidate\">\n"
									+"\t\t\t\t\t<h6 class=\"small pull-left\">"+candidatesArray[k].candidate+"</h6>\n"
									+"\t\t\t\t\t<h6 class=\"small pull-right\">"+candidatesArray[k].votes+"</h6>\n"
									+"\t\t\t\t\t<div class=\"progress clear\">\n"
									+"\t\t\t\t\t\t<div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\""+candidatesArray[k].votes/totalVotes*100+"\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:"+candidatesArray[k].votes/totalVotes*100+"%\">\n"
									+"\t\t\t\t\t\t\t<span class=\"sr-only\">"+candidatesArray[k].votes/totalVotes*100+"% Votes</span>\n"
									+"\t\t\t\t\t\t</div>\n"
									+"\t\t\t\t\t</div>\n"
									+"\t\t\t\t</div>\n";
					}
				}
				voteHtml +=	"\t\t\t</div>\n";
				
				json[i].election_generic[j].voters = JSON.parse(json[i].election_generic[j].voters);
			}
			voteHtml += "\t\t</div>\n";
		}
		
		
		var backgroundHtml;
		var statusHtml;
		var substatusHtml;
		var typeHtml = "";
		var countdown = true;
		var countdownId;
		var deadline;
		
		if(json[i].status == "upcoming"){
			backgroundHtml = "info-background";
			statusHtml = "<i class=\"fa fa-info\"></i> Upcoming";
			countdown = true;
			countdownId = "clock-"+json[i].id;
			substatusHtml = "\t\t\t<div class=\"text-clock\" id=\"clock-"+json[i].id+"\">\n"
							+"\t\t\t\t<i class=\"fa fa-clock-o\"></i>\n"
							+"\t\t\t\t<span class=\"days\"></span>d\n"
							+"\t\t\t\t<span class=\"hours\"></span>h\n"
							+"\t\t\t\t<span class=\"minutes\"></span>m\n"
							+"\t\t\t\t<span class=\"seconds\"></span>s\n"
							+"\t\t\t</div>\n";
			deadline = new Date(Date.parse(new Date()) + (4 * 24 * 60 * 60 * 1000)+ (35*1000));
		}else if(json[i].status == "active"){
			backgroundHtml = "success-background";
			statusHtml = "<i class=\"fa fa-circle-o-notch fa-spin\"></i> Active";
			countdown = true;
			countdownId = "clock-"+json[i].id;
			substatusHtml = "\t\t\t<div class=\"text-clock\" id=\"clock-"+json[i].id+"\">\n"
							+"\t\t\t\t<i class=\"fa fa-clock-o\"></i>\n"
							+"\t\t\t\t<span class=\"days\"></span>d\n"
							+"\t\t\t\t<span class=\"hours\"></span>h\n"
							+"\t\t\t\t<span class=\"minutes\"></span>m\n"
							+"\t\t\t\t<span class=\"seconds\"></span>s\n"
							+"\t\t\t</div>\n";
			deadline = new Date(Date.parse(new Date()) + (6 * 24 * 60 * 60 * 1000));
		}else if(json[i].status == "deleted"){
			typeHtml = "election-deleted";
			backgroundHtml = "danger-background";
			statusHtml = "<i class=\"fa fa-info\"></i> Deleted";
		}else if(json[i].status == "paused"){
			backgroundHtml = "warning-background";
			statusHtml = "<i class=\"fa fa-pause\"></i> Paused";
		}else if(json[i].status == "stopped"){
			backgroundHtml = "danger-background";
			statusHtml = "<i class=\"fa fa-stop\"></i> Stopped";
		}else if(json[i].status == "done"){
			typeHtml = "election-done";
			backgroundHtml = "done-background";
			statusHtml = "<i class=\"fa fa-check\"></i> Done";
		}else{
			error = true;
			console.error("Undefined Election status: "+json[i].status+" (index="+i+", id="+json[i].id+")");
		}
		
		if(error){
			html = createAlert("danger","An error occured. Please try again or report this to a developer.");
		}else{
			html = "<div id=\"election-"+json[i].id+"\" class=\"section-container\">\n"
			+"\t<div class=\"election-status section-header "+backgroundHtml+" more-parent\">\n"
			+"\t\t<div class=\"pull-left\">\n"
			+"\t\t\t<h4 class=\"bold\">"+json[i].name+" - "+json[i].id+"</h4>\n"
			+"\t\t\t<h5>"+json[i].start+" - "+json[i].end+"</h5>\n"
			+"\t\t</div>\n"
			+"\t\t<div class=\"pull-right\">\n"
			+"\t\t\t<h4>"+statusHtml+"</h4>\n"
			+substatusHtml
			+"\t\t</div>\n"
			+"\t</div>\n"
			+"\t<div id=\"more-election-"+json[i].id+"\" class=\"section-body clear displayNone\">\n"
			+voteHtml
			+"\t</div>\n"
			+"</div>";
		}
		console.log(html);
		$('#elections-container').append(html);
		if(countdown != "false"){
			initializeClock(countdownId, deadline);
		}
	}
	// resetOnClick();
}


var deadline = new Date(Date.parse(new Date()) + 12 * 24 * 60 * 60 * 1000);
initializeClock('clockdiv', deadline);
deadline = new Date(Date.parse(new Date()) + (4 * 24 * 60 * 60 * 1000)+ (35*1000));
initializeClock('clockdiv1', deadline);