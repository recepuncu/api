<?php

function option_value_add(){
	Flight::authorization( Flight::request() );
	global $db;	
	
	try {
		$request = Flight::request();	
		if( $db->has("option_value", array("renk_beden"=>$request->data["renk_beden"])) ){			
			$option_value_id = $db->get("option_value", "option_value_id", array("renk_beden"=>$request->data["renk_beden"]));
		}else{		
			$option_value_id = $db->insert("option_value", array(
				"option_id" => 13,
				"sort_order" => 0,
				"renk_beden" => $request->data["renk_beden"]
			));
			if($option_value_id){
				$db->insert("option_value_description", array(
					"option_value_id" => $option_value_id,
					"language_id" => 1,
					"option_id" => 13,
					"name" => $request->data["renk_beden"]
				));
			}
		}
		$datas = $db->get("option_value", "*", array("option_value_id"=>$option_value_id));
		$result = array('state' => true, 'data'=>$datas, 'Messages' => array() );		
	}
	catch(Exception $e){
		$result = array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) );
	}
		
	Flight::json( $result );	
};