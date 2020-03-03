<?php

/**
* Ajax File called in product page add to cart
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

require_once(dirname(__FILE__).'../../../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../../../init.php');

/**
 * This page permit to add a product to a shopping list
 */

if (strcmp(Tools::getToken(false), Tools::getValue('static_token')))
	die(Tools::jsonEncode(array('result'=>$module->l('Invalid Token', 'ajaxproductshoppinglist'))));


$idShoppingList = Tools::getValue('id_shopping_list');
$idProduct = Tools::getValue('id_product');
$idProductAttribute = Tools::getValue('id_product_attribute');
$title = Tools::getValue('title');

$context = Context::getContext();
$module = new ShoppingList();
$customer = $context->cookie->id_customer;
$shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $customer);

if($shoppingListObj == null) {
    die(Tools::jsonEncode(array('result'=>$module->l('An error occur', 'ajaxproductshoppinglist'))));
}

$result = $shoppingListObj->addProduct($idProduct, $idProductAttribute, $title);
if($result) {
    die(Tools::jsonEncode(array('result'=>$module->l('Product added successfully', 'ajaxproductshoppinglist'))));
}
else {
    die(Tools::jsonEncode(array('result'=>$module->l('This product seems already exist in this shopping list', 'ajaxproductshoppinglist'))));
}

