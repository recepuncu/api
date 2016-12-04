<?php

require_once ('../config.php');
require_once ('api_config.php');
require_once ('funcs.php');
require_once ('lib/flight/Flight.php');
require_once ('lib/medoo.php');

$db = new medoo(array(
	'database_type' => 'mysql',
	'database_name' => DB_DATABASE,
	'server' => DB_HOSTNAME,
	'username' => DB_USERNAME,
	'password' => DB_PASSWORD,
	'prefix' => DB_PREFIX,
	'charset' => 'utf8'
));

Flight::map('authorization', function ($request) {
	$headers = parseRequestHeaders($request->data);
	if (!array_key_exists("Auth", $headers))
		{
		Flight::halt(401, "Unauthorized");
		}
	  else
	if ($headers["Auth"] != API_KEY)
		{
		Flight::halt(401, "Unauthorized");
		}
});
	
include ("setting.php");
Flight::route('/setting/option/list(/@take:[0-9]+(/@skip:[0-9]+))', 'option_list');
Flight::route('/setting/option/value/list(/@take:[0-9]+(/@skip:[0-9]+))', 'option_value_list');
Flight::route('/setting/option/value/list_by_option_id/@option_id:[0-9]+', 'option_value_list_by_option_id');
Flight::route('/setting/currency/list(/@take:[0-9]+(/@skip:[0-9]+))', 'currency_list');
Flight::route('/setting/stock-status/list(/@take:[0-9]+(/@skip:[0-9]+))', 'stock_status_list');
Flight::route('/setting/manufacturer/list(/@take:[0-9]+(/@skip:[0-9]+))', 'manufacturer_list');
Flight::route('/setting/tax-class/list(/@take:[0-9]+(/@skip:[0-9]+))', 'tax_class_list');
Flight::route('POST /setting/tax-class/add', 'tax_class_add');
Flight::route('/setting/country-class/list(/@take:[0-9]+(/@skip:[0-9]+))', 'country_list');
Flight::route('/setting/language/list(/@take:[0-9]+(/@skip:[0-9]+))', 'language_list');
Flight::route('/setting/order-status/list(/@take:[0-9]+(/@skip:[0-9]+))', 'order_status_list');
Flight::route('/setting/store/list(/@take:[0-9]+(/@skip:[0-9]+))', 'store_list');

include ("customer.php");
Flight::route('/customer/list(/@take:[0-9]+(/@skip:[0-9]+))', 'customer_list');

include ("category.php");
Flight::route('/category/listbyname/@name', 'category_listbyname');

include ("product.php");
Flight::route('/product/get/@product_id:[0-9]+', 'get_product_by_id');
Flight::route('/product/list(/@take:[0-9]+(/@skip:[0-9]+))', 'product_list');
Flight::route('POST /product/add', 'product_add');
Flight::route('POST /product/update', 'product_update');

include ("product_image.php");
Flight::route('POST /product/image/add', 'product_image_add');
Flight::route('/product/image/delete/@product_image_id:[0-9]+', 'product_image_delete');

include ("product_option_value.php");
Flight::route('POST /product/product_option_value/add', 'product_option_value_add');
Flight::route('/product/product_option_value/delete/@product_option_value_id:[0-9]+', 'product_option_value_delete');
Flight::route('POST /product/product_option_value/update/quantities', 'product_option_value_update_quantities');
Flight::route('POST /product/product_option_value/update/price', 'product_option_value_update_price');

include ("order.php");
Flight::route('/order/list/@store_id:[0-9]+/@order_status_id:[0-9]+(/@take:[0-9]+(/@skip:[0-9]+))', 'order_list');
Flight::route('POST /order/history/add', 'order_history_add');

include ("option.php");
Flight::route('POST /option/value/add', 'option_value_add');

Flight::start();