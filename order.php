<?php

function order_history_add(){
	Flight::authorization( Flight::request() );
	global $db;	
	
	try {
		$request = Flight::request();
		$last_order_history_id = $db->insert( "order_history", array(
			"order_id" => $request->data["order_id"],
			"order_status_id" => $request->data["order_status_id"],
			"notify" => $request->data["notify"],
			"comment" => $request->data["comment"],
			"date_added" => date("Y-m-d H:i:s")
		));
		if($last_order_history_id){
			$updated_rows = $db->update("order", array("order_status_id"=>$request->data["order_status_id"]), array("order_id" => $request->data["order_id"]));
			if($updated_rows>0){
				$data = $db->get("order_history", "*", array("order_history_id"=>$last_order_history_id));
				$result = array('state' => true, 'data'=>$data, 'Messages' => array() );
			}else{
				$db->delete("order_history", array("order_id"=>$request->data["order_id"]));
				throw new Exception('Sipariş durumu güncellenirken hata oluştu!');
			}
		}
	}
	catch(Exception $e){
		$result = array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) );
	}
	
	Flight::json( $result );	
};

function order_list($store_id=0,$order_status_id=1, $take, $skip){	
	Flight::authorization( Flight::request() );
	global $db;
	
	$order_result = array();
	$order_object = array();
			
	$sql = "SELECT ".DB_PREFIX."order.* FROM (
				SELECT 
				MAX(".DB_PREFIX."order_history.order_history_id) max_order_history_id
				FROM ".DB_PREFIX."order_history
				GROUP BY ".DB_PREFIX."order_history.order_id
			) AS max_order_history_id
			JOIN ".DB_PREFIX."order_history order_history_join ON order_history_join.order_history_id = max_order_history_id
			JOIN ".DB_PREFIX."order ON ".DB_PREFIX."order.order_id = order_history_join.order_id
			WHERE ".DB_PREFIX."order.store_id = " . $store_id . " AND order_history_join.order_status_id = " . $order_status_id;
	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);
	if($datas){
		
		$order_rows = $datas->fetchAll(PDO::FETCH_ASSOC);		
		foreach($order_rows as $order_row){

			$order_object["order"] = $order_row;
			
			$customer = $db->get("customer", "*",
				array("customer_id" => $order_row["customer_id"])
			);
			
			$custom_fields = json_decode($customer["custom_field"]); 
			if(!empty($custom_fields)){
				foreach($custom_fields as $custom_field_key => $custom_field){
					$custom_field_name = $custom_field_key;
					$custom_field_name = $db->get("custom_field_description", "name",
						array("custom_field_id" => $custom_field_key)
					);
					$customer[$custom_field_name] = $custom_field;
				}
			}
			
			$order_object["customer"] = $customer;
			
			$order_object["order_history"] = 
				$db->query("SELECT ".DB_PREFIX."order_status.name as order_status_name, ".DB_PREFIX."order_history.*
								FROM ".DB_PREFIX."order_history
								JOIN ".DB_PREFIX."order_status ON ".DB_PREFIX."order_status.order_status_id = ".DB_PREFIX."order_history.order_status_id
								WHERE ".DB_PREFIX."order_history.order_id = " . $order_row["order_id"]
			)->fetchAll(PDO::FETCH_ASSOC);
			
			$order_object["order_product"] = 
				$db->query("SELECT
							".DB_PREFIX."product_description.name as product_description_name,
							".DB_PREFIX."order_product.order_product_id,
							".DB_PREFIX."order_product.product_id,
							".DB_PREFIX."order_product.name as order_product_name,
							".DB_PREFIX."order_product.model,
							".DB_PREFIX."order_product.quantity,
							".DB_PREFIX."order_product.price,
							".DB_PREFIX."order_product.total,
							".DB_PREFIX."order_product.tax,
							".DB_PREFIX."order_product.reward,
							".DB_PREFIX."order_option.order_option_id,
							".DB_PREFIX."order_option.product_option_id,
							".DB_PREFIX."order_option.product_option_value_id,
							".DB_PREFIX."order_option.name as order_option_name,
							".DB_PREFIX."order_option.value as order_option_value,
							".DB_PREFIX."order_option.type as order_option_type,
							".DB_PREFIX."product_description.name as product_description_name,
							".DB_PREFIX."option_description.name as option_description_name,
							".DB_PREFIX."option_value_description.name as option_value_description_name,
							".DB_PREFIX."product_option_value.seller_stock_code
							FROM ".DB_PREFIX."order_product
							LEFT JOIN ".DB_PREFIX."order_option ON ".DB_PREFIX."order_option.order_product_id = ".DB_PREFIX."order_product.order_product_id
							LEFT JOIN ".DB_PREFIX."product_option_value ON ".DB_PREFIX."product_option_value.product_option_value_id = ".DB_PREFIX."order_option.product_option_value_id
							LEFT JOIN ".DB_PREFIX."product_description ON ".DB_PREFIX."product_description.product_id = ".DB_PREFIX."order_product.order_product_id
							LEFT JOIN ".DB_PREFIX."option_description ON ".DB_PREFIX."option_description.option_id = ".DB_PREFIX."order_option.product_option_id
							LEFT JOIN ".DB_PREFIX."option_value_description ON ".DB_PREFIX."option_value_description.option_value_id = ".DB_PREFIX."order_option.product_option_value_id
							WHERE ".DB_PREFIX."order_product.order_id = " . $order_row["order_id"]
			)->fetchAll(PDO::FETCH_ASSOC);			
			
			$order_object["order_total"] = $db->select("order_total", "*",
				array("order_id" => $order_row["order_id"])
			);			
									
			$order_result[] = $order_object;
		}//order_rows
		
		Flight::json( array('state' => true, 'data'=>$order_result, 'Messages' => array()) );
		
	}else{
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );
	}
};