<?php

/**
* Class ShoppingListObject
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
/********
* Logging
//		$logger = new FileLogger(0); //0 == debug level, logDebug() won’t work without this.
//		$logger->setFilename(_PS_ROOT_DIR_."/log/debug.log");
//			$logger->logDebug($sql);         
**/
class ShoppingListObject extends ObjectModel 
{
	/** @var int Id Shopping List */
	public $id_shopping_list;
    
    /** @var int Id Customer */
	public $id_customer;
		
	/** @var string Title */
	public $title;
	
	/** @var date Date Add */
	public $date_add;
    
    /** @var date Date Update */
	public $date_upd;
    
    /** @var int Status */
	public $status;
	
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'shopping_list',
        'primary' => 'id_shopping_list',
        'multilang' => FALSE,
        'fields' => array(
            'id_shopping_list' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => TRUE),
            'title' => array(
                'type' => self::TYPE_STRING, 
                'validate' => 'isString',
                'align' => 'center',
                'filter_key' => 'title'
            ),
//            'couleur' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
//            'conditionnement' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
//            'millesime' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );
    
    public function getNumberShoppingListByIdCustomer($idCustomer){
        $result = $this->getByIdCustomer($idCustomer);
        
        return count($result);
    }
	
    public function getByIdCustomer($idCustomer){
        $results = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'shopping_list` shoppinglist
            WHERE shoppinglist.`id_customer` = '.(int)$idCustomer.' '.
            'AND STATUS=1'
        );

        return $results;
    }
    
    public static function loadByIdAndCustomer($idShoppingList, $idCustomer) {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'shopping_list` shoppinglist
            WHERE shoppinglist.`id_shopping_list` = '.(int)$idShoppingList.' '.
            'AND shoppinglist.`id_customer` = '.(int)$idCustomer
        );
        
        if(!empty($result['id_shopping_list'])) {
            return new ShoppingListObject($result['id_shopping_list']);
        }
        else {
            return null;
        }
    }
    
    public function getOneProduct($idShoppingList, $idProduct, $idProductAttribute) {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'shopping_list_product` shoppinglistproduct
            WHERE shoppinglistproduct.`id_shopping_list` = '.(int)$idShoppingList.' '.
            'AND shoppinglistproduct.`id_product` = '.(int)$idProduct.' '.
            'AND shoppinglistproduct.`id_product_attribute` = '.(int)$idProductAttribute
        );
        
        return $result;
    }

    // Version modifiée pour récupérer les champs supplémentaires couleur, conditionnement et millésime
	// Suppression des produits inactifs (active = 1)
    public function getAllProducts() {
	//            LEFT OUTER JOIN ('._DB_PREFIX_.'product_attribute vzmpa 

        $results = Db::getInstance()->executeS('
            SELECT slp.id_shopping_list, slp.id_product, slp.id_product_attribute, slp.title, slp.quantity,
            fvl.value AS millesime, fvlCoul.value AS couleur, fvlCont.value AS contenant, fvlCond.value as conditionnement, p.reference as reference, pl.name as designation

            FROM `'._DB_PREFIX_.'shopping_list_product` slp
            
            LEFT OUTER JOIN ('._DB_PREFIX_.'product_attribute vzmpa 

            INNER JOIN '._DB_PREFIX_.'product_attribute_combination vzmpac ON vzmpa.id_product_attribute = vzmpac.id_product_attribute
            INNER JOIN '._DB_PREFIX_.'attribute_lang vzmal ON vzmpac.id_attribute = vzmal.id_attribute
            INNER JOIN '._DB_PREFIX_.'attribute vzmag ON vzmal.id_attribute = vzmag.id_attribute AND vzmag.id_attribute_group=3

            INNER JOIN '._DB_PREFIX_.'product_attribute_combination vzpac ON vzpac.id_product_attribute = vzmpa.id_product_attribute
            INNER JOIN '._DB_PREFIX_.'attribute_lang vzal ON vzpac.id_attribute = vzal.id_attribute
            INNER JOIN '._DB_PREFIX_.'attribute vzag ON vzal.id_attribute = vzag.id_attribute AND vzag.id_attribute_group=1

            ) ON slp.id_product = vzmpa.id_product and slp.id_product_attribute = vzmpa.id_product_attribute

            INNER JOIN `'._DB_PREFIX_.'product` p ON p.id_product=slp.id_product
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON pl.id_product=slp.id_product and pl.id_lang = 1
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fp ON fp.id_product=slp.id_product AND fp.id_feature=8
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON fvl.id_feature_value = fp.id_feature_value

			LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fpCond ON fpCond.id_product=slp.id_product AND fpCond.id_feature=9
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvlCond ON fvlCond.id_feature_value = fpCond.id_feature_value

			LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fpCoul ON fpCoul.id_product=slp.id_product AND fpCoul.id_feature=11
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvlCoul ON fvlCoul.id_feature_value = fpCoul.id_feature_value

			LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fpCont ON fpCont.id_product=slp.id_product AND fpCont.id_feature=12
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvlCont ON fvlCont.id_feature_value = fpCont.id_feature_value

            WHERE slp.`id_shopping_list` = '.(int)$this->id_shopping_list .  ' and p.active = 1'
        );

        return $results;
    }

    // Affiche tout le cataloque - utilisée dans accountfulllistproduct
    public static function getFullAllProducts() {
        $results = Db::getInstance()->executeS('
            SELECT p.id_product, pl.name as title ,p.reference AS reference,
            fvlm.value AS millesime, fvlCoul.value AS couleur, fvlcont.value AS contenant, 1 as conditionnement, fvlcond.value AS conditionnement
            FROM `'._DB_PREFIX_.'product` p 

            LEFT OUTER JOIN '._DB_PREFIX_.'product_lang pl ON pl.id_product = p.id_product AND pl.id_lang=1

            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fpm ON fpm.id_product=p.id_product AND fpm.id_feature=8
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvlm ON fvlm.id_feature_value = fpm.id_feature_value

            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fpcoul ON fpcoul.id_product=p.id_product AND fpcoul.id_feature=11
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvlcoul ON fvlcoul.id_feature_value = fpcoul.id_feature_value

            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fpcond ON fpcond.id_product=p.id_product AND fpcond.id_feature=9
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvlcond ON fvlcond.id_feature_value = fpcond.id_feature_value

            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_product` fpcont ON fpcont.id_product=p.id_product AND fpcont.id_feature=12
            LEFT OUTER JOIN `'._DB_PREFIX_.'feature_value_lang` fvlcont ON fvlcont.id_feature_value = fpcont.id_feature_value

            WHERE p.available_for_order=1 and p.active = 1 and p.reference <> "" ORDER BY p.reference'
        );

        return $results;
    }

    
    public function deleteProduct($idProduct, $idProductAttribute) {
		$bReturn = false;
        try {
			$result = Db::getInstance()->execute('
				DELETE 
				FROM `'._DB_PREFIX_.'shopping_list_product` 
				WHERE `id_shopping_list` = '.(int)$this->id_shopping_list.' '.
				'AND `id_product` = '.(int)$idProduct.' '.
				'AND `id_product_attribute` = '.(int)$idProductAttribute.' ' 
			);
			$bReturn = true;
        }
        catch (Exception $e) {
            $bReturn=false;
        }
        
        return $bReturn;
    }//deleteProduct
    
    public function addProduct($idProduct, $idProductAttribute, $title) {
		$bReturn = false;
        try {
/*						$logger = new FileLogger(0); //0 == debug level, logDebug() won’t work without this.
					$logger->setFilename(_PS_ROOT_DIR_."/log/debug.log");

					$logger->logDebug("S1");
*/

            $sql = 'INSERT INTO '._DB_PREFIX_.'shopping_list_product (id_shopping_list, id_product, id_product_attribute, title,quantity)'.
			' VALUES ('.(int)$this->id_shopping_list.', '.(int)$idProduct.', '.(int)$idProductAttribute.', \''.pSQL($title).'\',0);';
			
			//$logger->logDebug($sql);
            $result = Db::getInstance()->execute($sql);
			$bReturn = true;
        }
        catch (Exception $e) {
            $bReturn=false;
        }
        
        return $bReturn;
    }//addProduct
 
	public function isproductinShoppingList($pidProduct) {
		$bReturn = false;
		try {
            $sql = 'SELECT count(*) as nbre FROM `'._DB_PREFIX_.'shopping_list_product` 
                where  id_shopping_list = '.(int)$this->id_shopping_list.' and id_product= '.(int)$pidProduct.';';
            $result = Db::getInstance()->getRow($sql);
			$bReturn = ((int)$result['nbre'] >0);
        }
        catch (Exception $e) {
            $bReturn=false;
        }
        
        return $bReturn;
    }//isproductinShoppingList

	public function UpdateQty($pidProduct, $pqte) {
		$bReturn = false;
		try {
            $sql = 'UPDATE '._DB_PREFIX_.'shopping_list_product'.
				' SET quantity = ' . $pqte. ''.
                ' where  id_shopping_list = '.(int)$this->id_shopping_list.' and id_product= '.(int)$pidProduct.';';

				$result = Db::getInstance()->execute($sql);
			$bReturn = true;
        }
        catch (Exception $e) {
            $bReturn=false;
        }
        
        return $bReturn;
    }//UpdateQty
	
	public static function updateShoppingListCustomer($pidCustommer, $pProduct)
	{
	
		$shoppingListObj = new ShoppingListObject();
		$shoppingListList = $shoppingListObj->getByIdCustomer($pidCustommer);
		if (count($shoppingListList)>0 )
		{
			$shoppingListObj= shoppingListObject::loadByIdAndCustomer($shoppingListList[0]['id_shopping_list'],$pidCustommer) ;
			// Mise à jour de la shoppingList
			foreach ($pProduct as $product) 
			{
					$shoppingListObj->UpdateQty((int)$product['id_product'], $product['cart_quantity']);				
			}
			
		}
	}//updateShoppingListCustomer

}