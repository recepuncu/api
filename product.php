<?php

function get_product_by_id($product_id){
	Flight::authorization( Flight::request() );
	global $db;
	
	$sql = "SELECT op.*, 
			opd.language_id, opd.name, opd.description, opd.tag
			,opd.meta_description, opd.meta_keyword 
			FROM ".DB_PREFIX."product op
			LEFT JOIN ".DB_PREFIX."product_description opd ON opd.product_id = op.product_id
			WHERE op.product_id = " . $product_id;
	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : ";";

	$datas = $db->query($sql);
	if($datas){
		$results = array();
		foreach($datas->fetchAll(PDO::FETCH_ASSOC) as $data){
			
			$product_options = array();
			$sub_datas = $db->query("SELECT popval.*, variants.option_description, variants.option_value_description
										FROM ".DB_PREFIX."product_option_value popval
										JOIN (
											SELECT opval.option_id, options.name option_description, opval.option_value_id, opvaldesc.name option_value_description 
											FROM ".DB_PREFIX."option_value opval
											JOIN (
												SELECT op.option_id, opdesc.name FROM ".DB_PREFIX."option op
												JOIN ".DB_PREFIX."option_description opdesc ON op.option_id = opdesc.option_id
											) options ON options.option_id = opval.option_id
											JOIN ".DB_PREFIX."option_value_description opvaldesc ON opvaldesc.option_value_id = opval.option_value_id
										) variants ON variants.option_value_id = popval.option_value_id
										WHERE popval.product_id = ".$data["product_id"].";");
			if($sub_datas){
				$product_options = $sub_datas->fetchAll(PDO::FETCH_ASSOC);
			}
			
			$product = array();
			$product["product_id"] = $data["product_id"];
			$product["model"] = $data["model"];
			$product["sku"] = $data["sku"];
			$product["upc"] = $data["upc"];
			$product["ean"] = $data["ean"];
			$product["jan"] = $data["jan"];
			$product["isbn"] = $data["isbn"];
			$product["mpn"] = $data["mpn"];
			$product["location"] = $data["location"];
			$product["quantity"] = $data["quantity"];
			$product["stock_status_id"] = $data["stock_status_id"];
			
			$product["images"] = $db->select("product_image", "*", array("product_id"=>$data["product_id"]));
			
			$product["categories"] = $db->select("product_to_category", "category_id", array("product_id"=>$data["product_id"]));
			
			$product["manufacturer_id"] = $data["manufacturer_id"];
			$product["shipping"] = $data["shipping"];
			$product["price"] = $data["price"];
			$product["points"] = $data["points"];
			$product["tax_class_id"] = $data["tax_class_id"];
			$product["date_available"] = $data["date_available"];
			$product["weight"] = $data["weight"];
			$product["weight_class_id"] = $data["weight_class_id"];
			$product["length"] = $data["length"];
			$product["width"] = $data["width"];
			$product["height"] = $data["height"];
			$product["length_class_id"] = $data["length_class_id"];
			$product["subtract"] = $data["subtract"];
			$product["minimum"] = $data["minimum"];
			$product["sort_order"] = $data["sort_order"];
			$product["status"] = $data["status"];
			$product["date_added"] = $data["date_added"];
			$product["date_modified"] = $data["date_modified"];
			$product["viewed"] = $data["viewed"];
			$product["language_id"] = $data["language_id"];
			$product["name"] = $data["name"];
			$product["description"] = $data["description"];
			$product["tag"] = $data["tag"];
			$product["meta_description"] = $data["meta_description"];
			$product["meta_keyword"] = $data["meta_keyword"];			
			$product["product_options"] = $product_options;
			$results[] = $product;
		}		
		Flight::json( array('state' => true, 'data'=>$results, 'Messages' => array()) );
	}else{
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );
	}
};

function product_list($take, $skip){
	Flight::authorization( Flight::request() );
	global $db;
	
	$sql = "SELECT op.*, 
			opd.language_id, opd.name, opd.description, opd.tag, 
			opd.meta_description, opd.meta_keyword 
			FROM ".DB_PREFIX."product op
			LEFT JOIN ".DB_PREFIX."product_description opd ON opd.product_id = op.product_id";
	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : ";";

	$datas = $db->query($sql);
	if($datas){
		$results = array();
		foreach($datas->fetchAll(PDO::FETCH_ASSOC) as $data){
			
			$product_options = array();
			$sub_datas = $db->query("SELECT popval.*, variants.option_description, variants.option_value_description
										FROM ".DB_PREFIX."product_option_value popval
										JOIN (
											SELECT opval.option_id, options.name option_description, opval.option_value_id, opvaldesc.name option_value_description 
											FROM ".DB_PREFIX."option_value opval
											JOIN (
												SELECT op.option_id, opdesc.name FROM ".DB_PREFIX."option op
												JOIN ".DB_PREFIX."option_description opdesc ON op.option_id = opdesc.option_id
											) options ON options.option_id = opval.option_id
											JOIN ".DB_PREFIX."option_value_description opvaldesc ON opvaldesc.option_value_id = opval.option_value_id
										) variants ON variants.option_value_id = popval.option_value_id
										WHERE popval.product_id = ".$data["product_id"].";");
			if($sub_datas){
				$product_options =  $sub_datas->fetchAll(PDO::FETCH_ASSOC);
			}
			
			$product = array();
			$product["product_id"] = $data["product_id"];
			$product["model"] = $data["model"];
			$product["sku"] = $data["sku"];
			$product["upc"] = $data["upc"];
			$product["ean"] = $data["ean"];
			$product["jan"] = $data["jan"];
			$product["isbn"] = $data["isbn"];
			$product["mpn"] = $data["mpn"];
			$product["location"] = $data["location"];
			$product["quantity"] = $data["quantity"];
			$product["stock_status_id"] = $data["stock_status_id"];
			$product["image"] = $data["image"];
			$product["manufacturer_id"] = $data["manufacturer_id"];
			$product["shipping"] = $data["shipping"];
			$product["price"] = $data["price"];
			$product["points"] = $data["points"];
			$product["tax_class_id"] = $data["tax_class_id"];
			$product["date_available"] = $data["date_available"];
			$product["weight"] = $data["weight"];
			$product["weight_class_id"] = $data["weight_class_id"];
			$product["length"] = $data["length"];
			$product["width"] = $data["width"];
			$product["height"] = $data["height"];
			$product["length_class_id"] = $data["length_class_id"];
			$product["subtract"] = $data["subtract"];
			$product["minimum"] = $data["minimum"];
			$product["sort_order"] = $data["sort_order"];
			$product["status"] = $data["status"];
			$product["date_added"] = $data["date_added"];
			$product["date_modified"] = $data["date_modified"];
			$product["viewed"] = $data["viewed"];
			$product["language_id"] = $data["language_id"];
			$product["name"] = $data["name"];
			$product["description"] = $data["description"];
			$product["tag"] = $data["tag"];
			$product["meta_description"] = $data["meta_description"];
			$product["meta_keyword"] = $data["meta_keyword"];			
			$product["product_options"] = $product_options;
			$results[] = $product;
		}		
		Flight::json( array('state' => true, 'data'=>$results, 'Messages' => array()) );
	}else{
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );
	}
};

function product_add(){
	Flight::authorization( Flight::request() );
	global $db;
	
	$request = Flight::request();
	
	$required_fields = array('model',
							'quantity',
							'stock_status_id',
							'manufacturer_id',
							'price',
							'tax_class_id',
							'name',
							'description',
							'category_ids',
							'sku',
							'language_id');
	$request_keys = reset($request->data);
	foreach($required_fields as $required_field){
		if(!array_key_exists($required_field, $request_keys)){
			Flight::halt(400, json_encode( array("Requirements Fields: " => implode(',', $required_fields)) ));
		}
	}
	
	$db->pdo->beginTransaction();
	try {
		
		if( $db->has( "product", array("OR"=>array("model" => $request->data["model"], "sku" => $request->data["sku"])) ) ){
			throw new Exception('Aynı model veya sku ile kayıtlı başka bir ürün bulunuyor.', 200);
		}
		
		$seller_stock_codes = array();
		if(array_key_exists('product_options', $request_keys)){
			$product_options = $request->data["product_options"];	
			foreach($product_options as $product_option){
				foreach($product_option["option_values"] as $option_value){						
					$seller_stock_codes[] = $option_value["seller_stock_code"];
				}
			}		
		}		
		if( $db->has("product_option_value", array("seller_stock_code" => $seller_stock_codes)) ){		
			throw new Exception('Aynı seller stock code ile kayıtlı başka bir ürün bulunuyor.', 200);
		}	
		
		$thumb = array();		
		if(array_key_exists('images', $request_keys)){
			foreach($request->data["images"] as $image){
				$filename = $image;
				try 
				{
					if((strpos($filename, "http://") !== false) 
						|| (strpos($filename, "https://") !== false)){
							$http_filename = end( @explode('/', $filename) );
							if(!file_exists(DIR_IMAGE_API.DIR_IMAGE_SUFFIX_API . $http_filename)){
								$http_file_content = file_get_contents($filename);
								file_put_contents(DIR_IMAGE_API.DIR_IMAGE_SUFFIX_API . $http_filename, $http_file_content);
								$filename = DIR_IMAGE_SUFFIX_API.$http_filename;			
							}else{
								$filename = DIR_IMAGE_SUFFIX_API.$http_filename;
							}
					}		
				}
				catch(Exception $ex) {}
				$thumb[] = $filename;
			}
		}
		
		/**
		* Fiyat İşlemleri - Kdv Dahil/Hariç
		*/
		$price = $request->data['price'];	
		$tax_rate = $db->query("SELECT " . DB_PREFIX . "tax_rate.rate FROM " . DB_PREFIX . "tax_class
			JOIN " . DB_PREFIX . "tax_rule ON " . DB_PREFIX . "tax_rule.tax_class_id = oc_tax_class.tax_class_id
			JOIN " . DB_PREFIX . "tax_rate ON " . DB_PREFIX . "tax_rate.tax_rate_id = oc_tax_rule.tax_rate_id
			WHERE " . DB_PREFIX . "tax_class.tax_class_id = ".(int)$request->data['tax_class_id']."
			GROUP BY " . DB_PREFIX . "tax_rate.rate LIMIT 1");												
		if ($tax_rate) {
			$tax_rate_row = $tax_rate->fetch(PDO::FETCH_ASSOC);
			if($tax_rate_row){
				$rate = ($tax_rate_row['rate'] / 100) + 1;				
				if(array_key_exists("tax_class_excl", $request_keys)){
					if($request->data["tax_class_excl"]==0){ //KDV DAHİL
						$price = ((float)$request->data['price'] / $rate);
					}else{
						$request->data['price'] = ((float)$request->data['price'] / $rate);
						$price = ((float)$request->data['price'] * $rate); //KDV HARİÇ
					}
				}else{
					$price = ((float)$request->data['price'] / $rate); //KDV DAHİL
				}
			}
		}
		$request->data['price'] = $price;
		
		$last_product_id = $db->insert( "product", array(
			"model" => $request->data["model"],
			"sku" => $request->data["sku"],
			"image" => $thumb[0],
			"quantity" => $request->data["quantity"],
			"stock_status_id" => $request->data["stock_status_id"],			
			"manufacturer_id" => $request->data["manufacturer_id"],
			"price" => $request->data["price"],
			"tax_class_id" => $request->data["tax_class_id"],
			"status" => 1,
			"date_added" => date("Y-m-d H:i:s"),
			"date_modified" => date("Y-m-d H:i:s")
		));
		
		if($last_product_id > 0){	
				
			$db->insert( "product_description", array(
				"product_id" => $last_product_id,
				"language_id" => $request->data["language_id"],
				"name" => $request->data["name"],
				"description" => $request->data["description"],
				"meta_title" => $request->data["name"]
			));	
			
			foreach($request->data["category_ids"] as $category_id){
				$db->insert( "product_to_category", array(
					"product_id" => $last_product_id,
					"category_id" => $category_id
				));	
			}
			
			if(array_key_exists('images', $request_keys)){ //opsiyonel
				if(count($thumb)>1){
					foreach($thumb as $image){
						$db->insert( "product_image", array(
							"product_id" => $last_product_id,
							"image" => $image
						));
					}
				}
			}
	
			$db->insert( "product_to_layout", array(
				"product_id" => $last_product_id,
				"store_id" => 0,
				"layout_id" => 0
			));	
			
			$db->insert( "product_to_store", array(
				"product_id" => $last_product_id,
				"store_id" => 0
			));
			
			if(array_key_exists('product_options', $request_keys)){

					$product_options = $request->data["product_options"];
					
					foreach($product_options as $product_option){
						$last_product_option_id = $db->insert( "product_option", array(
							"product_id" => $last_product_id,
							"option_id" => $product_option["option_id"],
							"value" => $product_option["option_description"],
							"required" => $product_option["required"]
						));
						if($last_product_option_id > 0){					
							foreach($product_option["option_values"] as $option_value){						
								$db->insert( "product_option_value", array(
									"product_option_id" => $last_product_option_id,
									"product_id" => $last_product_id,
									"option_id" => $product_option["option_id"],
									"option_value_id" => $option_value["option_value_id"],
									"quantity" => $option_value["quantity"],
									"subtract" => $option_value["subtract"],
									"price" => $option_value["price"],
									"price_prefix" => $option_value["price_prefix"],
									"points" => $option_value["points"],
									"points_prefix" => $option_value["points_prefix"],
									"weight" => $option_value["weight"],
									"weight_prefix" => $option_value["weight_prefix"],
									"seller_stock_code" => $option_value["seller_stock_code"]
								));	
							}		
						}					
					}			
			}
			
			$db->insert( "url_alias", array(
				"query" => "product_id=" . $last_product_id,
				"keyword" => slugify($request->data["name"])
			));			
						
			$db->pdo->commit();	
			Flight::json( array('state' => true, 'data'=>get_product_by_id($last_product_id), 'Messages' => array()) );
		} else{
			Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );
		}
	}
	catch(Exception $e){
		$db->pdo->rollBack();
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) ) );
	}	
};

function product_update(){		
	Flight::authorization( Flight::request() );
	global $db;
	
	$request = Flight::request();
	
	$required_fields = array('product_id',
							'stock_status_id',
							'quantity',
							'manufacturer_id',							
							'price',
							'tax_class_id',
							'name',
							'description');
	$request_keys = reset($request->data);
	foreach($required_fields as $required_field){
		if(!array_key_exists($required_field, $request_keys)){
			Flight::halt(400, json_encode( array("Requirements Fields: " => implode(',', $required_fields)) ));
		}
	}

	try {
		
		/**
		* Fiyat İşlemleri - Kdv Dahil/Hariç
		*/
		$price = $request->data['price'];	
		$tax_rate = $db->query("SELECT " . DB_PREFIX . "tax_rate.rate FROM " . DB_PREFIX . "tax_class
			JOIN " . DB_PREFIX . "tax_rule ON " . DB_PREFIX . "tax_rule.tax_class_id = oc_tax_class.tax_class_id
			JOIN " . DB_PREFIX . "tax_rate ON " . DB_PREFIX . "tax_rate.tax_rate_id = oc_tax_rule.tax_rate_id
			WHERE " . DB_PREFIX . "tax_class.tax_class_id = ".(int)$request->data['tax_class_id']."
			GROUP BY " . DB_PREFIX . "tax_rate.rate LIMIT 1");												
		if ($tax_rate) {
			$tax_rate_row = $tax_rate->fetch(PDO::FETCH_ASSOC);
			if($tax_rate_row){
				$rate = ($tax_rate_row['rate'] / 100) + 1;				
				if(array_key_exists("tax_class_excl", $request_keys)){
					if($request->data["tax_class_excl"]==0){ //KDV DAHİL
						$price = ((float)$request->data['price'] / $rate);
					}else{
						$request->data['price'] = ((float)$request->data['price'] / $rate);
						$price = ((float)$request->data['price'] * $rate); //KDV HARİÇ
					}
				}else{
					$price = ((float)$request->data['price'] / $rate); //KDV DAHİL
				}
			}
		}
		$request->data['price'] = $price;		
					
		$db->update( "product", array(
			"quantity" => $request->data["quantity"],
			"stock_status_id" => $request->data["quantity"]==0 ? 1 : $request->data["stock_status_id"],
			"manufacturer_id" => $request->data["manufacturer_id"],
			"price" => $request->data["price"],
			"tax_class_id" => $request->data["tax_class_id"],
			"date_modified" => date("Y-m-d H:i:s")
		), array("product_id" => $request->data["product_id"]));
			
		$db->update( "product_description", array(
			"name" => $request->data["name"],
			"description" => $request->data["description"],
			"meta_title" => $request->data["name"]
		), array("product_id" => $request->data["product_id"]));	
							
		Flight::json( array('state' => true, 'data'=>get_product_by_id($request->data["product_id"]), 'Messages' => array()) );
	}
	catch(Exception $e){
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) ) );
	}	
};