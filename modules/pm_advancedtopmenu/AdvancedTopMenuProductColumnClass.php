<?php
/**
  * PM_AdvancedTopMenu Front Office Feature
  *
  * @category menu
  * @authors Presta-Module.com <support@presta-module.com>
  * @copyright Presta-Module 2011-2015
  * +
  * +Languages: EN, FR
  * +PS version: 1.6, 1.5, 1.4
  **/
class AdvancedTopMenuProductColumnClass extends ObjectModel
{
	public $id;
	public $id_column;
	public $id_product;
	public $p_image_type;
	public $show_title = 1;
	public $show_price = 1;
	public $show_add_to_cart = 1;
	public $show_more_info = 1;
	public $show_quick_view = 1;
	protected $tables = array ('pm_advancedtopmenu_prod_column');
	protected $fieldsRequired = array('id_column','id_product');
 	protected $fieldsSize = array();
 	protected $fieldsValidate = array();
	protected $table = 'pm_advancedtopmenu_prod_column';
	protected $identifier = 'id_product_column';
	public static $definition = array(
		'table' => 'pm_advancedtopmenu_prod_column',
		'primary' => 'id_product_column',
		'multishop' => false,
		'multilang_shop' => false,
		'multilang' => false,
	);
	public function getFields() {
		parent::validateFields();
		if (isset($this->id)) {
			$fields['id_product_column'] = intval($this->id);
		}
		$fields['id_column'] = intval($this->id_column);
		$fields['id_product'] = intval($this->id_product);
		$fields['show_title'] = intval($this->show_title);
		$fields['show_price'] = intval($this->show_price);
		$fields['show_add_to_cart'] = intval($this->show_add_to_cart);
		$fields['show_more_info'] = intval($this->show_more_info);
		$fields['show_quick_view'] = intval($this->show_quick_view);
		$fields['p_image_type'] = pSQL($this->p_image_type);
		return $fields;
	}
	public static function getByIdColumn($id_column) {
		$row = Db::getInstance()->getRow('
			SELECT `id_product_column`
			FROM `'._DB_PREFIX_.'pm_advancedtopmenu_prod_column`
			WHERE `id_column`='.intval($id_column)
		);
		if (isset($row['id_product_column'])) {
			return new AdvancedTopMenuProductColumnClass($row['id_product_column']);
		}
		return false;
	}
}
