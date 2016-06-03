<?php
	$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
	if(isset($_GET['id'])){
		$html = file_get_contents('http://www.starcitizenaces.org/profile/'.$_GET['id']);
		$start = strpos($html,'<span class="cover_header_name_text">')+37;
		// var_dump($start);
		$count =0;
		$name = "";
		while(substr($html,$start,1) != '<'){
			if(substr($html,$start,1) != "\t" && substr($html,$start,1) != "\n"  && ord(substr($html,$start,1)) != 13){
				$name .= substr($html,$start,1);
			}
			$start++;
			if($count > 200){
				echo json_encode(array("status"=>"error"));
				break;
			}
			$count++;
		}
		$array = array("status"=>"success","username"=>$name);
		// var_dump($array);
		echo json_encode($array);
	}else{
		echo json_encode(array("status"=>"error"));
	}
?>