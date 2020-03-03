<?php

/**
* MODULE ShoppingList
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classes/ShoppingListObject.php');

class ShoppingList extends Module {

    public function __construct() {
        $this->name = 'shoppinglist';
        $this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'Olivier Michaud';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6'); 
        $this->module_key = "6ecaf5d4443aea1ca6093730a3fcf27b";

        parent::__construct();

        $this->displayName = $this->l('Shopping List');
        $this->description = $this->l('Permit to a customer to add/edit shopping list and add Product to them');
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Shopping List Module?');
    }

    public function install() {

        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);
        
        $sql = array();
	
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'shopping_list` (
                  `id_shopping_list` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `id_customer` INT(10) UNSIGNED NOT NULL,
                  `title` VARCHAR(255) NOT NULL,
                  `status` TINYINT(1) NOT NULL DEFAULT 1,
                  `date_add` DATETIME NOT NULL,
                  `date_upd` DATETIME NOT NULL,
                  PRIMARY KEY (`id_shopping_list`),
                  UNIQUE  `SHOPPING_LIST_U1` (`id_shopping_list`),
                  UNIQUE  `SHOPPING_LIST_U2` (`id_customer`,`title`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'shopping_list_product` (
                  `id_shopping_list` INT(10) UNSIGNED NOT NULL,
                  `id_product` INT(10) UNSIGNED NOT NULL,
                  `id_product_attribute` INT(10) UNSIGNED NULL,
                  `title` VARCHAR(255) NOT NULL,
                  PRIMARY KEY (`id_shopping_list`, `id_product`,`id_product_attribute`),
                  UNIQUE  `SHOPPING_LIST_U3` (`id_shopping_list`, `id_product`,`id_product_attribute`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
                                
        if (!parent::install() OR 
            !$this->registerHook('displayCustomerAccount') OR
            !$this->registerHook('displayHeader') OR
            !$this->registerHook('ActionAuthentication') OR
            !$this->registerHook('displayRightColumnProduct') OR
            !$this->runSql($sql)
        ) {
            return false;
        }
        
        return true;
    }
    
    public function uninstall() {       
        $sql = array();
	
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'shopping_list`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'shopping_list_product`';
        
        if (!parent::uninstall() OR
            !$this->runSql($sql) 
        ) {
            return false;
        }

        return true;
    }
    
    public function runSql($sql) {
        foreach ($sql as $s) {
			if (!Db::getInstance()->Execute($s)){
				return FALSE;
			}
        }
        
        return TRUE;
    }
    
    public function hookDisplayHeader() {
        $this->context->controller->addCSS($this->_path.'views/css/shoppinglist.css');
        $this->context->controller->addJS($this->_path.'views/js/shoppinglist.js');
    }
    
    public function hookDisplayRightColumnProduct() {
        if($this->context->customer->isLogged()) {
            $this->createDefaultShoppingListIfNotExist();
            
            $customer = $this->context->cookie->id_customer;
            $shoppingListObj = new ShoppingListObject();
            $shoppingList = $shoppingListObj->getByIdCustomer($customer);
            
            $product = new Product(Tools::getValue('id_product'));

            $this->context->smarty->assign('title', $product->name[1]);
            $this->context->smarty->assign('shoppingList', $shoppingList);
            return $this->display(__FILE__, 'views/templates/hook/product.tpl');
        }
    }
    
    public function hookDisplayLeftColumnProduct() {
        return $this->hookDisplayRightColumnProduct();
    }
    
    public function hookDisplayFooterProduct() {
        return $this->hookDisplayRightColumnProduct();
    }
    
    public function hookActionAuthentication() {
        $this->createDefaultShoppingListIfNotExist();
    }
    
    public function hookDisplayCustomerAccount() {
        $this->createDefaultShoppingListIfNotExist();
        return $this->display(__FILE__, 'views/templates/hook/customer_account.tpl');
    }
    
    private function createDefaultShoppingListIfNotExist() {
        //Get Shopping List of User Or If no Shopping List we create One
        $shoppingListObj = new ShoppingListObject();
        if($shoppingListObj->getNumberShoppingListByIdCustomer($this->context->cookie->id_customer) == 0) {
            $shoppingListObj->id_customer = $this->context->cookie->id_customer;
            $shoppingListObj->title = $this->l('My List');
            $shoppingListObj->status = 1;
            $shoppingListObj->date_add = new \DateTime();
            $shoppingListObj->date_upd = new \DateTime();
            
            $shoppingListObj->add();
        }
    }
}