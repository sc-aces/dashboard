/*global $*/
/*global createAlert*/

$(document).ready(function(){
	// fetchShips("");
	$('#search-ships').keyup(function(event){
		console.log($(this).val());
		fetchShips($(this).val());
	});
	
	$('.has-acronym').change(function(event){
		var ident = $(this).attr('id').split('-');
		log(ident);
		ident = ident[2];
		log(ident);
		if($('#'+$(this).attr('id')).is(':checked')){
			log('#acronym-input-'+ident+'-container');
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
});


function fetchShips(query){
	$.ajax({
		type: 'GET',
		headers: { },
		url: "fetch-ships.php?q="+query,
		 
		success: function (jsonString) {
			buildShips(JSON.parse(jsonString));
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

function buildShips(json){
	console.log(json);
	var supportPrefix = "../../../support/";
	var html = "";
	if(json.notFound){
		html = createAlert("warning","No ships found!");
	}else{
		for(var i=0; i<json.length;i++) {
			var shipNameShort = json[i].shipName.replace(" ","_").replace("-","__");
			html += "<div id=\"ship-"+shipNameShort+"\" class=\"ship pull-left\">\n"
						+"\t<div class=\"ship-header\">\n"
							+"\t\t<img src=\""+supportPrefix+"images/shipImages/"+json[i].shipName+".jpg\"/>\n"
							+"\t\t<div id=\"manufacturer-logo-container\"><img class=\"manufacturer-logo\" src=\""+supportPrefix+"images/manufacIcons/"+json[i].manufacturer+".png\"/></div>"
							+"\t\t<div class=\"ship-info-header\">\n"
								+"\t\t\t<h3>"+json[i].shipName+"</h3>\n"
								+"\t\t\t<p><em>"+json[i].manufacturer+"</em></p>\n"
							+"\t\t</div>\n"
						+"\t</div>\n"
						+"\t<div class=\"ship-info-body\">\n"
							+"\t\t<hr class=\"custom-hr\">\n";
			if(json[i].manufacturerAcronym && json[i].manufacturerAcronym!="NULL")				
				html +=	"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Acronym</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+json[i].manufacturerAcronym+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n";
			html +=		"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Role</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+json[i].shipRole+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Crew</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+json[i].shipCrew+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Length</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+json[i].shipLength+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Mass</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+json[i].shipMass+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">URL</div>\n"
								+"\t\t\t<div class=\"col-xs-6\"><a href=\"http://robertsspaceindustries.com/pledge/ships/"+json[i].rsiUrl+"\"><i class=\"fa fa-external-link\"></i></a></div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"row\">\n"
								+"\t\t\t<div class=\"col-xs-3\">Price</div>\n"
								+"\t\t\t<div class=\"col-xs-6\">"+json[i].shipPrice+"</div>\n"
							+"\t\t</div>\n"
							+"\t\t<hr class=\"custom-hr\">\n"
							+"\t\t<div class=\"button-container\">\n"
								+"\t\t\t<button class=\"btn btn-warning bold lightbox-btn cboxElement\" type=\"button\" href=\"#ship-"+shipNameShort+"-edit\"><i class=\"fa fa-pencil-square-o fa-gl\"></i> Edit Ship</button>\n"
								+"\t\t\t<button class=\"btn btn-danger bold lightbox-btn cboxElement\" type=\"button\" href=\"#ship-"+shipNameShort+"-delete\"><i class=\"fa fa-times fa-gl\"></i> Delete Ship</button>\n"
							+"\t\t</div>\n"
						+"\t</div>\n"
						+"\t<div class=\"displayNone\">\n"
							+"\t\t<div id=\"ship-"+shipNameShort+"-edit\">\n"
								+"\t\t\t<div class=\"edit-ship\">\n"
									+"\t\t\t\t<h4 class=\"text-left\"><span class=\"warning-color\"><i class=\"fa fa-pencil-square-o fa-gl\"></i></span> Edit Ship</h4>\n"
									+"\t\t\t\t<hr>\n"
									+"\t\t\t\t<form action=\"\" id=\"edit-"+shipNameShort+"-ship-form\" class=\"form-horizontal\" method=\"post\">\n"
										+"\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"edit-"+shipNameShort+"-ship-form\">\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"name\" class=\"col-sm-2 control-label\">Ship Name</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-space-shuttle fa-rotate-270 fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"name\" placeholder=\"Carrack\" default=\""+json[i].shipName+"\" value=\""+json[i].shipName+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"manufcature\" class=\"col-sm-2 control-label\">manufacturer</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-copyright fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"manufacturer\" placeholder=\"Anvil Aerospace\" default=\""+json[i].manufacturer+"\" value=\""+json[i].manufacturer+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"acronym\" class=\"col-sm-2 control-label\">Acronym</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"checkbox\">\n"
													+"\t\t\t\t\t\t\t\t<label>\n"
														+"\t\t\t\t\t\t\t\t<input class=\"has-acronym\" id=\"has-acronym-"+shipNameShort+"-edit\" name=\"hasAcronym\" type=\"checkbox\"";
															if(json[i].manufacturerAcronym && json[i].manufacturerAcronym!="NULL")
																html += " checked";
			html +=									"> manufacturer has acronym\n"
													+"\t\t\t\t\t\t\t\t</label>\n"
												+"\t\t\t\t\t\t\t</div>\n"
												+"\t\t\t\t\t\t\t<div id=\"acronym-input-"+shipNameShort+"-edit-container\"";
													if(!json[i].manufacturerAcronym || json[i].manufacturerAcronym=="NULL")
														html += " class=\"displayNone\"";
			html +=							">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group\">\n"
														+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-trademark fa-gl\"></i></div>\n"
														+"\t\t\t\t\t\t\t\t<input id=\"acronym-input-"+shipNameShort+"-edit\" type=\"text\" class=\"form-control\" name=\"acronym\"";
															if(json[i].manufacturerAcronym && json[i].manufacturerAcronym!="NULL")
																html += " value=\""+json[i].manufacturerAcronym+"\" default=\""+json[i].manufacturerAcronym+"\"";
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
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"role\" placeholder=\"Exploration\" default=\""+json[i].shipRole+"\" value=\""+json[i].shipRole+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"crew\" class=\"col-sm-2 control-label\">Crew</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-users fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"crew\" placeholder=\"3\" default=\""+json[i].shipCrew+"\" value=\""+json[i].shipCrew+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"length\" class=\"col-sm-2 control-label\">Length</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-arrows-h fa-gl\"></i></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"length\" placeholder=\"63\" default=\""+json[i].shipLength+"\" value=\""+json[i].shipLength+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"mass\" class=\"col-sm-2 control-label\">Mass</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\"><i class=\"fa fa-cube fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"mass\" placeholder=\"25000\" default=\""+json[i].shipMass+"\" value=\""+json[i].shipMass+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"url\" class=\"col-sm-2 control-label\">URL</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-link fa-gl\"></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"url\" placeholder=\""+json[i].rsiUrl+"\" default=\""+json[i].rsiUrl+"\" value=\""+json[i].rsiUrl+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
											+"\t\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<label for=\"price\" class=\"col-sm-2 control-label\">Price</label>\n"
											+"\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												+"\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													+"\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-usd fa-gl\"></i></i></div>\n"
													+"\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"price\" placeholder=\"125\" default=\""+json[i].shipPrice+"\" value=\""+json[i].shipPrice+"\" required>\n"
												+"\t\t\t\t\t\t\t</div>\n"
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
									+"\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-times fa-gl\"></i></span> Delete Ship</h4>\n"
									+"\t\t\t\t<hr>\n"
									+"\t\t\t\t<form action=\"\" id=\"delete-"+shipNameShort+"-ship-form\" method=\"post\">\n"
										+"\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"delete-"+shipNameShort+"-ship-form\">\n"
										+"\t\t\t\t\t<div class=\"checkbox\">\n"
											+"\t\t\t\t\t\t<label>\n"
												+"\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"deleteShip\">\n"
												+"Completely delete the ship <strong>"+json[i].shipName+"</strong> from database.\n"
											+"\t\t\t\t\t\t</label>\n"
										+"\t\t\t\t\t</div>\n"
										+"\t\t\t\t\t<div class=\"form-group\">\n"
											+"\t\t\t\t\t\t<button form=\"delete-"+shipNameShort+"-ship-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-times fa-gl\"></i> Delete Ship</button>\n"
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
}