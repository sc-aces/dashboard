<?php
	$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
	if(isset($_GET['id'])){
		$html = file_get_contents('http://www.starcitizenaces.org/profile/'.$_GET['id']);
		$start = strpos($html,'<div class="title">Site Tags</div>')+34;
		$end = strpos($html,'<div class="title">Awards</div>')-111;
		$array = explode('<div class="tag">',str_replace("\n","",str_replace("\t", "", substr($html,$start,$end-$start))));

		$tags=array();
		foreach($array as &$val){
			$val = strip_tags($val);
			if(strstr($val, "Council"))
				array_push($tags,"2");
			elseif(strstr($val,"Website Admin"))
				array_push($tags,"1");
			elseif(strstr($val,"Tech Wizard" ))
				array_push($tags,"0");
			else{
				if(!in_array("3",$tags))
					array_push($tags,"3");
			}
		}
		// var_dump($array);
		if(!in_array("3",$tags))
			array_push($tags,"3");
		echo json_encode(array("status"=>implode($tags,",")));
	}else{
		echo json_encode(array("status"=>"error"));
	}
?>