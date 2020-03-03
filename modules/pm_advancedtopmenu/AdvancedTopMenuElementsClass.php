<?php
/**
  * PM_AdvancedTopMenu Front Office Feature
  *
  * @category menu
  * @authors Presta-Module.com <support@presta-module.com>
  * @copyright Presta-Module 2011-2015
  * +
  * +Languages: EN, FR
  * +PS version: 1.6, 1.5, 1.4, 1.3, 1.2, 1.1
  **/
class		AdvancedTopMenuElementsClass extends ObjectModel
{
	public 		$id;
	/** @var string Value */
	public 		$link;
	/** @var string name */
	public 		$name;
	public 		$id_column;
	public 		$id_category;
	public 		$id_cms;
	public 		$id_supplier;
	public 		$id_manufacturer;
	public 		$have_icon;
	public 		$image_type;
	public 		$image_legend;
	public 		$privacy;
	public 		$type;
	public		$id_column_depend;
	public 		$position = 0;
	public 		$active = 1;
	public 		$active_mobile = 1;
	public 		$target;
	protected $tables = array ('pm_advancedtopmenu_elements', 'pm_advancedtopmenu_elements_lang');
	protected 	$fieldsRequired = array('active','id_column','type');
 	protected 	$fieldsSize = array('active' => 1);
 	protected 	$fieldsValidate = array('active' => 'isBool');
 	protected 	$fieldsSizeLang = array('link' => 256,'name' => 256);
 	protected 	$fieldsValidateLang = array('name' => 'isCatalogName', 'link' => 'isUrl', 'have_icon'=>'isBool', 'image_type'=>'isString', 'image_legend' => 'isCatalogName');
	protected 	$table = 'pm_advancedtopmenu_elements';
	protected 	$identifier = 'id_element';
	public static $definition = array(
		'table' => 'pm_advancedtopmenu_elements',
		'primary' => 'id_element',
		'multishop' => false,
		'multilang_shop' => false,
		'multilang' => true,
		'fields' => array(
			'name' => 				array('type' => 3, 'lang' => true, 'required' => false, 'size' => 64),
			'link' => 				array('type' => 3, 'lang' => true, 'required' => false),
			'have_icon' =>	 		array('type' => 3, 'lang' => true, 'required' => false),
			'image_type' => 		array('type' => 3, 'lang' => true, 'required' => false),
			'image_legend' => 		array('type' => 3, 'lang' => true, 'required' => false),
		)
	);
	public function __construct($id_element = NULL, $id_lang = NULL)
	{
		parent::__construct($id_element, $id_lang);
	}
	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_element'] = intval($this->id);
		$fields['id_column'] = intval($this->id_column);
		$fields['id_category'] = intval($this->id_category);
		$fields['id_cms'] = intval($this->id_cms);
		$fields['id_supplier'] = intval($this->id_supplier);
		$fields['id_manufacturer'] = intval($this->id_manufacturer);
		$fields['type'] = intval($this->type);
		$fields['active'] 	= intval($this->active);
		$fields['active_mobile'] = intval($this->active_mobile);
		$fields['position'] 	= intval($this->position);
		$fields['privacy'] = intval($this->privacy);
		$fields['id_column_depend'] 	= intval($this->id_column_depend);
		$fields['target'] = pSQL($this->target);
		return $fields;
	}
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		$fieldsArray = array('name', 'link', 'have_icon', 'image_type', 'image_legend');
		$fields = array();
		$languages = Language::getLanguages(false);
		$defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
			$fields[$language['id_lang']][$this->identifier] = intval($this->id);
			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());
				if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']]);
				elseif (in_array($field, $this->fieldsRequiredLang))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage]);
				else
					$fields[$language['id_lang']][$field] = '';
			}
		}
		return $fields;
	}
	public	function add($autodate = true, $nullValues = false)
	{
		$ret = parent::add($autodate, $nullValues);
		return $ret;
	}
	public	function update($nullValues = false)
	{
		$ret = parent::update($nullValues);
		return $ret;
	}
	public function delete()
	{
		$toDelete = array(intval($this->id));
		$toDelete = array_unique($toDelete);
		$languages = Language::getLanguages(false);
		foreach ($toDelete as $id_element) {
			foreach ($languages as $language)
				if (file_exists(_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/element_icons/' . $id_element . '-' . $language['iso_code'] . '.' . ($this->image_type[$language['id_lang']] ? $this->image_type[$language['id_lang']] : 'jpg')))
					@unlink(_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/element_icons/' . $id_element . '-' . $language['iso_code'] . '.' . ($this->image_type[$language['id_lang']] ? $this->image_type[$language['id_lang']] : 'jpg'));
		}
		$list = sizeof($toDelete) > 1 ? implode(',', $toDelete) : intval($this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_advancedtopmenu_elements` WHERE `id_element` IN ('.$list.')');
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_advancedtopmenu_elements_lang` WHERE `id_element` IN ('.$list.')');
		return true;
	}
	public static function getMenuColumnElements($id_column, $id_lang, $active = true, $groupRestrict = false)
	{
		$sql_groups_join = '';
		$sql_groups_where = '';
		if ($groupRestrict && ((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Group::isFeatureActive()) || (version_compare(_PS_VERSION_, '1.5.0.0', '<') && version_compare(_PS_VERSION_, '1.2.5.0', '>=')))) {
			$groups = PM_AdvancedTopMenu::getCustomerGroups();
			if (sizeof($groups)) {
				$sql_groups_join = 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = ca.`id_category`)';
				$sql_groups_where = 'AND IF (ate.`id_category` IS NULL OR ate.`id_category` = 0, 1, cg.`id_group` IN ('.implode(',', $groups).'))';
			}
		}
		$sql = 'SELECT ate.*, atel.*,
		cl.link_rewrite, cl.meta_title,
		cal.link_rewrite as category_link_rewrite, cal.name as category_name,
		m.name as manufacturer_name,
		s.name as supplier_name
		FROM `'._DB_PREFIX_.'pm_advancedtopmenu_elements` ate
				LEFT JOIN `'._DB_PREFIX_.'pm_advancedtopmenu_elements_lang` atel ON (ate.`id_element` = atel.`id_element` AND atel.`id_lang` = '.intval($id_lang).')
				LEFT JOIN '._DB_PREFIX_.'cms c ON (c.id_cms = ate.`id_cms`)
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlAssociation('cms', 'c',false):'').'
				LEFT JOIN '._DB_PREFIX_.'cms_lang cl ON (c.id_cms = cl.id_cms AND cl.id_lang = '.intval($id_lang).')
				LEFT JOIN '._DB_PREFIX_.'category ca ON (ca.id_category = ate.`id_category`)
				' . $sql_groups_join . '
				LEFT JOIN '._DB_PREFIX_.'category_lang cal ON (ca.id_category = cal.id_category AND cal.id_lang = '.intval($id_lang).(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlRestrictionOnLang('cal'):'').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (ate.`id_manufacturer` = m.`id_manufacturer`)
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlAssociation('manufacturer', 'm',false):'').'
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (ate.`id_supplier` = s.`id_supplier`)
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlAssociation('supplier', 's',false):'').'
				WHERE '.($active?' ate.`active` = 1 AND':'').' ate.`id_column` = '.intval($id_column).'
				'.($active?'AND	((ate.`id_manufacturer` = 0 AND ate.`id_supplier` = 0 AND ate.`id_category` = 0 AND ate.`id_cms` = 0)
				OR c.id_cms IS NOT NULL OR m.id_manufacturer IS NOT NULL OR ca.id_category IS NOT NULL OR s.`id_supplier` IS NOT NULL)':'').'
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND (cms_shop.`id_shop` = '.(int)Context::getContext()->shop->id.' OR cms_shop.`id_shop` IS NULL) AND (manufacturer_shop.`id_shop` = '.(int)Context::getContext()->shop->id.' OR manufacturer_shop.`id_shop` IS NULL) AND (supplier_shop.`id_shop` = '.(int)Context::getContext()->shop->id.' OR supplier_shop.`id_shop` IS NULL)':'')
				. $sql_groups_where . '
				GROUP BY ate.`id_element`
				ORDER BY ate.`position`';
		return Db::getInstance()->ExecuteS($sql);
	}
	public static function getMenuColumnsElements($menus, $id_lang, $active = true, $groupRestrict = false)
	{
		$elements = array();
		foreach($menus as $columns) {
			foreach($columns as $column) {
				$elements[$column['id_column']] = self::getMenuColumnElements($column['id_column'], $id_lang, $active, $groupRestrict);
			}
		}
		return $elements;
	}
	public static function getElementIds($ids_column)
	{
		$result =  Db::getInstance()->ExecuteS('
		SELECT `id_element`
		FROM '._DB_PREFIX_.'pm_advancedtopmenu_elements
		WHERE `id_column` IN('.pSQL($ids_column).')');
		$elements = array();
		foreach ($result as $row) {
			$elements[] = $row['id_element'];
		}
		return $elements;
	}
	public static function getIdElementCategoryDepend($id_column,$id_category)
	{
		$row = Db::getInstance()->getRow('SELECT `id_element`
				FROM `'._DB_PREFIX_.'pm_advancedtopmenu_elements`
				WHERE `id_column_depend` = '.intval($id_column).' AND `id_category` = '.intval($id_category));
		return (isset($row['id_element'])?$row['id_element']:false);
	}
	public static function getIdElementManufacturerDepend($id_column,$id_manufacturer)
	{
		$row = Db::getInstance()->getRow('SELECT `id_element`
				FROM `'._DB_PREFIX_.'pm_advancedtopmenu_elements`
				WHERE `id_column_depend` = '.intval($id_column).' AND `id_manufacturer` = '.intval($id_manufacturer));
		return (isset($row['id_element'])?$row['id_element']:false);
	}
	public static function getIdElementSupplierDepend($id_column,$id_supplier)
	{
		$row = Db::getInstance()->getRow('SELECT `id_element`
				FROM `'._DB_PREFIX_.'pm_advancedtopmenu_elements`
				WHERE `id_column_depend` = '.intval($id_column).' AND `id_supplier` = '.intval($id_supplier));
		return (isset($row['id_element'])?$row['id_element']:false);
	}
}
?>
