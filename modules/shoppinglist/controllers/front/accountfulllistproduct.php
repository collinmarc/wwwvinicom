<?php

/**
* Class ShoppingListAccountFullListProductModuleFrontController
* 
* Adaptation de la shopping list créée par Olivier Michaud et modifiée par VEZIM
* dans le cadre du projet Vinicom
* 
* @author Pascal Véron
* @copyright  VEZIM SARL
*
*  
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class ShoppingListAccountFullListProductModuleFrontController extends ModuleFrontController {
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
            case 'addOneToCart':    $this->addOneToCart();                     break;
            default:                $this->indexFullListProduct();             break;
        }
	}

	/**
	 * Index full list product
	 */
	public function indexFullListProduct()
	{
        //$shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        //$fullListProducts = $shoppingListObj->getFullAllProducts();
        $fullListProducts = ShoppingListObject::getFullAllProducts();

        $this->context->smarty->assign('messages', $this->messages);
        $this->context->smarty->assign('errors', $this->errors);
        //$this->context->smarty->assign('shoppingListObj', $shoppingListObj);
        $this->context->smarty->assign('fullListProducts', $fullListProducts);
		$this->addCSS(_MODULE_DIR_."shoppinglist/views/css/shoppinglist2.css"); 
        $this->setTemplate('accountfulllistproductindex.tpl');
	}
    
    /**
	 * Insert a product to cart - Call by function addOneToCart
	 */
    private function updateProductInCart($idProduct, $idProductAttribute, $qty) {
        $productObj = new Product($idProduct);
        if ($idProductAttribute != 0) {
            $productObj->id_product_attribute = $idProductAttribute;
            
            //Get Combination minimal Quantity to add
            $combination = new Combination($idProductAttribute);
            $minimalQuantity = $qty * $combination->minimal_quantity;
        }
        else {
        	//get product minimal quantity to add
        	$minimalQuantity = $qty * $productObj->minimal_quantity;
        }
        
        //$shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        //$product = $shoppingListObj->getOneProduct($idShoppingList, $idProduct, $idProductAttribute);

        // on n'a pas de shopping list, du coup on ne récupère pas $product (utilisée dans l'affichage des messages d'erreur uniquement ?)

        if(Configuration::get('PS_CATALOG_MODE')) {
            $this->errors[] = $this->module->l('The shop is desactivated', 'accountfulllistproduct');
        }
        elseif (!$productObj->existsInDatabase($idProduct, 'product')) {
            $this->errors[] = $this->module->l('The product', 'accountfulllistproduct').' "'.$product['title'].'" '.$this->module->l('does not exist', 'accountfulllistproduct');
        }
        elseif(!$productObj->active) {
            $this->errors[] = $this->module->l('The product', 'accountfulllistproduct').' "'.$product['title'].'" '.$this->module->l('was desactivate', 'accountfulllistproduct');
        }
        elseif (!$productObj->available_for_order) {
            $this->errors[] = $this->module->l('The product', 'accountfulllistproduct').' "'.$product['title'].'" '.$this->module->l('was not avalaible for order', 'accountfulllistproduct');
        }
        
        elseif(!$productObj->checkQty(2)) {
            $this->errors[] = $this->module->l('The product', 'accountfulllistproduct').' "'.$product['title'].'" '.$this->module->l('has no sufficient stock available', 'accountfulllistproduct');
        }
        else {
            $cartObj = new Cart($this->context->cookie->id_cart);
            $cartObj->updateQty($minimalQuantity, $idProduct, $idProductAttribute);
            $this->messages[] = $this->module->l('The product', 'accountfulllistproduct').' "'.$product['title'].'" '.$this->module->l('was added to cart', 'accountfulllistproduct');
        }
    }
    
    /**
	 * Adding a product to cart
	 */
    public function addOneToCart() {
        $idProduct = Tools::getValue('id_product');
        $idProductAttribute = Tools::getValue('id_product_attribute');
        $qty = Tools::getValue('qty');
        
        $this->updateProductInCart($idProduct, $idProductAttribute, $qty);

        $this->indexFullListProduct();
    }
    
    
}

