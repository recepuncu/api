<?php

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Recep Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die('Yetkisiz olan giremez.');
}else{
	if(($_SERVER['PHP_AUTH_USER'] != "admin") 
		|| ($_SERVER['PHP_AUTH_PW'] != "123654")){
			die('Yetkisiz olan giremez.');
	}
}

require_once('../config.php');
require_once('lib/medoo.php');

$db = new medoo(array(
	'database_type' => 'mysql',	
    'database_name' => DB_DATABASE,
    'server' => DB_HOSTNAME,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD,	
	'prefix' => DB_PREFIX,
	'charset' => 'utf8'
));

if(isset($_POST["product_option_value_id"]) 
	&& isset($_POST["seller_stock_code"])){
	$product_option_value_update = $db->update("product_option_value", 
												array("seller_stock_code" =>$_POST["seller_stock_code"]), 
												array("product_option_value_id"=>intval($_POST["product_option_value_id"])));
	echo( json_encode( array("ResultValue" => ($product_option_value_update > 0 ? true : false), "ResultText" => ($product_option_value_update > 0 ? "Güncellendi." : "Güncellenemedi!")) ) );
	return;
}

$sql = "SELECT 
		pov.product_option_value_id, p.product_id
		, pd.name product_description, ovd.name option_value_description, pov.seller_stock_code  
		-- , p.quantity, pov.quantity 
		FROM ".DB_PREFIX."product_option_value pov
		JOIN ".DB_PREFIX."product p ON p.product_id = pov.product_id
		JOIN ".DB_PREFIX."product_description pd ON pd.product_id = pov.product_id
		JOIN ".DB_PREFIX."option_value_description ovd ON ovd.option_value_id = pov.option_value_id
		ORDER BY p.product_id, pov.product_option_value_id";

$query = $db->query($sql);

$datas = array();

if($query)
	$datas = $query->fetchAll(PDO::FETCH_ASSOC);	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="tr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Entegrasyon</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style type="text/css">
#tblUrunler tbody tr {
	cursor: pointer;
}
.selected {
	background-color: #039 !important;
	color: #FFF;
}
</style>
</head>

<body>

<div class="container">
  <h2>Entegrasyon</h2>
  <p>Nebim stok kodu ile Opencart stok kodu eşleştirme tablosu:</p>
  <table id="tblUrunler" class="table table-bordered table-hover table-striped">
    <thead>
      <tr>
        <th class="text-center">Ürün Adı</th>
        <th class="text-center">Açıklama</th>
        <th class="text-center">Stok Kodu</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($datas as $data):  ?>
      <tr>
        <td><?php echo $data["product_description"]; ?></td>
        <td><?php echo $data["option_value_description"]; ?></td>
        <td>		
        <div class="input-group">
          <input type="text" class="form-control input-sm" placeholder="Nebim stok kodunu buraya girin." value="<?php echo $data["seller_stock_code"]; ?>" id="product_option_value<?php echo $data["product_option_value_id"]; ?>" />
          <span class="input-group-btn">
            <button data-id="<?php echo $data["product_option_value_id"]; ?>" class="btn btn-default btn-sm btn_save_seller_stock_code" type="button"><span class="glyphicon glyphicon-floppy-save"></span>&nbsp;</button>
          </span>
        </div><!-- /input-group -->        
        </td>
      </tr>
      <?php endforeach;  ?>
    </tbody>
  </table>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript">
$(function(){
	$('#tblUrunler tbody tr').on('click', function(){
		$('#tblUrunler tbody tr').removeClass('selected');
		$(this).addClass('selected');
	});
	$(".btn_save_seller_stock_code").on('click', function(){
		var id = $(this).data("id");
		$.post("seller_stock_code.php", {"product_option_value_id": id, "seller_stock_code": $("#product_option_value"+id).val()}, function(res){
			var result = $.parseJSON(res);
			alert(result.ResultText);
		});
	});
});
</script>
</body>
</html>