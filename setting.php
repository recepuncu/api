<?php



function option_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT op.option_id, opdesc.name FROM ".DB_PREFIX."option op

			JOIN ".DB_PREFIX."option_description opdesc ON op.option_id = opdesc.option_id";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : ";";



	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};



function option_value_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT opval.option_id, options.name option_description, opval.option_value_id, opvaldesc.name option_value_description
			, ove.renk_beden
			FROM ".DB_PREFIX."option_value opval

			JOIN (

				SELECT op.option_id, opdesc.name FROM ".DB_PREFIX."option op

				JOIN ".DB_PREFIX."option_description opdesc ON op.option_id = opdesc.option_id

			) options ON options.option_id = opval.option_id

			JOIN ".DB_PREFIX."option_value_description opvaldesc ON opvaldesc.option_value_id = opval.option_value_id
			LEFT JOIN ".DB_PREFIX."option_value_entegrasyon ove ON ove.option_value_id = opval.option_value_id";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : ";";



	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};



function option_value_list_by_option_id($option_id){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT opval.option_id, options.name option_description, opval.option_value_id, opvaldesc.name option_value_description 

			FROM ".DB_PREFIX."option_value opval

			JOIN (

				SELECT op.option_id, opdesc.name FROM ".DB_PREFIX."option op

				JOIN ".DB_PREFIX."option_description opdesc ON op.option_id = opdesc.option_id

			) options ON options.option_id = opval.option_id

			JOIN ".DB_PREFIX."option_value_description opvaldesc ON opvaldesc.option_value_id = opval.option_value_id

			WHERE opval.option_id = " . $option_id . " ";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : ";";



	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};



function currency_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT * FROM ".DB_PREFIX."currency";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};



function stock_status_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT * FROM ".DB_PREFIX."stock_status";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};



function manufacturer_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	$sql = "SELECT * FROM ".DB_PREFIX."manufacturer";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};

function tax_class_add(){

	Flight::authorization( Flight::request() );

	global $db;	

	$result = NULL;

	try {

		$request = Flight::request();	

		$tax_rate_id = $db->insert( "tax_rate", array(
			"geo_zone_id" => "1",
			"name" => $request->data["title"],
			"rate" => $request->data["rate"],
			"type" => "P",
			"date_added" => date("Y-m-d H:i:s"),
			"date_modified" => date("Y-m-d H:i:s")
		));
		
		$tax_class_id = $db->insert( "tax_class", array(			
			"title" => $request->data["title"],
			"description" => $request->data["description"],
			"date_added" => date("Y-m-d H:i:s"),
			"date_modified" => date("Y-m-d H:i:s")
		));	

		$tax_rule_id = $db->insert( "tax_rule", array(			
			"tax_class_id" => $tax_class_id,
			"tax_rate_id" => $tax_rate_id,			
			"based" => "payment",
			"priority" => "1"
		));			

	} catch(Exception $e){

		$result = array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>$e->getMessage()) );

	}

	Flight::json( $result );

};

function tax_class_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT * FROM ".DB_PREFIX."tax_class";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};

function country_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT * FROM ".DB_PREFIX."country";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};

function language_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT * FROM ".DB_PREFIX."language";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};



function order_status_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT * FROM ".DB_PREFIX."order_status";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};



function store_list($take, $skip){	

	Flight::authorization( Flight::request() );

	global $db;

	

	$sql = "SELECT * FROM ".DB_PREFIX."store";

	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);

	if($datas){

		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );

	}else{

		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );

	}

};