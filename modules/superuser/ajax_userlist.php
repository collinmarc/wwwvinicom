<?php
// Développé par VEZIM SARL le 14/09/2016
// Liste filtrée des clients enregistrés utilisée pour l'autocompletion du module superuser

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__). '/superuser.php');

$customers_list = Customer::getCustomers();

$filteredCustomers = array();

foreach ($customers_list as &$customer) {
	$pattern = '/'.$_GET["q"].'/i';
	if (preg_match($pattern, $customer['company']) || preg_match($pattern, $customer['firstname']) || preg_match($pattern, $customer['lastname']) || preg_match($pattern, $customer['email'])) {
		array_push($filteredCustomers, $customer);
	}
}

echo json_encode($filteredCustomers);
