<?php

function product_image_add(){
	Flight::authorization( Flight::request() );
	global $db;	
	
	try {
		$request = Flight::request();
		$filename = $request->data["image"];
		try 
		{
			if((strpos($filename, "http://") !== false) 
				|| (strpos($filename, "https://") !== false)){
					$http_filename = end( @explode('/', $filename) );
					if(!file_exists(DIR_IMAGE_API.DIR_IMAGE_SUFFIX_API . $http_filename)){
						$http_file_content = file_get_contents($filename);
						file_put_contents(DIR_IMAGE_API . $http_filename, $http_file_content);
						$filename = DIR_IMAGE_SUFFIX_API.$http_filename;			
					}
			}		
		}
		catch(Exception $ex) {}
		$last_product_image_id = $db->insert( "product_image", array(
			"product_id" => $request->data["product_id"],
			"image" => $filename
		));
		if($last_product_image_id){
			$result1 = $db->get("product_image", "*", array("product_image_id"=>$last_product_image_id));
			$result = array('state' => true, 'data'=>$result1, 'Messages' => array() );
		}
	}
	catch(Exception $e){
		$result = array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) );
	}
	
	Flight::json( $result );	
};

function product_image_delete($product_image_id){
	
	Flight::authorization( Flight::request() );
	global $db;	
	try {
		$rows = $db->delete( "product_image", array("product_image_id" => $product_image_id));		
		$result = array('state' => false, 'data'=>array(), 'Messages' => '');
		if($rows > 0){
			$result = array('state' => true, 'data'=>array(), 'Messages' => 'Kayıtlar silindi.');
		}
	}
	catch(Exception $e){
		$result = array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) );
	}	
	Flight::json( $result );	
};