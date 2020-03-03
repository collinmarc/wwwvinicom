<?php

/**
* Class ShoppingListAccountShoppingListProductModuleFrontController
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class ShoppingListAccountShoppingListProductModuleFrontController extends ModuleFrontController {
    private $messages;
    
    public function __construct()
	{
		parent::__construct();
        $this->messages = null;
        $this->errors = null;
        $this->context = Context::getContext();
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
        
        $action = Tools::getValue('action');
        switch($action) {
            case 'delete':  
            case 'deleteConfirm':   $this->deleteShoppingListProduct();            break;
            case 'addOneToCart':    $this->addOneToCart();                         break;
            case 'addAllToCart':    $this->addAllToCart();                         break;
            default:                $this->indexShoppingListProduct();             break;
        }
	}

	/**
	 * Index shopping list product
	 */
	public function indexShoppingListProduct($idShoppingList = null)
	{
        if($idShoppingList == null) {
            $idShoppingList = Tools::getValue('id_shopping_list');
        }

        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        $shoppingListProducts = $shoppingListObj->getAllProducts();
        
        $this->context->smarty->assign('messages', $this->messages);
        $this->context->smarty->assign('errors', $this->errors);
        $this->context->smarty->assign('shoppingListObj', $shoppingListObj);
        $this->context->smarty->assign('shoppingListProducts', $shoppingListProducts);
        $this->setTemplate('accountshoppinglistproductindex.tpl');
	}
    
    /**
	 * Delete shopping list product
	 */
	public function deleteShoppingListProduct()
	{
        $action = Tools::getValue('action');
        $idProduct = Tools::getValue('id_product');
        $idProductAttribute = Tools::getValue('id_product_attribute');
        $idShoppingList = Tools::getValue('id_shopping_list');
        
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        
        if($shoppingListObj == null) {
            $this->errors[] = $this->module->l('An error occur', 'accountshoppinglistproduct');
            $this->indexShoppingListProduct($idShoppingList);
            return;
        } 
        if($action == "delete") {
            $this->context->smarty->assign('id_product', $idProduct);
            $this->context->smarty->assign('id_product_attribute', $idProductAttribute);
            $this->context->smarty->assign('id_shopping_list', $idShoppingList);
            $this->context->smarty->assign('title', $shoppingListObj->title);
            $this->setTemplate('accountshoppinglistproductdelete.tpl');
        }
        if($action == "deleteConfirm") {
            if ($shoppingListObj->deleteProduct($idProduct, $idProductAttribute)) {
                $this->messages[] = $this->module->l('Product deleted', 'accountshoppinglistproduct');
            }
            else {
                $this->errors[] = $this->module->l('An error occur', 'accountshoppinglistproduct');
            }
            
            $this->indexShoppingListProduct($idShoppingList);
        }
	}
    
    /**
	 * Insert a product to cart - Call by function addOneToCart and addAllToCart
	 */
    private function updateProductInCart($idShoppingList, $idProduct, $idProductAttribute) {
        $productObj = new Product($idProduct);
        if ($idProductAttribute != 0) {
            $productObj->id_product_attribute = $idProductAttribute;
            
            //Get Combination minimal Quantity to add
            $combination = new Combination($idProductAttribute);
            $minimalQuantity = $combination->minimal_quantity;
        }
        else {
        	//get product minimal quantity to add
        	$minimalQuantity = $productObj->minimal_quantity;
        }
        
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        $product = $shoppingListObj->getOneProduct($idShoppingList, $idProduct, $idProductAttribute);

        if(Configuration::get('PS_CATALOG_MODE')) {
            $this->errors[] = $this->module->l('The shop is desactivated', 'accountshoppinglistproduct');
        }
        elseif (!$productObj->existsInDatabase($idProduct, 'product')) {
            $this->errors[] = $this->module->l('The product', 'accountshoppinglistproduct').' "'.$product['title'].'" '.$this->module->l('does not exist', 'accountshoppinglistproduct');
        }
        elseif(!$productObj->active) {
            $this->errors[] = $this->module->l('The product', 'accountshoppinglistproduct').' "'.$product['title'].'" '.$this->module->l('was desactivate', 'accountshoppinglistproduct');
        }
        elseif (!$productObj->available_for_order) {
            $this->errors[] = $this->module->l('The product', 'accountshoppinglistproduct').' "'.$product['title'].'" '.$this->module->l('was not avalaible for order', 'accountshoppinglistproduct');
        }
        
        elseif(!$productObj->checkQty(2)) {
            $this->errors[] = $this->module->l('The product', 'accountshoppinglistproduct').' "'.$product['title'].'" '.$this->module->l('has no sufficient stock available', 'accountshoppinglistproduct');
        }
        else {
            $cartObj = new Cart($this->context->cookie->id_cart);
            $cartObj->updateQty($minimalQuantity, $idProduct, $idProductAttribute);
            $this->messages[] = $this->module->l('The product', 'accountshoppinglistproduct').' "'.$product['title'].'" '.$this->module->l('was added to cart', 'accountshoppinglistproduct');
        }
    }
    
    /**
	 * Adding a product to cart
	 */
    public function addOneToCart() {
        $idShoppingList = Tools::getValue('id_shopping_list');
        $idProduct = Tools::getValue('id_product');
        $idProductAttribute = Tools::getValue('id_product_attribute');
        
        $this->updateProductInCart($idShoppingList, $idProduct, $idProductAttribute);

        $this->indexShoppingListProduct($idShoppingList);
    }
    
    /**
	 * Adding all products to cart
	 */
    public function addAllToCart() {
        $idShoppingList = Tools::getValue('id_shopping_list');
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        $products = $shoppingListObj->getAllProducts();
        
        foreach($products as $product) {
            $this->updateProductInCart($idShoppingList, $product['id_product'], $product['id_product_attribute']);
        }

        $this->indexShoppingListProduct($idShoppingList);
    }
    
}

