<?php



function category_listbyname($name){

	Flight::authorization( Flight::request() );

	global $db;

	$all_cats = array();

	$cats = $db->query("SELECT 

						c1.category_id, c1.parent_id, d1.name

						FROM ".DB_PREFIX."category c1

							JOIN ".DB_PREFIX."category_description d1 ON d1.category_id = c1.category_id

						WHERE c1.`status` = 1 AND d1.name <> ''

						ORDER BY c1.category_id, c1.parent_id;");

	if($cats){

		$all_cats = $cats->fetchAll(PDO::FETCH_ASSOC);

	}

	

	$result_cats = array();

	if(strlen($name) >= 3){

		foreach($all_cats as $cat){

			if (stripos($cat["name"], $name) !== false) {

				$ust_kategoriler = array();

				$ust_kategori = ust_kategori_bul($cat["parent_id"], $all_cats);

				

				if(!empty($ust_kategori)){

					$ust_kategoriler[] = $ust_kategori["name"];

					

					for($i=0; $i<2000; $i++){

						$p = $ust_kategori;				

						$ust_kategori = ust_kategori_bul($p["parent_id"], $all_cats);

						if(!empty($ust_kategori)){

							$ust_kategoriler[] = $ust_kategori["name"];

						}

					}

				}

		

				if(!empty($ust_kategoriler)){

					$cat["name"] = implode(' > ', array_reverse($ust_kategoriler)).' > '.$cat["name"];

				}

				$result_cats[] = $cat;

			}

		}		

	}

	
	header('Content-Type: application/json; charset=utf-8');
	if(!empty($result_cats)){
		
		echo json_encode( array('state' => true, 'data'=>$result_cats, 'Messages' => array()), JSON_UNESCAPED_UNICODE);

	}else{

		echo json_encode( array('state' => false, 'data'=>array(), 'Messages' => array('db'=>$db->error(), 'ex'=>NULL)), JSON_UNESCAPED_UNICODE);

	}

};