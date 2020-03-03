<?php

/**
* Class ShoppingListAccountShoppingListModuleFrontController
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class ShoppingListAccountShoppingListModuleFrontController extends ModuleFrontController {
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
            case 'add':             $this->addShoppingList();               break;
            case 'delete':  
            case 'deleteConfirm':   $this->deleteShoppingList();            break;
            case 'update':          $this->updateShoppingList();            break;
            default:                $this->indexShoppingList();             break;
        }
	}

	/**
	 * Index shopping list
	 */
	public function indexShoppingList()
	{
        $shoppingListObj = new ShoppingListObject();
        
        //Get List of all 
        $shoppingList = $shoppingListObj->getByIdCustomer($this->context->cookie->id_customer);
        
        $this->context->smarty->assign('messages', $this->messages);
        $this->context->smarty->assign('errors', $this->errors);
        $this->context->smarty->assign('shoppingList', $shoppingList);
        $this->setTemplate('accountshoppinglistindex.tpl');
	}
    
    /**
	 * Add shopping list
	 */
	public function addShoppingList() {
        $shoppingListObj = new ShoppingListObject();
        
        $title = Tools::getValue('title');
        if (!empty($title)) {
            $shoppingListObj->id_customer = $this->context->cookie->id_customer;
            $shoppingListObj->title = $title;
            $shoppingListObj->status = 1;
            $date = new \DateTime();
            $shoppingListObj->date_add = $date;
            $shoppingListObj->date_upd = $date;
            
            try {
                $shoppingListObj->add();
                $this->messages[] = $this->module->l('Shopping List added', 'accountshoppinglist');
            }
            catch (Exception $e) {
                $this->errors[] = $this->module->l('Error! Perhaps this Shopping list already exist', 'accountshoppinglist');
            }
            
            $this->indexShoppingList();
        }
        else {
            $this->context->smarty->assign('introduction', $this->module->l('Add a new Shopping list', 'accountshoppinglist'));
            $this->context->smarty->assign('action', 'add');
            $this->context->smarty->assign('submit', $this->module->l('Add', 'accountshoppinglist'));
            $this->context->smarty->assign('shoppingListObj', $shoppingListObj);
            $this->setTemplate('accountshoppinglistform.tpl');
        }
    }
    
    /**
	 * Update shopping list
	 */
	public function updateShoppingList() {      
        $idShoppingList = Tools::getValue('id_shopping_list');
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        
        $title = Tools::getValue('title');
        if (!empty($title)) {
            $shoppingListObj->title = $title;
            $shoppingListObj->date_upd = new \DateTime();
            try {
                $shoppingListObj->update();
                $this->messages[] = $this->module->l('Shopping List updated', 'accountshoppinglist');
            }
            catch (Exception $e) {
                $this->errors[] = $this->module->l('Error! Perhaps this Shopping list already exist', 'accountshoppinglist');
            }
            
            $this->indexShoppingList();
        }
        else {
            $this->context->smarty->assign('introduction', $this->module->l('Update a shopping list', 'accountshoppinglist'));
            $this->context->smarty->assign('action', 'update');
            $this->context->smarty->assign('submit', $this->module->l('Update', 'accountshoppinglist'));
            $this->context->smarty->assign('shoppingListObj', $shoppingListObj);
            $this->setTemplate('accountshoppinglistform.tpl');
        }
    }
    
    /**
	 * Delete shopping list
	 */
	public function deleteShoppingList()
	{
        $action = Tools::getValue('action');
        $idShoppingList = Tools::getValue('id_shopping_list');
        $shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $this->context->cookie->id_customer);
        
        if($shoppingListObj == null) {
            $this->errors[] = $this->module->l('An error occur', 'accountshoppinglist');
            $this->indexShoppingList();
            return;
        } 
        if($action == "delete") {
            $this->context->smarty->assign('shoppingListObj', $shoppingListObj);
            $this->setTemplate('accountshoppinglistdelete.tpl');
        }
        if($action == "deleteConfirm") {
            //It's not possible to delete when only one shopping list
            if($shoppingListObj->getNumberShoppingListByIdCustomer($this->context->cookie->id_customer) > 1) {
                $shoppingListObj->status = 0;
                $shoppingListObj->date_upd = new \DateTime();
                $shoppingListObj->update();
                $this->messages[] = $this->module->l('Shopping List deleted', 'accountshoppinglist');
            }
            else {
                $this->errors[] = $this->module->l('Impossible to delete this shopping list! It\'s necessary to have one shopping list', 'accountshoppinglist');
            }
            
            $this->indexShoppingList();
        }
	}
}