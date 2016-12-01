<?php

function customer_list($take, $skip){	
	Flight::authorization( Flight::request() );
	global $db;
	
	$sql = "SELECT c.customer_id,
							c.store_id,
							c.firstname,
							c.lastname,
							c.email,
							c.telephone,
							c.fax,
							c.password,
							c.salt,
							c.cart,
							c.wishlist,
							c.newsletter,
							c.address_id,
							c.customer_group_id,
							c.ip,
							c.status,
							c.approved,
							c.token,
							c.date_added,
							a.address_id a_address_id,
							a.customer_id a_customer_id,
							a.firstname a_firstname,
							a.lastname a_lastname,
							a.company,
							a.company_id,
							a.tax_id,
							a.address_1,
							a.address_2,
							a.city,
							a.postcode,
							a.country_id,
							a.zone_id	
						FROM ".DB_PREFIX."customer c
						LEFT JOIN ".DB_PREFIX."address a ON c.address_id = a.address_id";
	$sql .= !empty($take) ? ( !empty($skip) ? " LIMIT $skip, $take" : " LIMIT $take") : "";

	$datas = $db->query($sql);
	if($datas){
		Flight::json( array('state' => true, 'data'=>$datas->fetchAll(PDO::FETCH_ASSOC), 'Messages' => array()) );
	}else{
		Flight::json( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)) );
	}
};