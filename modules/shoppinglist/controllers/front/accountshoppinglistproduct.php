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
       // Récupération de l'action et aiguillage
        $action = Tools::getValue('action');
        switch($action) {
            case 'delete':  
            case 'deleteConfirm':   $this->deleteShoppingListProduct();            break;
            case 'addOneToCart':    $this->addOneToCart();                         break;
            case 'addAllToCart':    $this->addAllToCart();                         break; // => ajout dans le panier et affichage du panier
            case 'gotoCatalog':    	$this->saveShoppingListAndGotoCatalog();       break; // => sauvegarde dans le cookie et retour au catalogue
            default:                $this->indexShoppingListProduct();             break; // Affichage de la liste 
        }

	}

	/**
	 * Affichgage de la Shopping List
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
		$this->context->smarty->assign('HOOK_LEFT_COLUMN', null); // Suppression de la colonne de gauche
		$this->context->smarty->assign('HOOK_TOP', null); // Suppression du menu Haut
		$this->addCSS(_MODULE_DIR_."shoppinglist/views/css/shoppinglist2.css"); 
		// Récupération des Quantités sockées dans le cookie
		if ($this->context->cookie->__isset('shoppingListProducts'))
		{
			$Quantities = json_decode($this->context->cookie->shoppingListProducts,true); // Decodage Json en tableau 
			$this->context->smarty->assign('Quantities', $Quantities);
		}

		$this->setTemplate('accountshoppinglistproductindex.tpl');
	}//indexShoppingListProduct
    
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
    private function updateProductInCart($idShoppingList, $idProduct, $idProductAttribute, $qty) {
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
        
//        elseif(!$productObj->checkQty(2)) {
//          $this->errors[] = $this->module->l('The product', 'accountshoppinglistproduct').' "'.$product['title'].'" '.$this->module->l('has no sufficient stock available', 'accountshoppinglistproduct');
//        }
        else {
            $cartObj = new Cart($this->context->cookie->id_cart);
			if ($cartObj->id == null)
			{
				$this->createCart();
				$cartObj = new Cart($this->context->cookie->id_cart);
			}
            $cartObj->updateQty($minimalQuantity, $idProduct, $idProductAttribute, false, "set");
            //$this->messages[] = $this->module->l('The product', 'accountshoppinglistproduct').' "'.$product['title'].'" '.$this->module->l('was added to cart', 'accountshoppinglistproduct');
        }
    }
    
    /**
	 * Adding a product to cart
	 */
    public function addOneToCart() {
        $idShoppingList = Tools::getValue('id_shopping_list');
        $idProduct = Tools::getValue('id_product');
        $idProductAttribute = Tools::getValue('id_product_attribute');
        $qty = Tools::getValue('qty');
        
        $this->updateProductInCart($idShoppingList, $idProduct, $idProductAttribute, $qty);

        $this->indexShoppingListProduct($idShoppingList);
    }
    
    /**
	 * Sauvegarde des Qtes Saisies dans la Shoppinglist er redirection vers le catalog
	 */
    public function saveShoppingListToCookie() {
        $idShoppingList = Tools::getValue('id_shopping_list');
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
		if ($shoppingListObj!=null)
		{
			$products = $shoppingListObj->getAllProducts(); // Récupération de tous les produits de la ShoppingList
			$tabShoppingList['IdShoppingList'] = $idShoppingList; // Création du tableau
			foreach($products as $product) {
				// Pour Chaque produit (id) , récupération de la qte Saisie
				$qte = Tools::getValue('qty_'.$product['id_product']);
				if ($qte!='')
				{
					$tabShoppingList['Q'.$product['id_product']]=$qte; // Stockage de la Quantité dans le tableau
				}
			}//foreach

			// Affection dans le cookie
			$this->context->cookie->__set('shoppingListProducts' , json_encode($tabShoppingList));

		}
	}

    /**
	 * Sauvegarde des Qtes Saisies dans la Shoppinglist er redirection vers le catalog
	 */
    public function saveShoppingListAndGotoCatalog() {
			$this->saveShoppingListToCookie(); 
			Tools::redirect($this->context->link->getCategoryLink(12, true));
	}//saveShoppingListAndGotoCatalog
	
	
    /**
	 * Adding all products to cart
	 *    Sauvegarde de la shoppingList dans le cookie
	 *	   Ajout des produit acheté dans le panier
	 *		Redirectoin vers la confirtmation de commande
	 */
    public function addAllToCart() {
		$this->saveShoppingListToCookie();
		$this->createCart();
        $idShoppingList = Tools::getValue('id_shopping_list');
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
		if ($shoppingListObj!=null)
		{
			// Récupération de la ShoppingList
			$products = $shoppingListObj->getAllProducts();
			foreach($products as $product) {
				// Pour Chaque produit (id) , récupération de la qte Saisie
				$qte = Tools::getValue('qty_'.$product['id_product'],'0');
				if ($qte!='0')
				{
					$this->updateProductInCart($idShoppingList, $product['id_product'], $product['id_product_attribute'],$qte);
				}
			}//foreach

		}
		// Display Cart
		Tools::redirect("index.php?controller=order-opc");
    }//addAllToCart


	/**
	* create a new Cart if it Doesn't Exist
	**/
	private function createCart()
	{
		//if (is_null($this->context->cart)) {

			$this->context->cart = 
				new Cart();
		//}

		if (is_null($this->context->cart->id_lang)) {
			 $this->context->cart->id_lang = $this->context->cookie->id_lang;
		}

		if (is_null($this->context->cart->id_currency)) {
			 $this->context->cart->id_currency = $this->context->cookie->id_currency;
		}

		if (is_null($this->context->cart->id_customer)) {
			 $this->context->cart->id_customer = $this->context->cookie->id_customer;
		}

		if (is_null($this->context->cart->id_guest)) {

			if (empty($this->context->cookie->id_guest)){
				$this->context->cookie->__set(
					'id_guest', 
					Guest::getFromCustomer($this->context->cookie->id_customer)
				);
			}
			$this->context->cart->id_guest = $this->context->cookie->id_guest;
		}

		if (is_null($this->context->cart->id)) {

			 $this->context->cart->add();

			 $this->context->cookie->__set('id_cart', $this->context->cart->id);
		}
		// Clean All Products
		$this->context->cart->deleteAssociations();
	}//createCart

    
}

