/*
	global $
	global createAlert
	global resetOnClick
	global itemsPerPage
 */

var shipJson;

$(document).ready(function(){
	fetchShips("");
	$('#search-ships').keyup(function(event){
		console.log($(this).val());
		fetchShips($(this).val());
	});
	resetOnClick();
});


function fetchShips(query){
	$.ajax({
		type: 'GET',
		headers: { },
		url: "fetch-ships.php?q="+query,
		 
		success: function (jsonString) {
			shipJson = JSON.parse(jsonString);
			buildShips(0);
		},
		error: function(request, status, error){
			console.error("fetchShip - Status: "+status+", Error: "+error);
		}
	});
}

//Fixes ColorboxUI when dropdown of internal element appears
function acronymColorboxFix(add){
	var height;
	
	if(add){
		height= $('#cboxMiddleRight').css('height').substr(0,$('#cboxMiddleRight').css('height').length-2);
		height = parseInt(height)+34;
		$('#cboxLoadedContent').css('height',height+'px');
		
		height= $('#cboxMiddleRight').css('height').substr(0,$('#cboxMiddleRight').css('height').length-2);
		height = parseInt(height)+34;
		$('#cboxMiddleRight').css('height',height+'px');
		$('#cboxMiddleLeft').css('height',height+'px');
		$('#cboxContent').css('height',height+'px');
		
		height= $('#colorbox').css('height').substr(0,$('#colorbox').css('height').length-2);
		height = parseInt(height)+34;
		$('#colorbox').css('height',height+'px');
		
		height= $('#cboxWrapper').css('height').substr(0,$('#cboxWrapper').css('height').length-2);
		height = parseInt(height)+34;
		$('#cboxWrapper').css('height',height+'px');
	}
	else{
		height= $('#cboxMiddleRight').css('height').substr(0,$('#cboxMiddleRight').css('height').length-2);
		height = parseInt(height)+34;
		$('#cboxLoadedContent').css('height',height+'px');
		
		height= $('#cboxMiddleRight').css('height').substr(0,$('#cboxMiddleRight').css('height').length-2);
		height = parseInt(height)-34;
		$('#cboxMiddleRight').css('height',height+'px');
		$('#cboxMiddleLeft').css('height',height+'px');
		$('#cboxContent').css('height',height+'px');
		
		height= $('#colorbox').css('height').substr(0,$('#colorbox').css('height').length-2);
		height = parseInt(height)-34;
		$('#colorbox').css('height',height+'px');
		
		height= $('#cboxWrapper').css('height').substr(0,$('#cboxWrapper').css('height').length-2);
		height = parseInt(height)-34;
		$('#cboxWrapper').css('height',height+'px');
	}
}

function buildShips(page){
	page = parseInt(page,10);
	var paginationHtml = "";
	var i;
	for(i=0; i<Math.ceil(shipJson.length/itemsPerPage); i++){
		var pageNum = i+1;
		var activeClass = "";
		if(page+1 == pageNum)
			activeClass= " class=\"active\"";
		paginationHtml += "\t<li"+activeClass+"><a class=\"pagination-button\" id=\"pagination-"+i+"\">"+pageNum+"</a></li>\n";
	}
	$('#ship-pagination').html(paginationHtml);
	$('.pagination-button').click(function(event){
		buildShips($(this).attr('id').substring(11));
	});
	
	var loopStop;
	if(itemsPerPage + (itemsPerPage*page) > shipJson.length)
		loopStop = shipJson.length;
	else
		loopStop = itemsPerPage + (itemsPerPage*page);
	var supportPrefix = "../../support/images/dynamicfleet/";
	var html = "";
	if(shipJson.notFound){
		html = createAlert("warning","No ships found!");
	}else{
		var count = 0;
		for(i=page*itemsPerPage; i<loopStop;i++) {
			count++;
			var shipNameShort = shipJson[i].shipName.replace(" ","_").replace("-","__");
			html += "<div id=\"ship-"+shipNameShort+"\" class=\"ship pull-left\">\n"
						+"\t<div class=\"ship-header\">\n"
							+"\t\t<img src=\""+supportPrefix+"shipImages/"+shipJson[i].shipName+".jpg\"/>\n"
							+"\t\t<div id=\"manufacturer-logo-container\"><img class=\"manufacturer-logo\" src=\""+supportPrefix+"manufacIcons/"+shipJson[i].manufacturer+".png\"/></div>"
							+"\t\t<div class=\"ship-info-header\">\n"
								+"\t\t\t<h3>"+shipJson[i].shipName+"</h3>\n"
								+"\t\t\t<p><em>"+shipJson[i].manufacturer+"</em></p>\n"
							+"\t\t</div>\n"
						+"\t</div>\n"
						+"\t<div class=\"ship-info-body\">\n"
							+"\t\t<hr class=\"custom-hr\">\n";
			if(shipJson[i].manufacturerAcronym && shipJson[i].manufacturerAcronym!="NULL")
				html +=	"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Acronym</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+shipJson[i].manufacturerAcronym+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n";
			html +=		"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Role</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+shipJson[i].shipRole+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Crew</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+shipJson[i].shipCrew+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Length</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+shipJson[i].shipLength+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Mass</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+shipJson[i].shipMass+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">URL</div>\n"
								+"\t\t\t<div class=\"col-xs-6\"><a href=\"http://robertsspaceindustries.com/pledge/ships/"+shipJson[i].rsiUrl+"\"><i class=\"fa fa-external-link\"></i></a></div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Price</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+shipJson[i].shipPrice+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"button-container\">\n"
								+"\t\t\t<button class=\"btn btn-warning bold lightbox-btn\" type=\"button\" href=\"#ship-"+shipNameShort+"-edit\"><i class=\"fa fa-pencil-square-o fa-gl\"></i> Edit Ship</button>\n"
								+"\t\t\t<button class=\"btn btn-danger bold lightbox-btn\" type=\"button\" href=\"#ship-"+shipNameShort+"-delete\"><i class=\"fa fa-trash-o fa-gl\"></i> Delete Ship</button>\n"
							+"\t\t</div>\n"
						+"\t</div>\n"
						+"\t<div class=\"displayNone\">\n"
							+"\t\t<div id=\"ship-"+shipNameShort+"-edit\">\n"
								+"\t\t\t<div class=\"edit-ship\">\n"
									+"\t\t\t\t<h4 class=\"text-left\"><span class=\"warning-color\"><i class=\"fa fa-pencil-square-o fa-gl\"></i></span> Edit Ship</h4>\n"
									+"\t\t\t\t<hr>\n"
									+"\t\t\t\t<form action=\"\" id=\"edit-"+shipNameShort+"-ship-form\" class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">\n"
										+"\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"edit-"+shipNameShort+"-ship-form\">\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"name\" class=\"col-sm-2 control-label\">Ship Name</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-space-shuttle fa-rotate-270 fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"name\" placeholder=\"Carrack\" default=\""+shipJson[i].shipName+"\" value=\""+shipJson[i].shipName+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"manufcature\" class=\"col-sm-2 control-label\">manufacturer</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-copyright fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"manufacturer\" placeholder=\"Anvil Aerospace\" default=\""+shipJson[i].manufacturer+"\" value=\""+shipJson[i].manufacturer+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"acronym\" class=\"col-sm-2 control-label\">Acronym</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"checkbox\">\n"
													+"\t\t\t\t\t\t\t\t<label>\n"
														+"\t\t\t\t\t\t\t\t<input class=\"has-acronym\" id=\"has-acronym-"+shipNameShort+"-edit\" name=\"hasAcronym\" type=\"checkbox\"";
															if(shipJson[i].manufacturerAcronym && shipJson[i].manufacturerAcronym!="NULL")
																html += " checked";
			html +=									"> Manufacturer has acronym\n"
													+"\t\t\t\t\t\t\t\t</label>\n"
												+"\t\t\t\t\t\t\t</div>\n"
												+"\t\t\t\t\t\t\t<div id=\"acronym-input-"+shipNameShort+"-container\"";
													if(!shipJson[i].manufacturerAcronym || shipJson[i].manufacturerAcronym=="NULL")
														html += " class=\"displayNone\"";
			html +=							">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group\">\n"
														+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-trademark fa-gl\"></i></div>\n"
														+"\t\t\t\t\t\t\t\t<input id=\"acronym-input-"+shipNameShort+"-edit\" type=\"text\" class=\"form-control\" name=\"acronym\"";
															if(shipJson[i].manufacturerAcronym && shipJson[i].manufacturerAcronym!="NULL")
																html += " value=\""+shipJson[i].manufacturerAcronym+"\" default=\""+shipJson[i].manufacturerAcronym+"\"";
			html +=									" placeholder=\"MISC\">\n"
													+"\t\t\t\t\t\t\t\t</div>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"role\" class=\"col-sm-2 control-label\">Role</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-tag fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"role\" placeholder=\"Exploration\" default=\""+shipJson[i].shipRole+"\" value=\""+shipJson[i].shipRole+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"crew\" class=\"col-sm-2 control-label\">Crew</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-users fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"crew\" placeholder=\"3\" default=\""+shipJson[i].shipCrew+"\" value=\""+shipJson[i].shipCrew+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"length\" class=\"col-sm-2 control-label\">Length</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-arrows-h fa-gl\"></i></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"length\" placeholder=\"63\" default=\""+shipJson[i].shipLength+"\" value=\""+shipJson[i].shipLength+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"mass\" class=\"col-sm-2 control-label\">Mass</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\"><i class=\"fa fa-cube fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"mass\" placeholder=\"25000\" default=\""+shipJson[i].shipMass+"\" value=\""+shipJson[i].shipMass+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"url\" class=\"col-sm-2 control-label\">URL</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-link fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"url\" placeholder=\""+shipJson[i].rsiUrl+"\" default=\""+shipJson[i].rsiUrl+"\" value=\""+shipJson[i].rsiUrl+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"price\" class=\"col-sm-2 control-label\">Price</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-usd fa-gl\"></i></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"price\" placeholder=\"125\" default=\""+shipJson[i].shipPrice+"\" value=\""+shipJson[i].shipPrice+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\""+shipNameShort+"-fileToUpload\" class=\"col-sm-2 control-label\">Image</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t<input class=\"upload\" type=\"file\" name=\"fileToUpload\" id=\""+shipNameShort+"-fileToUpload\">\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											 +"\t\t\t\t\t\t<div class=\"col-sm-offset-2 col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<button form=\"edit-"+shipNameShort+"-ship-form\" class=\"btn btn-warning\" type=\"submit\"><i class=\"fa fa-pencil-square-o fa-gl\"></i> Edit Ship</button>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
									+"\t\t\t\t</form>\n"
								+"\t\t\t</div>\n"
								+"\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you click the edit. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
							+"\t\t</div>\n"
							+"\t\t<div id=\"ship-"+shipNameShort+"-delete\">\n"
								+"\t\t\t<div class=\"delete-ship\">\n"
									+"\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-trash-o fa-gl\"></i></span> Delete Ship</h4>\n"
									+"\t\t\t\t<hr>\n"
									+"\t\t\t\t<form action=\"\" id=\"delete-"+shipNameShort+"-ship-form\" method=\"post\">\n"
										+"\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"delete-"+shipNameShort+"-ship-form\">\n"
										+"\t\t\t\t\t<div class=\"checkbox\">\n"
											+"\t\t\t\t\t\t<label>\n"
												+"\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"deleteShip\">\n"
												+"Completely delete the ship <strong>"+shipJson[i].shipName+"</strong> from database.\n"
											+"\t\t\t\t\t\t</label>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<button form=\"delete-"+shipNameShort+"-ship-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-trash-o fa-gl\"></i> Delete Ship</button>\n"
										+"\t\t\t\t\t</div>\n"
									+"\t\t\t\t</form>\n"
								+"\t\t\t</div>\n"
								+"\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click delete. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
							+"\t\t</div>\n"
						+"\t</div>\n"
					+"</div>\n";
		}
	}
	$('#ship-container').html(html);
	
	// Init colorbox
	$('.lightbox-btn').colorbox({inline:true, maxWidth:"95%", maxHeight:"95%"});
	
	// Reset Input
	$('.reset-input').click(function(event) {
		 $(this).siblings().val($(this).siblings().attr("default"));
		 $(this).removeClass('reset-input-active');
	});
	$('.reset-input').siblings().keyup(function(event){
		$(this).siblings().addClass('reset-input-active');
	});
	
	// Toggle ship acronym
	$('.has-acronym').change(function(event){
		var ident = $(this).attr('id').split('-');
		console.log(ident);
		ident = ident[2];
		console.log(ident);
		if($('#'+$(this).attr('id')).is(':checked')){
			console.log('#acronym-input-'+ident+'-container');
			$('#acronym-input-'+ident+'-container').slideDown("slow", function() {});
			$('#acronym-input-'+ident).attr("required",true);
			acronymColorboxFix(true);
		}
		else{
			$('#acronym-input-'+ident+'-container').slideUp("slow", function() {});
			$('#acronym-input-'+ident).attr("required",false);
			acronymColorboxFix(false);
		}
	});
}