/*
	global $
	global createAlert
	global initializeClock
	global resetOnClick
	global months
	global itemsPerPage
*/

var electionJson;

$(document).ready(function(event){
	fetchElections("");
	$('#search-elections').keyup(function(event){
		console.log($(this).val());
		fetchElections("?q="+$(this).val());
	});
	var currentDateUtc = new Date();
	currentDateUtc.setTime(currentDateUtc.getTime()+currentDateUtc.getTimezoneOffset()*60*1000);
	var currentDateUtcString = currentDateUtc.getFullYear()+"-"+(currentDateUtc.getMonth()+1)+"-"+currentDateUtc.getDate();
	// $('#election-start').attr("value",currentDateUtcString);
	
	$('#election-datepicker').datepicker({
		format: "yyyy/mm/dd",
		todayBtn: "linked",
		clearBtn: true,
		autoclose: true,
		todayHighlight: true,
		startDate: "+0d",
		endDate: "+1y"
	});
});

function fetchElections(query){
	$.ajax({
		type: 'GET',
		url: 'fetch-elections.php'+query,
		success: function (jsonString) {
			electionJson = JSON.parse(jsonString);
			buildElections(0);
		},
		error: function(request, status, error){
			console.error("fetchShip - Status: "+status+", Error: "+error);
		}
	});
}

function buildElections(page){
	var clocks = [];
	var html = "";
	var finalHtml = "";
		page = parseInt(page,10);
	var paginationHtml = "";
	var i;
	for(i=0; i<Math.ceil(electionJson.length/itemsPerPage); i++){
		var pageNum = i+1;
		var activeClass = "";
		if(page+1 == pageNum)
			activeClass= " class=\"active\"";
		paginationHtml += "\t<li"+activeClass+"><a class=\"pagination-button\" id=\"pagination-"+i+"\">"+pageNum+"</a></li>\n";
	}
	$('#ship-pagination').html(paginationHtml);
	$('.pagination-button').click(function(event){
		buildElections($(this).attr('id').substring(11));
	});
	
	var loopStop;
	if(itemsPerPage + (itemsPerPage*page) > electionJson.length)
		loopStop = electionJson.length;
	else
		loopStop = itemsPerPage + (itemsPerPage*page);
	if(electionJson[0].status == "error"){
		finalHtml = createAlert("warning","No elections found!");
	}else{
		console.log("loopstop: "+loopStop);
		console.log(electionJson);
		for(i=page*itemsPerPage; i<loopStop;i++){
			console.log(electionJson[i]);
			var error = false;
			
			if(electionJson[i].election_generic.length > 0){
				var voteHtml = "\t\t<h4 class=\"clear\">Results</h4>\n"
						+"\t\t<hr class=\"custom-hr increase-margins clear\">\n"
						+"\t\t<div class=\"election-result-container\">\n";
				var election_genericArray = electionJson[i].election_generic;
				for(var j=0; j<election_genericArray.length; j++){
					voteHtml += "\t\t\t<div id=\""+election_genericArray[j].id+"-"+election_genericArray[j].election_id+"\" class=\"election-result\">\n"
							+"\t\t\t\t<h5 class=\"text-center\">"+election_genericArray[j].name+"</h5>\n";
					election_genericArray[j].candidates = JSON.parse(electionJson[i].election_generic[j].candidates);
					var candidatesArray = election_genericArray[j].candidates.candidates;
					if(candidatesArray[0] != null){
						var totalVotes = electionJson[i].election_generic[j].candidates.totalVotes;
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
					
					electionJson[i].election_generic[j].voters = JSON.parse(electionJson[i].election_generic[j].voters);
				}
				voteHtml += "\t\t</div>\n";
			}
			
			var controlsPopupHtml = "";
			var controlsHtml = "";
			var backgroundHtml = "";
			var statusHtml = "";
			var substatusHtml = "";
			var typeHtml = "";
			var countdown = false;
			var deadline;
			
			var currentDateUtc = new Date();
			currentDateUtc.setTime(currentDateUtc.getTime()-currentDateUtc.getTimezoneOffset()*60*1000);
		
			var startDate =new Date(electionJson[i].start);
			startDate.setTime(startDate.getTime()-startDate.getTimezoneOffset()*60*1000);
			
			var endDate = new Date(electionJson[i].end);
			endDate.setTime(endDate.getTime()-endDate.getTimezoneOffset()*60*1000);
			
			var dateString = startDate.getDate()+" "+months[startDate.getMonth()]+", "+startDate.getFullYear()+" - "+endDate.getDate()+" "+months[endDate.getMonth()]+", "+endDate.getFullYear();
			
			if(electionJson[i].status == "upcoming"){
			backgroundHtml = "info-background";
			statusHtml = "<i class=\"fa fa-info\"></i> Upcoming";
			countdown = true;
			substatusHtml = "\t\t\t<div class=\"text-clock\" title=\"Time remaining until election\" id=\"clock-"+electionJson[i].id+"\">\n"
							+"\t\t\t\t<i class=\"fa fa-clock-o\"></i>\n"
							+"\t\t\t\t<span class=\"days\"></span>d\n"
							+"\t\t\t\t<span class=\"hours\"></span>h\n"
							+"\t\t\t\t<span class=\"minutes\"></span>m\n"
							+"\t\t\t\t<span class=\"seconds\"></span>s\n"
							+"\t\t\t</div>\n";
			controlsHtml = "\t\t\t<button id=\"editElection-button\" class=\"btn btn-warning bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_edit\"><i class=\"fa fa-pencil-square-o\"></i> Edit Election</button>"
							+"\t\t\t<button id=\"deleteElection-button\" class=\"btn btn-danger bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_delete\">"
							+"<i class=\"fa fa-times\"></i> Delete Election</button>\n"
							+"\t\t\t<button id=\"startElection-button\" class=\"btn btn-success bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_start\">"
							+"<i class=\"fa fa-play\"></i> Start Election</button>\n";
			controlsPopupHtml = "\t\t\t<div id=\"election-"+electionJson[i].id+"_edit\">\n"
								+"\t\t\t\t<div class=\"edit-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"warning-color\"><i class=\"fa fa-pencil-square-o fa-gl\"></i></span> Edit Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"edit_-"+electionJson[i].id+"-election-form\" class=\"form-horizontal\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"edit_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"inline-form-column\">\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label for=\"name\" class=\"col-sm-2 control-label\">Name</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<div class=\"input-group\">\n"
								+"\t\t\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\">\n"
								+"\t\t\t\t\t\t\t\t\t\t\t<i class=\"fa fa-pencil fa-gl\"></i>\n"
								+"\t\t\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"name\" placeholder=\"Election Name\" default=\""+electionJson[i].name+"\" value=\""+electionJson[i].name+"\" required=\"\">\n"
								+"\t\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label for=\"election-description\" class=\"col-sm-2 control-label\">Description</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<textarea class=\"form-control\" id=\"description\" form=\"edit_-"+electionJson[i].id+"-election-form\" name=\"description\" placeholder=\"Description of election...\">"+electionJson[i].description+"</textarea>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">Date</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<div class=\"input-daterange input-group\" id=\"election-datepicker-"+electionJson[i].id+"\">\n"
								+"\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"input-sm form-control\" value=\""+electionJson[i].start.substring(0,10)+"\" name=\"start\">\n" 
								+"\t\t\t\t\t\t\t\t\t\t<span class=\"input-group-addon\">to</span>\n"
								+"\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"input-sm form-control\" value=\""+electionJson[i].end.substring(0,10)+"\" name=\"end\">\n" 
								+"\t\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label for=\"election-tags\" class=\"col-sm-2 control-label\">Tags</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<textarea class=\"form-control\" id=\"election-tags\" form=\"edit_-"+electionJson[i].id+"-election-form\" name=\"tags\" placeholder=\"Enter the user tags that can vote and view this election (CSV)...\">"+electionJson[i].access_tags+"</textarea>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-offset-2 col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<button form=\"edit_-"+electionJson[i].id+"-election-form\" class=\"btn btn-warning\" type=\"submit\"><i class=\"fa fa-pencil-square-o fa-gl\"></i> Edit Election</button>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n"
								+"\t\t\t<div id=\"election-"+electionJson[i].id+"_delete\">\n"
								+"\t\t\t\t<div class=\"delete-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-times fa-gl\"></i></span> Delete Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"delete_-"+electionJson[i].id+"-election-form\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"delete_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"checkbox\">\n"
								+"\t\t\t\t\t\t\t<label>\n"
								+"\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"commitChange\">\n"
								+"\t\t\t\t\t\t\t\tDelete the election: <strong>"+electionJson[i].name+" (ID: "+electionJson[i].id+")</strong>\n"
								+"\t\t\t\t\t\t\t</label>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t<button form=\"delete_-"+electionJson[i].id+"-election-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-times fa-gl\"></i> Delete Election</button>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n"
								+"\t\t\t<div id=\"election-"+electionJson[i].id+"_start\">\n"
								+"\t\t\t\t<div class=\"start-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"success-color\"><i class=\"fa fa-play fa-gl\"></i></span> Manually Start Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"start_-"+electionJson[i].id+"-election-form\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"start_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"checkbox\">\n"
								+"\t\t\t\t\t\t\t<label>\n"
								+"\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"commitChange\">\n"
								+"\t\t\t\t\t\t\t\tManually Start the election: <strong>"+electionJson[i].name+" (ID: "+electionJson[i].id+")</strong>\n"
								+"\t\t\t\t\t\t\t</label>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t<button form=\"start_-"+electionJson[i].id+"-election-form\" class=\"btn btn-success\" type=\"submit\"><i class=\"fa fa-play fa-gl\"></i> Start Election</button>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n";
			deadline = startDate;
			}else if(electionJson[i].status == "active"){
			backgroundHtml = "success-background";
			statusHtml = "<i class=\"fa fa-circle-o-notch fa-spin\"></i> Active";
			countdown = true;
			substatusHtml = "\t\t\t<div class=\"text-clock\" title=\"Time remaining in election\" id=\"clock-"+electionJson[i].id+"\">\n"
							+"\t\t\t\t<i class=\"fa fa-clock-o\"></i>\n"
							+"\t\t\t\t<span class=\"days\"></span>d\n"
							+"\t\t\t\t<span class=\"hours\"></span>h\n"
							+"\t\t\t\t<span class=\"minutes\"></span>m\n"
							+"\t\t\t\t<span class=\"seconds\"></span>s\n"
							+"\t\t\t</div>\n";
			controlsHtml = "\t\t\t<button id=\"editElection-button\" class=\"btn btn-warning bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_edit\"><i class=\"fa fa-pencil-square-o\"></i> Edit Election</button>"
							+"\t\t\t<button id=\"deleteElection-button\" class=\"btn btn-danger bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_delete\">"
							+"<i class=\"fa fa-times\"></i> Delete Election</button>\n"
							+"\t\t\t<button id=\"stopElection-button\" class=\"btn btn-danger bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_stop\">"
							+"<i class=\"fa fa-stop\"></i> Stop Election</button>\n";
			controlsPopupHtml = "\t\t\t<div id=\"election-"+electionJson[i].id+"_edit\">\n"
								+"\t\t\t\t<div class=\"edit-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"warning-color\"><i class=\"fa fa-pencil-square-o fa-gl\"></i></span> Edit Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"edit_-"+electionJson[i].id+"-election-form\" class=\"form-horizontal\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"edit_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"inline-form-column\">\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label for=\"name\" class=\"col-sm-2 control-label\">Name</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<div class=\"input-group\">\n"
								+"\t\t\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\">\n"
								+"\t\t\t\t\t\t\t\t\t\t\t<i class=\"fa fa-pencil fa-gl\"></i>\n"
								+"\t\t\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"name\" placeholder=\"Election Name\" default=\""+electionJson[i].name+"\" value=\""+electionJson[i].name+"\" required=\"\">\n"
								+"\t\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label for=\"election-description\" class=\"col-sm-2 control-label\">Description</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<textarea class=\"form-control\" id=\"description\" form=\"edit_-"+electionJson[i].id+"-election-form\" name=\"description\" placeholder=\"Description of election...\">"+electionJson[i].description+"</textarea>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label class=\"col-sm-2 control-label\">Date</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<div class=\"input-daterange input-group\" id=\"election-datepicker-"+electionJson[i].id+"\">\n"
								+"\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"input-sm form-control\" value=\""+electionJson[i].start.substring(0,10)+"\" name=\"start\">\n" 
								+"\t\t\t\t\t\t\t\t\t\t<span class=\"input-group-addon\">to</span>\n"
								+"\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"input-sm form-control\" value=\""+electionJson[i].end.substring(0,10)+"\" name=\"end\">\n" 
								+"\t\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<label for=\"election-tags\" class=\"col-sm-2 control-label\">Tags</label>\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<textarea class=\"form-control\" id=\"election-tags\" form=\"edit_-"+electionJson[i].id+"-election-form\" name=\"tags\" placeholder=\"Enter the user tags that can vote and view this election (CSV)...\">"+electionJson[i].access_tags+"</textarea>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t\t<div class=\"col-sm-offset-2 col-sm-10\">\n"
								+"\t\t\t\t\t\t\t\t\t<button form=\"edit_-"+electionJson[i].id+"-election-form\" class=\"btn btn-warning\" type=\"submit\"><i class=\"fa fa-pencil-square-o fa-gl\"></i> Edit Election</button>\n"
								+"\t\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n"
								+"\t\t\t<div id=\"election-"+electionJson[i].id+"_delete\">\n"
								+"\t\t\t\t<div class=\"delete-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-times fa-gl\"></i></span> Delete Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"delete_-"+electionJson[i].id+"-election-form\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"delete_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"checkbox\">\n"
								+"\t\t\t\t\t\t\t<label>\n"
								+"\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"commitChange\">\n"
								+"\t\t\t\t\t\t\t\tDelete the election: <strong>"+electionJson[i].name+" (ID: "+electionJson[i].id+")</strong>\n"
								+"\t\t\t\t\t\t\t</label>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t<button form=\"delete_-"+electionJson[i].id+"-election-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-times fa-gl\"></i> Delete Election</button>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n"
								+"\t\t\t<div id=\"election-"+electionJson[i].id+"_stop\">\n"
								+"\t\t\t\t<div class=\"stop-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-stop fa-gl\"></i></span> Manually Stop Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"stop_-"+electionJson[i].id+"-election-form\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"stop_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"checkbox\">\n"
								+"\t\t\t\t\t\t\t<label>\n"
								+"\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"commitChange\">\n"
								+"\t\t\t\t\t\t\t\tManually Stop the election: <strong>"+electionJson[i].name+" (ID: "+electionJson[i].id+")</strong>\n"
								+"\t\t\t\t\t\t\t</label>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t<button form=\"stop_-"+electionJson[i].id+"-election-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-stop fa-gl\"></i> Stop Election</button>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n";
			deadline = endDate;
			}else if(electionJson[i].status == "deleted"){
			typeHtml = " election-deleted";
			backgroundHtml = "danger-background";
			statusHtml = "<i class=\"fa fa-info\"></i> Deleted";
			substatusHtml = "\t\t\t<h5><i class=\"fa fa-calendar-o\"></i> "+electionJson[i].date_deleted+"</h5>\n";
			controlsHtml = "\t\t\t<button id=\"restoreElection-button\" class=\"btn btn-success bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_restore\">"
							+"<i class=\"fa fa-arrow-up\"></i> Restore Election</button>\n";
			controlsPopupHtml = "\t\t\t<div id=\"election-"+electionJson[i].id+"_restore\">\n"
								+"\t\t\t\t<div class=\"restore-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"success-color\"><i class=\"fa fa-arrow-up fa-gl\"></i></span> Restore Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"restore_-"+electionJson[i].id+"-election-form\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"restore_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"checkbox\">\n"
								+"\t\t\t\t\t\t\t<label>\n"
								+"\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"commitChange\">\n"
								+"\t\t\t\t\t\t\t\tRestore the election: <strong>"+electionJson[i].name+" (ID: "+electionJson[i].id+")</strong>\n"
								+"\t\t\t\t\t\t\t</label>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t<button form=\"restore_-"+electionJson[i].id+"-election-form\" class=\"btn btn-success\" type=\"submit\"><i class=\"fa fa-arrow-up fa-gl\"></i> Restore Election</button>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n";
			}else if(electionJson[i].status == "stopped"){
			backgroundHtml = "danger-background";
			statusHtml = "<i class=\"fa fa-stop\"></i> Stopped";
			substatusHtml = "\t\t\t<h5><i class=\"fa fa-calendar-o\"></i> "+electionJson[i].last_status_date+"</h5>\n";
			controlsHtml = "\t\t\t<button id=\"deleteElection-button\" class=\"btn btn-danger bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_delete\">"
							+"<i class=\"fa fa-times\"></i> Delete Election</button>\n";
			controlsPopupHtml = "\t\t\t<div id=\"election-"+electionJson[i].id+"_delete\">\n"
								+"\t\t\t\t<div class=\"delete-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-times fa-gl\"></i></span> Delete Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"delete_-"+electionJson[i].id+"-election-form\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"delete_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"checkbox\">\n"
								+"\t\t\t\t\t\t\t<label>\n"
								+"\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"commitChange\">\n"
								+"\t\t\t\t\t\t\t\tDelete the election: <strong>"+electionJson[i].name+" (ID: "+electionJson[i].id+")</strong>\n"
								+"\t\t\t\t\t\t\t</label>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t<button form=\"delete_-"+electionJson[i].id+"-election-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-times fa-gl\"></i> Delete Election</button>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n";
			}else if(electionJson[i].status == "done"){
			typeHtml = " election-done";
			backgroundHtml = "done-background";
			statusHtml = "<i class=\"fa fa-check\"></i> Done";
			substatusHtml = "\t\t\t<h5><i class=\"fa fa-calendar-o\"></i> "+electionJson[i].last_status_date+"</h5>\n";
			controlsHtml = "\t\t\t<button id=\"deleteElection-button\" class=\"btn btn-danger bold lightbox-btn\" type=\"button\" href=\"#election-"+electionJson[i].id+"_delete\">"
							+"<i class=\"fa fa-times\"></i> Delete Election</button>\n";
			controlsPopupHtml = "\t\t\t<div id=\"election-"+electionJson[i].id+"_delete\">\n"
								+"\t\t\t\t<div class=\"delete-election\">\n"
								+"\t\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-times fa-gl\"></i></span> Delete Election</h4>\n"
								+"\t\t\t\t\t<hr>\n"
								+"\t\t\t\t\t<form action=\"\" id=\"delete_-"+electionJson[i].id+"-election-form\" method=\"post\">\n"
								+"\t\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"delete_-"+electionJson[i].id+"-election-form\">\n"
								+"\t\t\t\t\t\t<div class=\"checkbox\">\n"
								+"\t\t\t\t\t\t\t<label>\n"
								+"\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"commitChange\">\n"
								+"\t\t\t\t\t\t\t\tDelete the election: <strong>"+electionJson[i].name+" (ID: "+electionJson[i].id+")</strong>\n"
								+"\t\t\t\t\t\t\t</label>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t\t<div class=\"form-group\">\n"
								+"\t\t\t\t\t\t\t<button form=\"delete_-"+electionJson[i].id+"-election-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-times fa-gl\"></i> Delete Election</button>\n"
								+"\t\t\t\t\t\t</div>\n"
								+"\t\t\t\t\t</form>\n"
								+"\t\t\t\t</div>\n"
								+"\t\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
								+"\t\t\t</div>\n";
			}else{
				error = true;
				console.error("Undefined Election status: "+electionJson[i].status+" (index="+i+", id="+electionJson[i].id+")");
			}
			
			if(error){
				html = createAlert("danger","An error occured. Please try again or report this to a developer.");
			}else{
				html = "<div id=\"election-"+electionJson[i].id+"\" class=\"section-container "+typeHtml+"\">\n"
						+"\t<div class=\"election-status section-header "+backgroundHtml+" more-dynamic-parent\">\n"
						+"\t\t<div class=\"pull-left\">\n"
						+"\t\t\t<h4 class=\"bold\">"+electionJson[i].name+" - "+electionJson[i].id+"</h4>\n"
						+"\t\t\t<h5>"+dateString+"</h5>\n"
						+"\t\t</div>\n"
						+"\t\t<div class=\"pull-right\">\n"
						+"\t\t\t<h4>"+statusHtml+"</h4>\n"
						+substatusHtml
						+"\t\t</div>\n"
						+"\t</div>\n"
						+"\t<div id=\"more-dynamic-election-"+electionJson[i].id+"\" class=\"section-body clear displayNone\">\n"
						+voteHtml
						+"\t\t<h4>Election Controls</h4>\n"
						+"\t\t<hr class=\"custom-hr\">\n"
						+"\t\t<div class=\"election-controls\">\n"
						+controlsHtml
						+"\t\t</div>\n"
						+"\t\t<div class=\"displayNone\">\n"
						+controlsPopupHtml
						+"\t\t</div>\n"
					 	+"\t</div>\n"
						+"</div>\n";
			}
			// console.log(html);
			if(electionJson)
				finalHtml += html;
			if(countdown){
				clocks.push("clock-"+electionJson[i].id);
				clocks.push(deadline);
			}
		}
		
	}
	$('#elections-container').html(finalHtml);
	var j=0;
	while(j<clocks.length){
		initializeClock(clocks[j], clocks[j+1]);
		j += 2;
	}
	resetOnClick();
	$('.lightbox-btn').colorbox({inline:true, maxWidth:"95%", maxHeight:"95%"});
}