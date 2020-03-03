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
class		AdvancedTopMenuClass extends ObjectModel
{
	public 		$id;
	public 		$id_category;
	public 		$id_cms;
	public 		$id_supplier;
	public 		$id_manufacturer;
	public		$id_shop;
	public 		$name;
	public 		$link;
	public 		$active = 1;
	public 		$active_mobile = 1;
	public 		$type;
	public 		$privacy;
	public 		$have_icon;
	public 		$image_type;
	public 		$image_legend;
	public 		$value_over;
	public 		$value_under;
	public 		$target;
	public		$txt_color_menu_tab;
	public		$txt_color_menu_tab_hover;
	public		$fnd_color_menu_tab;
	public		$fnd_color_menu_tab_over;
	public 		$width_submenu;
	public 		$minheight_submenu;
	public 		$position_submenu;
	public		$fnd_color_submenu;
	public		$border_color_submenu;
	public 		$border_color_tab;
	public 		$border_size_tab;
	public 		$border_size_submenu;
	protected $tables = array ('pm_advancedtopmenu', 'pm_advancedtopmenu_lang');
	protected 	$fieldsRequired = array('active','type');
 	protected 	$fieldsSize = array('active' => 1);
 	protected 	$fieldsValidate = array('active' => 'isBool');
	protected 	$fieldsRequiredLang = array();
 	protected 	$fieldsSizeLang = array('name' => 64);
 	protected 	$fieldsValidateLang = array('name' => 'isCatalogName', 'link'=>'isUrl', 'have_icon'=>'isBool', 'image_type'=>'isString', 'image_legend'=>'isCatalogName');
	protected 	$table = 'pm_advancedtopmenu';
	protected 	$identifier = 'id_menu';
	public static $definition = array(
		'table' => 'pm_advancedtopmenu',
		'primary' => 'id_menu',
		'multishop' => true,
		'multilang_shop' => false,
		'multilang' => true,
		'fields' => array(
			'name' => 				array('type' => 3, 'lang' => true, 'required' => false, 'size' => 64),
			'link' => 				array('type' => 3, 'lang' => true, 'required' => false),
			'value_over' => 		array('type' => 3, 'lang' => true, 'required' => false),
			'value_under' => 		array('type' => 3, 'lang' => true, 'required' => false),
			'have_icon' =>	 		array('type' => 3, 'lang' => true, 'required' => false),
			'image_type' => 		array('type' => 3, 'lang' => true, 'required' => false),
			'image_legend' => 		array('type' => 3, 'lang' => true, 'required' => false),
		)
	);
	public function __construct($id_menu = NULL, $id_lang = NULL, $id_shop = null)
	{
		 if(version_compare(_PS_VERSION_, '1.3.0.0', '<')) {
			$this->fieldsValidateLang['value_over'] = 'isCleanHTML';
			$this->fieldsValidateLang['value_under'] = 'isCleanHTML';
		 }else {
		 	$this->fieldsValidateLang['value_over'] = 'isString';
			$this->fieldsValidateLang['value_under'] = 'isString';
		 }
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			if (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.5.2.0', '<=') && class_exists ("ShopPrestaModule")) {
				ShopPrestaModule::PrestaModule_setAssoTable(self::$definition['table']);
			} else {
				Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
			}
			parent::__construct($id_menu, $id_lang, $id_shop);
		} else {
			parent::__construct($id_menu, $id_lang);
		}
	}
	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_menu'] = intval($this->id);
		$fields['active'] = intval($this->active);
		$fields['active_mobile'] = intval($this->active_mobile);
		$fields['id_shop'] 	= intval($this->id_shop);
		$fields['id_category'] = intval($this->id_category);
		$fields['id_cms'] = intval($this->id_cms);
		$fields['id_supplier'] = intval($this->id_supplier);
		$fields['id_manufacturer'] = intval($this->id_manufacturer);
		$fields['type'] = intval($this->type);
		$fields['target'] = pSQL($this->target);
		$fields['privacy'] = intval($this->privacy);
		$fields['txt_color_menu_tab'] = pSQL($this->txt_color_menu_tab);
		$fields['txt_color_menu_tab_hover'] = pSQL($this->txt_color_menu_tab_hover);
		$fields['fnd_color_menu_tab'] = pSQL($this->fnd_color_menu_tab);
		$fields['fnd_color_menu_tab_over'] = pSQL($this->fnd_color_menu_tab_over);
		$fields['width_submenu'] = pSQL($this->width_submenu);
		$fields['minheight_submenu'] = pSQL($this->minheight_submenu);
		$fields['position_submenu'] = intval($this->position_submenu);
		$fields['fnd_color_submenu'] = pSQL($this->fnd_color_submenu);
		$fields['border_color_submenu'] = pSQL($this->border_color_submenu);
		$fields['border_color_tab'] = pSQL($this->border_color_tab);
		$fields['border_size_tab'] = pSQL($this->border_size_tab);
		$fields['border_size_submenu'] = pSQL($this->border_size_submenu);
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
			$fields[$language['id_lang']]['value_over'] = (isset($this->value_over[$language['id_lang']])) ? pSQL($this->value_over[$language['id_lang']], true) : '';
			$fields[$language['id_lang']]['value_under'] = (isset($this->value_under[$language['id_lang']])) ? pSQL($this->value_under[$language['id_lang']], true) : '';
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
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$this->id_shop = Context::getContext()->shop->id;
		}
		$ret = parent::add($autodate,$nullValues);
		return $ret;
	}
	public	function update($nullValues = false)
	{
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$this->id_shop = Context::getContext()->shop->id;
		$ret = parent::update($nullValues);
		return $ret;
	}
	public function delete()
	{
		if(!isset($this->id) || !$this->id) return;
		$toDelete = array(intval($this->id));
		$toDelete = array_unique($toDelete);
		$languages = Language::getLanguages(false);
		foreach ($toDelete as $id_menu) {
			foreach ($languages as $language)
				if (file_exists(_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/menu_icons/' . $id_menu . '-' . $language['iso_code'] . '.'. ($this->image_type[$language['id_lang']] ? $this->image_type[$language['id_lang']] : 'jpg')))
					@unlink(_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/menu_icons/' . $id_menu . '-' . $language['iso_code'] . '.'. ($this->image_type[$language['id_lang']] ? $this->image_type[$language['id_lang']] : 'jpg'));
		}
		$columnsWrap = AdvancedTopMenuColumnWrapClass::getColumnWrapIds($this->id);
		foreach ($columnsWrap as $id_wrap) {
			$obj = new AdvancedTopMenuColumnWrapClass($id_wrap);
			$obj->delete();
		}
		return parent::delete();
	}
	public static function menuHaveDepend($id_menu)
	{
		$sql = 'SELECT `id_column`
				FROM `'._DB_PREFIX_.'pm_advancedtopmenu_columns`
				WHERE `id_menu_depend` = '.intval($id_menu);
		return Db::getInstance()->ExecuteS($sql);
	}
	public static function getMenusId()
	{
		$sql = 'SELECT atp.`id_menu`
		FROM `'._DB_PREFIX_.'pm_advancedtopmenu` atp';
		return Db::getInstance()->ExecuteS($sql);
	}
	public static function getMenus($id_lang, $active = true, $get_from_all_shops = false, $groupRestrict = false)
	{
		$sql_groups_join = '';
		$sql_groups_where = '';
		if ($groupRestrict && ((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Group::isFeatureActive()) || (version_compare(_PS_VERSION_, '1.5.0.0', '<') && version_compare(_PS_VERSION_, '1.2.5.0', '>=')))) {
			$groups = PM_AdvancedTopMenu::getCustomerGroups();
			if (sizeof($groups)) {
				$sql_groups_join = 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = ca.`id_category`)';
				$sql_groups_where = 'AND IF (atp.`id_category` IS NULL OR atp.`id_category` = 0, 1, cg.`id_group` IN ('.implode(',', $groups).'))';
			}
		}
		$sql = 'SELECT atp.*, atpl.*,
				cl.link_rewrite, cl.meta_title,
				cal.link_rewrite as category_link_rewrite, cal.name as category_name,
				m.name as manufacturer_name,
				s.name as supplier_name
				FROM `'._DB_PREFIX_.'pm_advancedtopmenu` atp
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive() ? self::addSqlAssociation('pm_advancedtopmenu', 'atp', 'id_menu', true, null, ($get_from_all_shops ? 'all' :false)):'').'
				LEFT JOIN `'._DB_PREFIX_.'pm_advancedtopmenu_lang` atpl ON (atp.`id_menu` = atpl.`id_menu` AND atpl.`id_lang` = '.intval($id_lang).')
				LEFT JOIN '._DB_PREFIX_.'cms c ON (c.id_cms = atp.`id_cms`)
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlAssociation('cms', 'c',false, true, null, ($get_from_all_shops ? 'all' : false)):'').'
				LEFT JOIN '._DB_PREFIX_.'cms_lang cl ON (c.id_cms = cl.id_cms AND cl.id_lang = '.intval($id_lang).')
				LEFT JOIN '._DB_PREFIX_.'category ca ON (ca.id_category = atp.`id_category`)
				' . $sql_groups_join . '
				LEFT JOIN '._DB_PREFIX_.'category_lang cal ON (ca.id_category = cal.id_category AND cal.id_lang = '.intval($id_lang).(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlRestrictionOnLang('cal'):'').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (atp.`id_manufacturer` = m.`id_manufacturer`)
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlAssociation('manufacturer', 'm',false, true, null, ($get_from_all_shops ? 'all' : false)):'').'
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (atp.`id_supplier` = s.`id_supplier`)
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlAssociation('supplier', 's',false, true, null, ($get_from_all_shops ? 'all' : false)):'').'
				'.($active ? '
				WHERE atp.`active` = 1
				AND ((atp.`id_manufacturer` = 0 AND atp.`id_supplier` = 0 AND atp.`id_category` = 0 AND atp.`id_cms` = 0)
				OR c.id_cms IS NOT NULL OR m.id_manufacturer IS NOT NULL OR ca.id_category IS NOT NULL OR s.`id_supplier` IS NOT NULL)
				' . $sql_groups_where : '').'
				GROUP BY atp.`id_menu`
				ORDER BY atp.`position`';
		return Db::getInstance()->ExecuteS($sql);
	}
	public static function addSqlAssociation($table, $alias, $identifier, $inner_join = true, $on = null, $shops = false)
	{
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			if ($shops == 'all') $ids_shop = array_values(Shop::getCompleteListOfShopsID());
			else if (is_array($shops) && sizeof($shops)) $ids_shop = array_values($shops);
			else if (is_numeric($shops) ) $ids_shop = array($shops);
			else $ids_shop = array_values(Shop::getContextListShopID());
			$table_alias = $alias.'_shop';
			if (strpos($table, '.') !== false) list($table_alias, $table) = explode('.', $table);
			$sql = (($inner_join) ? ' INNER' : ' LEFT').' JOIN `'._DB_PREFIX_.$table.'_shop` '.$table_alias.'
						ON '.$table_alias.'.'.$identifier.' = '.$alias.'.'.$identifier.'
						AND '.$table_alias.'.id_shop IN ('.implode(', ', $ids_shop).') '
						.(($on) ? ' AND '.$on : '');
			return $sql;
		}
		return;
	}
}
?>
