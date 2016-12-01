<?php

function product_option_value_add(){
	Flight::authorization( Flight::request() );
	global $db;	
	
	$result = NULL;
	try {
		$request = Flight::request();
		
		if( $db->has("product_option_value", array("seller_stock_code" => $request->data["seller_stock_code"])) ){
			Flight::json( array('state' => false, 'data'=>(array)$request->data, 'Messages' => array('db'=>$db->error(), 'ex'=>'Aynı seller stock code ile kayıtlı başka bir ürün bulunuyor.')) );
			exit(200);
		}		

		$product_option_id = "";
		$has_product_option = $db->has( "product_option", 
			array("AND" => 
				array(
					"product_id" => $request->data["product_id"], 
					"option_id" => $request->data["option_id"]
				)
			)
		);
		if(!$has_product_option){
			$product_option_id = $db->insert( "product_option", array(
				"product_id" => $request->data["product_id"],
				"option_id" => $request->data["option_id"],
				"value" => $request->data["option_description"],
				"required" => $request->data["option_required"]
			));
		}else{
			$product_option_id = $db->get( "product_option", "product_option_id", array("AND" => 
				array(
					"product_id" => $request->data["product_id"], 
					"option_id" => $request->data["option_id"]
				)
			));
			
		}
		
		if(!empty($product_option_id)){
			$last_product_option_value = $db->insert( "product_option_value", array(
				"product_option_id" => $product_option_id,
				"product_id" => $request->data["product_id"],
				"option_id" => $request->data["option_id"],
				"option_value_id" => $request->data["option_value_id"],
				"quantity" => $request->data["quantity"],
				"subtract" => $request->data["subtract"],
				"price" => $request->data["price"],
				"price_prefix" => $request->data["price_prefix"],
				"points" => $request->data["points"],
				"points_prefix" => $request->data["points_prefix"],
				"weight" => $request->data["weight"],
				"weight_prefix" => $request->data["weight_prefix"],
				"seller_stock_code" => $request->data["seller_stock_code"]
			));			
			if(intval($last_product_option_value)>0){
				$result_query = $db->query("SELECT od.name option_description, ovd.name option_value_description, pov.* 
										FROM ".DB_PREFIX."product_option_value pov
										JOIN ".DB_PREFIX."option_description od ON od.option_id = pov.option_id 
										JOIN ".DB_PREFIX."option_value_description ovd ON ovd.option_value_id = pov.option_value_id
										WHERE pov.product_option_value_id = " . $last_product_option_value);
				if($result_query){
					$results = $result_query->fetchAll(PDO::FETCH_ASSOC);					
					$result = array('state' => true, 'data'=>$results[0], 'Messages' => array('db'=>$db->error(), 'ex'=>NULL) );
				}
			}else{
				$result = array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL) );
			}	
		}
	}
	catch(Exception $e){
		$result = array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) );
	}
	
	Flight::json( $result );
};

function product_option_value_delete($product_option_value_id){
	
	Flight::authorization( Flight::request() );
	global $db;	
	try {
		$rows = $db->delete( "product_option_value", array("product_option_value_id" => $product_option_value_id));		
		$result = array('state' => false, 'code'=>404, 'data'=>array(), 'Messages' => ( $rows < 1 ? 'Ürün seçeneği bulunamadı.' : '' ));
		if($rows > 0){
			$result = array('state' => true, 'code'=>200, 'data'=>array(), 'Messages' => 'Kayıtlar silindi.');
		}
	}
	catch(Exception $e){
		$result = array('state' => false, 'code'=>500, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) );
	}
	Flight::json( $result );	
};

function product_option_value_update_quantities(){	
	Flight::authorization( Flight::request() );
	global $db;
	
	$request = Flight::request();
	
	$request_data = unserialize($request->data["data"]);

	try {
		$updated_seller_stock_codes = array();
		foreach($request_data as $data){
			$updated = $db->update( "product_option_value", array(
				"quantity" => $data["quantity"]
			), array("seller_stock_code" => $data["seller_stock_code"]));
			if($updated){
				$updated_seller_stock_codes[] = $data["seller_stock_code"];
			}
		}
		$result = $db->select("product_option_value", "*", array("seller_stock_code"=>$updated_seller_stock_codes));
		Flight::json( array('state' => true, 'data'=>$result, 'Messages' => array() ) );
	}
	catch(Exception $e){
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) ) );
	}	
};

function product_option_value_update_price(){	
	Flight::authorization( Flight::request() );
	global $db;
	
	$request = Flight::request();
	
	$request_data = unserialize($request->data["data"]);

	try {
		$updated_seller_stock_codes = array();
		foreach($request_data as $data){
			$updated = $db->update( "product_option_value", array(
				"price" => $data["price"]
			), array("seller_stock_code" => $data["seller_stock_code"]));
			if($updated){
				$updated_seller_stock_codes[] = $data["seller_stock_code"];
			}
		}
		$result = $db->select("product_option_value", "*", array("seller_stock_code"=>$updated_seller_stock_codes));
		Flight::json( array('state' => true, 'data'=>$result, 'Messages' => array() ) );
	}
	catch(Exception $e){
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) ) );
	}	
};