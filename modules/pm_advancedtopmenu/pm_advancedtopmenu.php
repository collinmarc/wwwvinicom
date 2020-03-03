<?php
/**
 * PM_AdvancedTopMenu Front Office Feature
 *
 * @category menu
 * @authors Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module 2011-2015
 * @version 1.11.1
 *
 * *************************************
 * *       Advanced Top Menu      		*
 * *   http://www.presta-module.com   	*
 * *************************************
 * +
 * +Languages: EN, FR
 * +PS version: 1.6, 1.5, 1.4, 1.3, 1.2, 1.1
 **/
include_once (_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/ShopOverrided.php');
include_once (_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/AdvancedTopMenuClass.php');
include_once (_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/AdvancedTopMenuColumnWrapClass.php');
include_once (_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/AdvancedTopMenuColumnClass.php');
include_once (_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/AdvancedTopMenuElementsClass.php');
include_once (_PS_ROOT_DIR_ . '/modules/pm_advancedtopmenu/AdvancedTopMenuProductColumnClass.php');
class PM_AdvancedTopMenu extends Module {
	private $_html;
	public static $_module_prefix = 'ATM';
	private $errors = array ();
	private $defaultLanguage;
	private $languages;
	private $_iso_lang;
	private $_context;
	private $_cookie;
	private $_smarty;
	private $_employee;
	private $current_category_product_url = false;
	private $activeAllreadySet = false;
	private $base_config_url;
	private $gradient_separator = '-';
	private $rebuildable_type = array (3, 4, 5 );
	private $font_families = array ("Arial, Helvetica, sans-serif", "'Arial Black', Gadget, sans-serif", "'Bookman Old Style', serif", "'Comic Sans MS', cursive", "Courier, monospace", "'Courier New', Courier, monospace", "Garamond, serif", "Georgia, serif", "Impact, Charcoal, sans-serif", "'Lucida Console', Monaco, monospace", "'Lucida Sans Unicode', 'Lucida Grande', sans-serif", "'MS Sans Serif', Geneva, sans-serif", "'MS Serif', 'New York', sans-serif", "'Palatino Linotype', 'Book Antiqua', Palatino, serif", "Symbol, sans-serif", "Tahoma, Geneva, sans-serif", "'Times New Roman', Times, serif", "'Trebuchet MS', Helvetica, sans-serif", "Verdana, Geneva, sans-serif", "Webdings, sans-serif", "Wingdings, 'Zapf Dingbats', sans-serif" );
	private $allowFileExtension = array ('gif', 'jpg', 'jpeg', 'png' );
	private $keepVarActif = array ('id_category', 'id_product', 'id_manufacturer', 'id_supplier', 'id_cms' );
	private $link_targets;
	protected static $_forceCompile;
	protected static $_caching;
	protected static $_compileCheck;
	protected $_copyright_link = array(
		'link'	=> 'http://www.presta-module.com/',
		'img'	=> '//www.presta-module.com/img/logo-module.jpg'
	);
	protected $_support_link = false;
	protected $_getting_started = false;
	private $tables = array ('pm_advancedtopmenu', 'pm_advancedtopmenu_lang', 'pm_advancedtopmenu_columns_wrap', 'pm_advancedtopmenu_columns_wrap_lang', 'pm_advancedtopmenu_columns', 'pm_advancedtopmenu_columns_lang', 'pm_advancedtopmenu_elements', 'pm_advancedtopmenu_elements_lang' );
	const INSTALL_SQL_FILE = 'install.sql';
	const GLOBAL_CSS_FILE = 'css/pm_advancedtopmenu_global.css';
	const ADVANCED_CSS_FILE = 'css/pm_advancedtopmenu_advanced.css';
	const ADVANCED_CSS_FILE_RESTORE = 'css/reset-pm_advancedtopmenu_advanced.css';
	const DYN_CSS_FILE = 'css/pm_advancedtopmenu.css';
	function __construct() {
		$this->name = 'pm_advancedtopmenu';
		if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
			$this->tab = 'Presta-Module';
		else {
			$this->author = 'Presta-Module';
			$this->tab = 'front_office_features';
			$this->module_key = '22fb589ff4648a10756b4ad805180259';
		}
		$this->version = '1.11.1';
		parent::__construct();
		$this->page = basename(__FILE__, '.php');
		$this->initClassVar();
		if ($this->_onBackOffice()) {
			$this->displayName = $this->l('Advanced Top Menu');
			$this->description = $this->l('Horizontal menu with sub menu in column');
			$this->_fieldsOptions = array (
				'ATM_RESP_CONT_CLASSES' => array ('title' => $this->l('Menu container (#adtm_menu)'), 'desc' => $this->l('On bootstrap themes, you may have to enter "container" in order to center the menu'), 'type' => 'text', 'default' => '', 'advanced' => true),
				'ATM_INNER_CLASSES' => array ('title' => $this->l('Menu inner container (#adtm_menu_inner)'), 'desc' => $this->l('On bootstrap themes, you may have to enter "container" in order to center the menu when using sticky view'), 'type' => 'text', 'default' => 'clearfix', 'advanced' => true),
				'ATM_RESPONSIVE_MODE' => array ('title' => $this->l('Enable responsive mode'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => true, 'mobile' => true),
				'ATM_RESPONSIVE_THRESHOLD' => array ('title' => $this->l('Threshold width that will enable mobile menu toggle (px)'), 'desc' => '', 'type' => 'text', 'default' => '767', 'mobile' => true),
				'ATM_RESP_TOGGLE_HEIGHT' => array ('title' => $this->l('Height (px)'), 'desc' => '', 'type' => 'text', 'default' => '40', 'mobile' => true),
				'ATM_RESP_TOGGLE_FONT_SIZE' => array ('title' => $this->l('Font size (px)'), 'desc' => '', 'type' => 'select', 'default' => 16, 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATM_RESP_TOGGLE_TEXT' => array ('title' => $this->l('Label'), 'desc' => '', 'type' => 'textLang', 'default' => $this->l('Menu'), 'size' => 20, 'mobile' => true),
				'ATM_RESP_TOGGLE_BG_COLOR_OP' => array ('title' => $this->l('Background color (open state)'), 'desc' => '', 'type' => 'gradient', 'default' => '#ffffff', 'mobile' => true),
				'ATM_RESP_TOGGLE_BG_COLOR_CL' => array ('title' => $this->l('Background color (close state)'), 'desc' => '', 'type' => 'gradient', 'default' => '#e5e5e5', 'mobile' => true),
				'ATM_RESP_TOGGLE_COLOR_OP' => array ('title' => $this->l('Font color (open state)'), 'desc' => '', 'type' => 'color', 'default' => '#333333', 'mobile' => true),
				'ATM_RESP_TOGGLE_COLOR_CL' => array ('title' => $this->l('Font color (close state)'), 'desc' => '', 'type' => 'color', 'default' => '#666666', 'mobile' => true),
				'ATM_RESP_TOGGLE_ICON' => array ('title' => $this->l('Icon'), 'desc' => '', 'type' => 'image', 'default' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYAgMAAACdGdVrAAAACVBMVEUAAAAAAAAAAACDY+nAAAAAAnRSTlMA3Pn2U8cAAAAaSURBVAjXY4CCrFVAsJJhFRigUjA5FEBvfQDmRTo/uCG3BQAAAABJRU5ErkJggg==', 'mobile' => true),
				'ATM_RESP_MENU_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '5px 10px 5px 10px', 'mobile' => true),
				'ATMR_MENU_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0', 'mobile' => true),
				'ATM_RESP_MENU_FONT_SIZE' => array ('title' => $this->l('Font size (px)'), 'desc' => '', 'type' => 'select', 'default' => 18, 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_MENU_FONT_BOLD' => array ('title' => $this->l('Bold text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => true, 'mobile' => true),
				'ATMR_MENU_FONT_TRANSFORM' => array ('title' => $this->l('Text transform'), 'desc' => '', 'type' => 'select', 'default' => 'uppercase', 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_MENU_FONT_FAMILY' => array ('title' => $this->l('Font family'), 'desc' => '', 'type' => 'select', 'default' => 0, 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_MENU_BGCOLOR_OP' => array ('title' => $this->l('Background color (open state)'), 'desc' => '', 'type' => 'gradient', 'default' => '#333333-#000000', 'mobile' => true),
				'ATMR_MENU_BGCOLOR_CL' => array ('title' => $this->l('Background color (close state)'), 'desc' => '', 'type' => 'gradient', 'default' => '', 'mobile' => true),
				'ATMR_MENU_COLOR' => array ('title' => $this->l('Font color'), 'desc' => '', 'type' => 'color', 'default' => '#484848', 'mobile' => true),
				'ATMR_MENU_BORDERCOLOR' => array ('title' => $this->l('Border color'), 'desc' => '', 'type' => 'color', 'default' => '#d6d4d4', 'mobile' => true),
				'ATMR_MENU_BORDERSIZE' => array ('title' => $this->l('Border size (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 1px 1px 1px', 'mobile' => true),
				'ATMR_SUBMENU_BGCOLOR' => array ('title' => $this->l('Background color'), 'desc' => '', 'type' => 'gradient', 'default' => '#ffffff-#fcfcfc', 'mobile' => true),
				'ATMR_SUBMENU_BORDERCOLOR' => array ('title' => $this->l('Border color'), 'desc' => '', 'type' => 'color', 'default' => '#e5e5e5', 'mobile' => true),
				'ATMR_SUBMENU_BORDERSIZE' => array ('title' => $this->l('Border size (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 1px 0 1px', 'mobile' => true),
				'ATM_RESP_SUBMENU_ICON_OP' => array ('title' => $this->l('Icon for opened state'), 'desc' => '', 'type' => 'image', 'default' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYBAMAAAASWSDLAAAAFVBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAASAQCkAAAABnRSTlMAHiXy6t8iJwLjAAAARUlEQVQY02OgKWBUAJFMYJJB1AhEChuCOSLJCkBpNxAHRBsBRVIUIJpUkhVgEmAlIKVgAFIDUgmXgkmAzXWCMqA20hgAAI+xB05evnCbAAAAAElFTkSuQmCC', 'mobile' => true),
				'ATM_RESP_SUBMENU_ICON_CL' => array ('title' => $this->l('Icon for closed state'), 'desc' => '', 'type' => 'image', 'default' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYBAMAAAASWSDLAAAAFVBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAASAQCkAAAABnRSTlMAHiXy6t8iJwLjAAAANUlEQVQY02MgFwgisZmMFZA4Zo5IUiLJSFKMbkZESqUoYKjDNFw5RYAYCSckW0IEULxAPgAAZQ0HP01tIysAAAAASUVORK5CYII=', 'mobile' => true),
				'ATMR_COLUMNWRAP_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0', 'mobile' => true),
				'ATMR_COLUMNWRAP_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0', 'mobile' => true),
				'ATMR_COLUMNWRAP_BORDERCOLOR' => array ('title' => $this->l('Border color'), 'desc' => '', 'type' => 'color', 'default' => '#e5e5e5', 'mobile' => true),
				'ATMR_COLUMNWRAP_BORDERSIZE' => array ('title' => $this->l('Border size (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 1px 0', 'mobile' => true),
				'ATMR_COLUMN_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 5px 0', 'mobile' => true),
				'ATMR_COLUMN_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 10px 5px 10px', 'mobile' => true),
				'ATMR_COLUMNTITLE_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0', 'mobile' => true),
				'ATMR_COLUMNTITLE_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '8px 10px 8px 0', 'mobile' => true),
				'ATM_RESP_COLUMN_FONT_SIZE' => array ('title' => $this->l('Font size (px)'), 'desc' => '', 'type' => 'select', 'default' => 18, 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_COLUMN_FONT_BOLD' => array ('title' => $this->l('Bold text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => true, 'mobile' => true),
				'ATMR_COLUMN_FONT_TRANSFORM' => array ('title' => $this->l('Text transform'), 'desc' => '', 'type' => 'select', 'default' => 'none', 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_COLUMN_FONT_FAMILY' => array ('title' => $this->l('Font family'), 'desc' => '', 'type' => 'select', 'default' => 0, 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_COLUMN_TITLE_COLOR' => array ('title' => $this->l('Font color'), 'desc' => '', 'type' => 'color', 'default' => '#333333', 'mobile' => true),
				'ATMR_COLUMN_ITEM_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '5px 0 5px 10px', 'mobile' => true),
				'ATMR_COLUMN_ITEM_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '15px 0 15px 0', 'mobile' => true),
				'ATM_RESP_COLUMN_ITEM_FONT_SIZE' => array ('title' => $this->l('Font size (px)'), 'desc' => '', 'type' => 'select', 'default' => 16, 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_COLUMN_ITEM_FONT_BOLD' => array ('title' => $this->l('Bold text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false, 'mobile' => true),
				'ATMR_COLUMN_ITEM_FONT_TRANSFORM' => array ('title' => $this->l('Text transform'), 'desc' => '', 'type' => 'select', 'default' => 'none', 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_COLUMN_ITEM_FONT_FAMILY' => array ('title' => $this->l('Font family'), 'desc' => '', 'type' => 'select', 'default' => 0, 'list' => array (), 'identifier' => 'id', 'mobile' => true),
				'ATMR_COLUMN_ITEM_COLOR' => array ('title' => $this->l('Font color'), 'desc' => '', 'type' => 'color', 'default' => '#777777', 'mobile' => true),
				'ATM_MENU_CONT_HOOK' => array ('title' => $this->l('Menu location'), 'onchange' => 'setMenuContHook(this.value);', 'desc' => '', 'type' => 'select', 'default' => 'top', 'list' => array (array ('id' => 'top', 'name' => $this->l('displayTop (default)') ), array ('id' => 'nav', 'name' => $this->l('displayNav') ) ), 'identifier' => 'id' ),
				'ATM_THEME_COMPATIBILITY_MODE' => array ('title' => $this->l('Enable theme compatibility mode'), 'desc' => $this->l('Enable only if theme layout is corrupted after installation'), 'cast' => 'intval', 'type' => 'bool', 'default' => true ),
				'ATM_CACHE' => array ('title' => $this->l('Enable cache'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => true ),
				'ATM_AUTOCOMPLET_SEARCH' => array ('title' => $this->l('Enable autocomplete in search input'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => true ),
				'ATM_MENU_CONT_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_MENU_CONT_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '20px 0 0 0' ),
				'ATM_MENU_CONT_BORDERCOLOR' => array ('title' => $this->l('Border color'), 'desc' => '', 'type' => 'color', 'default' => '#333333' ),
				'ATM_MENU_CONT_BORDERSIZE' => array ('title' => $this->l('Border size (px)'), 'desc' => '', 'type' => '4size', 'default' => '5px 0 0 0' ),
				'ATM_MENU_CONT_POSITION' => array ('title' => $this->l('Position'), 'desc' => '', 'type' => 'select', 'default' => 'relative', 'list' => array (array ('id' => 'relative', 'name' => $this->l('Relative (default)') ), array ('id' => 'absolute', 'name' => $this->l('Absolute') ), array ('id' => 'sticky', 'name' => $this->l('Sticky') ) ), 'identifier' => 'id' ),
				'ATM_MENU_CONT_POSITION_TRBL' => array ('title' => $this->l('Positioning (px)'), 'desc' => '', 'type' => '4size_position', 'default' => '' ),
				'ATM_MENU_GLOBAL_ACTIF' => array ('title' => $this->l('Enable active state for current menu'), 'desc' => $this->l('Background and font color values from over settings will be used'), 'cast' => 'intval', 'type' => 'bool', 'default' => true ),
				'ATM_MENU_GLOBAL_WIDTH' => array ('title' => $this->l('Width (px)'), 'desc' => $this->l('Put 0 for automatic width'), 'type' => 'text', 'default' => '0' ),
				'ATM_MENU_GLOBAL_HEIGHT' => array ('title' => $this->l('Height (px)'), 'desc' => '', 'type' => 'text', 'default' => '56' ),
				'ATM_MENU_GLOBAL_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_MENU_GLOBAL_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_MENU_GLOBAL_ZINDEX' => array ('title' => $this->l('z-index value (CSS)'), 'desc' => $this->l('Increase if your cart block is under the menu bar'), 'type' => 'text', 'default' => '9' ),
				'ATM_MENU_GLOBAL_BGCOLOR' => array ('title' => $this->l('Background color'), 'desc' => '', 'type' => 'gradient', 'default' => '#f6f6f6-#e6e6e6' ),
				'ATM_MENU_GLOBAL_BORDERCOLOR' => array ('title' => $this->l('Border color'), 'desc' => '', 'type' => 'color', 'default' => '#e9e9e9' ),
				'ATM_MENU_GLOBAL_BORDERSIZE' => array ('title' => $this->l('Border size (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 3px 0' ),
				'ATM_MENU_BOX_SHADOW' => array ('title' => $this->l('Shadow'), 'desc' => '', 'type' => 'shadow', 'default' => '0px 5px 13px 0px' ),
				'ATM_MENU_BOX_SHADOWCOLOR' => array ('title' => $this->l('Shadow color'), 'desc' => '', 'type' => 'color', 'default' => '#000000' ),
				'ATM_MENU_BOX_SHADOWOPACITY' => array ('title' => $this->l('Shadow opacity'), 'desc' => '', 'type' => 'slider', 'default' => 20, 'min' => 0, 'max' => 100, 'step' => 1, 'suffix' => '%'),
				'ATM_MENU_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 20px 0 20px' ),
				'ATM_MENU_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_MENU_FONT_SIZE' => array ('title' => $this->l('Font size (px)'), 'desc' => '', 'type' => 'select', 'default' => 18, 'list' => array (), 'identifier' => 'id' ),
				'ATM_MENU_FONT_BOLD' => array ('title' => $this->l('Bold text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_MENU_FONT_UNDERLINE' => array ('title' => $this->l('Underline text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_MENU_FONT_UNDERLINEOV' => array ('title' => $this->l('Underline text (over)'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_MENU_FONT_TRANSFORM' => array ('title' => $this->l('Text transform'), 'desc' => '', 'type' => 'select', 'default' => 'none', 'list' => array (), 'identifier' => 'id' ),
				'ATM_MENU_FONT_FAMILY' => array ('title' => $this->l('Font family'), 'desc' => '', 'type' => 'select', 'default' => 0, 'list' => array (), 'identifier' => 'id' ),
				'ATM_MENU_COLOR' => array ('title' => $this->l('Font color'), 'desc' => '', 'type' => 'color', 'default' => '#484848' ),
				'ATM_MENU_COLOR_OVER' => array ('title' => $this->l('Font color (over)'), 'desc' => '', 'type' => 'color', 'default' => '#ffffff' ),
				'ATM_MENU_BGCOLOR' => array ('title' => $this->l('Background color'), 'desc' => '', 'type' => 'gradient', 'default' => '' ),
				'ATM_MENU_BGCOLOR_OVER' => array ('title' => $this->l('Background color (over)'), 'desc' => '', 'type' => 'gradient', 'default' => '#333333-#000000' ),
				'ATM_MENU_BORDERCOLOR' => array ('title' => $this->l('Border color'), 'desc' => '', 'type' => 'color', 'default' => '#d6d4d4' ),
				'ATM_MENU_BORDERSIZE' => array ('title' => $this->l('Border size (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 1px 0 1px' ),
				'ATM_SUBMENU_WIDTH' => array ('title' => $this->l('Width (px)'), 'desc' => $this->l('Put 0 for automatic width'), 'type' => 'text', 'default' => '0' ),
				'ATM_SUBMENU_HEIGHT' => array ('title' => $this->l('Minimal height (px)'), 'desc' => '', 'type' => 'text', 'default' => '0' ),
				'ATM_SUBMENU_ZINDEX' => array ('title' => $this->l('z-index value (CSS)'), 'desc' => $this->l('Increase if submenus are under your main content'), 'type' => 'text', 'default' => '1000' ),
				'ATM_SUBMENU_POSITION' => array ('title' => $this->l('Position'), 'desc' => '', 'cast' => 'intval', 'type' => 'select', 'default' => 2, 'list' => array (array ('id' => 1, 'name' => $this->l('Left-aligned current menu') ), array ('id' => 2, 'name' => $this->l('Left-aligned global menu') ) ), 'identifier' => 'id' ),
				'ATM_SUBMENU_BGCOLOR' => array ('title' => $this->l('Background color'), 'desc' => '', 'type' => 'gradient', 'default' => '#ffffff-#fcfcfc' ),
				'ATM_SUBMENU_BORDERCOLOR' => array ('title' => $this->l('Border color'), 'desc' => '', 'type' => 'color', 'default' => '#e5e5e5' ),
				'ATM_SUBMENU_BORDERSIZE' => array ('title' => $this->l('Border size (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 1px 1px 1px' ),
				'ATM_SUBMENU_BOX_SHADOW' => array ('title' => $this->l('Shadow'), 'desc' => '', 'type' => 'shadow', 'default' => '0px 5px 13px 0px' ),
				'ATM_SUBMENU_BOX_SHADOWCOLOR' => array ('title' => $this->l('Shadow color'), 'desc' => '', 'type' => 'color', 'default' => '#000000' ),
				'ATM_SUBMENU_BOX_SHADOWOPACITY' => array ('title' => $this->l('Shadow opacity'), 'desc' => '', 'type' => 'slider', 'default' => 20, 'min' => 0, 'max' => 100, 'step' => 1, 'suffix' => '%'),
				'ATM_SUBMENU_OPEN_DELAY' => array ('title' => $this->l('Opening delay'), 'desc' => '', 'type' => 'slider', 'default' => 0.3, 'min' => 0, 'max' => 2, 'step' => 0.1, 'suffix' => 's'),
				'ATM_SUBMENU_FADE_SPEED' => array ('title' => $this->l('Fading effect duration'), 'desc' => '', 'type' => 'slider', 'default' => 0.3, 'min' => 0, 'max' => 2, 'step' => 0.1, 'suffix' => 's'),
				'ATM_COLUMNWRAP_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_COLUMN_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_COLUMN_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 10px 0 10px' ),
				'ATM_COLUMNTITLE_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_COLUMNTITLE_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 10px 0 0' ),
				'ATM_COLUMN_FONT_SIZE' => array ('title' => $this->l('Font size (px)'), 'desc' => '', 'type' => 'select', 'default' => 16, 'list' => array (), 'identifier' => 'id' ),
				'ATM_COLUMN_FONT_BOLD' => array ('title' => $this->l('Bold text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => true),
				'ATM_COLUMN_FONT_UNDERLINE' => array ('title' => $this->l('Underline text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_COLUMN_FONT_UNDERLINEOV' => array ('title' => $this->l('Underline text (over)'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_COLUMN_FONT_TRANSFORM' => array ('title' => $this->l('Text transform'), 'desc' => '', 'type' => 'select', 'default' => 'none', 'list' => array (), 'identifier' => 'id' ),
				'ATM_COLUMN_FONT_FAMILY' => array ('title' => $this->l('Font family'), 'desc' => '', 'type' => 'select', 'default' => 0, 'list' => array (), 'identifier' => 'id' ),
				'ATM_COLUMN_TITLE_COLOR' => array ('title' => $this->l('Title font color'), 'desc' => '', 'type' => 'color', 'default' => '#333333' ),
				'ATM_COLUMN_TITLE_COLOR_OVER' => array ('title' => $this->l('Title font color (over)'), 'desc' => '', 'type' => 'color', 'default' => '#515151' ),
				'ATM_COLUMN_ITEM_PADDING' => array ('title' => $this->l('Inner spaces - padding (px)'), 'desc' => '', 'type' => '4size', 'default' => '3px 0 3px 0' ),
				'ATM_COLUMN_ITEM_MARGIN' => array ('title' => $this->l('Outer spaces - margin (px)'), 'desc' => '', 'type' => '4size', 'default' => '0 0 0 0' ),
				'ATM_COLUMN_ITEM_FONT_SIZE' => array ('title' => $this->l('Font size (px)'), 'desc' => '', 'type' => 'select', 'default' => 13, 'list' => array (), 'identifier' => 'id' ),
				'ATM_COLUMN_ITEM_FONT_BOLD' => array ('title' => $this->l('Bold text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_COLUMN_ITEM_FONT_UNDERLINE' => array ('title' => $this->l('Underline text'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_COLUMN_ITEM_FONT_UNDERLINEOV' => array ('title' => $this->l('Underline text (over)'), 'desc' => '', 'cast' => 'intval', 'type' => 'bool', 'default' => false),
				'ATM_COLUMN_ITEM_FONT_TRANSFORM' => array ('title' => $this->l('Text transform'), 'desc' => '', 'type' => 'select', 'default' => 'none', 'list' => array (), 'identifier' => 'id' ),
				'ATM_COLUMN_ITEM_FONT_FAMILY' => array ('title' => $this->l('Font family'), 'desc' => '', 'type' => 'select', 'default' => 0, 'list' => array (), 'identifier' => 'id' ),
				'ATM_COLUMN_ITEM_COLOR' => array ('title' => $this->l('Font color'), 'desc' => '', 'type' => 'color', 'default' => '#777777' ),
				'ATM_COLUMN_ITEM_COLOR_OVER' => array ('title' => $this->l('Font color (over)'), 'desc' => '', 'type' => 'color', 'default' => '#333333' ),
			);
			foreach (array_keys($this->_fieldsOptions) as $key) {
				if (strpos($key, 'FONT_TRANSFORM') !== false) {
					$this->_fieldsOptions[$key]['list'] = array(
						array('id' => 'none', 'name' => $this->l('Normal (inherit)')),
						array('id' => 'lowercase', 'name' => $this->l('lowercase')),
						array('id' => 'uppercase', 'name' => $this->l('UPPERCASE')),
						array('id' => 'capitalize', 'name' => $this->l('Capitalize')),
					);
				} else if (strpos($key, 'FONT_FAMILY') !== false) {
					$this->_fieldsOptions[$key]['list'][] = array('id' => 0, 'name' => $this->l('Use the same font family as my theme'));
					foreach ($this->font_families as $font_family) {
						$this->_fieldsOptions[$key]['list'][] = array('id' => $font_family, 'name' => $font_family);
					}
				} else if (strpos($key, 'FONT_SIZE') !== false) {
					$this->_fieldsOptions[$key]['list'][] = array('id' => 0, 'name' => $this->l('Use the same font size as my theme'));
					for($i = 8; $i <= 30; $i ++) {
						$this->_fieldsOptions[$key]['list'][] = array('id' => $i, 'name' => $i );
					}
				}
			}
			$this->link_targets = array (0 => $this->l('No target. W3C compliant.'), '_self' => $this->l('Open document in the same frame (_self)'), '_blank' => $this->l('Open document in a new window (_blank)'), '_top' => $this->l('Open document in the same window (_top)'), '_parent' => $this->l('Open document in the parent frame (_parent)') );
			$doc_url_tab['fr'] = 'http://www.presta-module.com/docs/fr/advancedtopmenu/';
			$doc_url_tab['en'] = 'http://www.presta-module.com/docs/en/advancedtopmenu/';
			$doc_url = $doc_url_tab['en'];
			if ($this->_iso_lang == 'fr') $doc_url = $doc_url_tab['fr'];
			$forum_url_tab['fr'] = 'http://www.prestashop.com/forums/topic/89128-module-pm-advancedtopmenu-menu-de-navigation-horizontal-en-colonnes/';
			$forum_url_tab['en'] = 'http://www.prestashop.com/forums/topic/89175-module-advancedtopmenu-horizontal-navigation-menu-with-columns/';
			$forum_url = $forum_url_tab['en'];
			if ($this->_iso_lang == 'fr') $forum_url = $forum_url_tab['fr'];
			$this->_support_link = array(
				array('link' => $forum_url, 'target' => '_blank', 'label' => $this->l('Forum topic')),
				array('link' => $doc_url, 'target' => '_blank', 'label' => $this->l('Online documentation')),
				array('link' => 'http://www.presta-module.com/contact-form.php', 'target' => '_blank', 'label' => $this->l('Support contact')),
			);
		}
	}
	function install() {
		if (!$this->updateDB() || ! parent::install())
			return false;
		if (!$this->registerHook('top') ||
			!$this->registerHook('header') ||
			(version_compare(_PS_VERSION_, '1.4.0.0', '>=') && !$this->registerHook('categoryUpdate')) ||
			(version_compare(_PS_VERSION_, '1.6.0.2', '>=') && !$this->registerHook('displayNav')) ||
			(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !$this->registerHook('actionObjectLanguageAddAfter')) ||
			!$this->installDefaultConfig())
		{
			return false;
		}
		Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'hook_module', array ('position' => 255 ), 'UPDATE', 'id_module = ' . $this->id . ' AND id_hook = ' . (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Hook::getIdByName('top') : Hook::get('top')));
		return true;
	}
	private function updateDB() {
		if (! file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
			return (false);
		else if (! $sql = file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
			return (false);
		$sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			$sql = str_replace('MYSQL_ENGINE', _MYSQL_ENGINE_, $sql);
		else
			$sql = str_replace('MYSQL_ENGINE', 'MyISAM', $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ( $sql as $query )
			if (! Db::getInstance()->Execute(trim($query)))
				return (false);
		return true;
	}
	private function columnExists($table, $column, $createIfNotExist = false, $type = false, $insertAfter = false) {
		$resultset = Db::getInstance()->ExecuteS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . $table . "`", true, false);
		foreach ( $resultset as $row )
			if ($row ['Field'] == $column)
				return true;
		if ($createIfNotExist && Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ADD `' . $column . '` ' . $type . ' ' . ($insertAfter ? ' AFTER `' . $insertAfter . '`' : '') . ''))
			return true;
		return false;
	}
	public function installDefaultConfig() {
		foreach ( $this->_fieldsOptions as $key => $field ) {
			$val = $field['default'];
			if (trim($val)) {
				if (is_array($val)) {
					$val = serialize($val);
				}
				if (Configuration::get($key) === false) {
					if (! Configuration::updateValue($key, $val))
						return false;
				}
			}
		}
		return true;
	}
	public function checkIfModuleIsUpdate($updateDb = false, $displayConfirm = true) {
		if (! $updateDb && $this->version != Configuration::get('ATM_LAST_VERSION'))
			return false;
		$isUpdate = true;
		$this->updateDB();
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			$nb_shop_entry = Db::getInstance()->getRow('SELECT COUNT(DISTINCT id_shop) as nb_shop_entry FROM `'._DB_PREFIX_.'pm_advancedtopmenu_shop`');
			$nb_shop_entry = $nb_shop_entry['nb_shop_entry'];
			$nb_menu_entry = Db::getInstance()->getRow('SELECT COUNT(DISTINCT id_menu) as nb_menu_entry FROM `'._DB_PREFIX_.'pm_advancedtopmenu`');
			$nb_menu_entry = $nb_menu_entry['nb_menu_entry'];
			if(!$nb_shop_entry && $nb_menu_entry) {
				$menus_id = AdvancedTopMenuClass::getMenusId();
				foreach ($menus_id as $menu) {
					foreach (Shop::getCompleteListOfShopsID() as $id_shop) {
						Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'pm_advancedtopmenu_shop` (id_shop, id_menu)
						VALUES ('.(int)$id_shop.', '.(int)$menu['id_menu'].')');
					}
				}
			}
		}
		$toUpdate = array(
			array('pm_advancedtopmenu', "id_shop", 'int(10) unsigned NOT NULL DEFAULT "0"', 'id_manufacturer'),
			array('pm_advancedtopmenu', "width_submenu", 'varchar(5) NOT NULL', 'border_color_tab'),
			array('pm_advancedtopmenu', "minheight_submenu", 'varchar(5) NOT NULL', 'width_submenu'),
			array('pm_advancedtopmenu', "position_submenu", 'tinyint(3) unsigned NOT NULL', 'minheight_submenu'),
			array('pm_advancedtopmenu_elements', "active", "tinyint(4)  NOT NULL DEFAULT '1'", 'target'),
			array('pm_advancedtopmenu', "active_mobile", "tinyint(4)  NOT NULL DEFAULT '1'", 'active'),
			array('pm_advancedtopmenu_columns', "active_mobile", "tinyint(4)  NOT NULL DEFAULT '1'", 'active'),
			array('pm_advancedtopmenu_columns_wrap', "active_mobile", "tinyint(4)  NOT NULL DEFAULT '1'", 'active'),
			array('pm_advancedtopmenu_elements', "active_mobile", "tinyint(4)  NOT NULL DEFAULT '1'", 'active'),
			array('pm_advancedtopmenu_lang', "have_icon", "varchar(1) NOT NULL DEFAULT ''", 'link'),
			array('pm_advancedtopmenu_lang', "image_type", "varchar(4) NOT NULL", 'have_icon'),
			array('pm_advancedtopmenu_lang', "image_legend", "varchar(256) NOT NULL DEFAULT ''", 'image_type'),
			array('pm_advancedtopmenu_columns_lang', "have_icon", "varchar(1) NOT NULL DEFAULT ''", 'link'),
			array('pm_advancedtopmenu_columns_lang', "image_type", "varchar(4) NOT NULL", 'have_icon'),
			array('pm_advancedtopmenu_columns_lang', "image_legend", "varchar(256) NOT NULL DEFAULT ''", 'image_type'),
			array('pm_advancedtopmenu_elements_lang', "have_icon", "varchar(1) NOT NULL DEFAULT ''", 'name'),
			array('pm_advancedtopmenu_elements_lang', "image_type", "varchar(4) NOT NULL", 'have_icon'),
			array('pm_advancedtopmenu_elements_lang', "image_legend", "varchar(256) NOT NULL DEFAULT ''", 'image_type'),
		);
		foreach ($toUpdate as $table => $infos) {
			if (!$this->columnExists($infos [0], $infos [1], $updateDb, $infos [2], $infos [3]))
				$isUpdate = false;
		}
		$languages = Language::getLanguages(false);
		$iconsDatabaseUpdate = array(
			array('pm_advancedtopmenu', 'id_menu', 'menu_icons'),
			array('pm_advancedtopmenu_columns', 'id_column', 'column_icons'),
			array('pm_advancedtopmenu_elements', 'id_element', 'element_icons'),
		);
		foreach ($iconsDatabaseUpdate as $iconsUpdateRow) {
			if ($this->columnExists($iconsUpdateRow[0], 'have_icon') && $this->columnExists($iconsUpdateRow[0] . '_lang', 'have_icon')) {
				$res = true;
				$imageList = Db::getInstance()->ExecuteS('SELECT `'. $iconsUpdateRow[1] .'`, `have_icon`, `image_type` FROM `'._DB_PREFIX_ . $iconsUpdateRow[0] . '`');
				if (self::_isFilledArray($imageList))
					foreach ($imageList as $imageRow) {
						$res &= Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . $iconsUpdateRow[0] . '_lang` SET `have_icon`="'.(int)$imageRow['have_icon'].'", `image_type`="'.pSQL($imageRow['image_type']).'" WHERE `'. $iconsUpdateRow[1] .'`="'.(int)$imageRow[$iconsUpdateRow[1]].'"');
						if (is_writable(dirname(__FILE__) . '/' . $iconsUpdateRow[2]))
							$imgPath = dirname(__FILE__) . '/'. $iconsUpdateRow[2] .'/' . (int)$imageRow[$iconsUpdateRow[1]] . '.' . $imageRow['image_type'];
							if (file_exists($imgPath) && is_readable($imgPath))
								foreach ($languages as $language) {
									$imgPathLang = dirname(__FILE__) . '/'. $iconsUpdateRow[2] .'/' . (int)$imageRow[$iconsUpdateRow[1]] . '-' . $language['iso_code'] . '.' . $imageRow['image_type'];
									file_put_contents($imgPathLang, file_get_contents($imgPath));
								}
					}
				if ($res)
					$res &= Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . $iconsUpdateRow[0] . '` DROP COLUMN `have_icon`, DROP COLUMN `image_type`');
			}
		}
		$toChange = array(
			array('pm_advancedtopmenu', "fnd_color_menu_tab", 'varchar(15)'),
			array('pm_advancedtopmenu', "fnd_color_menu_tab_over", 'varchar(15)'),
			array('pm_advancedtopmenu', "fnd_color_submenu", 'varchar(15)'),
			array('pm_advancedtopmenu_columns_wrap', "bg_color", 'varchar(15)'),
		);
		foreach ( $toChange as $table => $infos ) {
			$resultset = Db::getInstance()->ExecuteS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . $infos [0] . "` WHERE `Field` = '" . $infos [1] . "'", true, false);
			foreach ( $resultset as $row )
				if ($row ['Type'] != $infos [2]) {
					$isUpdate = false;
					if ($updateDb) {
						Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . $infos [0] . '` CHANGE `' . $infos [1] . '` `' . $infos [1] . '` ' . $infos [2] . '');
					}
				}
		}
		if ($updateDb) {
			if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
				$this->updateTablesEngine();
			$this->installDefaultConfig();
			if (Configuration::get('ATM_LAST_VERSION') && version_compare(Configuration::get('ATM_LAST_VERSION'), '1.9.8', '<=')) {
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
					foreach (Shop::getShops(true, null, true) as $id_shop)
						Configuration::updateValue('ATM_MENU_PADDING', '0 10px 0 10px', false, null, $id_shop);
				} else {
					Configuration::updateValue('ATM_MENU_PADDING', '0 10px 0 10px');
				}
			}
			Configuration::updateValue('ATM_LAST_VERSION', $this->version);
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
				foreach (Shop::getShops(true, null, true) as $id_shop) {
					$this->generateGlobalCss($id_shop);
				}
			} else {
				$this->generateGlobalCss();
			}
			$this->generateCss();
			$this->clearCache();
			if ($displayConfirm)
				$this->_html .= $this->displayConfirmation($this->l('Module updated successfully'));
		}
		return $isUpdate;
	}
	function updateTablesEngine() {
		foreach ( $this->tables as $tblName ) {
			if (_MYSQL_ENGINE_ == 'InnoDB') {
				if ($this->getTableEngine($tblName) != _MYSQL_ENGINE_)
					if (! Db::getInstance()->Execute('ALTER TABLE ' . _DB_PREFIX_ . $tblName . ' ENGINE = InnoDB'))
						return (false);
			}
		}
		return true;
	}
	private function getTableEngine($table) {
		$result = Db::getInstance()->ExecuteS('SHOW TABLE STATUS WHERE `Name` = "' . _DB_PREFIX_ . pSQL($table) . '"', true, false);
		return $result [0] ['Engine'];
	}
	public function checkPermissions() {
		$verifs = array(
			dirname(__FILE__) . '/css',
			dirname(__FILE__) . '/column_icons',
			dirname(__FILE__) . '/menu_icons',
			dirname(__FILE__) . '/element_icons',
			dirname(__FILE__) . '/../../tools/smarty/cache',
		);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			unset($verifs[4]);
			if (defined('_PS_CACHE_DIR_'))
				$verifs[] = _PS_CACHE_DIR_;
			else
				$verifs[] = dirname(__FILE__) . '/../../cache/smarty/cache';
		}
		$errors = array ();
		foreach ($verifs as $fileOrDir )
			if (!is_writable($fileOrDir))
				$errors[] = $fileOrDir;
		if (!sizeof($errors)) {
			return true;
		} else {
			$this->_html .= '<div class="warning warn clear">' . $this->l('Before you can configure your menu, please give write permission to  file(s) / folder(s) below:') . '<br />' . implode('<br />', $errors) . '</div>';
			return false;
		}
	}
	public function uninstall() {
		return parent::uninstall();
	}
	public function resetInstall() {
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu`');
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu_lang`');
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_wrap`');
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_wrap_lang`');
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns`');
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_lang`');
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu_elements`');
		Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'pm_advancedtopmenu_elements_lang`');
		if (! file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
			return (false);
		else if (! $sql = file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
			return (false);
		$sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ( $sql as $query )
			if (! Db::getInstance()->Execute(trim($query)))
				return (false);
	}
	public function uninstallDefaultConfig() {
		foreach ( $this->_fieldsOptions as $key => $field ) {
			Configuration::deleteByName($key);
		}
		return true;
	}
	public function saveConfig() {
		if (Tools::getValue('submitATMOptions') || Tools::getValue('submitATMMobileOptions') || Tools::getValue('submitAdvancedConfig')) {
			foreach ( $this->_fieldsOptions as $key => $field ) {
				if (Tools::getValue('submitATMMobileOptions') && (!isset($field['mobile']) || (isset($field['mobile']) && !$field['mobile'])))
					continue;
				else if (Tools::getValue('submitAdvancedConfig') && (!isset($field['advanced']) || (isset($field['advanced']) && !$field['advanced'])))
					continue;
				else if (Tools::getValue('submitATMOptions') && ((isset($field['mobile']) && $field['mobile']) || (isset($field['advanced']) && $field['advanced'])))
					continue;
				if ($field ['type'] == '4size' || $field ['type'] == 'shadow') {
					$_POST [$key] = $this->getBorderSizeFromArray(Tools::getValue($key));
					Configuration::updateValue($key, (isset($field ['cast']) ? $field ['cast'](Tools::getValue($key)) : Tools::getValue($key)));
				}
				elseif ($field ['type'] == '4size_position') {
					$_POST[$key] = $this->getPositionSizeFromArray(Tools::getValue($key), false);
					Configuration::updateValue($key, (isset($field ['cast']) ? $field ['cast'](Tools::getValue($key)) : Tools::getValue($key)));
				}
				elseif ($field ['type'] == 'gradient') {
					$_POST [$key] = $_POST [$key] [0] . (Tools::getValue($key . '_gradient') && isset($_POST [$key] [1]) && $_POST [$key] [1] ? $this->gradient_separator . $_POST [$key] [1] : '');
					Configuration::updateValue($key, (isset($field ['cast']) ? $field ['cast'](Tools::getValue($key)) : Tools::getValue($key)));
				}
				elseif ($field ['type'] == 'textLang') {
					$languages = Language::getLanguages(false);
					$list = array ();
					foreach ( $languages as $language )
						$list [$language ['id_lang']] = (isset($field ['cast']) ? $field ['cast'](Tools::getValue($key . '_' . $language ['id_lang'])) : Tools::getValue($key . '_' . $language ['id_lang']));
					Configuration::updateValue($key, $list);
				}
				elseif ($field ['type'] == 'image') {
					if (isset($_FILES[$key]) && is_array($_FILES[$key]) && isset($_FILES[$key]['size']) && $_FILES[$key]['size'] > 0 && isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['error']) && !$_FILES[$key]['error'] && file_exists($_FILES[$key]['tmp_name']) && filesize($_FILES[$key]['tmp_name']) > 0) {
						$val = 'data:'.(isset($_FILES[$key]['type']) && !empty($_FILES[$key]['type']) && preg_match('/image/', $_FILES[$key]['type']) ? $_FILES[$key]['type'] : 'image/jpg').';base64,'.base64_encode(file_get_contents($_FILES[$key]['tmp_name']));
						Configuration::updateValue($key, $val);
					} else if (Configuration::get($key) === false && !Tools::getValue($key.'_delete')) {
						Configuration::updateValue($key, $field['default']);
					}
					if (Tools::getValue($key.'_delete'))
						Configuration::updateValue($key, '');
				}
				else {
					if (! isset($field ['disable']))
						Configuration::updateValue($key, (isset($field ['cast']) ? $field ['cast'](Tools::getValue($key)) : Tools::getValue($key)));
				}
			}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
				foreach (Shop::getShops(true, null, true) as $id_shop) {
					$this->generateGlobalCss($id_shop);
				}
			} else {
				$this->generateGlobalCss();
			}
			$this->generateCss();
			$this->clearCache();
			$this->_html .= $this->displayConfirmation($this->l('Configuration updated successfully'));
		}
	}
	public function saveAdvancedConfig() {
		if (Tools::getValue('submitAdvancedConfig')) {
			$contextShops = array(1);
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $contextShops = array_values(Shop::getContextListShopID());
			$error = false;
			foreach ($contextShops as $id_shop) {
				$advanced_css_file_shop = str_replace('.css','-'.$id_shop.'.css',dirname(__FILE__) . '/' . self::ADVANCED_CSS_FILE);
				if (!file_put_contents($advanced_css_file_shop, Tools::getValue('advancedConfig')))
					$error = $this->l('Error while saving advanced styles');
			}
			if ($error) {
				$this->errors [] = $error;
			} else {
				$this->_html .= $this->displayConfirmation($this->l('Styles updated successfully'));
			}
		}
	}
	public function getContent() {
		/* $this->l('Choose language:') */
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->_html .= '<div id="pm_backoffice_wrapper" class="pm_bo_ps_'.substr(str_replace('.', '', _PS_VERSION_), 0, 2).'">';
		$this->initClassVar();
		$this->displayTitle();
		if ($this->checkPermissions()) {
			if (Tools::getValue('makeUpdate')) {
				$this->checkIfModuleIsUpdate(true);
				header('Location:' . $this->base_config_url);
				die();
			}
			if (! $this->checkIfModuleIsUpdate(false)) {
				$this->_html .= '
					<div class="warning warn clear"><p>' . $this->l('We have detected that you installed a new version of the module on your shop') . '</p>
						<p style="text-align: center"><a href="' . $this->base_config_url . '&makeUpdate=1" class="button">' . $this->l('Please click here in order to finish the installation process') . '</a></p>
					</div>';
				$this->includeAdminCssJs();
			}
			else {
				$this->_postProcess();
				$this->includeAdminCssJs();
				$this->_showRating(false);
				$this->displayTabsConfig();
			}
		}else $this->includeAdminCssJs();
		$this->_displaySupport();
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->_html .= '</div>';
		return $this->_html;
	}
	function displayTitle() {
		$this->_html .= '<h2>' . $this->displayName . '</h2>';
	}
	function displayTabsConfig() {
		$this->_maintenanceWarning();
		$this->_maintenanceButton();
		$this->_html .= '<hr class="pm_hr" />';
		$this->_html .= '
    <div id="wrapConfigTab">
    <ul style="height: 30px;" id="configTab">
          <li><a href="#config-1"><span><img src="' . $this->_path . 'logo.gif" /> ' . $this->l('Menu configuration') . '</span></a></li>
          <li><a href="#config-2"><span><img src="../img/admin/cog.gif" /> ' . $this->l('General settings') . '</span></a></li>
          <li><a href="#config-3"><span><img src="../img/admin/cog.gif" /> ' . $this->l('Mobile settings') . '</span></a></li>
          <li><a href="#config-4"><span><img src="' . $this->_path . 'img/document-code.png" /> ' . $this->l('Advanced styles') . '</span></a></li>
        </ul>';
		$this->_html .= '<div id="config-1">';
		$this->_displayForm();
		$this->_html .= '</div>';
		$this->_html .= '<div id="config-2">';
		$this->displayConfig();
		$this->_html .= '</div>';
		$this->_html .= '<div id="config-3">';
		$this->displayMobileConfig();
		$this->_html .= '</div>';
		$this->_html .= '<div id="config-4">';
		$this->displayAdvancedConfig();
		$this->_html .= '</div>
      </div>';
		$this->initColorPicker();
	}
	function displayAdvancedConfig() {
		$id_shop = 1;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $id_shop = (int)$this->_context->shop->id;
		$advanced_css_file = str_replace('.css','-'.$id_shop.'.css',dirname(__FILE__) . '/' . self::ADVANCED_CSS_FILE);
		if (!file_exists($advanced_css_file))
			file_put_contents($advanced_css_file, file_get_contents(dirname(__FILE__) . '/' . self::ADVANCED_CSS_FILE_RESTORE));
		$this->_html .= '
		<form action="' . $this->base_config_url . '#config-4" id="formAdvancedStyles_' . $this->name . '" name="formAdvancedStyles_' . $this->name . '" method="post" class="width3">
			<fieldset>
		';
		$this->_html .= '<h3>' . $this->l('CSS classes settings') . '</h3>';
		foreach ( $this->_fieldsOptions as $key => $field ) {
			if (!isset($field['advanced']) || isset($field['advanced']) && !$field['advanced'])
				continue;
			$this->_displayFormItem($key, $field);
		}
		$this->_html .= '<h3>' . $this->l('Advanced styles') . '</h3>';
		$this->_html .= '
				<div class="dynamicTextarea">
					<textarea name="advancedConfig" id="advancedConfig" cols="120" rows="30">' . @file_get_contents($advanced_css_file) . '</textarea>
				</div>
				<br />
				<center>
					<input type="submit" value="' . $this->l('   Save   ') . '" name="submitAdvancedConfig" class="button" />
				</center>
			</fieldset>
		</form>
		<script type="text/javascript">
			var editor = CodeMirror.fromTextArea(document.getElementById("advancedConfig"), {});
		</script>';
	}
	function includeAdminCssJs() {
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
			$this->_context->controller->addJqueryUI(array('ui.draggable', 'ui.droppable', 'ui.sortable', 'ui.widget', 'ui.dialog', 'ui.tabs', 'ui.datepicker', 'ui.slider'), 'base');
		} else if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$this->_context->controller->addJqueryUI(array('ui.draggable', 'ui.droppable', 'ui.sortable', 'ui.widget', 'ui.dialog', 'ui.tabs', 'ui.datepicker', 'ui.slider'), 'base');
			$this->_context->controller->addjQueryPlugin(array('autocomplete'));	
		} else if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
			$this->_html .= '
			<link type="text/css" rel="stylesheet" href="' . $this->_path . 'js/jqueryui/1.8.9/themes/smoothness/jquery-ui-1.8.9.custom.css" />
			<script type="text/javascript" src="' . $this->_path . 'js/jqueryui/1.8.9/jquery-ui-1.8.9.custom.min.js"></script>
			';
		} else {
			$this->_html .= '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'js/jqueryui/themes/default/ui.all.css" />
			<script type="text/javascript" src="' . $this->_path . 'js/ui.core.min.js"></script>
			<script type="text/javascript" src="' . $this->_path . 'js/ui.dialog.min.js"></script>
			<script type="text/javascript" src="' . $this->_path . 'js/ui.sortable.min.js"></script>
			<script type="text/javascript" src="' . $this->_path . 'js/ui.tabs.min.js"></script>
			<script type="text/javascript" src="' . $this->_path . 'js/ui.slider.min.js"></script>
			';
		}
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
			if (version_compare(_PS_VERSION_, '1.6.0.12', '>=')) {
				$this->_html .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/admin/tinymce.inc.js"></script>';
			} else {
				$this->_html .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/tinymce.inc.js"></script>';
			}
			$this->_html .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/tiny_mce/tiny_mce.js"></script>';
		} else if (version_compare(_PS_VERSION_, '1.4.1.0', '>=')) {
			$isoTinyMCE = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $this->_iso_lang . '.js') ? $this->_iso_lang : 'en');
			$ad = dirname($_SERVER ["PHP_SELF"]);
			$this->_html .= '<script type="text/javascript">
				var iso = \'' . $isoTinyMCE . '\' ;
				var pathCSS = \'' . _THEME_CSS_DIR_ . '\' ;
				var ad = \'' . $ad . '\' ;
			</script>';
			$this->_html .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/tiny_mce/tiny_mce.js"></script>';
		}
		else {
			$this->_html .= '<script type="text/javascript" src="' . __PS_BASE_URI__ . 'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>';
		}
		$this->_html .= '
		<link type="text/css" rel="stylesheet" href="' . $this->_path . 'css/admin.css" />
		<link type="text/css" rel="stylesheet" href="' . $this->_path . 'css/mbTabset.css" />
		<script type="text/javascript" src="' . $this->_path . 'js/admin.js"></script>
		<script type="text/javascript" src="' . $this->_path . 'js/jquery.tablednd_0_5.js"></script>
		<script type="text/javascript" src="' . $this->_path . 'js/mbTabset.min.js"></script>
		<script type="text/javascript" src="' . $this->_path . 'js/jquery.metadata.js"></script>
		<link rel="stylesheet" href="' . $this->_path . 'js/colorpicker/css/colorpicker.css" type="text/css" />
		<script type="text/javascript" src="' . $this->_path . 'js/colorpicker/js/colorpicker.js"></script>
		<script src="' . $this->_path . 'js/codemirror/codemirror.js" type="text/javascript"></script>
		<script src="' . $this->_path . 'js/codemirror/css.js" type="text/javascript"></script>
		<link type="text/css" rel="stylesheet" href="' . $this->_path . 'js/codemirror/codemirror.css" />
		<link type="text/css" rel="stylesheet" href="' . $this->_path . 'js/codemirror/default.css" />
		<script type="text/javascript" src="'.$this->_path . 'js/jquery.tipTip.js"></script>
		<script type="text/javascript">
			var id_language = Number(' . $this->defaultLanguage . ');
			var base_config_url = "' . $this->base_config_url . '";
		</script>';
	}
	function displayConfig() {
		if (! isset($this->_fieldsOptions) or ! sizeof($this->_fieldsOptions))
			return;
		$languages = Language::getLanguages(false);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			$this->_html .= '<div class="info"><p>' . $this->l('This configuration can be made by shop.'). '</p></div>';
		}
		if (version_compare(_PS_VERSION_, '1.6.0.2', '<') && isset($this->_fieldsOptions['ATM_MENU_CONT_HOOK'])) {
			unset($this->_fieldsOptions['ATM_MENU_CONT_HOOK']);
		}
		$this->_html .= '<form action="' . $this->base_config_url . '#config-2" id="formGlobal_' . $this->name . '" name="form_' . $this->name . '" method="post" class="width3" enctype="multipart/form-data">';
		$this->_html .= '<fieldset>';
      	$this->_html .= '<h3>'.$this->l('General settings').'</h3>';
		foreach ( $this->_fieldsOptions as $key => $field ) {
			if (isset($field['mobile']) && $field['mobile'] || isset($field['advanced']) && $field['advanced'])
				continue;
			if ($key == 'ATM_MENU_CONT_PADDING')
				$this->_html .= '<h3>' . $this->l('Menu container settings') . '</h3>';
			else if ($key == 'ATM_MENU_GLOBAL_ACTIF')
				$this->_html .= '<h3>' . $this->l('Navigation bar settings') . '</h3>';
			else if ($key == 'ATM_MENU_PADDING')
				$this->_html .= '<h3>' . $this->l('Tabs settings') . '</h3>';
			else if ($key == 'ATM_SUBMENU_WIDTH')
				$this->_html .= '<h3>' . $this->l('Submenus settings') . '</h3>';
			else if ($key == 'ATM_COLUMNWRAP_PADDING')
				$this->_html .= '<h3>' . $this->l('Columns settings') . '</h3>';
			else if ($key == 'ATM_COLUMN_PADDING') {
				$this->_html .= '<h3>' . $this->l('Items group settings') . '</h3>';
				$this->_html .= '<h4>' . $this->l('Container settings') . '</h4>';
			}
			else if ($key == 'ATM_COLUMNTITLE_PADDING')
				$this->_html .= '<h4>' . $this->l('Title settings') . '</h4>';
			else if ($key == 'ATM_COLUMN_ITEM_PADDING')
				$this->_html .= '<h3>' . $this->l('Items settings') . '</h3>';
			$this->_displayFormItem($key, $field);
		}
		$this->_html .= '<center>
          <input type="submit" value="' . $this->l('   Save   ') . '" name="submitATMOptions" class="button" />
        </center>
      </fieldset>
    </form>';
	}
	function displayMobileConfig() {
		if (! isset($this->_fieldsOptions) or ! sizeof($this->_fieldsOptions))
			return;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			$this->_html .= '<div class="info"><p>' . $this->l('This configuration can be made by shop.'). '</p></div>';
		}
		$this->_html .= '<form action="' . $this->base_config_url . '#config-3" id="formMobileGlobal_' . $this->name . '" name="form_mobile_' . $this->name . '" method="post" class="width3" enctype="multipart/form-data">';
		$this->_html .= '<fieldset>';
		$this->_html .= '<h3>' . $this->l('Responsive design settings') . '</h3>';
		$this->_html .= '<h5>' . $this->l('Enable only if your theme manage this behaviour') . '</h5>';
		foreach ( $this->_fieldsOptions as $key => $field ) {
			if (!isset($field['mobile']) || isset($field['mobile']) && !$field['mobile'])
				continue;
			if ($key == 'ATM_RESP_TOGGLE_HEIGHT')
				$this->_html .= '<h3>' . $this->l('Menu toggle settings') . '</h3>';
			else if ($key == 'ATM_RESP_MENU_PADDING')
				$this->_html .= '<h3>' . $this->l('Tabs settings') . '</h3>';
			else if ($key == 'ATMR_SUBMENU_BGCOLOR')
				$this->_html .= '<h3>' . $this->l('Submenus settings') . '</h3>';
			else if ($key == 'ATMR_COLUMNWRAP_PADDING')
				$this->_html .= '<h3>' . $this->l('Columns settings') . '</h3>';
			else if ($key == 'ATMR_COLUMN_PADDING') {
				$this->_html .= '<h3>' . $this->l('Items group settings') . '</h3>';
				$this->_html .= '<h4>' . $this->l('Container settings') . '</h4>';
			}
			else if ($key == 'ATMR_COLUMNTITLE_PADDING')
				$this->_html .= '<h4>' . $this->l('Title settings') . '</h4>';
			else if ($key == 'ATMR_COLUMN_ITEM_PADDING')
				$this->_html .= '<h3>' . $this->l('Items settings') . '</h3>';
			$this->_displayFormItem($key, $field);
		}
		$this->_html .= '<center>
          <input type="submit" value="' . $this->l('   Save   ') . '" name="submitATMMobileOptions" class="button" />
        </center>
      </fieldset>
    </form>';
	}
	private function _displayFormItem($key, $field) {
		$languages = Language::getLanguages(false);
		$val = Tools::getValue($key, Configuration::get($key));
			$this->_html .= '
        <div id="'. strtolower($key) .'-field">
        <label>' . $field ['title'] . ' </label>
        <div class="margin-form">';
			switch ($field ['type']) {
				case 'select' :
					$this->_html .= '<select id="' . $key . '" name="' . $key . '"' . (isset($field['onchange']) && $field['onchange'] ? ' onchange="'. $field['onchange'] : '') .'">';
					foreach ( $field ['list'] as $value )
						$this->_html .= '<option
              value="' . (isset($field ['cast']) ? $field ['cast']($value [$field ['identifier']]) : $value [$field ['identifier']]) . '"' . (($val === false && isset($field ['default']) && $field ['default'] === $value [$field ['identifier']]) || ($val == $value [$field ['identifier']]) ? ' selected="selected"' : '') . '>' . $value ['name'] . '</option>';
					$this->_html .= '</select>';
					if (isset($field['onchange']) && $field['onchange']) {
						$this->_html .= '<script type="text/javascript">$(document).ready(function() { $("select#'.$key.'").trigger("change"); });</script>';
					}
					break;
				case 'bool' :
					$this->_html .= '<label class="t" for="' . $key . '_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
          <input type="radio" name="' . $key . '" id="' . $key . '_on" value="1"' . ($val ? ' checked="checked"' : '') . '' . (isset($field ['disable']) && $field ['disable'] ? 'disabled="disabled"' : '') . ' />
          <label class="t" for="' . $key . '_on"> ' . $this->l('Yes') . '</label>
          <label class="t" for="' . $key . '_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
          <input type="radio" name="' . $key . '" id="' . $key . '_off" value="0" ' . (! $val ? 'checked="checked"' : '') . '' . (isset($field ['disable']) && $field ['disable'] ? 'disabled="disabled"' : '') . '/>
          <label class="t" for="' . $key . '_off"> ' . $this->l('No') . '</label>';
					break;
				case 'textLang' :
					foreach ( $languages as $language ) {
						$val = Tools::getValue($key . '_' . $language ['id_lang'], Configuration::get($key, $language ['id_lang']));
						$this->_html .= '
            <div id="' . $key . '_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
              <input size="' . $field ['size'] . '" type="text" name="' . $key . '_' . $language ['id_lang'] . '" value="' . $val . '" />
            </div>';
					}
					$this->_html .= $this->displayFlags($languages, $this->defaultLanguage, $key, $key, true);
					$this->_html .= '<div class="clear"></div>';
					break;
				case 'color' :
					$this->_html .= '<input type="text" name="' . $key . '" value="' . ($val === false && isset($field ['default']) && $field ['default'] ? $field ['default'] : $val) . '" size="20" class="pm_colorpicker" />' . (isset($field ['suffix']) ? $field ['suffix'] : '');
					break;
				case 'gradient' :
					$val = explode($this->gradient_separator, $val);
					if (isset($val [1])) {
						$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
						$color2 = htmlentities($val [1], ENT_COMPAT, 'UTF-8');
					}
					else
						$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
					$this->_html .= '<input type="text" name="' . $key . '[0]" id="' . $key . '_0" value="' . (!isset($color1) && isset($field ['default']) && $field ['default'] ? $field ['default'] : (isset($color1)?$color1:'')) . '" size="20" class="pm_colorpicker" />' . (isset($field ['suffix']) ? $field ['suffix'] : '');
					$this->_html .= '&nbsp; <span ' . (isset($color2) ? '' : 'style="display:none"') . ' id="' . $key . '_gradient"><input type="text" name="' . $key . '[1]" id="' . $key . '_1" value="' . (! isset($color2) ? '' : $color2) . '" size="20" class="pm_colorpicker" />' . (isset($field ['suffix']) ? $field ['suffix'] : '') . '</span>';
					$this->_html .= '&nbsp; <input type="checkbox" name="' . $key . '_gradient" value="1" ' . (isset($color2) ? 'checked=checked' : '') . ' /> &nbsp; ' . $this->l('Make a gradient');
					$this->_html .= '<script type="text/javascript">
            $("input[name=' . $key . '_gradient]").click(function() {
				showSpanIfChecked($(this),"#' . $key . '_gradient")
			});
            </script>';
					unset($color1);
					unset($color2);
					break;
				case 'password' :
					$this->_html .= '<input type="password" name="' . $key . '" value="' . $val . '" size="20" ' . (isset($field ['disable']) && $field ['disable'] ? 'disabled="disabled"' : '') . ' />' . (isset($field ['suffix']) ? $field ['suffix'] : '');
					break;
				case '4size' :
					if ($val || (isset($field ['default']) && $field ['default'])) {
						$borders = ($val !== false ? $val : @$field ['default']);
						$borders_size_tab = explode(' ', $borders);
						if (is_array($borders_size_tab)) {
							foreach ($borders_size_tab as &$borderValue) {
								if ($borderValue != 'auto') {
									$borderValue = intval(preg_replace('#px#', '', $borderValue));
								}
							}
						}
					}
					$this->_html .= $this->l('top') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? $borders_size_tab[0] : 0) . '" /> &nbsp; ';
					$this->_html .= $this->l('right') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? $borders_size_tab[1] : 0) . '" /> &nbsp; ';
					$this->_html .= $this->l('bottom') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? $borders_size_tab[2] : 0) . '" /> &nbsp; ';
					$this->_html .= $this->l('left') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? $borders_size_tab[3] : 0) . '" />';
					break;
				case '4size_position' :
					if ($val || (isset($field ['default']) && $field ['default'])) {
						$borders = ($val !== false ? $val : @$field ['default']);
						$borders_size_tab = explode(' ', $borders);
					}
					$this->_html .= $this->l('top') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) && isset($borders_size_tab[0]) && strlen($borders_size_tab[0]) ? intval(preg_replace('#px#', '', $borders_size_tab[0])) : '') . '" /> &nbsp; ' . $this->l('right') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) && isset($borders_size_tab[1]) && strlen($borders_size_tab[1]) ? intval(preg_replace('#px#', '', $borders_size_tab[1])) : '') . '" /> &nbsp; ' . $this->l('bottom') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) && isset($borders_size_tab[2]) && strlen($borders_size_tab[2]) ? intval(preg_replace('#px#', '', $borders_size_tab[2])) : '') . '" /> &nbsp; ' . $this->l('left') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) && isset($borders_size_tab[3]) && strlen($borders_size_tab[3]) ? intval(preg_replace('#px#', '', $borders_size_tab[3])) : '') . '" />';
					break;
				case 'image':
					if ($val !== false && $val == '')
						$this->_html .= '<span>N/A&nbsp;&nbsp;</span>';
					else if (($val === false && isset($field ['default']) && $field ['default']) || sizeof($val))
						$this->_html .= '<img src="'.($val === false && isset($field ['default']) && $field ['default'] ? $field ['default'] : $val).'" value="' . $val . '" />';
					$this->_html .= '<input type="file" name="' . $key . '" />';
					$this->_html .= '&nbsp; <input type="checkbox" name="' . $key . '_delete" /> &nbsp; ' . $this->l('Delete old image');
					break;
				case 'shadow':
					if ($val || (isset($field ['default']) && $field ['default'])) {
						$borders = ($val !== false ? $val : @$field ['default']);
						$borders_size_tab = explode(' ', $borders);
					}
					$this->_html .= $this->l('x') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? intval(preg_replace('#px#', '', $borders_size_tab [0])) : 0) . '" /> &nbsp; ' . $this->l('y') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? intval(preg_replace('#px#', '', $borders_size_tab [1])) : 0) . '" /> &nbsp; ' . $this->l('blur') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? intval(preg_replace('#px#', '', $borders_size_tab [2])) : 0) . '" /> &nbsp; ' . $this->l('spread distance') . ' <input size="3" type="text" name="' . $key . '[]" value="' . (isset($borders_size_tab) && is_array($borders_size_tab) ? intval(preg_replace('#px#', '', $borders_size_tab [3])) : 0) . '" />';
					break;
				case 'slider':
					$this->_html .= '<div class="pm_slider">';
					$this->_html .= '<input type="hidden" id="' . $key . '" name="' . $key . '" value="' . ($val === false && isset($field['default']) ? $field['default'] : $val) . '" />';
					$this->_html .= '<div id="slider-' . $key . '" style="display: inline-block; width:200px"></div>&nbsp;&nbsp;&nbsp;&nbsp;<em id="slider-suffix-' . $key . '">' . ($val === false && isset($field['default']) ? $field['default'] : $val) . ' ' . $field['suffix'] . '</em>';
					$this->_html .= '
					<script>
					$(function() {
						$("#slider-' . $key . '").slider({
							range: false,
							min: '. $field['min'] .',
							max: '. $field['max'] .',
							step: '. $field['step'] .',
							value: $("#' . $key . '").val(),
							slide: function(event, ui) {
								$("#' . $key . '").val(ui.value);
								$("#slider-suffix-' . $key . '").html(ui.value + " '. $field['suffix'] .'");
							}
						});
						$("#slider-' . $key . '").slider("value", $("#' . $key . '").val());
					});
					</script>';
					$this->_html .= '</div>';
					break;
				case 'text' :
				default :
					$this->_html .= '<input type="text" name="' . $key . '" value="' . ($val === false && isset($field ['default']) && $field ['default'] ? $field ['default'] : $val) . '" size="20" />' . (isset($field ['suffix']) ? $field ['suffix'] : '');
			}
			if (isset($field['desc']) && $field['desc']) {
				$this->_html .= '<img title="'.$field['desc'].'" id="' . $key . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
				$this->_html .= '<script type="text/javascript">pm_initTips("#' . $key . '")</script>';
			}
			$this->_html .= '</div>
			</div>';
	}
	private function initClassVar() {
		global $smarty, $cookie, $currentIndex, $employee;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$this->_context = Context::getContext();
			$this->_cookie = $this->_context->cookie;
			$this->_smarty = $this->_context->smarty;
		} else {
			$this->_cookie = $cookie;
			$this->_smarty = $smarty;
		}
		$this->_employee = $employee;
		$this->base_config_url = ((version_compare(_PS_VERSION_, '1.5.0.0', '<')) ? $currentIndex : $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller: '')) . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
		$languages = Language::getLanguages(false);
		$this->defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$this->_iso_lang = Language::getIsoById($this->_cookie->id_lang);
		$this->languages = $languages;
	}
	private function _displayForm() {
		$this->initClassVar();
		if (sizeof($this->errors)) {
			foreach ( $this->errors as $error )
				$this->_html .= $this->displayError($error);
		}
		$menus = AdvancedTopMenuClass::getMenus($this->_cookie->id_lang, false);
		if (! sizeof($menus))
			$this->_html .= '<p style="text-align:center;">' . $this->l('No tab') . '</p>';
		else {
			$this->_html .= '<div class="tabset" id="tabsetMenu">';
			foreach ( $menus as $menu ) {
				$this->_html .= '<a id="' . $menu ['id_menu'] . '" class="tab ' . ($menu ['id_menu'] == Tools::getValue('id_menu', false) ? 'sel' : '') . ' {content:\'cont_' . $menu ['id_menu'] . '\'}">' . $this->getAdminOutputNameValue($menu, false) . '</a>';
			}
			$this->_html .= '</div>';
			foreach ( $menus as $menu ) {
				$this->_html .= '<div id="cont_' . $menu ['id_menu'] . '">';
				$this->_html .= '<p>';
				$this->_html .= '<strong>' . $this->getAdminOutputNameValue($menu, true, 'menu') . '</strong>';
				$this->_html .= ' | ' . $this->getAdminOutputPrivacyValue($menu ['privacy']);
				$this->_html .= ' | <a href="' . $this->base_config_url . '&editMenu=1&id_menu=' . $menu ['id_menu'] . '#formTab" title="'.$this->l('Edit').'"><img src="../img/admin/edit.gif" /></a>';
				$this->_html .= ' | <a href="' . $this->base_config_url . '&deleteMenu=1&id_menu=' . $menu ['id_menu'] . '" onclick="return confirm(\'' . addcslashes($this->l('Delete item #'), "'") . $menu ['id_menu'] . ' ?\');" title="'.$this->l('Delete').'"><img src="../img/admin/delete.gif" /></a>';
				$this->_html .= ' | ' . $this->l('Displayed:') . ' <a href="' . $this->base_config_url . '&activeMenu=1&id_menu=' . $menu ['id_menu'] . '" class="ajax_script_load" title="'. ($menu['active'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($menu ['active'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveMenu' . $menu ['id_menu'] . '" /></a>';
				$this->_html .= ' | ' . $this->l('Displayed on mobile:') . ' <a href="' . $this->base_config_url . '&activeMobileMenu=1&id_menu=' . $menu ['id_menu'] . '" class="ajax_script_load" title="'. ($menu['active_mobile'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($menu ['active_mobile'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveMobileMenu' . $menu ['id_menu'] . '" /></a>';
				$this->_html .= '</p>';
				$columnsWrap = AdvancedTopMenuColumnWrapClass::getMenuColumnsWrap($menu ['id_menu'], $this->_cookie->id_lang, false);
				$this->_html .= '<div class="columnWrapSort">';
				if (! sizeof($columnsWrap))
					$this->_html .= '<p style="text-align:center;">' . $this->l('No column') . '</p>';
				else {
					foreach ( $columnsWrap as $columnWrap ) {
						$this->_html .= '<div class="menuColumnWrap" id="' . $columnWrap ['id_wrap'] . '">';
						$this->_html .= '<p>';
						$this->_html .= '<span class="dragWrap"><img src="' . $this->_path . 'img/arrow-move.png" title="'.$this->l('Move').'" /><b>' . $columnWrap ['internal_name'] . '</b></span>';
						$this->_html .= ' | ' . $this->getAdminOutputPrivacyValue($columnWrap ['privacy']);
						if ($columnWrap ['width'])
							$this->_html .= ' | ' . $this->l('Width:') . ' ' . $columnWrap ['width'] . 'px';
						$this->_html .= ' | <a href="' . $this->base_config_url . '&editColumnWrap=1&id_wrap=' . $columnWrap ['id_wrap'] . '&id_menu=' . $menu ['id_menu'] . '#formTab" title="'.$this->l('Edit').'"><img src="../img/admin/edit.gif" /></a>';
						$this->_html .= ' | <a href="' . $this->base_config_url . '&deleteColumnWrap=1&id_wrap=' . $columnWrap ['id_wrap'] . '&id_menu=' . $menu ['id_menu'] . '" onclick="return confirm(\'' . addcslashes($this->l('Delete item #'), "'") . $columnWrap ['id_wrap'] . ' ?\');" title="'.$this->l('Delete').'"><img src="../img/admin/delete.gif" /></a>';
						$this->_html .= ' | ' . $this->l('Displayed:') . ' <a href="' . $this->base_config_url . '&activeColumnWrap=1&id_wrap=' . $columnWrap ['id_wrap'] . '" class="ajax_script_load" title="'. ($columnWrap['active'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($columnWrap ['active'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveColumnWrap' . $columnWrap ['id_wrap'] . '" /></a>';
						$this->_html .= ' | ' . $this->l('Displayed on mobile:') . ' <a href="' . $this->base_config_url . '&activeMobileColumnWrap=1&id_wrap=' . $columnWrap ['id_wrap'] . '" class="ajax_script_load" title="'. ($columnWrap['active_mobile'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($columnWrap ['active_mobile'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveMobileColumnWrap' . $columnWrap ['id_wrap'] . '" /></a>';
						$this->_html .= '</p>';
						$columns = AdvancedTopMenuColumnClass::getMenuColums($columnWrap ['id_wrap'], $this->_cookie->id_lang, false);
						$this->_html .= '<div class="columnSort columnSort-' . $columnWrap ['id_wrap'] . '">';
						if (! sizeof($columns))
							$this->_html .= '<p style="text-align:center;">' . $this->l('No group') . '</p>';
						else {
							foreach ( $columns as $column ) {
								$columnElements = AdvancedTopMenuElementsClass::getMenuColumnElements($column ['id_column'], $this->_cookie->id_lang, false);
								$this->_html .= '<div class="menuColumn" id="' . $column ['id_column'] . '">';
								$this->_html .= '';
								$this->_html .= '<span class="dragColumn"><img src="' . $this->_path . 'img/arrow-move.png" title="'.$this->l('Move').'" />';
								if ($column['type'] != 8) {
									$this->_html .= '<strong>' . $this->getAdminOutputNameValue($column, true, 'column') . '</strong>';
								} else {
									$productInfos = AdvancedTopMenuProductColumnClass::getByIdColumn($column['id_column']);
									if (Validate::isLoadedObject($productInfos)) {
										$productObj = new Product($productInfos->id_product, false, $this->_cookie->id_lang);
										if (Validate::isLoadedObject($productObj)) {
											$this->_html .= '<strong>' . $this->l('Product:') . ' '. $productObj->name  .'</strong> <em>(ID: '. $productObj->id .')</em>';
										}
									}
								}
								$this->_html .= '</span>';
								$this->_html .= ' | ' . $this->getAdminOutputPrivacyValue($column ['privacy']);
								$this->_html .= ' | <a href="' . $this->base_config_url . '&editColumn=1&id_column=' . $column ['id_column'] . '&id_menu=' . $menu ['id_menu'] . '#formTab" title="'.$this->l('Edit').'"><img src="../img/admin/edit.gif" /></a>';
								$this->_html .= ' | <a href="' . $this->base_config_url . '&deleteColumn=1&id_column=' . $column ['id_column'] . '&id_menu=' . $menu ['id_menu'] . '" onclick="return confirm(\'' . addcslashes($this->l('Delete item #'), "'") . $column ['id_column'] . ' ?\');" title="'.$this->l('Delete').'"><img src="../img/admin/delete.gif" /></a>';
								$this->_html .= ' | ' . $this->l('Displayed:') . ' <a href="' . $this->base_config_url . '&activeColumn=1&id_column=' . $column ['id_column'] . '" class="ajax_script_load" title="'. ($column['active'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($column ['active'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveColumn' . $column ['id_column'] . '" /></a>';
								$this->_html .= ' | ' . $this->l('Displayed on mobile:') . ' <a href="' . $this->base_config_url . '&activeMobileColumn=1&id_column=' . $column ['id_column'] . '" class="ajax_script_load" title="'. ($column['active_mobile'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($column ['active_mobile'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveMobileColumn' . $column ['id_column'] . '" /></a>';
								if (self::_isFilledArray($columnsWrap) && sizeof($columnsWrap) > 1) {
									$this->_html .= ' | <form style="display:inline;" action="' . $this->base_config_url . '" method="post"><select name="id_wrap"><option>' . $this->l('Move to Column') . '</option>';
									foreach ( $columnsWrap as $columnWrap2 ) {
										if ($column['id_wrap'] != $columnWrap2 ['id_wrap']) {
											$this->_html .= '<option value="' . intval($columnWrap2 ['id_wrap']) . '">' . $columnWrap2 ['internal_name'] . '</option>';
										}
									}
									$this->_html .= '</select><input type="hidden" name="id_column" value="' . intval($column ['id_column']) . '" /><input type="hidden" name="id_menu" value="' . intval($menu ['id_menu']) . '" /><input type="submit" value="' . $this->l('OK') . '" name="submitFastChangeColumn" class="button" /></form>';
								}
								if ($column['type'] != 8) {
									$this->_html .= '<br /><br />';
									$this->_html .= '<table cellspacing="0" cellpadding="0" class="table table_sort" style="width:100%">
				                      <tbody>
				                        <tr class="nodrag nodrop">
				                          <th width="50">' . $this->l('ID') . '</th>
				                          <th width="100">' . $this->l('Type') . '</th>
				                          <th width="150">' . $this->l('Permit') . '</th>
				                          <th>' . $this->l('Value') . '</th>
				                          <th width="50">' . $this->l('Actions') . '</th>
				                          <th width="50">' . $this->l('Displayed') . '</th>
				                          <th width="80">' . $this->l('Displayed on mobile') . '</th>
				                        </tr>
	                    			  ';
									foreach ( $columnElements as $columnElement ) {
										$this->_html .= '<tr id="' . $columnElement ['id_element'] . '"><td>' . intval($columnElement ['id_element']) . '</td>
						                  <td>' . $this->getType($columnElement ['type']) . '</td>
						                  <td>' . $this->getAdminOutputPrivacyValue($columnElement ['privacy']) . '</td>
					             	     <td>' . $this->getAdminOutputNameValue($columnElement, true, 'element') . '</td>
						                  <td align="center"> <a href="' . $this->base_config_url . '&editElement=1&id_element=' . $columnElement ['id_element'] . '&id_menu=' . $menu ['id_menu'] . '#formTab" title="'.$this->l('Edit').'"><img src="../img/admin/edit.gif" /></a> <a href="' . $this->base_config_url . '&deleteElement=1&id_element=' . intval($columnElement ['id_element']) . '&id_menu=' . $menu ['id_menu'] . '" onclick="return confirm(\'' . addcslashes($this->l('Delete item #'), "'") . intval($columnElement ['id_element']) . ' ?\');" title="'.$this->l('Delete').'"><img src="../img/admin/delete.gif" /></a></td>';
										$this->_html .= ' <td align="center"> <a href="' . $this->base_config_url . '&activeElement=1&id_element=' . $columnElement ['id_element'] . '" class="ajax_script_load" title="'. ($columnElement['active'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($columnElement ['active'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveElement' . $columnElement ['id_element'] . '" /></a></td>';
										$this->_html .= ' <td align="center"> <a href="' . $this->base_config_url . '&activeMobileElement=1&id_element=' . $columnElement ['id_element'] . '" class="ajax_script_load" title="'. ($columnElement['active_mobile'] ? $this->l('Yes') : $this->l('No')) .'"><img src="../img/admin/' . ($columnElement ['active_mobile'] ? 'enabled' : 'disabled') . '.gif" id="imgActiveMobileElement' . $columnElement ['id_element'] . '" /></a></td>';
										$this->_html .= '</tr>';
									}
									$this->_html .= '</tbody>
	                          		</table>';
								}
                          		$this->_html .= '</tbody></div>';
							}
						}
						$this->_html .= '<br class="clear" /></div>';
						$this->_html .= '
            <script stype="text/javascript">
            $( ".columnSort-' . $columnWrap ['id_wrap'] . '" ).sortable({
              placeholder: "ui-state-highlight",
              delay: 300,
              handle : ".dragColumn",
              update: function(event, ui) {
                var orderColumn = $(this).sortable("toArray");
                saveOrderColumn(orderColumn.join(","));
              }
            });
            </script>';
						$this->_html .= '</div>';
					}
				}
				$this->_html .= '</div>';
				$this->_html .= '</div>';
			}
		}
		$this->_html .= ' <br />
                  <br />';
		$this->_html .= '<div id="wrapFormTab"><ul style="height: 30px;" id="formTab">
          <li' . (Tools::getValue('editMenu') && Tools::getValue('id_menu') ? ' class="ui-tabs-selected ui-tabs-active"' : '') . '><a href="#editMenuFormContainer" ><span><img src="' . $this->_path . 'img/menu.png" />' . (Tools::getValue('editMenu') && Tools::getValue('id_menu') ? $this->l('Edit tab') : $this->l('Add tab')) . '</span></a></li>
          <li' . (Tools::getValue('editColumnWrap') && Tools::getValue('id_wrap') ? ' class="ui-tabs-selected ui-tabs-active"' : '') . '><a href="#editColumnWrapContainer"><span><img src="' . $this->_path . 'img/column.png" />' . (Tools::getValue('editColumnWrap') && Tools::getValue('id_wrap') ? $this->l('Edit column') : $this->l('Add column')) . '</span></a></li>
          <li' . (Tools::getValue('editColumn') && Tools::getValue('id_column') ? ' class="ui-tabs-selected ui-tabs-active"' : '') . '><a href="#editColumnContainer"><span><img src="' . $this->_path . 'img/group.png" />' . (Tools::getValue('editColumn') && Tools::getValue('id_column') ? $this->l('Edit item group') : $this->l('Add item group')) . '</span></a></li>
          <li' . (Tools::getValue('editElement') && Tools::getValue('id_element') ? ' class="ui-tabs-selected ui-tabs-active"' : '') . '><a href="#editElementContainer"><span><img src="' . $this->_path . 'img/item.png" />' . (Tools::getValue('editElement') && Tools::getValue('id_element') ? $this->l('Edit item') : $this->l('Add item')) . '</span></a></li>
        </ul>';
		$cms = CMS::listCms(intval($this->_cookie->id_lang));
		$categories = Category::getCategories(intval($this->_cookie->id_lang), false);
		$manufacturer = Manufacturer::getManufacturers(false, $this->_cookie->id_lang, true);
		$supplier = Supplier::getSuppliers(false, $this->_cookie->id_lang, true);
		$menus = AdvancedTopMenuClass::getMenus($this->_cookie->id_lang, false);
		$this->_html .= '<div id="editMenuFormContainer">';
		$ObjAdvancedTopMenuClass = false;
		if (Tools::getValue('editMenu') && Tools::getValue('id_menu'))
			$ObjAdvancedTopMenuClass = new AdvancedTopMenuClass(Tools::getValue('id_menu'));
		$this->displayMenuForm($cms, $categories, $manufacturer, $supplier, $ObjAdvancedTopMenuClass);
		$this->_html .= '</div>';
		$this->_html .= '<div id="editColumnWrapContainer">';
		$ObjAdvancedTopMenuColumnWrapClass = false;
		if (Tools::getValue('editColumnWrap') && Tools::getValue('id_wrap'))
			$ObjAdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass(Tools::getValue('id_wrap'));
		$this->displayColumnWrapForm($menus, $ObjAdvancedTopMenuColumnWrapClass);
		$this->_html .= '</div>';
		$this->_html .= '<div id="editColumnContainer">';
		$ObjAdvancedTopMenuColumnClass = false;
		$ObjAdvancedTopMenuProductColumnClass = new AdvancedTopMenuProductColumnClass();;
		if (Tools::getValue('editColumn') && Tools::getValue('id_column')) {
			$ObjAdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass(Tools::getValue('id_column'));
			$ObjAdvancedTopMenuProductColumnClass = AdvancedTopMenuProductColumnClass::getByIdColumn(Tools::getValue('id_column'));
		}
		$cms = CMS::listCms(intval($this->_cookie->id_lang));
		$this->displayColumnForm($menus, $cms, $categories, $manufacturer, $supplier, $ObjAdvancedTopMenuColumnClass, $ObjAdvancedTopMenuProductColumnClass);
		$this->_html .= '</div>';
		$this->_html .= '<div id="editElementContainer">';
		$ObjAdvancedTopMenuElementsClass = false;
		if (Tools::getValue('editElement') && Tools::getValue('id_element'))
			$ObjAdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass(Tools::getValue('id_element'));
		$this->displayElementForm($menus, array(), $cms, $categories, $manufacturer, $supplier, $ObjAdvancedTopMenuElementsClass);
		$this->_html .= '</div>';
     	$this->_html .= '</div>';
		$this->initColorPicker();
		$this->initTinyMce();
	}
	private function initColorPicker() {
		$this->_html .= '<script type="text/javascript">
          var currentColorPicker = false;
            $("input[class=pm_colorpicker]").ColorPicker({
              onSubmit: function(hsb, hex, rgb, el) {
                $(el).val("#"+hex);
                $(el).ColorPickerHide();
              },
              onBeforeShow: function () {
                currentColorPicker = $(this);
                $(this).ColorPickerSetColor(this.value);
              },
              onChange: function (hsb, hex, rgb) {
                $(currentColorPicker).val("#"+hex);
              }
            })
            .bind("keyup", function(){
              $(this).ColorPickerSetColor(this.value);
            });
    </script>';
	}
private function initTinyMce()
	{
		if (version_compare(_PS_VERSION_, '1.4.1.0', '<')) {
			$this->_html .= '
			<script type="text/javascript">
				tinyMCE.init({
					mode : "specific_textareas",
					editor_selector : "rte",
					theme : "advanced",
					plugins : "safari,directionality,searchreplace,layer,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen",
					// Theme options
					theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
					theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
					theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
					content_css : "' . __PS_BASE_URI__ . 'themes/' . _THEME_NAME_ . '/css/global.css",
					document_base_url : "' . __PS_BASE_URI__ . '",
					width: "600",
					height: "auto",
					font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
					// Drop lists for link/image/media/template dialogs
					template_external_list_url : "lists/template_list.js",
					external_link_list_url : "lists/link_list.js",
					external_image_list_url : "lists/image_list.js",
					media_external_list_url : "lists/media_list.js",
					elements : "nourlconvert",
					convert_urls : false,
					language : "' . (file_exists(_PS_ROOT_DIR_ . '/js/tinymce/jscripts/tiny_mce/langs/' . $this->_iso_lang . '.js') ? $this->_iso_lang : 'en') . '"
				});
			</script>';
		} else {
			$isoTinyMCE = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $this->_iso_lang . '.js') ? $this->_iso_lang : 'en');
			$ad = dirname($_SERVER ["PHP_SELF"]);
			$this->_html .= '<script type="text/javascript">
				var iso = \'' . $isoTinyMCE . '\' ;
				var pathCSS = \'' . _THEME_CSS_DIR_ . '\' ;
				var ad = \'' . $ad . '\' ;
				var defaultIdLang = \'' . $this->_cookie->id_lang . '\' ;
			 </script>';
			$this->_html .= '<script type="text/javascript" src="' . $this->_path . 'js/pm_tinymce.inc.js"></script>';
		}
	}
	private function displayMenuForm($cms, $categories, $manufacturer, $supplier, $ObjAdvancedTopMenuClass) {
		$imgIconMenuDirIsWritable = is_writable(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/menu_icons');
		$haveDepend = false;
		$ids_lang = 'menuname¤menulink¤menu_value_over¤menu_value_under¤menuimage¤menuimagelegend';
		$this->_html .= '<script type="text/javascript">id_language = ' . intval($this->defaultLanguage) . ';</script>';
		if ($ObjAdvancedTopMenuClass)
			$haveDepend = AdvancedTopMenuClass::menuHaveDepend($ObjAdvancedTopMenuClass->id);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			$this->_html .= '<div class="warning warn clear">' . $this->l('Configuration can not be different by shop. It will be applied to all shop. However, you can create a menu for a particular shop.'). '</div>';
		}
		$this->_html .= '<form action="' . $this->base_config_url . '" method="post" id="menuform_' . $this->name . '" name="menuform_' . $this->name . '" method="post" enctype="multipart/form-data" class="width3">
    <div id="blocMenuForm">
        ' . ($ObjAdvancedTopMenuClass ? '<input type="hidden" name="id_menu" value="' . intval($ObjAdvancedTopMenuClass->id) . '" /><br /><a href="' . $this->base_config_url . '"><img src="../img/admin/arrow2.gif" />' . $this->l('Back') . '</a><br class="clear" /><br />' : '');
		$this->_html .= '<h3>' . $this->l('General settings') . '</h3>';
		$this->_html .= '<label>' . $this->l('Type') . '</label>
       <div class="margin-form"><select name="type" id="type_menu">
          <option value="">-- ' . $this->l('Choose') . ' --</option>
          <option value="1" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 1 ? 'selected="selected"' : '') . '>' . $this->l('CMS') . '</option>
          <option value="2" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 2 ? 'selected="selected"' : '') . '>' . $this->l('Link') . '</option>
          <option value="3" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 3 ? 'selected="selected"' : '') . '>' . $this->l('Category') . '</option>
           <option value="4" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 4 ? 'selected="selected"' : '') . '>' . $this->l('Manufacturer') . '</option>
          <option value="5" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 5 ? 'selected="selected"' : '') . '>' . $this->l('Supplier') . '</option>
          <option value="6" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 6 ? 'selected="selected"' : '') . '>' . $this->l('Search') . '</option>
          <option value="7" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 7 ? 'selected="selected"' : '') . '>' . $this->l('Only image or icon') . '</option>
       </select></div>';
		$this->_html .= '<script type="text/javascript">$(document).ready(function() { $("#type_menu").change(function() {showMenuType($(this),"menu");}); });</script>';
		if ($ObjAdvancedTopMenuClass && in_array($ObjAdvancedTopMenuClass->type, $this->rebuildable_type)) {
			$this->_html .= '<label>' . $this->l('Rebuild tree') . '</label>
          <div class="margin-form"><label class="t" for="rebuild_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="rebuild" id="rebuild_on" value="1" />
            <label class="t" for="rebuild_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="rebuild_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="rebuild" id="rebuild_off" value="0" checked=checked />
            <label class="t" for="rebuild_off"> ' . $this->l('No') . '</label><br />' . $this->l('Caution, this may change the appearance of your menu !') . '</div>';
		}
		$this->_html .= '<div class="add_category menu_element" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 3 ? '' : 'style="display:none;"') . '>';
		$this->displayCategoriesSelect($categories, ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->id_category : 0));
		$this->_html .= '<label>' . $this->l('Include Subcategories') . '</label>
          <div class="margin-form"><label class="t" for="menu_subcats_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="include_subs" id="menu_subcats_on" value="1" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 3 && $haveDepend ? ' checked=checked' : '') . ' />
            <label class="t" for="menu_subcats_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="menu_subcats_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="include_subs" id="menu_subcats_off" value="0" ' . (! $ObjAdvancedTopMenuClass || ($ObjAdvancedTopMenuClass->type == 3 && ! $haveDepend) ? ' checked=checked' : '') . ' />
            <label class="t" for="menu_subcats_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_cms menu_element"   ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 1 ? '' : 'style="display:none;"') . '>';
		$this->displayCmsSelect($cms, ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->id_cms : 0));
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_manufacturer menu_element"  ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 4 ? '' : 'style="display:none;"') . '>';
		$this->_html .= '<label>' . $this->l('All manufacturers') . '</label>
          <div class="margin-form"><label class="t" for="menu_submanu_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="include_subs_manu" id="menu_submanu_on" value="1" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 4 && $haveDepend ? ' checked=checked' : '') . ' />
            <label class="t" for="menu_submanu_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="menu_submanu_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="include_subs_manu" id="menu_submanu_off" value="0" ' . (! $ObjAdvancedTopMenuClass || ($ObjAdvancedTopMenuClass->type == 4 && ! $haveDepend) ? ' checked=checked' : '') . ' />
            <label class="t" for="menu_submanu_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<script type="text/javascript">$("#menu_submanu_on, #menu_submanu_off").click(function() {hideNextIfTrue($(this));});</script>';
		$this->_html .= '<div ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 4 && $haveDepend ? ' style="display:none"' : '') . '>';
		$this->displayManufacturerSelect($manufacturer, ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->id_manufacturer : 0));
		$this->_html .= '</div>';
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_supplier menu_element"  ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 5 ? '' : 'style="display:none;"') . '>';
		$this->_html .= '<label>' . $this->l('All suppliers') . '</label>
          <div class="margin-form"><label class="t" for="menu_subsuppl_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="include_subs_suppl" id="menu_subsuppl_on" value="1" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 5 && $haveDepend ? ' checked=checked' : '') . ' />
            <label class="t" for="menu_subsuppl_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="menu_subsuppl_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="include_subs_suppl" id="menu_subsuppl_off" value="0" ' . (! $ObjAdvancedTopMenuClass || ($ObjAdvancedTopMenuClass->type == 5 && ! $haveDepend) ? ' checked=checked' : '') . ' />
            <label class="t" for="menu_subsuppl_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<script type="text/javascript">$("#menu_subsuppl_on, #menu_subsuppl_off").click(function() {hideNextIfTrue($(this));});</script>';
		$this->_html .= '<div ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 5 && $haveDepend ? ' style="display:none"' : '') . '>';
		$this->displaySupplierSelect($supplier, ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->id_supplier : 0));
		$this->_html .= ' </div>';
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_link menu_element"  ' . ($ObjAdvancedTopMenuClass && ($ObjAdvancedTopMenuClass->type != 2 || $ObjAdvancedTopMenuClass->type != 7) ? 'style="display:none;"' : '') . '>
          <label>' . $this->l('Link') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="menulink_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input size="20" type="text" name="link_' . $language ['id_lang'] . '" class="adtmInputLink" value="' . ($ObjAdvancedTopMenuClass && isset($ObjAdvancedTopMenuClass->link[$language['id_lang']]) ? $ObjAdvancedTopMenuClass->link [$language ['id_lang']] : '') . '" />
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'menulink', true);
		$this->_html .= '<div class="clear"></div></div></div>';
		$this->_html .= '<label>' . $this->l('Prevent click on link') . '</label>
          <div class="margin-form">
          <input type="checkbox" name="clickable" id="menu_clickable" value="1" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->link [$this->defaultLanguage] == '#' ? ' checked=checked' : '') . '  />
          <small>' . $this->l('add a # in the link field, do not remove') . '</small>
          </div>';
		$this->_html .= '<script type="text/javascript">$("#menu_clickable").click(function() {setUnclickable($(this));});</script>';
		$this->displayTargetSelect(($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->target : 0));
		$this->_html .= '<div class="add_title menu_element"  ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 7 ? 'style="display:none;"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Title') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="menuname_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <textarea cols="20" rows="2" name="name_' . $language ['id_lang'] . '">' . ($ObjAdvancedTopMenuClass && isset($ObjAdvancedTopMenuClass->name[$language['id_lang']]) ? $ObjAdvancedTopMenuClass->name [$language ['id_lang']] : '') . '</textarea>
                <br />'. $this->l('(if filled, will replace original title)') .'
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'menuname', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '</div>';
		$this->_html .= '<label>' . $this->l('Active') . '</label>
          <div class="margin-form"><label class="t" for="menu_active_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_menu" id="menu_active_on" value="1"' . (! $ObjAdvancedTopMenuClass || ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->active) ? ' checked="checked"' : '') . ' />
            <label class="t" for="active_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="menu_active_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_menu" id="menu_active_off" value="0" ' . ($ObjAdvancedTopMenuClass && ! $ObjAdvancedTopMenuClass->active ? 'checked="checked"' : '') . '/>
            <label class="t" for="active_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<label>' . $this->l('Active on mobile') . '</label>
          <div class="margin-form"><label class="t" for="menu_active_mobile_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_mobile_menu" id="menu_active_mobile_on" value="1"' . (! $ObjAdvancedTopMenuClass || ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->active_mobile) ? ' checked="checked"' : '') . ' />
            <label class="t" for="active_mobile_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="menu_active_mobile_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_mobile_menu" id="menu_active_mobile_off" value="0" ' . ($ObjAdvancedTopMenuClass && ! $ObjAdvancedTopMenuClass->active_mobile ? 'checked="checked"' : '') . '/>
            <label class="t" for="active_mobile_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<label>' . $this->l('Privacy Options') . '</label>
        <div class="margin-form"><select name="privacy">
          <option value="0" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->privacy == 0 ? 'selected="selected"' : '') . '>' . $this->l('For all') . '</option>
          <option value="1" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->privacy == 1 ? 'selected="selected"' : '') . '>' . $this->l('Only for visitors') . '</option>
          <option value="2" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->privacy == 2 ? 'selected="selected"' : '') . '>' . $this->l('Only for registered users') . '</option>
       </select></div>';
		if (! $imgIconMenuDirIsWritable)
			$this->_html .= '<div class="warning warn clear">' . $this->l('To upload an icon, please assign CHMOD 777 to the directory:') . ' ' . _PS_ROOT_DIR_ . '/modules/' . $this->name . '/column_icons' . '</div>';
		$this->_html .= '<label>' . $this->l('Icon or image') . '</label>';
		$this->_html .= '<div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
			<div id="menuimage_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
				<input type="file" name="icon_' . $language ['id_lang'] . '" size="20" ' . (! $imgIconMenuDirIsWritable ? 'disabled=disabled' : '') . ' />
				' . ($ObjAdvancedTopMenuClass && isset($ObjAdvancedTopMenuClass->have_icon[$language['id_lang']]) && $ObjAdvancedTopMenuClass->have_icon[$language['id_lang']] ? '<input type="hidden" name="have_icon_' . $language ['id_lang'] . '" value="' . intval($ObjAdvancedTopMenuClass->have_icon[$language['id_lang']]) . '" /><br />
				<img src="' . $this->_path . 'menu_icons/' . $ObjAdvancedTopMenuClass->id . '-' . $language['iso_code'] . '.' . (isset($ObjAdvancedTopMenuClass->image_type[$language['id_lang']]) ? $ObjAdvancedTopMenuClass->image_type[$language['id_lang']] : 'jpg') . '?' . uniqid() . '" /><br />
				<input type="checkbox" name="unlink_icon_' . $language ['id_lang'] . '" value="1" /> &nbsp; ' . $this->l('Delete this image') : '') . '
				<small>(' . $this->l('gif, jpg, png') . ')</small>
			</div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'menuimage', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<label>' . $this->l('Image legend') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="menuimagelegend_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input type="text" maxlength="255" name="image_legend_' . $language ['id_lang'] . '" value="' . ($ObjAdvancedTopMenuClass && isset($ObjAdvancedTopMenuClass->image_legend[$language['id_lang']]) ? $ObjAdvancedTopMenuClass->image_legend[$language['id_lang']] : '') . '" />
                <br />'. $this->l('(if empty, title will be used)') .'
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'menuimagelegend', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<h3>' . $this->l('Style settings') . ' <small>(' . $this->l('if empty, the global styles are used') . ')</small></h3>';
		$this->_html .= '<div class="add_title menu_element"  ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->type == 7 ? 'style="display:none;"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Text color') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="txt_color_menu_tab" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->txt_color_menu_tab : '') . '" />
          </div>';
		$this->_html .= '<label>' . $this->l('Text color over') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="txt_color_menu_tab_hover" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->txt_color_menu_tab_hover : '') . '" />
          </div>';
		$this->_html .= '</div>';
		$color1 = false;
		$color2 = false;
		$val = false;
		if ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->fnd_color_menu_tab) {
			$val = explode($this->gradient_separator, $ObjAdvancedTopMenuClass->fnd_color_menu_tab);
			if (isset($val [1])) {
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($val [1], ENT_COMPAT, 'UTF-8');
			}
			else
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
		}
		$this->_html .= '<label>' . $this->l('Background color') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="fnd_color_menu_tab[0]" id="fnd_color_menu_tab_0" class="pm_colorpicker" value="' . (! $color1 ? '' : $color1) . '" size="20" />
          &nbsp; <span ' . (isset($color2) && $color2 ? '' : 'style="display:none"') . ' id="fnd_color_menu_tab_gradient"><input size="20" type="text" class="pm_colorpicker" name="fnd_color_menu_tab[1]" id="fnd_color_menu_tab_1" value="' . (! isset($color2) || ! $color2 ? '' : $color2) . '" size="20" /></span>
          &nbsp; <input type="checkbox" name="fnd_color_menu_tab_gradient" value="1" ' . (isset($color2) && $color2 ? 'checked=checked' : '') . ' /> &nbsp; ' . $this->l('Make a gradient') . '
          </div>';
		$this->_html .= '<script type="text/javascript">$("input[name=fnd_color_menu_tab_gradient]").click(function() {showSpanIfChecked($(this),"#fnd_color_menu_tab_gradient");});</script>';
		$color1 = false;
		$color2 = false;
		$val = false;
		if ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->fnd_color_menu_tab_over) {
			$val = explode($this->gradient_separator, $ObjAdvancedTopMenuClass->fnd_color_menu_tab_over);
			if (isset($val [1])) {
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($val [1], ENT_COMPAT, 'UTF-8');
			}
			else
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
		}
		$this->_html .= '<label>' . $this->l('Background color over') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="fnd_color_menu_tab_over[0]" id="fnd_color_menu_tab_over_0" class="pm_colorpicker" value="' . (! $color1 ? '' : $color1) . '" size="20" />
          &nbsp; <span ' . (isset($color2) && $color2 ? '' : 'style="display:none"') . ' id="fnd_color_menu_tab_over_gradient"><input size="20" type="text" class="pm_colorpicker" name="fnd_color_menu_tab_over[1]" id="fnd_color_menu_tab_over_1" value="' . (! isset($color2) || ! $color2 ? '' : $color2) . '" size="20" /></span>
          &nbsp; <input type="checkbox" name="fnd_color_menu_tab_over_gradient" value="1" ' . (isset($color2) && $color2 ? 'checked=checked' : '') . ' /> &nbsp; ' . $this->l('Make a gradient') . '
          </div>';
		$this->_html .= '<script type="text/javascript">$("input[name=fnd_color_menu_tab_over_gradient]").click(function() {showSpanIfChecked($(this),"#fnd_color_menu_tab_over_gradient");});</script>';
		if ($ObjAdvancedTopMenuClass) {
			$borders_size_tab = explode(' ', $ObjAdvancedTopMenuClass->border_size_tab);
		}
		$this->_html .= '<label>' . $this->l('Border size') . '</label>
          <div class="margin-form">
          ' . $this->l('top') . ' <input size="3" type="text" name="border_size_tab[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_tab !== '0' && isset($borders_size_tab [0]) ? intval(preg_replace('#px#', '', $borders_size_tab [0])) : '') . '" /> &nbsp;
          ' . $this->l('right') . ' <input size="3" type="text" name="border_size_tab[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_tab !== '0' && isset($borders_size_tab [1]) ? intval(preg_replace('#px#', '', $borders_size_tab [1])) : '') . '" /> &nbsp;
          ' . $this->l('bottom') . ' <input size="3" type="text" name="border_size_tab[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_tab !== '0' && isset($borders_size_tab [2]) ? intval(preg_replace('#px#', '', $borders_size_tab [2])) : '') . '" /> &nbsp;
          ' . $this->l('left') . ' <input size="3" type="text" name="border_size_tab[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_tab !== '0' && isset($borders_size_tab [3]) ? intval(preg_replace('#px#', '', $borders_size_tab [3])) : '') . '" />
          </div>';
		$this->_html .= '<label>' . $this->l('Border color') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="border_color_tab" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->border_color_tab : '') . '" />
          </div>';
		$this->_html .= '<h4>' . $this->l('Submenu settings') . '</h4>';
		$this->_html .= '<label>' . $this->l('Width (px)') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="width_submenu" value="' . ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->width_submenu : '') . '" />
          <small>(' . $this->l('Put 0 for automatic width') . ')</small>
          </div>';
		$this->_html .= '<label>' . $this->l('Minimal height (px)') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="minheight_submenu" value="' . ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->minheight_submenu : '') . '" />
          <small>(' . $this->l('Put 0 for automatic height') . ')</small>
          </div>';
		$this->_html .= '<label>' . $this->l('Position') . '</label>
      <div class="margin-form"><select name="position_submenu">
      <option value="" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->position_submenu == 0 ? 'selected="selected"' : '') . '>' . $this->l('Use global styles') . '</option>
      <option value="1" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->position_submenu == 1 ? 'selected="selected"' : '') . '>' . $this->l('Left-aligned current menu') . '</option>
      <option value="3" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->position_submenu == 3 ? 'selected="selected"' : '') . '>' . $this->l('Right-aligned current menu') . '</option>
      <option value="2" ' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->position_submenu == 2 ? 'selected="selected"' : '') . '>' . $this->l('Left-aligned global menu') . '</option>
      </select> &nbsp; <span></span>
      </div>';
		$color1 = false;
		$color2 = false;
		$val = false;
		if ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->fnd_color_submenu) {
			$val = explode($this->gradient_separator, $ObjAdvancedTopMenuClass->fnd_color_submenu);
			if (isset($val [1])) {
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($val [1], ENT_COMPAT, 'UTF-8');
			}
			else
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
		}
		$this->_html .= '<label>' . $this->l('Background color') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="fnd_color_submenu[0]" id="fnd_color_submenu_0" class="pm_colorpicker" value="' . (! $color1 ? '' : $color1) . '" size="20" />
          &nbsp; <span ' . (isset($color2) && $color2 ? '' : 'style="display:none"') . ' id="fnd_color_submenu_gradient"><input size="20" type="text" class="pm_colorpicker" name="fnd_color_submenu[1]" id="fnd_color_submenu_1" value="' . (! isset($color2) || ! $color2 ? '' : $color2) . '" size="20" /></span>
          &nbsp; <input type="checkbox" name="fnd_color_submenu_gradient" value="1" ' . (isset($color2) && $color2 ? 'checked=checked' : '') . ' /> &nbsp; ' . $this->l('Make a gradient') . '
          </div>';
		$this->_html .= '<script type="text/javascript">$("input[name=fnd_color_submenu_gradient]").click(function() {showSpanIfChecked($(this),"#fnd_color_submenu_gradient");});</script>';
		if ($ObjAdvancedTopMenuClass) {
			$borders_size_submenu = explode(' ', $ObjAdvancedTopMenuClass->border_size_submenu);
		}
		$this->_html .= '<label>' . $this->l('Border size') . '</label>
          <div class="margin-form">
          ' . $this->l('top') . ' <input size="3" type="text" name="border_size_submenu[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_submenu !== '0' && isset($borders_size_submenu [0]) ? intval(preg_replace('#px#', '', $borders_size_submenu [0])) : '') . '" /> &nbsp;
          ' . $this->l('right') . ' <input size="3" type="text" name="border_size_submenu[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_submenu !== '0' && isset($borders_size_submenu [1]) ? intval(preg_replace('#px#', '', $borders_size_submenu [1])) : '') . '" /> &nbsp;
          ' . $this->l('bottom') . ' <input size="3" type="text" name="border_size_submenu[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_submenu !== '0' && isset($borders_size_submenu [2]) ? intval(preg_replace('#px#', '', $borders_size_submenu [2])) : '') . '" /> &nbsp;
          ' . $this->l('left') . ' <input size="3" type="text" name="border_size_submenu[]" value="' . ($ObjAdvancedTopMenuClass && $ObjAdvancedTopMenuClass->border_size_submenu !== '0' && isset($borders_size_submenu [3]) ? intval(preg_replace('#px#', '', $borders_size_submenu [3])) : '') . '" />
          </div>';
		$this->_html .= '<label>' . $this->l('Border color') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="border_color_submenu" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuClass ? $ObjAdvancedTopMenuClass->border_color_submenu : '') . '" />
          </div>';
		$hasAdditionnalText = false;
		foreach ($this->languages as $language) {
			if (isset($ObjAdvancedTopMenuClass->value_over[$language['id_lang']]) && !empty($ObjAdvancedTopMenuClass->value_over[$language['id_lang']]) || isset($ObjAdvancedTopMenuClass->value_under[$language['id_lang']]) && !empty($ObjAdvancedTopMenuClass->value_under[$language['id_lang']])) {
				$hasAdditionnalText = true;
				break;
			}
		}
		$this->_html .= '<label>' . $this->l('Show additionnal text settings') . '</label>
          <div class="margin-form"><label class="t" for="tinymce_container_toggle_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="tinymce_container_toggle_menu" id="tinymce_container_toggle_on" value="1"' . ($hasAdditionnalText ? ' checked="checked"' : '') . ' />
            <label class="t" for="tinymce_container_toggle_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="tinymce_container_toggle_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="tinymce_container_toggle_menu" id="tinymce_container_toggle_off" value="0" ' . (!$hasAdditionnalText ? 'checked="checked"' : '') . '/>
            <label class="t" for="tinymce_container_toggle_off"> ' . $this->l('No') . '</label></div>';
        $this->_html .= '<div class="tinymce_container"' . ($hasAdditionnalText ? ' style="display: block"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Text displayed above columns') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="menu_value_over_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <textarea class="rte" cols="100" rows="10" name="value_over_' . $language ['id_lang'] . '">' . ($ObjAdvancedTopMenuClass && isset($ObjAdvancedTopMenuClass->value_over[$language['id_lang']]) ? htmlentities(stripslashes($ObjAdvancedTopMenuClass->value_over [$language ['id_lang']]), ENT_COMPAT, 'UTF-8') : '') . '</textarea>
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'menu_value_over', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<label>' . $this->l('Text displayed below columns') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="menu_value_under_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
          <textarea class="rte" cols="100" rows="10"  name="value_under_' . $language ['id_lang'] . '">' . ($ObjAdvancedTopMenuClass && isset($ObjAdvancedTopMenuClass->value_under[$language['id_lang']]) ? htmlentities(stripslashes($ObjAdvancedTopMenuClass->value_under [$language ['id_lang']]), ENT_COMPAT, 'UTF-8') : '') . '</textarea>
               </div>';
		}
		$this->_html .= '</div><!-- .tinymce_container -->';
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'menu_value_under', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<center>
            <input type="submit" value="' . $this->l('   Save   ') . '" name="submitMenu" class="button" />
          </center>
      </form></div><br />';
		if ($ObjAdvancedTopMenuClass)
			$this->_html .= '<script type="text/javascript">$(function(){showMenuType($("#type_menu"),"menu")});</script>';
	}
	private function displayColumnWrapForm($menus, $ObjAdvancedTopMenuColumnWrapClass) {
		$ids_lang = 'columnwrap_value_over¤columnwrap_value_under';
		$this->_html .= '<script type="text/javascript">id_language = ' . intval($this->defaultLanguage) . ';</script>';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			$this->_html .= '<div class="warning warn clear">' . $this->l('Configuration can not be different by shop.  It will be applied to all shop.'). '</div>';
		}
		$this->_html .= '<form action="' . $this->base_config_url . '" id="formColumn_' . $this->name . '" name="form_' . $this->name . '" method="post" enctype="multipart/form-data" class="width3">
    <div id="blocColumnWrapForm">
        ' . ($ObjAdvancedTopMenuColumnWrapClass ? '<input type="hidden" name="id_wrap" value="' . intval($ObjAdvancedTopMenuColumnWrapClass->id) . '" /><br /><a href="' . $this->base_config_url . '"><img src="../img/admin/arrow2.gif" />' . $this->l('Back') . '</a><br class="clear" /><br />' : '');
		$this->_html .= '<h3>' . $this->l('General settings') . '</h3>';
		$this->_html .= '<label>' . $this->l('Parent tab') . '</label>
       <div class="margin-form"><select name="id_menu">
          <!-- <option value="">-- ' . $this->l('Choose') . ' --</option> -->';
		foreach ( $menus as $menu ) {
			$this->_html .= '<option value="' . $menu ['id_menu'] . '" ' . ($ObjAdvancedTopMenuColumnWrapClass && $ObjAdvancedTopMenuColumnWrapClass->id_menu == $menu ['id_menu'] ? 'selected="selected"' : '') . '>' . $this->getAdminOutputNameValue($menu, false) . '</option>';
		}
		$this->_html .= ' </select></div>';
		$this->_html .= '<label>' . $this->l('Title (is not displayed in front office)') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="internal_name" value="' . ($ObjAdvancedTopMenuColumnWrapClass ? $ObjAdvancedTopMenuColumnWrapClass->internal_name : '') . '" />
          </div>';
		$this->_html .= '<label>' . $this->l('Active') . '</label>
          <div class="margin-form"><label class="t" for="columnwrap_active_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_column" id="columnwrap_active_on" value="1"' . (! $ObjAdvancedTopMenuColumnWrapClass || ($ObjAdvancedTopMenuColumnWrapClass && $ObjAdvancedTopMenuColumnWrapClass->active) ? ' checked="checked"' : '') . ' />
            <label class="t" for="columnwrap_active_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="columnwrap_active_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_column" id="columnwrap_active_off" value="0" ' . ($ObjAdvancedTopMenuColumnWrapClass && ! $ObjAdvancedTopMenuColumnWrapClass->active ? 'checked="checked"' : '') . '/>
            <label class="t" for="columnwrap_active_off"> ' . $this->l('No') . '</label></div>';
        $this->_html .= '<label>' . $this->l('Active on mobile') . '</label>
          <div class="margin-form"><label class="t" for="columnwrap_active_mobile_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_mobile_column" id="columnwrap_active_mobile_on" value="1"' . (! $ObjAdvancedTopMenuColumnWrapClass || ($ObjAdvancedTopMenuColumnWrapClass && $ObjAdvancedTopMenuColumnWrapClass->active_mobile) ? ' checked="checked"' : '') . ' />
            <label class="t" for="columnwrap_active_mobile_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="columnwrap_active_mobile_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_mobile_column" id="columnwrap_active_mobile_off" value="0" ' . ($ObjAdvancedTopMenuColumnWrapClass && ! $ObjAdvancedTopMenuColumnWrapClass->active_mobile ? 'checked="checked"' : '') . '/>
            <label class="t" for="columnwrap_active_mobile_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<label>' . $this->l('Privacy Options') . '</label>
        <div class="margin-form"><select name="privacy">
          <option value="0" ' . ($ObjAdvancedTopMenuColumnWrapClass && $ObjAdvancedTopMenuColumnWrapClass->privacy == 0 ? 'selected="selected"' : '') . '>' . $this->l('For all') . '</option>
          <option value="1" ' . ($ObjAdvancedTopMenuColumnWrapClass && $ObjAdvancedTopMenuColumnWrapClass->privacy == 1 ? 'selected="selected"' : '') . '>' . $this->l('Only for visitors') . '</option>
          <option value="2" ' . ($ObjAdvancedTopMenuColumnWrapClass && $ObjAdvancedTopMenuColumnWrapClass->privacy == 2 ? 'selected="selected"' : '') . '>' . $this->l('Only for registered users') . '</option>
       </select></div>';
		$this->_html .= '<h3>' . $this->l('Style settings') . ' <small>(' . $this->l('if empty, the global styles are used') . ')</small></h3>';
		$this->_html .= '<label>' . $this->l('Width') . '</label>
         <div class="margin-form">
        <input type="text" name="width" value="' . ($ObjAdvancedTopMenuColumnWrapClass ? $ObjAdvancedTopMenuColumnWrapClass->width : '') . '" size="20" />
      </div>';
		$color1 = false;
		$color2 = false;
		$val = false;
		if ($ObjAdvancedTopMenuColumnWrapClass && $ObjAdvancedTopMenuColumnWrapClass->bg_color) {
			$val = explode($this->gradient_separator, $ObjAdvancedTopMenuColumnWrapClass->bg_color);
			if (isset($val [1])) {
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($val [1], ENT_COMPAT, 'UTF-8');
			}
			else
				$color1 = htmlentities($val [0], ENT_COMPAT, 'UTF-8');
		}
		$this->_html .= '<label>' . $this->l('Background color') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="bg_color[0]" id="bg_color_0" class="pm_colorpicker" value="' . (! $color1 ? '' : $color1) . '" size="20" />
          &nbsp; <span ' . (isset($color2) && $color2 ? '' : 'style="display:none"') . ' id="bg_color_gradient"><input size="20" type="text" class="pm_colorpicker" name="bg_color[1]" id="bg_color_1" value="' . (! isset($color2) || ! $color2 ? '' : $color2) . '" size="20" /></span>
          &nbsp; <input type="checkbox" name="bg_color_gradient" value="1" ' . (isset($color2) && $color2 ? 'checked=checked' : '') . ' /> &nbsp; ' . $this->l('Make a gradient') . '
          </div>';
		$this->_html .= '<script type="text/javascript">$("input[name=bg_color_gradient]").click(function() {showSpanIfChecked($(this),"#bg_color_gradient");});</script>';
		$this->_html .= '<label>' . $this->l('Text color group') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="txt_color_column" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuColumnWrapClass ? $ObjAdvancedTopMenuColumnWrapClass->txt_color_column : '') . '" />
         </div>';
		$this->_html .= '<label>' . $this->l('Text color group over') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="txt_color_column_over" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuColumnWrapClass ? $ObjAdvancedTopMenuColumnWrapClass->txt_color_column_over : '') . '" />
        </div>';
		$this->_html .= '<label>' . $this->l('Text color items') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="txt_color_element" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuColumnWrapClass ? $ObjAdvancedTopMenuColumnWrapClass->txt_color_element : '') . '" />
         </div>';
		$this->_html .= '<label>' . $this->l('Text color items over') . '</label>
          <div class="margin-form">
          <input size="20" type="text" name="txt_color_element_over" class="pm_colorpicker" value="' . ($ObjAdvancedTopMenuColumnWrapClass ? $ObjAdvancedTopMenuColumnWrapClass->txt_color_element_over : '') . '" />
        </div>';
		$hasAdditionnalText = false;
		foreach ($this->languages as $language) {
			if (isset($ObjAdvancedTopMenuColumnWrapClass->value_over[$language['id_lang']]) && !empty($ObjAdvancedTopMenuColumnWrapClass->value_over[$language['id_lang']]) || isset($ObjAdvancedTopMenuColumnWrapClass->value_under[$language['id_lang']]) && !empty($ObjAdvancedTopMenuColumnWrapClass->value_under[$language['id_lang']])) {
				$hasAdditionnalText = true;
				break;
			}
		}
		$this->_html .= '<label>' . $this->l('Show additionnal text settings') . '</label>
          <div class="margin-form"><label class="t" for="tinymce_container_toggle_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="tinymce_container_toggle_menu" id="tinymce_container_toggle_on" value="1"' . ($hasAdditionnalText ? ' checked="checked"' : '') . ' />
            <label class="t" for="tinymce_container_toggle_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="tinymce_container_toggle_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="tinymce_container_toggle_menu" id="tinymce_container_toggle_off" value="0" ' . (!$hasAdditionnalText ? 'checked="checked"' : '') . '/>
            <label class="t" for="tinymce_container_toggle_off"> ' . $this->l('No') . '</label></div>';
        $this->_html .= '<div class="tinymce_container"' . ($hasAdditionnalText ? ' style="display: block"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Text displayed above column') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="columnwrap_value_over_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <textarea class="rte" cols="100" rows="10" name="value_over_' . $language ['id_lang'] . '">' . ($ObjAdvancedTopMenuColumnWrapClass && isset($ObjAdvancedTopMenuColumnWrapClass->value_over[$language['id_lang']]) ? htmlentities(stripslashes($ObjAdvancedTopMenuColumnWrapClass->value_over [$language ['id_lang']]), ENT_COMPAT, 'UTF-8') : '') . '</textarea>
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'columnwrap_value_over', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<label>' . $this->l('Text displayed below column') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="columnwrap_value_under_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
          <textarea class="rte" cols="100" rows="10"  name="value_under_' . $language ['id_lang'] . '">' . ($ObjAdvancedTopMenuColumnWrapClass && isset($ObjAdvancedTopMenuColumnWrapClass->value_under[$language['id_lang']]) ? htmlentities(stripslashes($ObjAdvancedTopMenuColumnWrapClass->value_under [$language ['id_lang']]), ENT_COMPAT, 'UTF-8') : '') . '</textarea>
               </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'columnwrap_value_under', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '</div><!-- .tinymce_container -->';
		$this->_html .= '<center>
            <input type="submit" value="' . $this->l('   Save   ') . '" name="submitColumnWrap" class="button" />
          </center>
      </form></div><br /><br />';
	}
	private function displayColumnForm($menus, $cms, $categories, $manufacturer, $supplier, $ObjAdvancedTopMenuColumnClass, $ObjAdvancedTopMenuProductColumnClass) {
		$ids_lang = 'columnname¤columnlink¤column_value_over¤column_value_under¤columnimage¤columnimagelegend';
		$this->_html .= '<script type="text/javascript">id_language = ' . intval($this->defaultLanguage) . ';</script>';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			$this->_html .= '<div class="warning warn clear">' . $this->l('Configuration can not be different by shop. It will be applied to all shop.'). '</div>';
		}
		$imgIconColumnDirIsWritable = is_writable(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/column_icons');
		$haveDepend = false;
		if ($ObjAdvancedTopMenuColumnClass)
			$haveDepend = AdvancedTopMenuColumnClass::columnHaveDepend($ObjAdvancedTopMenuColumnClass->id);
		$this->_html .= '<form action="' . $this->base_config_url . '" id="formColumn_' . $this->name . '" name="form_' . $this->name . '" method="post" enctype="multipart/form-data" class="width3">
    <div id="blocColumnForm">
        ' . ($ObjAdvancedTopMenuColumnClass ? '<input type="hidden" name="id_column" value="' . intval($ObjAdvancedTopMenuColumnClass->id) . '" /><br /><a href="' . $this->base_config_url . '"><img src="../img/admin/arrow2.gif" />' . $this->l('Back') . '</a><br class="clear" /><br />' : '');
		$this->_html .= '<h3>' . $this->l('General settings') . '</h3>';
        $this->_html .= '<label>' . $this->l('Parent tab') . '</label>
       <div class="margin-form"><select name="id_menu" id="id_menu_select2">';
		if (self::_isFilledArray($menus) && sizeof($menus) > 1)
			$this->_html .= '<option value="">-- ' . $this->l('Choose') . ' --</option>';
		foreach ( $menus as $menu ) {
			$this->_html .= '<option value="' . $menu ['id_menu'] . '" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->id_menu == $menu ['id_menu'] ? 'selected="selected"' : '') . '>' . $this->getAdminOutputNameValue($menu, false) . '</option>';
		}
		$this->_html .= ' </select></div>';
		$this->_html .= '<label>' . $this->l('Parent column') . '</label>
		<div class="margin-form" id="columnWrap_select">';
		if (Validate::isLoadedObject($ObjAdvancedTopMenuColumnClass))
			$this->_html .= $this->_getSelectColumnsWrap($ObjAdvancedTopMenuColumnClass->id_menu, $ObjAdvancedTopMenuColumnClass->id_wrap);
		else if (!Validate::isLoadedObject($ObjAdvancedTopMenuColumnClass) && self::_isFilledArray($menus) && sizeof($menus) == 1)
			$this->_html .= $this->_getSelectColumnsWrap($menus[0]['id_menu']);
		else {
			$this->_html .= '<div class="error inline-alert"><strong><u>' . $this->l('Please select a parent tab!') . '</u></strong></div>';
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$this->_html .= '<script>$(document).ready(function() { $(\'input[name="submitColumn"]\').attr(\'disabled\', \'disabled\').prop(\'disabled\', true); });</script>';
			} else {
				$this->_html .= '<script>$(document).ready(function() { $(\'input[name="submitColumn"]\').attr(\'disabled\', \'disabled\'); });</script>';
			}
		}
		$this->_html .= '</div>';
		$this->_html .= '
		<script type="text/javascript">
		$(document).ready(function() {
			$("#id_menu_select2").change(function() {
				showColumnWrapSelect($(this));
			});
		});
		</script>';
		$this->_html .= '<label>' . $this->l('Type') . '</label>
       <div class="margin-form"><select name="type" id="type_column">
          <option value="">-- ' . $this->l('Choose') . ' --</option>
          <option value="1" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 1 ? 'selected="selected"' : '') . '>' . $this->l('CMS') . '</option>
          <option value="2" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 2 ? 'selected="selected"' : '') . '>' . $this->l('Link') . '</option>
          <option value="3" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 3 ? 'selected="selected"' : '') . '>' . $this->l('Category') . '</option>
           <option value="4" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 4 ? 'selected="selected"' : '') . '>' . $this->l('Manufacturer') . '</option>
          <option value="5" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 5 ? 'selected="selected"' : '') . '>' . $this->l('Supplier') . '</option>
          <option value="6" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 6 ? 'selected="selected"' : '') . '>' . $this->l('Search') . '</option>
           <option value="7" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 7 ? 'selected="selected"' : '') . '>' . $this->l('Only image or icon') . '</option>
           ' . (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? '<option value="8" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 8 ? 'selected="selected"' : '') . '>' . $this->l('Product') . '</option>' : '') . '
        </select></div>';
		$this->_html .= '<script type="text/javascript">$(document).ready(function() { $("#type_column").change(function() {showMenuType($(this),"column");}); });</script>';
		$this->_html .= '<div class="add_category menu_element" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 3 ? '' : 'style="display:none;"') . '>';
		$this->displayCategoriesSelect($categories, ($ObjAdvancedTopMenuColumnClass ? $ObjAdvancedTopMenuColumnClass->id_category : 0));
		$this->_html .= '<label>' . $this->l('Include Subcategories') . '</label>
          <div class="margin-form"><label class="t" for="column_subcats_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="include_subs" id="column_subcats_on" value="1" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 3 && $haveDepend ? ' checked=checked' : '') . ' />
            <label class="t" for="column_subcats_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="column_subcats_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="include_subs" id="column_subcats_off" value="0" ' . (! $ObjAdvancedTopMenuColumnClass || ($ObjAdvancedTopMenuColumnClass->type == 3 && ! $haveDepend) ? ' checked=checked' : '') . ' />
            <label class="t" for="column_subcats_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_cms menu_element"   ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 1 ? '' : 'style="display:none;"') . '>';
		$this->displayCmsSelect($cms, ($ObjAdvancedTopMenuColumnClass ? $ObjAdvancedTopMenuColumnClass->id_cms : 0));
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_manufacturer menu_element"  ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 4 ? '' : 'style="display:none;"') . '>';
		$this->_html .= '<label>' . $this->l('All manufacturers') . '</label>
          <div class="margin-form"><label class="t" for="column_submanu_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="include_subs_manu" id="column_submanu_on" value="1" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 4 && $haveDepend ? ' checked=checked' : '') . ' />
            <label class="t" for="column_submanu_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="column_submanu_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="include_subs_manu" id="column_submanu_off" value="0" ' . (! $ObjAdvancedTopMenuColumnClass || ($ObjAdvancedTopMenuColumnClass->type == 4 && ! $haveDepend) ? ' checked=checked' : '') . ' />
            <label class="t" for="column_submanu_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<div ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 4 && $haveDepend ? ' style="display:none"' : '') . '>';
		$this->_html .= '<script type="text/javascript">$("#column_submanu_off,#column_submanu_on").click(function() {hideNextIfTrue($(this));});</script>';
		$this->displayManufacturerSelect($manufacturer, ($ObjAdvancedTopMenuColumnClass ? $ObjAdvancedTopMenuColumnClass->id_manufacturer : 0));
		$this->_html .= '</div>';
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_supplier menu_element"  ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 5 ? '' : 'style="display:none;"') . '>';
		$this->_html .= '<label>' . $this->l('All suppliers') . '</label>
          <div class="margin-form"><label class="t" for="column_subsuppl_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="include_subs_suppl" id="column_subsuppl_on" value="1" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 5 && $haveDepend ? ' checked=checked' : '') . ' />
            <label class="t" for="column_subsuppl_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="column_subsuppl_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="include_subs_suppl" id="column_subsuppl_off" value="0" ' . (! $ObjAdvancedTopMenuColumnClass || ($ObjAdvancedTopMenuColumnClass->type == 5 && ! $haveDepend) ? ' checked=checked' : '') . ' />
            <label class="t" for="column_subsuppl_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<script type="text/javascript">$("#column_subsuppl_on,#column_subsuppl_off").click(function() {hideNextIfTrue($(this));});</script>';
		$this->_html .= '<div ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 5 && $haveDepend ? ' style="display:none"' : '') . '>';
		$this->displaySupplierSelect($supplier, ($ObjAdvancedTopMenuColumnClass ? $ObjAdvancedTopMenuColumnClass->id_supplier : 0));
		$this->_html .= ' </div>';
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_link menu_element"  ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type != 2 ? 'style="display:none;"' : '') . '>
          <label>' . $this->l('Link') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="columnlink_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input size="20" type="text" name="link_' . $language ['id_lang'] . '" class="adtmInputLink" value="' . ($ObjAdvancedTopMenuColumnClass && isset($ObjAdvancedTopMenuColumnClass->link[$language['id_lang']]) ? $ObjAdvancedTopMenuColumnClass->link [$language ['id_lang']] : '') . '" />
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'columnlink', true);
		$this->_html .= '<div class="clear"></div></div></div>';
		$this->_html .= '<div class="prevent_click menu_element"  ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 8 ? 'style="display:none;"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Prevent click on link') . '</label>
          <div class="margin-form">
          <input type="checkbox" name="clickable" id="group_clickable" value="1" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->link [$this->defaultLanguage] ? ' checked=checked' : '') . '  />
          <small>' . $this->l('add a # in the link field, do not remove') . '</small>
          </div>';
		$this->_html .= '<script type="text/javascript">$("#group_clickable").click(function() {setUnclickable($(this));});</script>';
		$this->_html .= '</div>';
		$this->displayTargetSelect(($ObjAdvancedTopMenuColumnClass ? $ObjAdvancedTopMenuColumnClass->target : 0));
		if ($ObjAdvancedTopMenuColumnClass && in_array($ObjAdvancedTopMenuColumnClass->type, $this->rebuildable_type)) {
			$this->_html .= '<label>' . $this->l('Rebuild tree') . '</label>
          <div class="margin-form"><label class="t" for="rebuild_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="rebuild" id="rebuild_on" value="1" />
            <label class="t" for="rebuild_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="rebuild_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="rebuild" id="rebuild_off" value="0" checked=checked />
            <label class="t" for="rebuild_off"> ' . $this->l('No') . '</label><br />' . $this->l('Caution, this may change the appearance of your menu !') . '</div>';
		}
		$this->_html .= '<div class="add_title menu_element"  ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 7 ? 'style="display:none;"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Title') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="columnname_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input size="20" type="text" name="name_' . $language ['id_lang'] . '" value="' . ($ObjAdvancedTopMenuColumnClass && isset($ObjAdvancedTopMenuColumnClass->name[$language ['id_lang']]) ? $ObjAdvancedTopMenuColumnClass->name [$language ['id_lang']] : '') . '" />
                <br />'. $this->l('(if filled, will replace original title)') .'
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'columnname', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '</div>';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$this->_html .= '<div class="add_product_settings menu_element"  ' . (!$ObjAdvancedTopMenuColumnClass || $ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type != 8 ? 'style="display:none;"' : '') . '>';
			$currentProductName = 'N/A';
			if ($ObjAdvancedTopMenuProductColumnClass && isset($ObjAdvancedTopMenuProductColumnClass->id_product) && $ObjAdvancedTopMenuProductColumnClass->id_product) {
				$productObj = new Product($ObjAdvancedTopMenuProductColumnClass->id_product, false, $this->_cookie->id_lang);
				if (Validate::isLoadedObject($productObj)) {
					$currentProductName = $productObj->name;
				}
			}
			$this->_html .= '
			<label>' . $this->l('Product') . '</label>
			<div class="margin-form" style="padding-left: 0">
				<input size="40" type="text" id="id_product_search" value="" placeholder="' . $this->l('Enter your product name/ref here') . '" />&nbsp;
				<p class="adtm_current_product_name">' . $this->l('Current product:') . ' <span id="current_product_name">'. $currentProductName .' (ID: '. ($ObjAdvancedTopMenuProductColumnClass && isset($ObjAdvancedTopMenuProductColumnClass->id_product) ? intval($ObjAdvancedTopMenuProductColumnClass->id_product) : 'N/A') .')</span></p>
				<input size="20" type="hidden" name="id_product" value="' . ($ObjAdvancedTopMenuProductColumnClass && isset($ObjAdvancedTopMenuProductColumnClass->id_product) ? intval($ObjAdvancedTopMenuProductColumnClass->id_product) : '') . '" />
			</div>
			';
			$this->_html .= '<label>' . $this->l('Show title') . '</label>
			<div class="margin-form"><label class="t" for="show_title_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
			<input type="radio" name="show_title" id="show_title_on" value="1"' . (! $ObjAdvancedTopMenuProductColumnClass || ($ObjAdvancedTopMenuProductColumnClass && $ObjAdvancedTopMenuProductColumnClass->show_title) ? ' checked="checked"' : '') . ' />
			<label class="t" for="show_title_on"> ' . $this->l('Yes') . '</label>
			<label class="t" for="show_title_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
			<input type="radio" name="show_title" id="show_title_off" value="0" ' . ($ObjAdvancedTopMenuProductColumnClass && ! $ObjAdvancedTopMenuProductColumnClass->show_title ? 'checked="checked"' : '') . '/>
			<label class="t" for="show_title_off"> ' . $this->l('No') . '</label></div>';
			$this->_html .= '<label>' . $this->l('Show price') . '</label>
			<div class="margin-form"><label class="t" for="show_price_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
			<input type="radio" name="show_price" id="show_price_on" value="1"' . (! $ObjAdvancedTopMenuProductColumnClass || ($ObjAdvancedTopMenuProductColumnClass && $ObjAdvancedTopMenuProductColumnClass->show_price) ? ' checked="checked"' : '') . ' />
			<label class="t" for="show_price_on"> ' . $this->l('Yes') . '</label>
			<label class="t" for="show_price_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
			<input type="radio" name="show_price" id="show_price_off" value="0" ' . ($ObjAdvancedTopMenuProductColumnClass && ! $ObjAdvancedTopMenuProductColumnClass->show_price ? 'checked="checked"' : '') . '/>
			<label class="t" for="show_price_off"> ' . $this->l('No') . '</label></div>';
			$this->_html .= '<label>' . $this->l('Show "Add to cart" button') . '</label>
			<div class="margin-form"><label class="t" for="show_add_to_cart_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
			<input type="radio" name="show_add_to_cart" id="show_add_to_cart_on" value="1"' . (! $ObjAdvancedTopMenuProductColumnClass || ($ObjAdvancedTopMenuProductColumnClass && $ObjAdvancedTopMenuProductColumnClass->show_add_to_cart) ? ' checked="checked"' : '') . ' />
			<label class="t" for="show_add_to_cart_on"> ' . $this->l('Yes') . '</label>
			<label class="t" for="show_add_to_cart_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
			<input type="radio" name="show_add_to_cart" id="show_add_to_cart_off" value="0" ' . ($ObjAdvancedTopMenuProductColumnClass && ! $ObjAdvancedTopMenuProductColumnClass->show_add_to_cart ? 'checked="checked"' : '') . '/>
			<label class="t" for="show_add_to_cart_off"> ' . $this->l('No') . '</label></div>';
			$this->_html .= '<label>' . $this->l('Show "More" button') . '</label>
			<div class="margin-form"><label class="t" for="show_more_info_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
			<input type="radio" name="show_more_info" id="show_more_info_on" value="1"' . (! $ObjAdvancedTopMenuProductColumnClass || ($ObjAdvancedTopMenuProductColumnClass && $ObjAdvancedTopMenuProductColumnClass->show_more_info) ? ' checked="checked"' : '') . ' />
			<label class="t" for="show_more_info_on"> ' . $this->l('Yes') . '</label>
			<label class="t" for="show_more_info_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
			<input type="radio" name="show_more_info" id="show_more_info_off" value="0" ' . ($ObjAdvancedTopMenuProductColumnClass && ! $ObjAdvancedTopMenuProductColumnClass->show_more_info ? 'checked="checked"' : '') . '/>
			<label class="t" for="show_more_info_off"> ' . $this->l('No') . '</label></div>';
			if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
				$this->_html .= '<label>' . $this->l('Show "Quick view" button') . '</label>
				<div class="margin-form"><label class="t" for="show_quick_view_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
				<input type="radio" name="show_quick_view" id="show_quick_view_on" value="1"' . (! $ObjAdvancedTopMenuProductColumnClass || ($ObjAdvancedTopMenuProductColumnClass && $ObjAdvancedTopMenuProductColumnClass->show_quick_view) ? ' checked="checked"' : '') . ' />
				<label class="t" for="show_quick_view_on"> ' . $this->l('Yes') . '</label>
				<label class="t" for="show_quick_view_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
				<input type="radio" name="show_quick_view" id="show_quick_view_off" value="0" ' . ($ObjAdvancedTopMenuProductColumnClass && ! $ObjAdvancedTopMenuProductColumnClass->show_quick_view ? 'checked="checked"' : '') . '/>
				<label class="t" for="show_quick_view_off"> ' . $this->l('No') . '</label></div>';
			}
			$this->_html .= '<label>' . $this->l('Image format') . '</label>
			<div class="margin-form"><select name="p_image_type">
			<!-- <option value="">-- ' . $this->l('Choose') . ' --</option> -->';
			foreach ($this->getProductsImagesTypes() as $image_type_key => $image_type) {
				$this->_html .= '<option value="' . $image_type_key . '" ' . ($ObjAdvancedTopMenuProductColumnClass && $ObjAdvancedTopMenuProductColumnClass->p_image_type == $image_type_key ? 'selected="selected"' : '') . '>' . $image_type . '</option>';
			}
			$this->_html .= ' </select></div>';
			$this->_html .= '</div>';
		}
		$this->_html .= '<label>' . $this->l('Active') . '</label>
          <div class="margin-form"><label class="t" for="column_active_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_column" id="column_active_on" value="1"' . (! $ObjAdvancedTopMenuColumnClass || ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->active) ? ' checked="checked"' : '') . ' />
            <label class="t" for="active_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="column_active_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_column" id="column_active_off" value="0" ' . ($ObjAdvancedTopMenuColumnClass && ! $ObjAdvancedTopMenuColumnClass->active ? 'checked="checked"' : '') . '/>
            <label class="t" for="active_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<label>' . $this->l('Active on mobile') . '</label>
          <div class="margin-form"><label class="t" for="column_active_mobile_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_mobile_column" id="column_active_mobile_on" value="1"' . (! $ObjAdvancedTopMenuColumnClass || ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->active_mobile) ? ' checked="checked"' : '') . ' />
            <label class="t" for="active_mobile_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="column_active_mobile_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_mobile_column" id="column_active_mobile_off" value="0" ' . ($ObjAdvancedTopMenuColumnClass && ! $ObjAdvancedTopMenuColumnClass->active_mobile ? 'checked="checked"' : '') . '/>
            <label class="t" for="active_mobile_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<label>' . $this->l('Privacy Options') . '</label>
        <div class="margin-form"><select name="privacy">
          <option value="0" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->privacy == 0 ? 'selected="selected"' : '') . '>' . $this->l('For all') . '</option>
          <option value="1" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->privacy == 1 ? 'selected="selected"' : '') . '>' . $this->l('Only for visitors') . '</option>
          <option value="2" ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->privacy == 2 ? 'selected="selected"' : '') . '>' . $this->l('Only for registered users') . '</option>
       </select></div>';
		if (! $imgIconColumnDirIsWritable)
			$this->_html .= '<div class="warning warn clear">' . $this->l('To upload an icon, please assign CHMOD 777 to the directory:') . ' ' . _PS_ROOT_DIR_ . '/modules/' . $this->name . '/column_icons' . '</div>';
		$this->_html .= '<div class="add_image menu_element"  ' . ($ObjAdvancedTopMenuColumnClass && $ObjAdvancedTopMenuColumnClass->type == 8 ? 'style="display:none;"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Icon or image') . '</label>';
		$this->_html .= '<div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
			<div id="columnimage_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
				<input type="file" name="icon_' . $language ['id_lang'] . '" size="20" ' . (! $imgIconColumnDirIsWritable ? 'disabled=disabled' : '') . ' />
				' . ($ObjAdvancedTopMenuColumnClass && isset($ObjAdvancedTopMenuColumnClass->have_icon[$language['id_lang']]) && $ObjAdvancedTopMenuColumnClass->have_icon[$language['id_lang']] ? '<input type="hidden" name="have_icon_' . $language ['id_lang'] . '" value="' . intval($ObjAdvancedTopMenuColumnClass->have_icon[$language['id_lang']]) . '" /><br />
				<img src="' . $this->_path . 'column_icons/' . $ObjAdvancedTopMenuColumnClass->id . '-' . $language['iso_code'] . '.' . (isset($ObjAdvancedTopMenuColumnClass->image_type[$language['id_lang']]) ? $ObjAdvancedTopMenuColumnClass->image_type[$language['id_lang']] : 'jpg') . '?' . uniqid() . '" /><br />
				<input type="checkbox" name="unlink_icon_' . $language ['id_lang'] . '" value="1" /> &nbsp; ' . $this->l('Delete this image') : '') . '
				<small>(' . $this->l('gif, jpg, png') . ')</small>
			</div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'columnimage', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<label>' . $this->l('Image legend') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="columnimagelegend_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input type="text" maxlength="255" name="image_legend_' . $language ['id_lang'] . '" value="' . ($ObjAdvancedTopMenuColumnClass && isset($ObjAdvancedTopMenuColumnClass->image_legend[$language['id_lang']]) ? $ObjAdvancedTopMenuColumnClass->image_legend[$language['id_lang']] : '') . '" />
                <br />'. $this->l('(if empty, title will be used)') .'
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'columnimagelegend', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '</div>';
		$hasAdditionnalText = false;
		foreach ($this->languages as $language) {
			if (isset($ObjAdvancedTopMenuColumnClass->value_over[$language['id_lang']]) && !empty($ObjAdvancedTopMenuColumnClass->value_over[$language['id_lang']]) || isset($ObjAdvancedTopMenuColumnClass->value_under[$language['id_lang']]) && !empty($ObjAdvancedTopMenuColumnClass->value_under[$language['id_lang']])) {
				$hasAdditionnalText = true;
				break;
			}
		}
		$this->_html .= '<label>' . $this->l('Show additionnal text settings') . '</label>
          <div class="margin-form"><label class="t" for="tinymce_container_toggle_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="tinymce_container_toggle_menu" id="tinymce_container_toggle_on" value="1"' . ($hasAdditionnalText ? ' checked="checked"' : '') . ' />
            <label class="t" for="tinymce_container_toggle_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="tinymce_container_toggle_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="tinymce_container_toggle_menu" id="tinymce_container_toggle_off" value="0" ' . (!$hasAdditionnalText ? 'checked="checked"' : '') . '/>
            <label class="t" for="tinymce_container_toggle_off"> ' . $this->l('No') . '</label></div>';
        $this->_html .= '<div class="tinymce_container"' . ($hasAdditionnalText ? ' style="display: block"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Text displayed above group') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="column_value_over_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <textarea class="rte" cols="100" rows="10" name="value_over_' . $language ['id_lang'] . '">' . ($ObjAdvancedTopMenuColumnClass && isset($ObjAdvancedTopMenuColumnClass->value_over[$language['id_lang']]) ? htmlentities(stripslashes($ObjAdvancedTopMenuColumnClass->value_over [$language ['id_lang']]), ENT_COMPAT, 'UTF-8') : '') . '</textarea>
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'column_value_over', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<label>' . $this->l('Text displayed below group') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="column_value_under_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
          <textarea class="rte" cols="100" rows="10"  name="value_under_' . $language ['id_lang'] . '">' . ($ObjAdvancedTopMenuColumnClass && isset($ObjAdvancedTopMenuColumnClass->value_under[$language['id_lang']]) ? htmlentities(stripslashes($ObjAdvancedTopMenuColumnClass->value_under [$language ['id_lang']]), ENT_COMPAT, 'UTF-8') : '') . '</textarea>
               </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'column_value_under', true);
		$this->_html .= '<div class="clear"></div></div>';
   		$this->_html .= '</div><!-- .tinymce_container -->';
		$this->_html .= '<center>
            <input type="submit" value="' . $this->l('   Save   ') . '" name="submitColumn" class="button" />
          </center>
      </form></div><br /><br />';
		if ($ObjAdvancedTopMenuColumnClass)
			$this->_html .= '<script type="text/javascript">$(function(){showMenuType($("#type_column"),"column")});</script>';
	}
	private function displayElementForm($menus, $columns, $cms, $categories, $manufacturer, $supplier, $ObjAdvancedTopMenuElementClass) {
		$imgIconElementDirIsWritable = is_writable(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/element_icons');
		$ids_lang = 'elementname¤elementlink¤elementimage¤elementimagelegend';
		$this->_html .= '<script type="text/javascript">id_language = ' . intval($this->defaultLanguage) . ';</script>';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive()) {
			$this->_html .= '<div class="warning warn clear">' . $this->l('Configuration can not be different by shop. It will be applied to all shop.'). '</div>';
		}
		$this->_html .= '<form action="' . $this->base_config_url . '" id="formElement_' . $this->name . '" name="formElement_' . $this->name . '" method="post" enctype="multipart/form-data" class="width3">
    <div id="blocElementForm">
        ' . ($ObjAdvancedTopMenuElementClass ? '<input type="hidden" name="id_element" value="' . intval($ObjAdvancedTopMenuElementClass->id) . '" /><br /><a href="' . $this->base_config_url . '"><img src="../img/admin/arrow2.gif" />' . $this->l('Back') . '</a><br class="clear" /><br />' : '');
		$this->_html .= '<h3>' . $this->l('General settings') . '</h3>';
     	$this->_html .= '<label>' . $this->l('Parent tab') . '</label>
       <div class="margin-form"><select name="id_menu" id="id_menu_select">';
		if (self::_isFilledArray($menus) && sizeof($menus) > 1)
			$this->_html .= '<option value="">-- ' . $this->l('Choose') . ' --</option>';
		foreach ( $menus as $menu ) {
			$this->_html .= '<option value="' . $menu ['id_menu'] . '" ' . ($ObjAdvancedTopMenuElementClass && AdvancedTopMenuColumnClass::getIdMenuByIdColumn($ObjAdvancedTopMenuElementClass->id_column) == $menu ['id_menu'] ? 'selected="selected"' : '') . '>' . $this->getAdminOutputNameValue($menu, false) . '</option>';
		}
		$this->_html .= ' </select></div>';
		$this->_html .= '<label>' . $this->l('Parent group') . '</label>
       <div class="margin-form" id="column_select">';
		if (Validate::isLoadedObject($ObjAdvancedTopMenuElementClass))
			$this->_html .= $this->_getSelectColumns(AdvancedTopMenuColumnClass::getIdMenuByIdColumn($ObjAdvancedTopMenuElementClass->id_column), $ObjAdvancedTopMenuElementClass->id_column);
		else if (!Validate::isLoadedObject($ObjAdvancedTopMenuElementClass) && self::_isFilledArray($menus) && sizeof($menus) == 1)
			$this->_html .= $this->_getSelectColumns($menus[0]['id_menu']);
		else {
			$this->_html .= '<div class="error inline-alert"><strong><u>' . $this->l('Please select a parent tab!') . '</u></strong></div>';
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$this->_html .= '<script>$(document).ready(function() { $(\'input[name="submitElement"]\').attr(\'disabled\', \'disabled\').prop(\'disabled\', true); });</script>';
			} else {
				$this->_html .= '<script>$(document).ready(function() { $(\'input[name="submitElement"]\').attr(\'disabled\', \'disabled\'); });</script>';
			}
		}
		$this->_html .= '</div>';
		$this->_html .= '
		<script type="text/javascript">
			$(document).ready(function() {
				$("#id_menu_select").change(function() {
					showColumnSelect($(this));
				});
			});
		</script>';
		$this->_html .= '<label>' . $this->l('Type') . '</label>
       <div class="margin-form"><select name="type" id="type_element">
          <option value="">-- ' . $this->l('Choose') . ' --</option>
          <option value="1" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 1 ? 'selected="selected"' : '') . '>' . $this->l('CMS') . '</option>
          <option value="2" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 2 ? 'selected="selected"' : '') . '>' . $this->l('Link') . '</option>
          <option value="3" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 3 ? 'selected="selected"' : '') . '>' . $this->l('Category') . '</option>
           <option value="4" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 4 ? 'selected="selected"' : '') . '>' . $this->l('Manufacturer') . '</option>
          <option value="5" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 5 ? 'selected="selected"' : '') . '>' . $this->l('Supplier') . '</option>
          <option value="6" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 6 ? 'selected="selected"' : '') . '>' . $this->l('Search') . '</option>
           <option value="7" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 7 ? 'selected="selected"' : '') . '>' . $this->l('Only image or icon') . '</option>
       </select></div>';
		$this->_html .= '<script type="text/javascript">$(document).ready(function() { $("#type_element").change(function() {showMenuType($(this),"element");}); });</script>';
		$this->_html .= '<div class="add_category menu_element" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 3 ? '' : 'style="display:none;"') . '>';
		$this->displayCategoriesSelect($categories, ($ObjAdvancedTopMenuElementClass ? $ObjAdvancedTopMenuElementClass->id_category : 0));
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_cms menu_element"   ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 1 ? '' : 'style="display:none;"') . '>';
		$this->displayCmsSelect($cms, ($ObjAdvancedTopMenuElementClass ? $ObjAdvancedTopMenuElementClass->id_cms : 0));
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_manufacturer menu_element"  ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 4 ? '' : 'style="display:none;"') . '>';
		$this->displayManufacturerSelect($manufacturer, ($ObjAdvancedTopMenuElementClass ? $ObjAdvancedTopMenuElementClass->id_manufacturer : 0));
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_supplier menu_element"  ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 5 ? '' : 'style="display:none;"') . '>';
		$this->displaySupplierSelect($supplier, ($ObjAdvancedTopMenuElementClass ? $ObjAdvancedTopMenuElementClass->id_supplier : 0));
		$this->_html .= ' </div>';
		$this->_html .= '<div class="add_link menu_element"  ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type != 2 ? 'style="display:none;"' : '') . '>
          <label>' . $this->l('Link') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="elementlink_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input size="20" type="text" name="link_' . $language ['id_lang'] . '" class="adtmInputLink" value="' . ($ObjAdvancedTopMenuElementClass && isset($ObjAdvancedTopMenuElementClass->link[$language['id_lang']]) ? $ObjAdvancedTopMenuElementClass->link [$language ['id_lang']] : '') . '" />
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'elementlink', true);
		$this->_html .= '<div class="clear"></div></div></div>';
		$this->_html .= '<label>' . $this->l('Prevent click on link') . '</label>
          <div class="margin-form">
          <input type="checkbox" name="clickable" id="element_clickable" value="1" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->link [$this->defaultLanguage] ? ' checked=checked' : '') . '  />
          <small>' . $this->l('add a # in the link field, do not remove') . '</small>
          </div>';
		$this->_html .= '<script type="text/javascript">$("#element_clickable").click(function() {setUnclickable($(this));});</script>';
		$this->displayTargetSelect(($ObjAdvancedTopMenuElementClass ? $ObjAdvancedTopMenuElementClass->target : 0));
		$this->_html .= '<div class="add_title menu_element"  ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->type == 7 ? 'style="display:none;"' : '') . '>';
		$this->_html .= '<label>' . $this->l('Title') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="elementname_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input size="20" type="text" name="name_' . $language ['id_lang'] . '" value="' . ($ObjAdvancedTopMenuElementClass && isset($ObjAdvancedTopMenuElementClass->name[$language['id_lang']]) ? $ObjAdvancedTopMenuElementClass->name [$language ['id_lang']] : '') . '" />
                <br />'. $this->l('(if filled, will replace original title)') .'
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'elementname', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '</div>';
		$this->_html .= '<label>' . $this->l('Active') . '</label>
          <div class="margin-form"><label class="t" for="element_active_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_element" id="element_active_on" value="1"' . (! $ObjAdvancedTopMenuElementClass || ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->active) ? ' checked="checked"' : '') . ' />
            <label class="t" for="active_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="element_active_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_element" id="element_active_off" value="0" ' . ($ObjAdvancedTopMenuElementClass && ! $ObjAdvancedTopMenuElementClass->active ? 'checked="checked"' : '') . '/>
            <label class="t" for="active_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<label>' . $this->l('Active on mobile') . '</label>
          <div class="margin-form"><label class="t" for="element_active_mobile_on"><img src="../img/admin/enabled.gif" alt="' . $this->l('Yes') . '" title="' . $this->l('Yes') . '" /></label>
            <input type="radio" name="active_mobile_element" id="element_active_mobile_on" value="1"' . (! $ObjAdvancedTopMenuElementClass || ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->active_mobile) ? ' checked="checked"' : '') . ' />
            <label class="t" for="active_mobile_on"> ' . $this->l('Yes') . '</label>
            <label class="t" for="element_active_mobile_off"><img src="../img/admin/disabled.gif" alt="' . $this->l('No') . '" title="' . $this->l('No') . '" style="margin-left: 10px;" /></label>
            <input type="radio" name="active_mobile_element" id="element_active_mobile_off" value="0" ' . ($ObjAdvancedTopMenuElementClass && ! $ObjAdvancedTopMenuElementClass->active_mobile ? 'checked="checked"' : '') . '/>
            <label class="t" for="active_mobile_off"> ' . $this->l('No') . '</label></div>';
		$this->_html .= '<label>' . $this->l('Privacy Options') . '</label>
        <div class="margin-form"><select name="privacy">
          <option value="0" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->privacy == 0 ? 'selected="selected"' : '') . '>' . $this->l('For all') . '</option>
          <option value="1" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->privacy == 1 ? 'selected="selected"' : '') . '>' . $this->l('Only for visitors') . '</option>
          <option value="2" ' . ($ObjAdvancedTopMenuElementClass && $ObjAdvancedTopMenuElementClass->privacy == 2 ? 'selected="selected"' : '') . '>' . $this->l('Only for registered users') . '</option>
       </select></div>';
		if (! $imgIconElementDirIsWritable)
			$this->_html .= '<div class="warning warn clear">' . $this->l('To upload an icon, please assign CHMOD 777 to the directory:') . ' ' . _PS_ROOT_DIR_ . '/modules/' . $this->name . '/element_icons' . '</div>';
		$this->_html .= '<label>' . $this->l('Icon or image') . '</label>';
		$this->_html .= '<div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
			<div id="elementimage_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
				<input type="file" name="icon_' . $language ['id_lang'] . '" size="20" ' . (! $imgIconElementDirIsWritable ? 'disabled=disabled' : '') . ' />
				' . ($ObjAdvancedTopMenuElementClass && isset($ObjAdvancedTopMenuElementClass->have_icon[$language['id_lang']]) && $ObjAdvancedTopMenuElementClass->have_icon[$language['id_lang']] ? '<input type="hidden" name="have_icon_' . $language ['id_lang'] . '" value="' . intval($ObjAdvancedTopMenuElementClass->have_icon[$language['id_lang']]) . '" /><br />
				<img src="' . $this->_path . 'element_icons/' . $ObjAdvancedTopMenuElementClass->id . '-' . $language['iso_code'] . '.' . (isset($ObjAdvancedTopMenuElementClass->image_type[$language['id_lang']]) ? $ObjAdvancedTopMenuElementClass->image_type[$language['id_lang']] : 'jpg') . '?' . uniqid() . '" /><br />
				<input type="checkbox" name="unlink_icon_' . $language ['id_lang'] . '" value="1" /> &nbsp; ' . $this->l('Delete this image') : '') . '
				<small>(' . $this->l('gif, jpg, png') . ')</small>
			</div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'elementimage', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<label>' . $this->l('Image legend') . '</label>
          <div class="margin-form">';
		foreach ( $this->languages as $language ) {
			$this->_html .= '
              <div id="elementimagelegend_' . $language ['id_lang'] . '" style="display: ' . ($language ['id_lang'] == $this->defaultLanguage ? 'block' : 'none') . '; float: left;">
                <input type="text" maxlength="255" name="image_legend_' . $language ['id_lang'] . '" value="' . ($ObjAdvancedTopMenuElementClass && isset($ObjAdvancedTopMenuElementClass->image_legend[$language['id_lang']]) ? $ObjAdvancedTopMenuElementClass->image_legend[$language['id_lang']] : '') . '" />
                <br />'. $this->l('(if empty, title will be used)') .'
              </div>';
		}
		$this->_html .= $this->displayFlags($this->languages, $this->defaultLanguage, $ids_lang, 'elementimagelegend', true);
		$this->_html .= '<div class="clear"></div></div>';
		$this->_html .= '<center>
            <input type="submit" value="' . $this->l('   Save   ') . '" name="submitElement" class="button" />
          </center>
      </form></div><br /><br />';
		if ($ObjAdvancedTopMenuElementClass)
			$this->_html .= '<script type="text/javascript">$(function(){showMenuType($("#type_element"),"element")});</script>';
	}
	static public function hideCategoryPosition($name) {
		return preg_replace('/^[0-9]+\./', '', $name);
	}
	private function recurseCategory($categories, $current, $id_category = 1, $id_selected = 1) {
		$this->_html .= '<option value="' . $id_category . '"' . (($id_selected == $id_category) ? ' selected="selected"' : '') . '>' . str_repeat('&nbsp;', $current ['infos'] ['level_depth'] * 5) . (version_compare(_PS_VERSION_, '1.4.0.0', '<') ? self::hideCategoryPosition(stripslashes($current ['infos'] ['name'])) : stripslashes($current ['infos'] ['name'])) . '</option>';
		if (isset($categories [$id_category]))
			foreach ( $categories [$id_category] as $key => $row )
				$this->recurseCategory($categories, $categories [$id_category] [$key], $key, $id_selected);
	}
	private function _getChildrensCategories($categoryInformations, $selected, $levelDepth = false) {
		if (isset($categoryInformations['children']) && self::_isFilledArray($categoryInformations['children']))
			foreach ($categoryInformations['children'] as $idCategory=>$categoryInformations) {
				if (isset($categoryInformations['id']))
					$idCategory = (int)$categoryInformations['id'];
				$this->_html .= '<option value="' . $idCategory . '"' . (($selected == $idCategory) ? ' selected="selected"' : '') . '>' . str_repeat('&#150 ', ($levelDepth !== false ? $levelDepth : $categoryInformations['level_depth'])) . $categoryInformations['name'] . '</option>';
				$this->_getChildrensCategories($categoryInformations, $selected, ($levelDepth !== false ? $levelDepth + 1 : $levelDepth));
			}
	}
	private function _getNestedCategories($root_category = null, $id_lang = false) {
		$result = Db::getInstance()->executeS('
			SELECT c.*, cl.*
			FROM `'._DB_PREFIX_.'category` c
			'.Shop::addSqlAssociation('category', 'c').'
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
			RIGHT JOIN `'._DB_PREFIX_.'category` c2 ON c2.`id_category` = '.(int)$root_category.' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`
			WHERE `id_lang` = '.(int)$id_lang . ' AND c.`active` = 1
			ORDER BY c.`level_depth` ASC, category_shop.`position` ASC'
		);
		$categories = array();
		$buff = array();
		foreach ($result as $row) {
			$current = &$buff[$row['id_category']];
			$current = $row;
			if ($row['id_category'] == $root_category)
				$categories[$row['id_category']] = &$current;
			else
				$buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
		}
		return $categories;
	}
	private function displayCategoriesSelect($categories, $selected) {
		$this->_html .= '<label>' . $this->l('Category') . '</label>
      <div class="margin-form"><select name="id_category">
        <option value="">-- ' . $this->l('Choose') . ' --</option>';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
				$rootCategoryId = Category::getRootCategory()->id;
			else
				$rootCategoryId = Configuration::get('PS_ROOT_CATEGORY');
			foreach ($this->_getNestedCategories($rootCategoryId, $this->_cookie->id_lang) as $idCategory=>$categoryInformations) {
				if ($rootCategoryId != $idCategory)
					$this->_html .= '<option value="' . $idCategory . '"' . (($selected == $idCategory) ? ' selected="selected"' : '') . '>' . str_repeat('&#150 ', (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? $categoryInformations['level_depth'] - 1 : $categoryInformations['level_depth'])) . $categoryInformations['name'] . '</option>';
				$this->_getChildrensCategories($categoryInformations, $selected);
			}
		} else {
			$categories_bkup = $categories;
			$first_category = array_shift($categories_bkup);
			array_unshift($categories_bkup, $first_category);
			$first_category_final = array_shift($first_category);
			$this->recurseCategory($categories, $first_category_final, (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Category::getRootCategory()->id : 1), $selected);
		}
		$this->_html .= ' </select></div>';
	}
	private function displayTargetSelect($selected) {
		$this->_html .= '<label>' . $this->l('Target') . '</label>
      <div class="margin-form"><select name="target">';
		foreach ( $this->link_targets as $target => $value ) {
			$this->_html .= '<option value="' . $target . '" ' . ($selected === $target ? 'selected="selected"' : '') . '>' . $value . '</option>';
		}
		$this->_html .= ' </select></div>';
	}
	private function displayCmsSelect($cmss, $selected) {
		$this->_html .= '<label>' . $this->l('CMS') . '</label>
      <div class="margin-form"><select name="id_cms">
        <option value="">-- ' . $this->l('Choose') . ' --</option>';
		foreach ( $cmss as $cms ) {
			$this->_html .= '<option value="' . $cms ['id_cms'] . '" ' . ($selected == $cms ['id_cms'] ? 'selected="selected"' : '') . '>' . $cms ['meta_title'] . '</option>';
		}
		$this->_html .= ' </select></div>';
	}
	private function displayManufacturerSelect($manufacturers, $selected) {
		$this->_html .= '<label>' . $this->l('Manufacturer') . '</label>
      <div class="margin-form"><select name="id_manufacturer">
        <option value="">-- ' . $this->l('Choose') . ' --</option>';
		foreach ( $manufacturers as $manufacturer ) {
			$this->_html .= '<option value="' . $manufacturer ['id_manufacturer'] . '" ' . ($selected == $manufacturer ['id_manufacturer'] ? 'selected="selected"' : '') . '>' . $manufacturer ['name'] . '</option>';
		}
		$this->_html .= ' </select></div>';
	}
	private function displaySupplierSelect($suppliers, $selected) {
		$this->_html .= '<label>' . $this->l('Supplier') . '</label>
      <div class="margin-form"><select name="id_supplier">
        <option value="">-- ' . $this->l('Choose') . ' --</option>';
		foreach ( $suppliers as $supplier ) {
			$this->_html .= '<option value="' . $supplier ['id_supplier'] . '" ' . ($selected == $supplier ['id_supplier'] ? 'selected="selected"' : '') . '>' . $supplier ['name'] . '</option>';
		}
		$this->_html .= ' </select></div>';
	}
	protected function getType($type) {
		if ($type == 1)
			return $this->l('CMS');
		elseif ($type == 2)
			return $this->l('Link');
		elseif ($type == 3)
			return $this->l('Category');
		elseif ($type == 4)
			return $this->l('Manufacturer');
		elseif ($type == 5)
			return $this->l('Supplier');
		elseif ($type == 6)
			return $this->l('Search');
	}
	public function getLinkOutputValue($row, $type, $withExtra = true, $haveSub = false, $first_level = false) {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$link = $this->_context->link;
		} else {
			global $link;
		}
		$return = false;
		$name = false;
		$image_legend = false;
		$icone = false;
		$url = false;
		$linkNotClickable = false;
		if ((trim($row ['link'])) == '#')
			$linkNotClickable = true;
		if ($row ['type'] == 1) {
			if (trim($row ['name']))
				$name .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else
				$name .= htmlentities($row ['meta_title'], ENT_COMPAT, 'UTF-8');
			if ($withExtra && trim($row ['have_icon']))
				$icone .= $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg');
			$url .= $link->getCMSLink(intval($row ['id_cms']), $row ['link_rewrite']);
		}
		elseif ($row ['type'] == 2) {
			if (trim($row ['name']))
				$name .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
				//else $name .= $this->l('No label');
			if ($withExtra && trim($row ['have_icon']))
				$icone .= $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg');
			if (trim($row ['link']))
				$url .= htmlentities($row ['link'], ENT_COMPAT, 'UTF-8');
			else
				$linkNotClickable = true;
		}
		elseif ($row ['type'] == 3) {
			if (trim($row ['name']))
				$name .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else
				$name .= htmlentities($row ['category_name'], ENT_COMPAT, 'UTF-8');
			if (version_compare(_PS_VERSION_, '1.4.0.0', '<')) {
				$name = self::hideCategoryPosition($name);
			}
			if ($withExtra && trim($row ['have_icon']))
				$icone .= $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg');
			$url .= $link->getCategoryLink(intval($row ['id_category']), $row ['category_link_rewrite']);
		}
		elseif ($row ['type'] == 4) {
			if (trim($row ['name']))
				$name .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else
				$name .= htmlentities($row ['manufacturer_name'], ENT_COMPAT, 'UTF-8') . '';
			if ($withExtra && trim($row ['have_icon']))
				$icone .= $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg');
			if (intval($row['id_manufacturer']))
				$url .= $link->getManufacturerLink(intval($row ['id_manufacturer']), (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Tools::link_rewrite($row['manufacturer_name']) : Tools::link_rewrite($row['manufacturer_name'], false)));
			else
				$url .= $link->getPageLink('manufacturer.php');
		}
		elseif ($row ['type'] == 5) {
			if (trim($row ['name']))
				$name .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else
				$name .= htmlentities($row ['supplier_name'], ENT_COMPAT, 'UTF-8') . '';
			if ($withExtra && trim($row ['have_icon']))
				$icone .= $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg');
			if (intval($row['id_supplier']))
				$url .= $link->getSupplierLink(intval($row ['id_supplier']), (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Tools::link_rewrite($row['supplier_name']) : Tools::link_rewrite($row['supplier_name'], false)));
			else
				$url .= $link->getPageLink('supplier.php');
		}
		elseif ($row ['type'] == 6) {
			$this->_smarty->assign(
				array('atm_form_action_link' => (version_compare(_PS_VERSION_, '1.4.0.0', '<') ? __PS_BASE_URI__ . 'search.php' : $link->getPageLink('search.php')),
				'atm_search_id' => 'search_query_atm_' . $type . '_' . $row ['id_' . $type],
				'atm_have_icon' => trim($row ['have_icon']),
				'atm_withExtra' => $withExtra,
				'atm_icon_image_source' => $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg'),
				'atm_search_value' => trim(htmlentities($row['name'], ENT_COMPAT, 'UTF-8')),
				'atm_is_autocomplete_search' => Configuration::get('ATM_AUTOCOMPLET_SEARCH'),
				'atm_cookie_id_lang' => $this->_cookie->id_lang,
				'atm_pagelink_search' => (version_compare(_PS_VERSION_, '1.4.0.0', '<') ? __PS_BASE_URI__ . 'search.php' : $link->getPageLink('search.php')))
			);
			$adtmCacheId = sprintf('ADTM|%d|%s|%d|%s', $this->_cookie->id_lang, $this->_isLogged(), (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive() ? $this->_context->shop->id : 0), implode('-', self::getCustomerGroups()));
			return $this->display(__FILE__, 'pm_advancedtopmenu_search.tpl', $adtmCacheId);
		}
		elseif ($row ['type'] == 7) {
			$name = '';
			if ($withExtra && trim($row ['have_icon']))
				$icone .= $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg');
			if (trim($row ['link']))
				$url .= htmlentities($row ['link'], ENT_COMPAT, 'UTF-8');
			else
				$linkNotClickable = true;
		}
		$urlIsActive = false;
		if (! $this->activeAllreadySet && Configuration::get('ATM_MENU_GLOBAL_ACTIF')) {
			if($id_product = Tools::getValue('id_product')) {
				if(!$this->current_category_product_url) {
					$product = new Product($id_product,false,$this->_cookie->id_lang);
					if (isset($product->category) && !empty($product->category))
						$this->current_category_product_url = $link->getCategoryLink($product->id_category_default, $product->category);
				}
				$curUrl = $this->current_category_product_url;
			}
			if(!isset($curUrl) || !$curUrl) {
				$curUrl = explode('?', $_SERVER ['REQUEST_URI']);
				$curUrl = $curUrl [0] . $this->getKeepVar();
			}
			$destUrl = explode('?', $url);
			$destUrl = $destUrl [0] . (isset($destUrl [1]) ? $this->getKeepVar($destUrl [1]) : '');
			$destUrl = preg_replace('#https?://' . preg_quote($_SERVER ['HTTP_HOST'],'#') . '#i', '', $destUrl);
			if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && $destUrl == __PS_BASE_URI__ && Configuration::get('PS_REWRITING_SETTINGS') && Configuration::get('PS_CANONICAL_REDIRECT') && Language::countActiveLanguages() > 1)
				$destUrl .= Language::getIsoById($this->_cookie->id_lang).'/';
			$curUrl = preg_replace('#https?://' . preg_quote($_SERVER ['HTTP_HOST'],'#') . '#i', '', $curUrl);
			$pregCurUrl = preg_quote($curUrl,'#');
			$pregDestUrl = preg_quote($destUrl,'#');
			$urlIsActive = ((strlen($curUrl) <= strlen($destUrl)) ? preg_match('#' . $pregDestUrl . '#i', $curUrl) : false);
		}
		if ($url && $urlIsActive) {
			$idActif = 'advtm_menu_actif_' . uniqid();
		}
		$return .= '<a href="' . ($linkNotClickable ? '#' : $url) . '" title="' . $name . '" ' . ($row ['target'] ? 'target="' . htmlentities($row ['target'], ENT_COMPAT, 'UTF-8') . '"' : '') . ' class="' . ($linkNotClickable ? 'adtm_unclickable' : '') . (strpos($name, "\n") !== false ? ' a-multiline' : '') . ($first_level ? ' a-niveau1' : '') . ($url && $urlIsActive ? ' advtm_menu_actif ' . $idActif : '') . '">';
		if ($type == 'menu')
			$return .= '<span class="advtm_menu_span advtm_menu_span_' . intval($row ['id_menu']) . '">';
		if ($icone) {
			$iconWidth = $iconHeight = false;
			$iconPath = dirname(__FILE__) . '/' . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg');
			if (file_exists($iconPath) && is_readable($iconPath))
				list($iconWidth, $iconHeight) = getimagesize($iconPath);
			if (version_compare(_PS_VERSION_, '1.4.0.2', '>='))
				$icone = $link->getMediaLink($icone);
			if (trim($row['image_legend']))
				$image_legend = htmlentities($row['image_legend'], ENT_COMPAT, 'UTF-8');
			else
				$image_legend = $name;
			$return .= '<img src="' . $icone . '" alt="' . $image_legend . '" title="' . $image_legend . '" ' . ((int)$iconWidth > 0 ? 'width="'.(int)$iconWidth.'" ' : '') . ((int)$iconHeight > 0 ? 'height="'.(int)$iconHeight.'" ' : '') . 'class="adtm_menu_icon img-responsive" />';
		}
		$return .= nl2br($name);
		if ($type == 'menu')
			$return .= '</span>';
		if ($haveSub)
			$return .= '<!--[if gte IE 7]><!-->';
		$return .= '</a>';
		if ($url && $urlIsActive) {
			$return .= '<script type="text/javascript">activateParentMenu(".' . $idActif . '","' . $type . '");</script>';
			$this->activeAllreadySet = true;
		}
		return $return;
	}
	public function getAdminOutputPrivacyValue($privacy) {
		$return = '<img src="' . $this->_path . 'img/privacy-' . $privacy . '.png" title="'.$this->l('Privacy').'" />';
		if (! $privacy)
			$return .= ' ' . $this->l('For all');
		if ($privacy == 1)
			$return .= ' ' . $this->l('Only for visitors');
		if ($privacy == 2)
			$return .= ' ' . $this->l('Only for registered users');
		return $return;
	}
	public function getAdminOutputNameValue($row, $withExtra = true, $type = false) {
		$return = '';
		if ($row ['type'] == 1) {
			if ($withExtra && trim($row ['have_icon']))
				$return .= '<img src="' . $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg') . '" alt="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" />';
			if (trim($row ['name']))
				$return .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else
				$return .= htmlentities($row ['meta_title'], ENT_COMPAT, 'UTF-8');
		}
		elseif ($row ['type'] == 2) {
			if ($withExtra && trim($row ['have_icon']))
				$return .= '<img src="' . $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg') . '" alt="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" />';
			if (trim($row ['name']))
				$return .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else
				$return .= $this->l('No label');
		}
		elseif ($row ['type'] == 3) {
			if ($withExtra && trim($row ['have_icon']))
				$return .= '<img src="' . $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg') . '" alt="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" />';
			if (trim($row ['name']))
				$return .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else {
				if (version_compare(_PS_VERSION_, '1.4.0.0', '<')) {
					$row ['category_name'] = self::hideCategoryPosition($row ['category_name']);
				}
				$return .= htmlentities($row ['category_name'], ENT_COMPAT, 'UTF-8');
			}
		}
		elseif ($row ['type'] == 4) {
			if ($withExtra && trim($row ['have_icon']))
				$return .= '<img src="' . $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg') . '" alt="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" />';
			if (trim($row ['name']))
				$return .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			elseif (! $row ['id_manufacturer'] && ! trim($row ['name']))
				$return .= $this->l('No label');
			else
				$return .= htmlentities($row ['manufacturer_name'], ENT_COMPAT, 'UTF-8') . '';
		}
		elseif ($row ['type'] == 5) {
			if ($withExtra && trim($row ['have_icon']))
				$return .= '<img src="' . $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg') . '" alt="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" />';
			if (trim($row ['name']))
				$return .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			elseif (! $row ['id_supplier'] && ! trim($row ['name']))
				$return .= $this->l('No label');
			else
				$return .= htmlentities($row ['supplier_name'], ENT_COMPAT, 'UTF-8') . '';
		}
		elseif ($row ['type'] == 6) {
			if ($withExtra && trim($row ['have_icon']))
				$return .= '<img src="' . $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg') . '" alt="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" />';
			if (trim($row ['name']))
				$return .= htmlentities($row ['name'], ENT_COMPAT, 'UTF-8');
			else
				$return .= $this->l('No label');
		}
		elseif ($row ['type'] == 7) {
			if ($withExtra && trim($row ['have_icon']))
				$return .= '<img src="' . $this->_path . $type . '_icons/' . $row ['id_' . $type] . '-' . $this->_iso_lang . '.' . ($row ['image_type'] ? $row ['image_type'] : 'jpg') . '" alt="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($row ['name'], ENT_COMPAT, 'UTF-8') . '" />';
			$return .= $this->l('No label');
		}
		return $return;
	}
	protected function copyFromPost(&$object) {
		foreach ( $_POST as $key => $value ) {
			if ($key == 'active_column' || $key == 'active_menu' || $key == 'active_element')
				$key = 'active';
			else if ($key == 'active_mobile_column' || $key == 'active_mobile_menu' || $key == 'active_mobile_element')
				$key = 'active_mobile';
			if (key_exists($key, $object)) {
				$object->{$key} = $value;
			}
		}
		$rules = call_user_func(array (get_class($object), 'getValidationRules' ), get_class($object));
		if (sizeof($rules ['validateLang'])) {
			$languages = Language::getLanguages(false);
			foreach ( $languages as $language )
				foreach ( $rules ['validateLang'] as $field => $validation )
					if (isset($_POST [$field . '_' . intval($language ['id_lang'])])) {
						$object->{$field} [intval($language ['id_lang'])] = $_POST [$field . '_' . intval($language ['id_lang'])];
					}
		}
	}
	private function udpdateMenuType($AdvancedTopMenuClass) {
		switch ($AdvancedTopMenuClass->type) {
			case 3 :
				if (Tools::getValue('include_subs')) {
					if ($AdvancedTopMenuClass->id_category) {
						$firstChildCategories = array ();
						$firstChildCategories = $this->getSubCategoriesId($AdvancedTopMenuClass->id_category);
						$lastChildCategories = array ();
						$columnWithNoDepth = false;
						$columnWrapWithNoDepth = false;
						if (sizeof($firstChildCategories)) {
							foreach ( $firstChildCategories as $firstChildCategorie ) {
								$lastChildCategories = $this->getSubCategoriesId($firstChildCategorie ['id_category']);
								if (sizeof($lastChildCategories)) {
									$id_column = false;
									if (Tools::getValue('id_menu', false)) {
										$id_column = AdvancedTopMenuColumnClass::getIdColumnCategoryDepend($AdvancedTopMenuClass->id, $firstChildCategorie ['id_category']);
										if (! $id_column && ! Tools::getValue('rebuild'))
											continue;
									}
									$AdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass($id_column);
									if (! $id_column) {
										$AdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass();
										$AdvancedTopMenuColumnWrapClass->active = 1;
										$AdvancedTopMenuColumnWrapClass->id_menu = $AdvancedTopMenuClass->id;
										$AdvancedTopMenuColumnWrapClass->id_menu_depend = $AdvancedTopMenuClass->id;
										$AdvancedTopMenuColumnWrapClass->save();
										$AdvancedTopMenuColumnWrapClass->internal_name = $this->l('column') . '-' . $AdvancedTopMenuColumnWrapClass->id_menu . '-' . $AdvancedTopMenuColumnWrapClass->id;
										if (! $AdvancedTopMenuColumnWrapClass->save()) {
											$this->errors [] = Tools::displayError('An error occured during save column');
										}
										$AdvancedTopMenuColumnClass->id_wrap = $AdvancedTopMenuColumnWrapClass->id;
									}
									$AdvancedTopMenuColumnClass->active = ($id_column ? $AdvancedTopMenuColumnClass->active : 1);
									$AdvancedTopMenuColumnClass->id_menu = $AdvancedTopMenuClass->id;
									$AdvancedTopMenuColumnClass->id_menu_depend = $AdvancedTopMenuClass->id;
									$AdvancedTopMenuColumnClass->type = $AdvancedTopMenuClass->type;
									$AdvancedTopMenuColumnClass->id_category = $firstChildCategorie ['id_category'];
									if ($AdvancedTopMenuColumnClass->save()) {
										$elementPosition = 0;
										foreach ( $lastChildCategories as $lastChildCategory ) {
											$id_element = false;
											if (Tools::getValue('id_menu', false)) {
												$id_element = AdvancedTopMenuElementsClass::getIdElementCategoryDepend($id_column, $lastChildCategory ['id_category']);
												if (! $id_element && ! Tools::getValue('rebuild'))
													continue;
											}
											$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass($id_element);
											$AdvancedTopMenuElementsClass->active = ($id_element ? $AdvancedTopMenuElementsClass->active : 1);
											$AdvancedTopMenuElementsClass->type = $AdvancedTopMenuClass->type;
											$AdvancedTopMenuElementsClass->id_category = $lastChildCategory ['id_category'];
											$AdvancedTopMenuElementsClass->id_column = $AdvancedTopMenuColumnClass->id;
											$AdvancedTopMenuElementsClass->id_column_depend = $AdvancedTopMenuColumnClass->id;
											if (!$id_element)
												$AdvancedTopMenuElementsClass->position = $elementPosition;
											if (! $AdvancedTopMenuElementsClass->save()) {
												$this->errors [] = Tools::displayError('An error occured during save children category');
											}
											$elementPosition++;
										}
									}
									else {
										$this->errors [] = Tools::displayError('An error occured during save children category');
									}
								}
								else {
									$id_column = false;
									if (Tools::getValue('id_menu', false)) {
										$id_column = AdvancedTopMenuColumnClass::getIdColumnCategoryDependEmptyColumn($AdvancedTopMenuClass->id, $firstChildCategorie ['id_category']);
										if (! $id_column && ! Tools::getValue('rebuild'))
											continue;
										if ($id_column)
											$columnWithNoDepth = $id_column;
									}
									$AdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass($columnWithNoDepth);
									if (! $columnWithNoDepth) {
										$AdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass($columnWrapWithNoDepth);
										$AdvancedTopMenuColumnWrapClass->active = 1;
										$AdvancedTopMenuColumnWrapClass->id_menu = $AdvancedTopMenuClass->id;
										$AdvancedTopMenuColumnWrapClass->id_menu_depend = $AdvancedTopMenuClass->id;
										$AdvancedTopMenuColumnWrapClass->save();
										$AdvancedTopMenuColumnWrapClass->internal_name = $this->l('column') . $AdvancedTopMenuColumnWrapClass->id_menu . '-' . $AdvancedTopMenuColumnWrapClass->id;
										$AdvancedTopMenuColumnWrapClass->save();
										$AdvancedTopMenuColumnClass->id_wrap = $AdvancedTopMenuColumnWrapClass->id;
									}
									$AdvancedTopMenuColumnClass->active = ($columnWithNoDepth ? $AdvancedTopMenuColumnClass->active : 1);
									$AdvancedTopMenuColumnClass->id_menu = $AdvancedTopMenuClass->id;
									$AdvancedTopMenuColumnClass->id_menu_depend = $AdvancedTopMenuClass->id;
									$AdvancedTopMenuColumnClass->type = $AdvancedTopMenuClass->type;
									$AdvancedTopMenuColumnClass->id_category = $firstChildCategorie ['id_category'];
									if ($AdvancedTopMenuColumnClass->save()) {
										if (! $columnWrapWithNoDepth)
											$columnWrapWithNoDepth = $AdvancedTopMenuColumnClass->id_wrap;
									}
									else {
										$this->errors [] = Tools::displayError('An error occured during save children category');
									}
								}
							}
						}
					}
				}
				break;
			case 4 :
				if (Tools::getValue('include_subs_manu')) {
					$manufacturersId = $this->getManufacturersId();
					$columnWithNoDepth = false;
					if (sizeof($manufacturersId)) {
						$elementPosition = 0;
						foreach ( $manufacturersId as $manufacturerId ) {
							$id_column = false;
							if (Tools::getValue('id_menu', false)) {
								$id_column = AdvancedTopMenuColumnClass::getIdColumnManufacturerDependEmptyColumn($AdvancedTopMenuClass->id, $manufacturerId ['id_manufacturer']);
								if (! $id_column && ! Tools::getValue('rebuild'))
									continue;
								if ($id_column)
									$columnWithNoDepth = $id_column;
							}
							$AdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass($columnWithNoDepth);
							if (! $columnWithNoDepth) {
								$AdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass($columnWithNoDepth);
								$AdvancedTopMenuColumnWrapClass->active = 1;
								$AdvancedTopMenuColumnWrapClass->id_menu = $AdvancedTopMenuClass->id;
								$AdvancedTopMenuColumnWrapClass->id_menu_depend = $AdvancedTopMenuClass->id;
								$AdvancedTopMenuColumnWrapClass->save();
								$AdvancedTopMenuColumnWrapClass->internal_name = $this->l('column') . $AdvancedTopMenuColumnWrapClass->id_menu . '-' . $AdvancedTopMenuColumnWrapClass->id;
								$AdvancedTopMenuColumnWrapClass->save();
								$AdvancedTopMenuColumnClass->id_wrap = $AdvancedTopMenuColumnWrapClass->id;
							}
							$AdvancedTopMenuColumnClass->active = ($columnWithNoDepth ? $AdvancedTopMenuColumnClass->active : 1);
							$AdvancedTopMenuColumnClass->id_menu = $AdvancedTopMenuClass->id;
							$AdvancedTopMenuColumnClass->id_menu_depend = $AdvancedTopMenuClass->id;
							$AdvancedTopMenuColumnClass->type = 2;
							if ($AdvancedTopMenuColumnClass->save()) {
								if (! $columnWithNoDepth)
									$columnWithNoDepth = $AdvancedTopMenuColumnClass->id;
								$id_element = false;
								if (Tools::getValue('id_menu', false)) {
									$id_element = AdvancedTopMenuElementsClass::getIdElementManufacturerDepend($columnWithNoDepth, $manufacturerId ['id_manufacturer']);
									if (! $id_element && ! Tools::getValue('rebuild'))
										continue;
								}
								$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass($id_element);
								$AdvancedTopMenuElementsClass->active = ($id_element ? $AdvancedTopMenuElementsClass->active : 1);
								$AdvancedTopMenuElementsClass->type = $AdvancedTopMenuClass->type;
								$AdvancedTopMenuElementsClass->id_manufacturer = $manufacturerId ['id_manufacturer'];
								$AdvancedTopMenuElementsClass->id_column = $AdvancedTopMenuColumnClass->id;
								$AdvancedTopMenuElementsClass->id_column_depend = $AdvancedTopMenuColumnClass->id;
								if (!$id_element)
									$AdvancedTopMenuElementsClass->position = $elementPosition;
								if (! $AdvancedTopMenuElementsClass->save()) {
									$this->errors [] = Tools::displayError('An error occured during save manufacturer');
								}
								$elementPosition++;
							}
							else {
								$this->errors [] = Tools::displayError('An error occured during save manufacturer');
							}
						}
					}
				}
				break;
			case 5 :
				if (Tools::getValue('include_subs_suppl')) {
					$suppliersId = $this->getSuppliersId();
					$columnWithNoDepth = false;
					if (sizeof($suppliersId)) {
						$elementPosition = 0;
						foreach ( $suppliersId as $supplierId ) {
							$id_column = false;
							if (Tools::getValue('id_menu', false)) {
								$id_column = AdvancedTopMenuColumnClass::getIdColumnSupplierDependEmptyColumn($AdvancedTopMenuClass->id, $supplierId ['id_supplier']);
								if (! $id_column && ! Tools::getValue('rebuild'))
									continue;
								if ($id_column)
									$columnWithNoDepth = $id_column;
							}
							$AdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass($columnWithNoDepth);
							if (! $columnWithNoDepth) {
								$AdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass($columnWithNoDepth);
								$AdvancedTopMenuColumnWrapClass->active = 1;
								$AdvancedTopMenuColumnWrapClass->id_menu = $AdvancedTopMenuClass->id;
								$AdvancedTopMenuColumnWrapClass->id_menu_depend = $AdvancedTopMenuClass->id;
								$AdvancedTopMenuColumnWrapClass->save();
								$AdvancedTopMenuColumnWrapClass->internal_name = $this->l('column') . $AdvancedTopMenuColumnWrapClass->id_menu . '-' . $AdvancedTopMenuColumnWrapClass->id;
								$AdvancedTopMenuColumnWrapClass->save();
								$AdvancedTopMenuColumnClass->id_wrap = $AdvancedTopMenuColumnWrapClass->id;
							}
							$AdvancedTopMenuColumnClass->active = ($columnWithNoDepth ? $AdvancedTopMenuColumnClass->active : 1);
							$AdvancedTopMenuColumnClass->id_menu = $AdvancedTopMenuClass->id;
							$AdvancedTopMenuColumnClass->id_menu_depend = $AdvancedTopMenuClass->id;
							$AdvancedTopMenuColumnClass->type = 2;
							if ($AdvancedTopMenuColumnClass->save()) {
								if (! $columnWithNoDepth)
									$columnWithNoDepth = $AdvancedTopMenuColumnClass->id;
								$id_element = false;
								if (Tools::getValue('id_menu', false)) {
									$id_element = AdvancedTopMenuElementsClass::getIdElementSupplierDepend($columnWithNoDepth, $supplierId ['id_supplier']);
									if (! $id_element && ! Tools::getValue('rebuild'))
										continue;
								}
								$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass($id_element);
								$AdvancedTopMenuElementsClass->active = ($id_element ? $AdvancedTopMenuElementsClass->active : 1);
								$AdvancedTopMenuElementsClass->type = $AdvancedTopMenuClass->type;
								$AdvancedTopMenuElementsClass->id_supplier = $supplierId ['id_supplier'];
								$AdvancedTopMenuElementsClass->id_column = $AdvancedTopMenuColumnClass->id;
								$AdvancedTopMenuElementsClass->id_column_depend = $AdvancedTopMenuColumnClass->id;
								if (!$id_element)
									$AdvancedTopMenuElementsClass->position = $elementPosition;
								if (! $AdvancedTopMenuElementsClass->save()) {
									$this->errors [] = Tools::displayError('An error occured during save supplier');
								}
								$elementPosition++;
							}
							else {
								$this->errors [] = Tools::displayError('An error occured during save supplier');
							}
						}
					}
				}
				break;
		}
	}
	private function udpdateColumnType($AdvancedTopMenuColumnClass) {
		switch ($AdvancedTopMenuColumnClass->type) {
			case 3 :
				if (Tools::getValue('include_subs')) {
					if ($AdvancedTopMenuColumnClass->id_category) {
						$childCategories = array ();
						$childCategories = $this->getSubCategoriesId($AdvancedTopMenuColumnClass->id_category);
						if (sizeof($childCategories)) {
							$elementPosition = 0;
							foreach ( $childCategories as $childCategory ) {
								$id_element = false;
								if (Tools::getValue('id_column', false)) {
									$id_element = AdvancedTopMenuElementsClass::getIdElementCategoryDepend(Tools::getValue('id_column'), $childCategory ['id_category']);
									if (! $id_element && ! Tools::getValue('rebuild'))
										continue;
								}
								$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass($id_element);
								$AdvancedTopMenuElementsClass->active = ($id_element ? $AdvancedTopMenuElementsClass->active : 1);
								$AdvancedTopMenuElementsClass->type = $AdvancedTopMenuColumnClass->type;
								$AdvancedTopMenuElementsClass->id_category = $childCategory ['id_category'];
								$AdvancedTopMenuElementsClass->id_column = $AdvancedTopMenuColumnClass->id;
								$AdvancedTopMenuElementsClass->id_column_depend = $AdvancedTopMenuColumnClass->id;
								if (!$id_element)
									$AdvancedTopMenuElementsClass->position = $elementPosition;
								if (! $AdvancedTopMenuElementsClass->save()) {
									$this->errors [] = Tools::displayError('An error occured during save children category');
								}
								$elementPosition++;
							}
						}
					}
				}
				break;
			case 4 :
				if (Tools::getValue('include_subs_manu')) {
					$manufacturersId = $this->getManufacturersId();
					if (sizeof($manufacturersId)) {
						$elementPosition = 0;
						foreach ( $manufacturersId as $manufacturerId ) {
							$id_element = false;
							if (Tools::getValue('id_column', false)) {
								$id_element = AdvancedTopMenuElementsClass::getIdElementManufacturerDepend(Tools::getValue('id_column'), $manufacturerId ['id_manufacturer']);
								if (! $id_element && ! Tools::getValue('rebuild'))
									continue;
							}
							$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass($id_element);
							$AdvancedTopMenuElementsClass->active = ($id_element ? $AdvancedTopMenuElementsClass->active : 1);
							$AdvancedTopMenuElementsClass->type = $AdvancedTopMenuColumnClass->type;
							$AdvancedTopMenuElementsClass->id_manufacturer = $manufacturerId ['id_manufacturer'];
							$AdvancedTopMenuElementsClass->id_column = $AdvancedTopMenuColumnClass->id;
							$AdvancedTopMenuElementsClass->id_column_depend = $AdvancedTopMenuColumnClass->id;
							if (!$id_element)
								$AdvancedTopMenuElementsClass->position = $elementPosition;
							if (! $AdvancedTopMenuElementsClass->save()) {
								$this->errors [] = Tools::displayError('An error occured during save manufacturer');
							}
							$elementPosition++;
						}
					}
				}
				break;
			case 5 :
				if (Tools::getValue('include_subs_suppl')) {
					$suppliersId = $this->getSuppliersId();
					if (sizeof($suppliersId)) {
						$elementPosition = 0;
						foreach ( $suppliersId as $supplierId ) {
							$id_element = false;
							if (Tools::getValue('id_column', false)) {
								$id_element = AdvancedTopMenuElementsClass::getIdElementSupplierDepend(Tools::getValue('id_column'), $supplierId ['id_supplier']);
								if (! $id_element && ! Tools::getValue('rebuild'))
									continue;
							}
							$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass($id_element);
							$AdvancedTopMenuElementsClass->active = ($id_element ? $AdvancedTopMenuElementsClass->active : 1);;
							$AdvancedTopMenuElementsClass->type = $AdvancedTopMenuColumnClass->type;
							$AdvancedTopMenuElementsClass->id_supplier = $supplierId ['id_supplier'];
							$AdvancedTopMenuElementsClass->id_column = $AdvancedTopMenuColumnClass->id;
							$AdvancedTopMenuElementsClass->id_column_depend = $AdvancedTopMenuColumnClass->id;
							if (!$id_element)
								$AdvancedTopMenuElementsClass->position = $elementPosition;
							if (! $AdvancedTopMenuElementsClass->save()) {
								$this->errors [] = Tools::displayError('An error occured during save supplier');
							}
							$elementPosition++;
						}
					}
				}
				break;
		}
	}
	public function getManufacturersId() {
		return Db::getInstance()->ExecuteS('
    SELECT m.`id_manufacturer`
    FROM `' . _DB_PREFIX_ . 'manufacturer` m
    ORDER BY m.`name` ASC');
	}
	public function getSuppliersId() {
		return Db::getInstance()->ExecuteS('
    SELECT s.`id_supplier`
    FROM `' . _DB_PREFIX_ . 'supplier` s
    ORDER BY s.`name` ASC');
	}
	public function getSubCategoriesId($id_category, $active = true) {
		if (! Validate::isBool($active))
			die(Tools::displayError());
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$orderBy = 'category_shop.`position`';
		else if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			$orderBy = 'c.`position`';
		else
			$orderBy = 'c.`id_category`';
		$result = Db::getInstance()->ExecuteS('
    SELECT c.id_category
    FROM `' . _DB_PREFIX_ . 'category` c
	' . (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Shop::addSqlAssociation('category', 'c') : '') . '
    WHERE `id_parent` = ' . intval($id_category) . '
    ' . ($active ? 'AND `active` = 1' : '') . '
    GROUP BY c.`id_category`
    ORDER BY ' . $orderBy . ' ASC');
		return $result;
	}
	private function getFileExtension($filename) {
		$split = explode('.', $filename);
		$extension = end($split);
		return $extension;
	}
	private function _postProcessMenu() {
		$id_menu = Tools::getValue('id_menu', false);
		$AdvancedTopMenuClass = new AdvancedTopMenuClass($id_menu);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->errors = $AdvancedTopMenuClass->validateController();
		else $this->errors = $AdvancedTopMenuClass->validateControler();
		if (! sizeof($this->errors)) {
			$_POST ['border_size_tab'] = $this->getBorderSizeFromArray(Tools::getValue('border_size_tab'));
			$_POST ['border_size_submenu'] = $this->getBorderSizeFromArray(Tools::getValue('border_size_submenu'));
			$_POST ['fnd_color_menu_tab'] = $_POST ['fnd_color_menu_tab'] [0] . (Tools::getValue('fnd_color_menu_tab_gradient') && isset($_POST ['fnd_color_menu_tab'] [1]) && $_POST ['fnd_color_menu_tab'] [1] ? $this->gradient_separator . $_POST ['fnd_color_menu_tab'] [1] : '');
			$_POST ['fnd_color_menu_tab_over'] = $_POST ['fnd_color_menu_tab_over'] [0] . (Tools::getValue('fnd_color_menu_tab_over_gradient') && isset($_POST ['fnd_color_menu_tab_over'] [1]) && $_POST ['fnd_color_menu_tab_over'] [1] ? $this->gradient_separator . $_POST ['fnd_color_menu_tab_over'] [1] : '');
			$_POST ['fnd_color_submenu'] = $_POST ['fnd_color_submenu'] [0] . (Tools::getValue('fnd_color_submenu_gradient') && isset($_POST ['fnd_color_submenu'] [1]) && $_POST ['fnd_color_submenu'] [1] ? $this->gradient_separator . $_POST ['fnd_color_submenu'] [1] : '');
			$this->copyFromPost($AdvancedTopMenuClass);
			if (($AdvancedTopMenuClass->type == 4 && Tools::getValue('include_subs_manu')) || ($AdvancedTopMenuClass->type == 5 && Tools::getValue('include_subs_suppl'))) {
				$AdvancedTopMenuClass->id_manufacturer = 0;
				$AdvancedTopMenuClass->id_supplier = 0;
				if ($AdvancedTopMenuClass->type == 4)
					foreach ($AdvancedTopMenuClass->name as $id_lang => $name) {
						$title = '';
						if (empty($name)) {
							if (class_exists('Meta') && method_exists('Meta', 'getMetaByPage')) {
								$title = Meta::getMetaByPage('manufacturer', $id_lang);
								if (is_array($title) && isset($title['title']) && !empty($title['title']))
									$title = $title['title'];
							}
							if (empty($title))
								$title = $this->l('Manufacturers');
							$AdvancedTopMenuClass->name[$id_lang] = $title;
						}
					}
				else if ($AdvancedTopMenuClass->type == 5)
					foreach ($AdvancedTopMenuClass->name as $id_lang => $name) {
						$title = '';
						if (empty($name)) {
							if (class_exists('Meta') && method_exists('Meta', 'getMetaByPage')) {
								$title = Meta::getMetaByPage('supplier', $id_lang);
								if (is_array($title) && isset($title['title']) && !empty($title['title']))
									$title = $title['title'];
							}
							if (empty($title))
								$title = $this->l('Suppliers');
							$AdvancedTopMenuClass->name[$id_lang] = $title;
						}
					}
			}
			$languages = Language::getLanguages(false);
			if (! $id_menu) {
				if (! $AdvancedTopMenuClass->add())
					$this->errors [] = $this->l('Error during add menu');
			}
			elseif (! $AdvancedTopMenuClass->update())
				$this->errors [] = $this->l('Error during update menu');
			if (! sizeof($this->errors)) {
				$this->udpdateMenuType($AdvancedTopMenuClass);
				if (! sizeof($this->errors)) {
					foreach ($languages as $language) {
						$fileKey = 'icon_' . $language['id_lang'];
						if (isset($_FILES[$fileKey]['tmp_name']) and $_FILES[$fileKey]['tmp_name'] != NULL) {
							$ext = $this->getFileExtension($_FILES[$fileKey]['name']);
							if (! in_array($ext, $this->allowFileExtension) || ! getimagesize($_FILES[$fileKey]['tmp_name']) || ! move_uploaded_file($_FILES[$fileKey]['tmp_name'], _PS_ROOT_DIR_ . '/modules/' . $this->name . '/menu_icons/' . $AdvancedTopMenuClass->id . '-' . $language['iso_code'] . '.' . $ext))
								$this->errors [] = Tools::displayError('An error occured during the image upload');
							else {
								$AdvancedTopMenuClass->image_type[$language['id_lang']] = $ext;
								$AdvancedTopMenuClass->have_icon[$language['id_lang']] = 1;
								$AdvancedTopMenuClass->update();
							}
						}
						else if (Tools::getValue('unlink_icon_' . $language['id_lang'])) {
							unlink(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/menu_icons/' . $AdvancedTopMenuClass->id . '-' . $language['iso_code'] . '.' . ($AdvancedTopMenuClass->image_type[$language['id_lang']] ? $AdvancedTopMenuClass->image_type[$language['id_lang']] : 'jpg'));
							$AdvancedTopMenuClass->have_icon[$language['id_lang']] = '';
							$AdvancedTopMenuClass->image_type[$language['id_lang']] = '';
							$AdvancedTopMenuClass->image_legend[$language['id_lang']] = '';
							$AdvancedTopMenuClass->update();
						}
					}
					$this->generateCss();
					$this->_html .= $this->displayConfirmation($this->l('Menu has been updated successfully'));
				}
			}
			unset($_POST ['active']);
		}
	}
	private function _postProcessColumnWrap() {
		$id_wrap = Tools::getValue('id_wrap', false);
		$id_menu = Tools::getValue('id_menu', false);
		if (!$id_menu) {
			$this->errors [] = $this->l('Error during add column - Parent tab is not set');
		} else {
			$AdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass($id_wrap);
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->errors = $AdvancedTopMenuColumnWrapClass->validateController();
			else $this->errors = $AdvancedTopMenuColumnWrapClass->validateControler();
			if (! sizeof($this->errors)) {
				$_POST ['bg_color'] = $_POST ['bg_color'] [0] . (Tools::getValue('bg_color_gradient') && isset($_POST ['bg_color'] [1]) && $_POST ['bg_color'] [1] ? $this->gradient_separator . $_POST ['bg_color'] [1] : '');
				$this->copyFromPost($AdvancedTopMenuColumnWrapClass);
				unset($_POST ['active']);
				if (! $id_wrap) {
					if (! $AdvancedTopMenuColumnWrapClass->add())
						$this->errors [] = $this->l('Error during add column');
				}
				elseif (! $AdvancedTopMenuColumnWrapClass->update())
					$this->errors [] = $this->l('Error during update column');
				if (! sizeof($this->errors)) {
					$this->generateCss();
					$this->_html .= $this->displayConfirmation($this->l('Column has been updated successfully'));
				}
			}
		}
	}
	private function _postProcessColumn() {
		$id_column = Tools::getValue('id_column', false);
		$AdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass($id_column);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->errors = $AdvancedTopMenuColumnClass->validateController();
		else $this->errors = $AdvancedTopMenuColumnClass->validateControler();
		//$this->_html .= $this->displayError($this->l('Bad URL'));
		if (! sizeof($this->errors)) {
			$this->copyFromPost($AdvancedTopMenuColumnClass);
			if (!sizeof($this->errors)) {
				if ($AdvancedTopMenuColumnClass->type == 8) {
					$productElementsObj = false;
					if ($id_column) {
						$productElementsObj = AdvancedTopMenuProductColumnClass::getByIdColumn($id_column);
					}
					if (!$productElementsObj) {
						$productElementsObj = new AdvancedTopMenuProductColumnClass();
						$productElementsObj->id_column = 1;
					}
					$this->copyFromPost($productElementsObj);
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
						$this->errors = $productElementsObj->validateController();
					} else {
						$this->errors = $productElementsObj->validateControler();
					}
				}
				if (sizeof($this->errors)) {
					return;
				}
			}
			$languages = Language::getLanguages(false);
			unset($_POST ['active']);
			if (! $id_column) {
				if (! $AdvancedTopMenuColumnClass->add())
					$this->errors [] = $this->l('Error during add submenu');
			}
			elseif (! $AdvancedTopMenuColumnClass->update())
				$this->errors [] = $this->l('Error during update submenu');
			if (! sizeof($this->errors)) {
				$this->udpdateColumnType($AdvancedTopMenuColumnClass);
				foreach ($languages as $language) {
					$fileKey = 'icon_' . $language['id_lang'];
					if (isset($_FILES [$fileKey] ['tmp_name']) and $_FILES [$fileKey] ['tmp_name'] != NULL) {
						$ext = $this->getFileExtension($_FILES [$fileKey] ['name']);
						if (! in_array($ext, $this->allowFileExtension) || ! getimagesize($_FILES [$fileKey] ['tmp_name']) || ! move_uploaded_file($_FILES [$fileKey] ['tmp_name'], _PS_ROOT_DIR_ . '/modules/' . $this->name . '/column_icons/' . $AdvancedTopMenuColumnClass->id . '-' . $language['iso_code'] . '.' . $ext))
							$this->errors [] = Tools::displayError('An error occured during the image upload');
						else {
							$AdvancedTopMenuColumnClass->image_type[$language['id_lang']] = $ext;
							$AdvancedTopMenuColumnClass->have_icon[$language['id_lang']] = 1;
							$AdvancedTopMenuColumnClass->update();
						}
					}
					else if (Tools::getValue('unlink_icon_' . $language['id_lang'])) {
						unlink(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/column_icons/' . $AdvancedTopMenuColumnClass->id . '-' . $language['iso_code'] . '.' . ($AdvancedTopMenuColumnClass->image_type[$language['id_lang']] ? $AdvancedTopMenuColumnClass->image_type[$language['id_lang']] : 'jpg'));
						$AdvancedTopMenuColumnClass->have_icon[$language['id_lang']] = '';
						$AdvancedTopMenuColumnClass->image_type[$language['id_lang']] = '';
						$AdvancedTopMenuColumnClass->image_legend[$language['id_lang']] = '';
						$AdvancedTopMenuColumnClass->update();
					}
				}
				if ($AdvancedTopMenuColumnClass->type == 8) {
					$productElementsObj->id_column = $AdvancedTopMenuColumnClass->id;
					$productElementsObj->save();
				}
				$this->_html .= $this->displayConfirmation($this->l('Submenu has been updated successfully'));
			}
		}
	}
	private function _postProcessColumnElement() {
		$id_element = Tools::getValue('id_element', false);
		$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass($id_element);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->errors = $AdvancedTopMenuElementsClass->validateController();
		else $this->errors = $AdvancedTopMenuElementsClass->validateControler();
		//$this->_html .= $this->displayError($this->l('Bad URL'));
		if (! sizeof($this->errors)) {
			$this->copyFromPost($AdvancedTopMenuElementsClass);
			$languages = Language::getLanguages(false);
			if (! $id_element) {
				if (! $AdvancedTopMenuElementsClass->add())
					$this->errors [] = $this->l('Error during add element');
			}
			elseif (! $AdvancedTopMenuElementsClass->update())
				$this->errors [] = $this->l('Error during update element');
			if (! sizeof($this->errors)) {
				foreach ($languages as $language) {
					$fileKey = 'icon_' . $language['id_lang'];
					if (isset($_FILES [$fileKey] ['tmp_name']) and $_FILES [$fileKey] ['tmp_name'] != NULL) {
						$ext = $this->getFileExtension($_FILES [$fileKey] ['name']);
						if (! in_array($ext, $this->allowFileExtension) || ! getimagesize($_FILES [$fileKey] ['tmp_name']) || ! move_uploaded_file($_FILES [$fileKey] ['tmp_name'], _PS_ROOT_DIR_ . '/modules/' . $this->name . '/element_icons/' . $AdvancedTopMenuElementsClass->id . '-' . $language['iso_code'] . '.' . $ext))
							$this->errors [] = Tools::displayError('An error occured during the image upload');
						else {
							$AdvancedTopMenuElementsClass->image_type[$language['id_lang']] = $ext;
							$AdvancedTopMenuElementsClass->have_icon[$language['id_lang']] = 1;
							$AdvancedTopMenuElementsClass->update();
						}
					}
					else if (Tools::getValue('unlink_icon_' . $language['id_lang'])) {
						unlink(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/element_icons/' . $AdvancedTopMenuElementsClass->id . '-' . $language['iso_code'] . '.' . ($AdvancedTopMenuElementsClass->image_type[$language['id_lang']] ? $AdvancedTopMenuElementsClass->image_type[$language['id_lang']] : 'jpg'));
						$AdvancedTopMenuElementsClass->have_icon[$language['id_lang']] = '';
						$AdvancedTopMenuElementsClass->image_type[$language['id_lang']] = '';
						$AdvancedTopMenuElementsClass->image_legend[$language['id_lang']] = '';
						$AdvancedTopMenuElementsClass->update();
					}
				}
				$this->_html .= $this->displayConfirmation($this->l('Element has been updated successfully'));
			}
		}
	}
	private function _getSelectColumns($id_menu = false, $column_selected = false) {
		$columns = AdvancedTopMenuColumnClass::getMenuColumsByIdMenu((int)$id_menu, $this->_cookie->id_lang, false);
		$this->_smarty->assign(
			array(
				'columns'			=> $columns,
				'column_selected'	=> $column_selected,
				'objADTM'			=> new PM_AdvancedTopMenu()
			)
		);
		return $this->_smarty->fetch(dirname(__FILE__).'/column_select.tpl');
	}
	private function _getSelectColumnsWrap($id_menu = false, $columnWrap_selected = false) {
		$columnsWrap = AdvancedTopMenuColumnWrapClass::getMenuColumnsWrap((int)$id_menu, $this->_cookie->id_lang, false);
		$this->_smarty->assign(
			array(
				'columnsWrap'			=> $columnsWrap,
				'columnWrap_selected'	=> $columnWrap_selected
			)
		);
		return $this->_smarty->fetch(dirname(__FILE__).'/columnwrap_select.tpl');
	}
	private function _postProcess() {
		if (isset($_GET['dismissRating'])) {
			$this->_html = '';
			self::_cleanBuffer();
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				Configuration::updateGlobalValue('PM_'.self::$_module_prefix.'_DISMISS_RATING', 1);
			else
				Configuration::updateValue('PM_'.self::$_module_prefix.'_DISMISS_RATING', 1);
			die;
		}
		$this->saveConfig();
		$this->saveAdvancedConfig();
		if (Tools::getValue('activeMaintenance')) {
			echo $this->_postProcessMaintenance(self::$_module_prefix);
			die();
		}
		elseif (Tools::getValue('actionColumn') == 'get_select_columns') {
			$id_menu = Tools::getValue('id_menu',false);
			$column_selected = Tools::getValue('column_selected',false);
			self::_cleanBuffer();
			echo $this->_getSelectColumns($id_menu, $column_selected);
			die();
		}
		elseif (Tools::getValue('actionColumn') == 'get_select_columnsWrap') {
			$id_menu = Tools::getValue('id_menu',false);
			$columnWrap_selected = Tools::getValue('columnWrap_selected',false);
			self::_cleanBuffer();
			echo $this->_getSelectColumnsWrap($id_menu, $columnWrap_selected);
			die();
		}
		elseif (isset($_GET ['columnElementsPosition'])) {
			$order = $_GET ['columnElementsPosition'] ? explode(',', $_GET ['columnElementsPosition']) : array ();
			foreach ( $order as $position => $id_element ) {
				$row = array ('position' => intval($position) );
				Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'pm_advancedtopmenu_elements', $row, 'UPDATE', 'id_element =' . intval($id_element));
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $this->l('Saved');
			die();
		}
		elseif (isset($_GET ['menuPosition'])) {
			$order = $_GET ['menuPosition'] ? explode(',', $_GET ['menuPosition']) : array ();
			foreach ( $order as $position => $id_menu ) {
				if (! trim($id_menu))
					continue;
				$row = array ('position' => intval($position) );
				Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'pm_advancedtopmenu', $row, 'UPDATE', 'id_menu =' . intval($id_menu));
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $this->l('Saved');
			die();
		}
		elseif (isset($_GET ['columnPosition'])) {
			$order = $_GET ['columnPosition'] ? explode(',', $_GET ['columnPosition']) : array ();
			foreach ( $order as $position => $id_column ) {
				if (! trim($id_column))
					continue;
				$row = array ('position' => intval($position) );
				Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'pm_advancedtopmenu_columns', $row, 'UPDATE', 'id_column =' . intval($id_column));
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $this->l('Saved');
			die();
		}
		elseif (isset($_GET ['columnWrapPosition'])) {
			$order = $_GET ['columnWrapPosition'] ? explode(',', $_GET ['columnWrapPosition']) : array ();
			foreach ( $order as $position => $id_wrap ) {
				if (! trim($id_wrap))
					continue;
				$row = array ('position' => intval($position) );
				Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'pm_advancedtopmenu_columns_wrap', $row, 'UPDATE', 'id_wrap =' . intval($id_wrap));
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $this->l('Saved');
			die();
		}
		elseif (Tools::getValue('activeMenu') && Tools::getValue('id_menu')) {
			$return = '';
			$ObjAdvancedTopMenuClass = new AdvancedTopMenuClass(Tools::getValue('id_menu'));
			$ObjAdvancedTopMenuClass->active = ($ObjAdvancedTopMenuClass->active ? 0 : 1);
			if ($ObjAdvancedTopMenuClass->save()) {
				$return .= '$("#imgActiveMenu' . $ObjAdvancedTopMenuClass->id . '").attr("src","../img/admin/' . ($ObjAdvancedTopMenuClass->active ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activemenu","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activemenu","' . $this->l('Error during update menu') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		}
		elseif (Tools::getValue('activeColumnWrap') && Tools::getValue('id_wrap')) {
			$return = '';
			$ObjAdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass(Tools::getValue('id_wrap'));
			$ObjAdvancedTopMenuColumnWrapClass->active = ($ObjAdvancedTopMenuColumnWrapClass->active ? 0 : 1);
			if ($ObjAdvancedTopMenuColumnWrapClass->save()) {
				$return .= '$("#imgActiveColumnWrap' . $ObjAdvancedTopMenuColumnWrapClass->id . '").attr("src","../img/admin/' . ($ObjAdvancedTopMenuColumnWrapClass->active ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activecolumnwrap","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activecolumnwrap","' . $this->l('Error during update column') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		}
		elseif (Tools::getValue('activeColumn') && Tools::getValue('id_column')) {
			$return = '';
			$ObjAdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass(Tools::getValue('id_column'));
			$ObjAdvancedTopMenuColumnClass->active = ($ObjAdvancedTopMenuColumnClass->active ? 0 : 1);
			if ($ObjAdvancedTopMenuColumnClass->save()) {
				$return .= '$("#imgActiveColumn' . $ObjAdvancedTopMenuColumnClass->id . '").attr("src","../img/admin/' . ($ObjAdvancedTopMenuColumnClass->active ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activegroup","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activegroup","' . $this->l('Error during update group') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		}
		elseif (Tools::getValue('activeElement') && Tools::getValue('id_element')) {
			$return = '';
			$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass(Tools::getValue('id_element'));
			$AdvancedTopMenuElementsClass->active = ($AdvancedTopMenuElementsClass->active ? 0 : 1);
			if ($AdvancedTopMenuElementsClass->save()) {
				$return .= '$("#imgActiveElement' . $AdvancedTopMenuElementsClass->id . '").attr("src","../img/admin/' . ($AdvancedTopMenuElementsClass->active ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activeelement","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activeelement","' . $this->l('Error during update element') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		} elseif (Tools::getValue('activeMobileMenu') && Tools::getValue('id_menu')) {
			$return = '';
			$ObjAdvancedTopMenuClass = new AdvancedTopMenuClass(Tools::getValue('id_menu'));
			$ObjAdvancedTopMenuClass->active_mobile = ($ObjAdvancedTopMenuClass->active_mobile ? 0 : 1);
			if ($ObjAdvancedTopMenuClass->save()) {
				$return .= '$("#imgActiveMobileMenu' . $ObjAdvancedTopMenuClass->id . '").attr("src","../img/admin/' . ($ObjAdvancedTopMenuClass->active_mobile ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activemobilemenu","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activemobilemenu","' . $this->l('Error during update menu') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		} elseif (Tools::getValue('activeMobileColumnWrap') && Tools::getValue('id_wrap')) {
			$return = '';
			$ObjAdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass(Tools::getValue('id_wrap'));
			$ObjAdvancedTopMenuColumnWrapClass->active_mobile = ($ObjAdvancedTopMenuColumnWrapClass->active_mobile ? 0 : 1);
			if ($ObjAdvancedTopMenuColumnWrapClass->save()) {
				$return .= '$("#imgActiveMobileColumnWrap' . $ObjAdvancedTopMenuColumnWrapClass->id . '").attr("src","../img/admin/' . ($ObjAdvancedTopMenuColumnWrapClass->active_mobile ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activemobilecolumnwrap","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activemobilecolumnwrap","' . $this->l('Error during update column') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		} elseif (Tools::getValue('activeMobileColumn') && Tools::getValue('id_column')) {
			$return = '';
			$ObjAdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass(Tools::getValue('id_column'));
			$ObjAdvancedTopMenuColumnClass->active_mobile = ($ObjAdvancedTopMenuColumnClass->active_mobile ? 0 : 1);
			if ($ObjAdvancedTopMenuColumnClass->save()) {
				$return .= '$("#imgActiveMobileColumn' . $ObjAdvancedTopMenuColumnClass->id . '").attr("src","../img/admin/' . ($ObjAdvancedTopMenuColumnClass->active_mobile ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activemobilegroup","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activemobilegroup","' . $this->l('Error during update group') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		} elseif (Tools::getValue('activeMobileElement') && Tools::getValue('id_element')) {
			$return = '';
			$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass(Tools::getValue('id_element'));
			$AdvancedTopMenuElementsClass->active_mobile = ($AdvancedTopMenuElementsClass->active_mobile ? 0 : 1);
			if ($AdvancedTopMenuElementsClass->save()) {
				$return .= '$("#imgActiveMobileElement' . $AdvancedTopMenuElementsClass->id . '").attr("src","../img/admin/' . ($AdvancedTopMenuElementsClass->active_mobile ? 'enabled' : 'disabled') . '.gif");';
				$return .= 'show_info("activemobileelement","' . $this->l('Saved') . '");';
			}
			else {
				$return .= 'show_info("activemobileelement","' . $this->l('Error during update element') . '");';
			}
			$this->clearCache();
			self::_cleanBuffer();
			echo $return;
			die();
		}
		elseif (Tools::getValue('deleteMenu') && Tools::getValue('id_menu')) {
			$ObjAdvancedTopMenuClass = new AdvancedTopMenuClass(Tools::getValue('id_menu'));
			if ($ObjAdvancedTopMenuClass->delete())
				$this->_html .= $this->displayConfirmation($this->l('Menu has been deleted'));
			else
				$this->errors [] = $this->l('Error during delete column');
			$this->clearCache();
		}
		elseif (Tools::getValue('deleteColumnWrap') && Tools::getValue('id_wrap')) {
			$ObjAdvancedTopMenuColumnWrapClass = new AdvancedTopMenuColumnWrapClass(Tools::getValue('id_wrap'));
			if ($ObjAdvancedTopMenuColumnWrapClass->delete())
				$this->_html .= $this->displayConfirmation($this->l('Column has been deleted'));
			else
				$this->errors [] = $this->l('Error during delete column');
			$this->clearCache();
		}
		elseif (Tools::getValue('deleteColumn') && Tools::getValue('id_column')) {
			$ObjAdvancedTopMenuColumnClass = new AdvancedTopMenuColumnClass(Tools::getValue('id_column'));
			if ($ObjAdvancedTopMenuColumnClass->delete())
				$this->_html .= $this->displayConfirmation($this->l('Group has been deleted'));
			else
				$this->errors [] = $this->l('Error during delete Group');
			$this->clearCache();
		}
		elseif (Tools::getValue('deleteElement') && Tools::getValue('id_element')) {
			$AdvancedTopMenuElementsClass = new AdvancedTopMenuElementsClass(Tools::getValue('id_element'));
			if ($AdvancedTopMenuElementsClass->delete())
				$this->_html .= $this->displayConfirmation($this->l('Item has been deleted'));
			else
				$this->errors [] = $this->l('Error during delete item');
			$this->clearCache();
		}
		elseif (isset($_POST ['submitMenu'])) {
			$this->_postProcessMenu();
			$this->clearCache();
		}
		elseif (isset($_POST ['submitColumnWrap'])) {
			$this->_postProcessColumnWrap();
			$this->clearCache();
		}
		elseif (isset($_POST ['submitColumn'])) {
			$this->_postProcessColumn();
			$this->clearCache();
		}
		elseif (isset($_POST ['submitElement'])) {
			$this->_postProcessColumnElement();
			$this->clearCache();
		}
		elseif (isset($_POST ['submitFastChangeColumn'])) {
			$id_column = Tools::getValue('id_column');
			$id_wrap = Tools::getValue('id_wrap');
			if ($id_wrap && $id_column) {
				$row = array ('id_wrap' => intval($id_wrap) );
				Db::getInstance()->AutoExecute(_DB_PREFIX_ . 'pm_advancedtopmenu_columns', $row, 'UPDATE', 'id_column =' . intval($id_column));
			}
			$this->clearCache();
		}
	}
	public function realStripTags4Smarty($str, $allowable_tags = false) {
		return strip_tags($str, $allowable_tags);
	}
	private function getKeepVar($vars = false) {
		if (! intval(Configuration::get('PS_REWRITING_SETTINGS'))) {
			if ($vars)
				parse_str($vars, $vars);
			else
				$vars = $_GET;
			foreach ( $this->keepVarActif as $key ) {
				if (isset($vars [$key])) {
					return '?' . $key . '=' . intval($vars [$key]);
				}
			}
		}
		return '';
	}
	private function getBorderSizeFromArray($borderArray) {
		if (! is_array($borderArray))
			return false;
		$borderStr = '';
		$borderCountEmpty = 0;
		foreach ( $borderArray as $border ) {
			if ($border === '')
				$borderCountEmpty ++;
			if ($border == 'auto')
				$borderStr .= 'auto ';
			else
				$borderStr .= intval($border) . 'px ';
		}
		return ($borderCountEmpty < count($borderArray) ? substr($borderStr, 0, - 1) : 0);
	}
	private function getPositionSizeFromArray($positionArray, $toCSSString = true) {
		if (!is_array($positionArray) || sizeof($positionArray) < 4)
			return '';
		$positionStr = '';
		if ($toCSSString) {
			if (strlen(trim($positionArray[0])) > 0)
				$positionStr .= 'top:' . intval($positionArray[0]) . 'px;';
			if (strlen(trim($positionArray[1])) > 0)
				$positionStr .= 'right:' . intval($positionArray[1]) . 'px;';
			if (strlen(trim($positionArray[2])) > 0)
				$positionStr .= 'bottom:' . intval($positionArray[2]) . 'px;';
			if (strlen(trim($positionArray[3])) > 0)
				$positionStr .= 'left:' . intval($positionArray[3]) . 'px;';
		} else {
			foreach ($positionArray as $position)
				if (strlen(trim($position)) > 0)
					$positionStr .= intval($position) . 'px ';
				else
					$positionStr .= ' ';
		}
		return $positionStr;
	}
	private function _getConfigKeys() {
		$config = $configResponsive = array();
		foreach ($this->_fieldsOptions as $key => $data)
			if (isset($data['mobile']) && $data['mobile'])
				$configResponsive[] = $key;
			else
				$config[] = $key;
		return array($config, $configResponsive);
	}
	function generateGlobalCss($id_shop = false) {
		list($config, $configResponsive) = $this->_getConfigKeys();
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $id_shop != false) {
			$configGlobalCss = Configuration::getMultiple($config, null, null, $id_shop);
			$configResponsiveCss = Configuration::getMultiple($configResponsive, null, null, $id_shop);
		} else {
			$configGlobalCss = Configuration::getMultiple($config);
			$configResponsiveCss = Configuration::getMultiple($configResponsive);
		}
		if (empty($configResponsiveCss['ATMR_MENU_BGCOLOR_OP'])) {
			$configResponsiveCss['ATMR_MENU_BGCOLOR_OP'] = $configGlobalCss['ATM_MENU_BGCOLOR_OVER'];
		}
		if (empty($configResponsiveCss['ATMR_MENU_BGCOLOR_CL'])) {
			$configResponsiveCss['ATMR_MENU_BGCOLOR_CL'] = $configGlobalCss['ATM_MENU_BGCOLOR'];
		}
		$css = array ();
		$configGlobalCss ['ATM_MENU_GLOBAL_BGCOLOR'] = explode($this->gradient_separator, $configGlobalCss ['ATM_MENU_GLOBAL_BGCOLOR']);
		if (isset($configGlobalCss ['ATM_MENU_GLOBAL_BGCOLOR'] [1])) {
			$color1 = htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8');
			$color2 = htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_BGCOLOR'] [1], ENT_COMPAT, 'UTF-8');
			$css [] = '#adtm_menu_inner {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
		}
		else
			$css [] = '#adtm_menu_inner {background-color:' . htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8') . ';}';
		$configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY'] = round($configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY'] / 100, 1);
		if ($configGlobalCss['ATM_MENU_CONT_POSITION'] == 'sticky') {
			$css [] = '#adtm_menu {position:relative;padding:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_MARGIN'], ENT_COMPAT, 'UTF-8') . ';border-color:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . ';border-width:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . ';-moz-box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; -webkit-box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; -o-box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; filter:progid:DXImageTransform.Microsoft.Shadow(color=' . htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], ENT_COMPAT, 'UTF-8') . ', Direction=137, Strength=0);}';
		} else {
			$css [] = '#adtm_menu {position:' . htmlentities($configGlobalCss['ATM_MENU_CONT_POSITION'], ENT_COMPAT, 'UTF-8') . ';padding:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_MARGIN'], ENT_COMPAT, 'UTF-8') . ';border-color:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . ';border-width:' . htmlentities($configGlobalCss ['ATM_MENU_CONT_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . ';-moz-box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; -webkit-box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; -o-box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; box-shadow: '. htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_MENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; filter:progid:DXImageTransform.Microsoft.Shadow(color=' . htmlentities($configGlobalCss['ATM_MENU_BOX_SHADOWCOLOR'], ENT_COMPAT, 'UTF-8') . ', Direction=137, Strength=0);}';
		}
		$configGlobalCss['ATM_MENU_CONT_POSITION_TRBL'] = $this->getPositionSizeFromArray(explode(' ', $configGlobalCss['ATM_MENU_CONT_POSITION_TRBL']));
		if (!empty($configGlobalCss['ATM_MENU_CONT_POSITION_TRBL']))
			$css [] = '#adtm_menu {' . htmlentities($configGlobalCss['ATM_MENU_CONT_POSITION_TRBL'], ENT_COMPAT, 'UTF-8') . '}';
		$css [] = '#adtm_menu_inner {padding:' . htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_MARGIN'], ENT_COMPAT, 'UTF-8') . ';border-color:' . htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . ';border-width:' . htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .li-niveau1 a.a-niveau1 {min-height:' . intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT']) . 'px;line-height:' . intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT']) . 'px;}';
		$css [] = '#adtm_menu .li-niveau1 a.a-niveau1.a-multiline {line-height:' . number_format(intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT'])/2, 2) . 'px;}';
		if ($configGlobalCss ['ATM_MENU_GLOBAL_WIDTH'])
			$css [] = '#adtm_menu_inner {width:' . htmlentities($configGlobalCss ['ATM_MENU_GLOBAL_WIDTH'], ENT_COMPAT, 'UTF-8') . 'px;}';
		$css [] = '#adtm_menu .li-niveau1 {min-height:' . intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT']) . 'px; line-height:' . (intval($configGlobalCss ['ATM_COLUMN_FONT_SIZE'])+ 5) . 'px;}';
		$css [] = '#adtm_menu .li-niveau1 a.a-niveau1 .advtm_menu_span {min-height:' . (intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT'])) . 'px;line-height:' . intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT']) . 'px;}';
		$css [] = '#adtm_menu .li-niveau1 a.a-niveau1.a-multiline .advtm_menu_span {line-height:' . number_format(intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT'])/2, 2) . 'px;}';
		$topDiff = 0;
		$atmMenuMarginTable = explode(' ', $configGlobalCss['ATM_MENU_MARGIN']);
		$atmMenuPaddingTable = explode(' ', $configGlobalCss['ATM_MENU_PADDING']);
		if (sizeof($atmMenuMarginTable) == 4)
			$topDiff += intval($atmMenuMarginTable[0]) + intval($atmMenuMarginTable[2]);
		if (sizeof($atmMenuPaddingTable) == 4)
			$topDiff += intval($atmMenuPaddingTable[0]) + intval($atmMenuPaddingTable[2]);
		$css [] = '#adtm_menu ul#menu li div.adtm_sub {top:' . intval($configGlobalCss ['ATM_MENU_GLOBAL_HEIGHT'] + $topDiff) . 'px;}';
		$css [] = '.li-niveau1 a span {padding:' . htmlentities($configGlobalCss ['ATM_MENU_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configGlobalCss ['ATM_MENU_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '.li-niveau1 .advtm_menu_span, .li-niveau1 a .advtm_menu_span {color:' . htmlentities($configGlobalCss ['ATM_MENU_COLOR'], ENT_COMPAT, 'UTF-8') . ';}';
		$configGlobalCss ['ATM_MENU_BGCOLOR'] = explode($this->gradient_separator, $configGlobalCss ['ATM_MENU_BGCOLOR']);
		if (isset($configGlobalCss ['ATM_MENU_BGCOLOR'] [1])) {
			$color1 = htmlentities($configGlobalCss ['ATM_MENU_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8');
			$color2 = htmlentities($configGlobalCss ['ATM_MENU_BGCOLOR'] [1], ENT_COMPAT, 'UTF-8');
			$css [] = '.li-niveau1 a .advtm_menu_span, .li-niveau1 .advtm_menu_span {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
		}
		else
			$css [] = '.li-niveau1 a .advtm_menu_span, .li-niveau1 .advtm_menu_span {background-color:' . htmlentities($configGlobalCss ['ATM_MENU_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8') . ';}';
		$configGlobalCss ['ATM_MENU_BGCOLOR_OVER'] = explode($this->gradient_separator, $configGlobalCss ['ATM_MENU_BGCOLOR_OVER']);
		if (isset($configGlobalCss ['ATM_MENU_BGCOLOR_OVER'] [1])) {
			$color1 = htmlentities($configGlobalCss ['ATM_MENU_BGCOLOR_OVER'] [0], ENT_COMPAT, 'UTF-8');
			$color2 = htmlentities($configGlobalCss ['ATM_MENU_BGCOLOR_OVER'] [1], ENT_COMPAT, 'UTF-8');
			$css [] = '.li-niveau1 a:hover .advtm_menu_span, .li-niveau1 a.advtm_menu_actif .advtm_menu_span, .li-niveau1 .advtm_menu_span:hover, .li-niveau1:hover > a.a-niveau1 .advtm_menu_span {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
		}
		else
			$css [] = '.li-niveau1 a:hover .advtm_menu_span, .li-niveau1 a.advtm_menu_actif .advtm_menu_span, .li-niveau1 .advtm_menu_span:hover, .li-niveau1:hover > a.a-niveau1 .advtm_menu_span {background-color:' . htmlentities($configGlobalCss ['ATM_MENU_BGCOLOR_OVER'] [0], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '.li-niveau1 a.a-niveau1 {border-color:' . htmlentities($configGlobalCss ['ATM_MENU_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . ';border-width:' . htmlentities($configGlobalCss ['ATM_MENU_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . ';}';
		$configGlobalCss['ATM_SUBMENU_BOX_SHADOWOPACITY'] = round($configGlobalCss['ATM_SUBMENU_BOX_SHADOWOPACITY'] / 100, 1);
		$css [] = '.li-niveau1 .adtm_sub {border-color:' . htmlentities($configGlobalCss ['ATM_SUBMENU_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . '; border-width:' . htmlentities($configGlobalCss ['ATM_SUBMENU_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . '; -moz-box-shadow: '. htmlentities($configGlobalCss['ATM_SUBMENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_SUBMENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_SUBMENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; -webkit-box-shadow: '. htmlentities($configGlobalCss['ATM_SUBMENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_SUBMENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_SUBMENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; -o-box-shadow: '. htmlentities($configGlobalCss['ATM_SUBMENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_SUBMENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_SUBMENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; box-shadow: '. htmlentities($configGlobalCss['ATM_SUBMENU_BOX_SHADOW'], ENT_COMPAT, 'UTF-8') .' ' . htmlentities($this->_hex2rgb($configGlobalCss['ATM_SUBMENU_BOX_SHADOWCOLOR'], $configGlobalCss['ATM_SUBMENU_BOX_SHADOWOPACITY']), ENT_COMPAT, 'UTF-8') . '; filter:progid:DXImageTransform.Microsoft.Shadow(color=' . htmlentities($configGlobalCss ['ATM_SUBMENU_BOX_SHADOWCOLOR'], ENT_COMPAT, 'UTF-8') . ', Direction=137, Strength=0);}';
		$configGlobalCss ['ATM_SUBMENU_BGCOLOR'] = explode($this->gradient_separator, $configGlobalCss ['ATM_SUBMENU_BGCOLOR']);
		if (isset($configGlobalCss ['ATM_SUBMENU_BGCOLOR'] [1])) {
			$color1 = htmlentities($configGlobalCss ['ATM_SUBMENU_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8');
			$color2 = htmlentities($configGlobalCss ['ATM_SUBMENU_BGCOLOR'] [1], ENT_COMPAT, 'UTF-8');
			$css [] = '.li-niveau1 .adtm_sub {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
		}
		else
			$css [] = '.li-niveau1 .adtm_sub {background-color:' . htmlentities($configGlobalCss ['ATM_SUBMENU_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8') . ';}';
		if ($configGlobalCss ['ATM_SUBMENU_WIDTH']) {
			$css [] = '.li-niveau1 .adtm_sub {width:' . htmlentities($configGlobalCss ['ATM_SUBMENU_WIDTH'], ENT_COMPAT, 'UTF-8') . 'px;}';
		}
		if ($configGlobalCss ['ATM_SUBMENU_HEIGHT']) {
			$css [] = '.li-niveau1 .adtm_sub {min-height:' . htmlentities($configGlobalCss ['ATM_SUBMENU_HEIGHT'], ENT_COMPAT, 'UTF-8') . 'px;}';
			$css [] = '* html .li-niveau1 .adtm_sub {height:' . htmlentities($configGlobalCss ['ATM_SUBMENU_HEIGHT'], ENT_COMPAT, 'UTF-8') . 'px;}';
			$css [] = '#adtm_menu div.adtm_column_wrap {min-height:' . htmlentities($configGlobalCss ['ATM_SUBMENU_HEIGHT'], ENT_COMPAT, 'UTF-8') . 'px;}';
			$css [] = '* html #adtm_menu div.adtm_column_wrap {height:' . htmlentities($configGlobalCss ['ATM_SUBMENU_HEIGHT'], ENT_COMPAT, 'UTF-8') . 'px;}';
		}
		if (isset($configGlobalCss['ATM_SUBMENU_OPEN_DELAY']) && $configGlobalCss['ATM_SUBMENU_OPEN_DELAY'] > 0)
			$css [] = '#adtm_menu ul#menu .li-niveau1:hover div.adtm_sub {transition-delay: '. $configGlobalCss['ATM_SUBMENU_OPEN_DELAY'] .'s;}';
		if (isset($configGlobalCss['ATM_SUBMENU_FADE_SPEED']) && $configGlobalCss['ATM_SUBMENU_FADE_SPEED'] > 0) {
			$css [] = '#adtm_menu ul#menu div.adtm_sub {opacity: 0;}';
			$css [] = '#adtm_menu ul#menu .li-niveau1:hover div.adtm_sub {opacity: 1;transition-property: opacity;transition-duration: '. $configGlobalCss['ATM_SUBMENU_FADE_SPEED'] .'s;}';
		}
		$css [] = '.adtm_column_wrap span.column_wrap_title, .adtm_column_wrap span.column_wrap_title a {color:' . htmlentities($configGlobalCss ['ATM_COLUMN_TITLE_COLOR'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '.adtm_column_wrap a {color:' . htmlentities($configGlobalCss ['ATM_COLUMN_ITEM_COLOR'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column_wrap {padding:' . htmlentities($configGlobalCss ['ATM_COLUMNWRAP_PADDING'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column {padding:' . htmlentities($configGlobalCss ['ATM_COLUMN_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configGlobalCss ['ATM_COLUMN_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column ul.adtm_elements li a {padding:' . htmlentities($configGlobalCss ['ATM_COLUMN_ITEM_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configGlobalCss ['ATM_COLUMN_ITEM_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column_wrap span.column_wrap_title {padding:' . htmlentities($configGlobalCss ['ATM_COLUMNTITLE_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configGlobalCss ['ATM_COLUMNTITLE_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .li-niveau1 a.a-niveau1 .advtm_menu_span {'. ($configGlobalCss['ATM_MENU_FONT_SIZE'] ? 'font-size:' . htmlentities($configGlobalCss ['ATM_MENU_FONT_SIZE'], ENT_COMPAT, 'UTF-8') . 'px;' : '') . ' font-weight:'. ($configGlobalCss['ATM_MENU_FONT_BOLD'] ? 'bold' : 'normal') . '; text-decoration:'. ($configGlobalCss['ATM_MENU_FONT_UNDERLINE'] ? 'underline' : 'none') . '; text-transform:' . htmlentities($configGlobalCss['ATM_MENU_FONT_TRANSFORM'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .li-niveau1 a.a-niveau1:hover .advtm_menu_span, #adtm_menu .li-niveau1 a.advtm_menu_actif .advtm_menu_span, .li-niveau1:hover > a.a-niveau1 .advtm_menu_span {color:' . htmlentities($configGlobalCss ['ATM_MENU_COLOR_OVER'], ENT_COMPAT, 'UTF-8') . '; text-decoration:'. ($configGlobalCss['ATM_MENU_FONT_UNDERLINEOV'] ? 'underline' : 'none') . ';}';
		if ($configGlobalCss ['ATM_MENU_FONT_FAMILY'])
			$css [] = '#adtm_menu .li-niveau1 a.a-niveau1 .advtm_menu_span {font-family:' . htmlentities($configGlobalCss ['ATM_MENU_FONT_FAMILY'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column span.column_wrap_title, #adtm_menu .adtm_column span.column_wrap_title a {'. ($configGlobalCss['ATM_COLUMN_FONT_SIZE'] ? 'font-size:' . htmlentities($configGlobalCss ['ATM_COLUMN_FONT_SIZE'], ENT_COMPAT, 'UTF-8') . 'px;' : '') . ' font-weight:'. ($configGlobalCss['ATM_COLUMN_FONT_BOLD'] ? 'bold' : 'normal') . '; text-decoration:'. ($configGlobalCss['ATM_COLUMN_FONT_UNDERLINE'] ? 'underline' : 'none') . '; text-transform:' . htmlentities($configGlobalCss['ATM_COLUMN_FONT_TRANSFORM'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column span.column_wrap_title:hover, #adtm_menu .adtm_column span.column_wrap_title a:hover {color:' . htmlentities($configGlobalCss ['ATM_COLUMN_TITLE_COLOR_OVER'], ENT_COMPAT, 'UTF-8') . '; text-decoration:'. ($configGlobalCss['ATM_COLUMN_FONT_UNDERLINEOV'] ? 'underline' : 'none') . ';}';
		if ($configGlobalCss ['ATM_COLUMN_FONT_FAMILY'])
			$css [] = '#adtm_menu .adtm_column span.column_wrap_title, #adtm_menu .adtm_column span.column_wrap_title a {font-family:' . htmlentities($configGlobalCss ['ATM_COLUMN_FONT_FAMILY'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column ul.adtm_elements li, #adtm_menu .adtm_column ul.adtm_elements li a {'. ($configGlobalCss['ATM_COLUMN_ITEM_FONT_SIZE'] ? 'font-size:' . htmlentities($configGlobalCss ['ATM_COLUMN_ITEM_FONT_SIZE'], ENT_COMPAT, 'UTF-8') . 'px;' : '') . ' font-weight:'. ($configGlobalCss['ATM_COLUMN_ITEM_FONT_BOLD'] ? 'bold' : 'normal') . '; text-decoration:'. ($configGlobalCss['ATM_COLUMN_ITEM_FONT_UNDERLINE'] ? 'underline' : 'none') . '; text-transform:' . htmlentities($configGlobalCss['ATM_COLUMN_ITEM_FONT_TRANSFORM'], ENT_COMPAT, 'UTF-8') . ';}';
		$css [] = '#adtm_menu .adtm_column ul.adtm_elements li:hover, #adtm_menu .adtm_column ul.adtm_elements li a:hover {color:' . htmlentities($configGlobalCss ['ATM_COLUMN_ITEM_COLOR_OVER'], ENT_COMPAT, 'UTF-8') . '; text-decoration:'. ($configGlobalCss['ATM_COLUMN_ITEM_FONT_UNDERLINEOV'] ? 'underline' : 'none') . ';}';
		if ($configGlobalCss ['ATM_COLUMN_ITEM_FONT_FAMILY'])
			$css [] = '#adtm_menu .adtm_column ul.adtm_elements li, #adtm_menu .adtm_column ul.adtm_elements li a {font-family:' . htmlentities($configGlobalCss ['ATM_COLUMN_ITEM_FONT_FAMILY'], ENT_COMPAT, 'UTF-8') . ';}';
		if (intval($configGlobalCss ['ATM_SUBMENU_POSITION']) == 1) {
			$css [] = '#adtm_menu ul#menu li.li-niveau1:hover, #adtm_menu ul#menu li.li-niveau1 a.a-niveau1:hover {position:relative;}';
		} else if (intval($configGlobalCss ['ATM_SUBMENU_POSITION']) == 2) {
			$css [] = '.li-niveau1 .adtm_sub {width: 100%}';
			$css [] = '#adtm_menu table.columnWrapTable {table-layout:fixed;}';
		}
		if ($configGlobalCss['ATM_MENU_GLOBAL_ZINDEX'])
			$css [] = '#adtm_menu {z-index:' . (int)$configGlobalCss ['ATM_MENU_GLOBAL_ZINDEX'] . ';}';
		if ($configGlobalCss['ATM_SUBMENU_ZINDEX'])
			$css [] = '.li-niveau1 .adtm_sub {z-index:' . (int)$configGlobalCss ['ATM_SUBMENU_ZINDEX'] . ';}';
		if ($configResponsiveCss['ATM_RESPONSIVE_MODE'] == 1 && (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] > 0) {
			$css [] = 'div#adtm_menu_inner {width: inherit !important;}';
			$css [] = '#adtm_menu ul .advtm_menu_toggle {display: none;}';
			$css [] = '@media (max-width: ' . (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] . 'px) {';
			$css [] = '#adtm_menu {position:relative; top:none; left:none; right:none; bottom:none;}';
			$css [] = '#adtm_menu .advtm_hide_mobile {display:none!important;}';
			$css [] = '#adtm_menu a.a-niveau1, #adtm_menu .advtm_menu_span { height: auto !important; }';
			$css [] = '#adtm_menu ul li.li-niveau1 {display: none;}';
			$css [] = '#adtm_menu ul li.advtm_menu_toggle {display: block; width: 100%;}';
			$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button {width: 100%; cursor: pointer;}';
			$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-position: right 15px center; background-repeat: no-repeat;}';
			$css [] = '#adtm_menu .searchboxATM {display: none;}';
			$css [] = '#adtm_menu .adtm_menu_icon { height: auto; max-width: 100%; }';
			$css [] = '#adtm_menu ul .li-niveau1 .adtm_sub {width: auto; height: auto; min-height: inherit;}';
			$css [] = '#adtm_menu ul div.adtm_column_wrap {min-height: inherit; width: 100% !important;}';
			if (isset($configResponsiveCss['ATM_RESP_TOGGLE_ICON']) && !empty($configResponsiveCss['ATM_RESP_TOGGLE_ICON']))
				$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-image: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . '); background-position: right 15px center; background-repeat: no-repeat;}';
			$css [] = '#adtm_menu .li-niveau1 a.a-niveau1 .advtm_menu_span {'. ($configResponsiveCss['ATM_RESP_MENU_FONT_SIZE'] ? 'font-size:' . htmlentities($configResponsiveCss['ATM_RESP_MENU_FONT_SIZE'], ENT_COMPAT, 'UTF-8') . 'px;' : '') . ' font-weight:'. ($configResponsiveCss['ATMR_MENU_FONT_BOLD'] ? 'bold' : 'normal') . '; text-transform:' . htmlentities($configResponsiveCss['ATMR_MENU_FONT_TRANSFORM'], ENT_COMPAT, 'UTF-8') . '; font-family:' . htmlentities($configResponsiveCss['ATMR_MENU_FONT_FAMILY'], ENT_COMPAT, 'UTF-8') . ';}';			
			$css [] = '#adtm_menu .adtm_column span.column_wrap_title, #adtm_menu .adtm_column span.column_wrap_title a {'. ($configResponsiveCss['ATM_RESP_COLUMN_FONT_SIZE'] ? 'font-size:' . htmlentities($configResponsiveCss ['ATM_RESP_COLUMN_FONT_SIZE'], ENT_COMPAT, 'UTF-8') . 'px;' : '') . ' font-weight:'. ($configResponsiveCss['ATMR_COLUMN_FONT_BOLD'] ? 'bold' : 'normal') . '; text-transform:' . htmlentities($configResponsiveCss['ATMR_COLUMN_FONT_TRANSFORM'], ENT_COMPAT, 'UTF-8') . '; font-family:' . htmlentities($configResponsiveCss['ATMR_COLUMN_FONT_FAMILY'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu .adtm_column ul.adtm_elements li, #adtm_menu .adtm_column ul.adtm_elements li a {'. ($configResponsiveCss['ATM_RESP_COLUMN_ITEM_FONT_SIZE'] ? 'font-size:' . htmlentities($configResponsiveCss ['ATM_RESP_COLUMN_ITEM_FONT_SIZE'], ENT_COMPAT, 'UTF-8') . 'px;' : '') . ' font-weight:'. ($configResponsiveCss['ATMR_COLUMN_ITEM_FONT_BOLD'] ? 'bold' : 'normal') . '; text-transform:' . htmlentities($configResponsiveCss['ATMR_COLUMN_ITEM_FONT_TRANSFORM'], ENT_COMPAT, 'UTF-8') . '; font-family:' . htmlentities($configResponsiveCss['ATMR_COLUMN_ITEM_FONT_FAMILY'], ENT_COMPAT, 'UTF-8') . ';}';
			if (isset($configResponsiveCss['ATM_RESP_TOGGLE_COLOR_OP']) && !empty($configResponsiveCss['ATM_RESP_TOGGLE_COLOR_OP']))
				$css [] = '#adtm_menu.adtm_menu_toggle_open ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {color:' . htmlentities($configResponsiveCss ['ATM_RESP_TOGGLE_COLOR_OP'], ENT_COMPAT, 'UTF-8') . ';}';
			if (isset($configResponsiveCss['ATM_RESP_TOGGLE_COLOR_CL']) && !empty($configResponsiveCss['ATM_RESP_TOGGLE_COLOR_CL']))
				$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {color:' . htmlentities($configResponsiveCss ['ATM_RESP_TOGGLE_COLOR_CL'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {'. ($configResponsiveCss['ATM_RESP_MENU_FONT_SIZE'] ? 'font-size:' . htmlentities($configResponsiveCss['ATM_RESP_TOGGLE_FONT_SIZE'], ENT_COMPAT, 'UTF-8') . 'px;' : '') . 'min-height:' . intval($configResponsiveCss['ATM_RESP_TOGGLE_HEIGHT']) . 'px;line-height:' . intval($configResponsiveCss['ATM_RESP_TOGGLE_HEIGHT']) . 'px;}';
			$configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_OP'] = explode($this->gradient_separator, $configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_OP']);
			if (isset($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_OP'] [1])) {
				$color1 = htmlentities($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_OP'][0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_OP'][1], ENT_COMPAT, 'UTF-8');
				if (isset($configResponsiveCss['ATM_RESP_TOGGLE_ICON']) && !empty($configResponsiveCss['ATM_RESP_TOGGLE_ICON']))
					$css [] = '#adtm_menu.adtm_menu_toggle_open li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
				else
					$css [] = '#adtm_menu.adtm_menu_toggle_open li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
			} else {
				$css [] = '#adtm_menu.adtm_menu_toggle_open li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-color:' . htmlentities($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_OP'][0], ENT_COMPAT, 'UTF-8') . ';}';
			}
			$configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_CL'] = explode($this->gradient_separator, $configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_CL']);
			if (isset($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_CL'] [1])) {
				$color1 = htmlentities($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_CL'][0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_CL'][1], ENT_COMPAT, 'UTF-8');
				if (isset($configResponsiveCss['ATM_RESP_TOGGLE_ICON']) && !empty($configResponsiveCss['ATM_RESP_TOGGLE_ICON']))
					$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_TOGGLE_ICON'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
				else
					$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
			} else {
				$css [] = '#adtm_menu ul li.advtm_menu_toggle a.adtm_toggle_menu_button span.adtm_toggle_menu_button_text {background-color:' . htmlentities($configResponsiveCss['ATM_RESP_TOGGLE_BG_COLOR_CL'][0], ENT_COMPAT, 'UTF-8') . ';}';
			}
			if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']))
				$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li.li-niveau1.sub a.a-niveau1 span {background-image: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center;}';
			if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']))
				$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li.li-niveau1.sub.adtm_sub_open a.a-niveau1 span {background-image: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center;}';
			$css [] = '.li-niveau1 a span {padding:' . htmlentities($configResponsiveCss['ATM_RESP_MENU_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configResponsiveCss['ATMR_MENU_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '.li-niveau1 a.a-niveau1 {border-color:' . htmlentities($configResponsiveCss['ATMR_MENU_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . ';border-width:' . htmlentities($configResponsiveCss['ATMR_MENU_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '.li-niveau1 .advtm_menu_span, .li-niveau1 a .advtm_menu_span {color:' . htmlentities($configResponsiveCss['ATMR_MENU_COLOR'], ENT_COMPAT, 'UTF-8') . ';}';
			$configResponsiveCss['ATMR_MENU_BGCOLOR_CL'] = explode($this->gradient_separator, $configResponsiveCss['ATMR_MENU_BGCOLOR_CL']);
			if (isset($configResponsiveCss['ATMR_MENU_BGCOLOR_CL'] [1])) {
				$color1 = htmlentities($configResponsiveCss['ATMR_MENU_BGCOLOR_CL'][0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($configResponsiveCss['ATMR_MENU_BGCOLOR_CL'][1], ENT_COMPAT, 'UTF-8');
				if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']))
					$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li.li-niveau1.sub a.a-niveau1 span {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
				$css [] = '.li-niveau1 a .advtm_menu_span, .li-niveau1 .advtm_menu_span {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
			} else {
				$css [] = '.li-niveau1 a .advtm_menu_span, .li-niveau1 .advtm_menu_span {background-color:' . htmlentities($configResponsiveCss['ATMR_MENU_BGCOLOR_CL'][0], ENT_COMPAT, 'UTF-8') . ';}';
			}
			$configResponsiveCss['ATMR_MENU_BGCOLOR_OP'] = explode($this->gradient_separator, $configResponsiveCss['ATMR_MENU_BGCOLOR_OP']);
			if (isset($configResponsiveCss['ATMR_MENU_BGCOLOR_OP'][1])) {
				$color1 = htmlentities($configResponsiveCss['ATMR_MENU_BGCOLOR_OP'][0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($configResponsiveCss['ATMR_MENU_BGCOLOR_OP'][1], ENT_COMPAT, 'UTF-8');
				if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']))
					$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li.li-niveau1.sub.adtm_sub_open a.a-niveau1 span {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
				$css [] = '#adtm_menu.adtm_menu_toggle_open .li-niveau1.sub.adtm_sub_open a .advtm_menu_span, .li-niveau1 a:hover .advtm_menu_span, .li-niveau1 a.advtm_menu_actif .advtm_menu_span, .li-niveau1 .advtm_menu_span:hover, .li-niveau1:hover > a.a-niveau1 .advtm_menu_span {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
			} else {
				$css [] = '#adtm_menu.adtm_menu_toggle_open .li-niveau1.sub.adtm_sub_open a .advtm_menu_span, .li-niveau1 a:hover .advtm_menu_span, .li-niveau1 a.advtm_menu_actif .advtm_menu_span, .li-niveau1 .advtm_menu_span:hover, .li-niveau1:hover > a.a-niveau1 .advtm_menu_span {background-color:' . htmlentities($configResponsiveCss['ATMR_MENU_BGCOLOR_OP'][0], ENT_COMPAT, 'UTF-8') . ';}';
			}
			$configResponsiveCss['ATMR_SUBMENU_BGCOLOR'] = explode($this->gradient_separator, $configResponsiveCss['ATMR_SUBMENU_BGCOLOR']);
			if (isset($configResponsiveCss['ATMR_SUBMENU_BGCOLOR'] [1])) {
				$color1 = htmlentities($configResponsiveCss['ATMR_SUBMENU_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8');
				$color2 = htmlentities($configResponsiveCss['ATMR_SUBMENU_BGCOLOR'] [1], ENT_COMPAT, 'UTF-8');
				$css [] = '.li-niveau1 .adtm_sub {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\'); background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '));background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . '); background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ');}';
			} else {
				$css [] = '.li-niveau1 .adtm_sub {background-color:' . htmlentities($configResponsiveCss['ATMR_SUBMENU_BGCOLOR'] [0], ENT_COMPAT, 'UTF-8') . ';}';
			}
			$css [] = '.li-niveau1 .adtm_sub {border-color:' . htmlentities($configResponsiveCss['ATMR_SUBMENU_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . '; border-width:' . htmlentities($configResponsiveCss['ATMR_SUBMENU_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu .adtm_column_wrap {padding:' . htmlentities($configResponsiveCss['ATMR_COLUMNWRAP_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configResponsiveCss['ATMR_COLUMNWRAP_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu .adtm_column_wrap_td {border-color:' . htmlentities($configResponsiveCss['ATMR_COLUMNWRAP_BORDERCOLOR'], ENT_COMPAT, 'UTF-8') . ';border-width:' . htmlentities($configResponsiveCss['ATMR_COLUMNWRAP_BORDERSIZE'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu .adtm_column {padding:' . htmlentities($configResponsiveCss['ATMR_COLUMN_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configResponsiveCss['ATMR_COLUMN_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu .adtm_column_wrap span.column_wrap_title {padding:' . htmlentities($configResponsiveCss['ATMR_COLUMNTITLE_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configResponsiveCss['ATMR_COLUMNTITLE_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '.adtm_column_wrap span.column_wrap_title, .adtm_column_wrap span.column_wrap_title a {color:' . htmlentities($configResponsiveCss['ATMR_COLUMN_TITLE_COLOR'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu .adtm_column ul.adtm_elements li a {padding:' . htmlentities($configResponsiveCss['ATMR_COLUMN_ITEM_PADDING'], ENT_COMPAT, 'UTF-8') . ';margin:' . htmlentities($configResponsiveCss['ATMR_COLUMN_ITEM_MARGIN'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '.adtm_column_wrap a {color:' . htmlentities($configResponsiveCss['ATMR_COLUMN_ITEM_COLOR'], ENT_COMPAT, 'UTF-8') . ';}';
			$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li.li-niveau1 {display: block;float: none;}';
			$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li.li-niveau1 a.a-niveau1 {float: none;}';
			$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li div.adtm_sub  {display: none; position: static; height: auto;}';
			$css [] = '#adtm_menu.adtm_menu_toggle_open ul#menu li div.adtm_sub.adtm_submenu_toggle_open  {display: block;}';
			$css [] = '#adtm_menu.adtm_menu_toggle_open table.columnWrapTable {display: table !important; width: 100% !important;}';
			$css [] = '#adtm_menu.adtm_menu_toggle_open table.columnWrapTable tr td {display: block;}';
			if (isset($configGlobalCss['ATM_SUBMENU_OPEN_DELAY']) && $configGlobalCss['ATM_SUBMENU_OPEN_DELAY'] > 0)
				$css [] = '#adtm_menu ul#menu .li-niveau1:hover div.adtm_sub {transition-delay: 0s;}';
			if (isset($configGlobalCss['ATM_SUBMENU_FADE_SPEED']) && $configGlobalCss['ATM_SUBMENU_FADE_SPEED'] > 0) {
				$css [] = '#adtm_menu ul#menu div.adtm_sub {opacity: 1;}';
				$css [] = '#adtm_menu ul#menu .li-niveau1:hover div.adtm_sub {transition-duration: 0s;}';
			}
			$css [] = '}';
		}
		$ids_shop = array(1);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			if ($id_shop != false) $ids_shop = array($id_shop);
			else $ids_shop = array_values(Shop::getContextListShopID());
		}
		$global_css_file = array();
		foreach ($ids_shop as $id_shop)
			$global_css_file[] = str_replace('.css','-'.$id_shop.'.css',dirname(__FILE__). '/' . self::GLOBAL_CSS_FILE);
		if (sizeof($css) && sizeof($global_css_file))
			foreach ($global_css_file as $value) file_put_contents($value, implode("\n", $css));
	}
	function generateCss() {
		list($config, $configResponsive) = $this->_getConfigKeys();
		$menus = AdvancedTopMenuClass::getMenus($this->_cookie->id_lang, true, true);
		$columnsWrap = AdvancedTopMenuColumnWrapClass::getColumnsWrap();
		$css = array ();
		foreach ( $menus as $menu ) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (int)$menu['id_shop'] != false) {
				$configGlobalCss = Configuration::getMultiple($config, null, null, (int)$menu['id_shop']);
				$configResponsiveCss = Configuration::getMultiple($configResponsive, null, null, (int)$menu['id_shop']);
			} else {
				$configGlobalCss = Configuration::getMultiple($config);
				$configResponsiveCss = Configuration::getMultiple($configResponsive);
			}
			if ($menu ['txt_color_menu_tab'])
				$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ' a .advtm_menu_span_' . $menu ['id_menu'] . ' {color:' . htmlentities($menu ['txt_color_menu_tab'], ENT_COMPAT, 'UTF-8') . '!important;}';
			if ($menu ['txt_color_menu_tab_hover']) {
				$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ':hover > a.a-niveau1 .advtm_menu_span_' . $menu ['id_menu'] . ' {color:' . htmlentities($menu ['txt_color_menu_tab_hover'], ENT_COMPAT, 'UTF-8') . '!important;}';
				$css [] = '* html .advtm_menu_' . $menu ['id_menu'] . ' a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', * html .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ' {color:' . htmlentities($menu ['txt_color_menu_tab_hover'], ENT_COMPAT, 'UTF-8') . '!important;}';
			}
			if ($menu ['fnd_color_menu_tab']) {
				$menu ['fnd_color_menu_tab'] = explode($this->gradient_separator, $menu ['fnd_color_menu_tab']);
				if (isset($menu ['fnd_color_menu_tab'] [1])) {
					$color1 = htmlentities($menu ['fnd_color_menu_tab'] [0], ENT_COMPAT, 'UTF-8');
					$color2 = htmlentities($menu ['fnd_color_menu_tab'] [1], ENT_COMPAT, 'UTF-8');
					$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' a .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;}';
					if ($configResponsiveCss['ATM_RESPONSIVE_MODE'] == 1 && (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] > 0) {
						if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']))
							$css [] = '@media (max-width: ' . (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] . 'px) { .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . ' a .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;} }';
						if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']))
							$css [] = '@media (max-width: ' . (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] . 'px) { .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . '.adtm_sub_open a .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;} }';
					}
				}
				else
					$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' a .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color:' . htmlentities($menu ['fnd_color_menu_tab'] [0], ENT_COMPAT, 'UTF-8') . '!important;filter: none!important; background: ' . htmlentities($menu ['fnd_color_menu_tab'] [0], ENT_COMPAT, 'UTF-8') . '!important;background: ' . htmlentities($menu ['fnd_color_menu_tab'] [0], ENT_COMPAT, 'UTF-8') . '!important;}';
			}
			if ($menu ['fnd_color_menu_tab_over']) {
				$menu ['fnd_color_menu_tab_over'] = explode($this->gradient_separator, $menu ['fnd_color_menu_tab_over']);
				if (isset($menu ['fnd_color_menu_tab_over'] [1])) {
					$color1 = htmlentities($menu ['fnd_color_menu_tab_over'] [0], ENT_COMPAT, 'UTF-8');
					$color2 = htmlentities($menu ['fnd_color_menu_tab_over'] [1], ENT_COMPAT, 'UTF-8');
					$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ':hover > a.a-niveau1 .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color: ' . $color1 . '!important;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;}';
					$css [] = '* html .advtm_menu_' . $menu ['id_menu'] . ' a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', * html .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color:transparent!important;background:transparent!important;filter:none!important;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important;}';
					if ($configResponsiveCss['ATM_RESPONSIVE_MODE'] == 1 && (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] > 0) {
						if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL']))
							$css [] = '@media (max-width: ' . (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] . 'px) { .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . ' a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ', .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . ':hover > a.a-niveau1 .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_CL'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;} }';
						if (isset($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']) && !empty($configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP']))
							$css [] = '@media (max-width: ' . (int)$configResponsiveCss['ATM_RESPONSIVE_THRESHOLD'] . 'px) { .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . '.adtm_sub_open a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . '.adtm_sub_open a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ', .adtm_menu_toggle_open .advtm_menu_' . $menu ['id_menu'] . '.adtm_sub_open:hover > a.a-niveau1 .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: url(' . $configResponsiveCss['ATM_RESP_SUBMENU_ICON_OP'] . ') no-repeat right 15px center, linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;} }';
					}
				}
				else {
					$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ':hover > a.a-niveau1 .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color:' . htmlentities($menu ['fnd_color_menu_tab_over'] [0], ENT_COMPAT, 'UTF-8') . '!important;filter: none!important; background: ' . htmlentities($menu ['fnd_color_menu_tab_over'] [0], ENT_COMPAT, 'UTF-8') . '!important;background: ' . htmlentities($menu ['fnd_color_menu_tab_over'] [0], ENT_COMPAT, 'UTF-8') . '!important;}';
					$css [] = '* html .advtm_menu_' . $menu ['id_menu'] . ' a:hover .advtm_menu_span_' . $menu ['id_menu'] . ', .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif .advtm_menu_span_' . $menu ['id_menu'] . ' {background-color:' . htmlentities($menu ['fnd_color_menu_tab_over'] [0], ENT_COMPAT, 'UTF-8') . '!important;filter:none!important;}';
					$css [] = '* html .advtm_menu_' . $menu ['id_menu'] . ' a:hover, .advtm_menu_' . $menu ['id_menu'] . ' a.advtm_menu_actif {filter:none!important;}';
				}
			}
			if ($menu ['border_size_tab']) {
				$css [] = 'li.advtm_menu_' . $menu ['id_menu'] . ' a.a-niveau1 {border-width:' . htmlentities($menu ['border_size_tab'], ENT_COMPAT, 'UTF-8') . '!important;}';
			}
			if ($menu ['border_color_tab']) {
				$css [] = 'li.advtm_menu_' . $menu ['id_menu'] . ' a.a-niveau1 {border-color:' . htmlentities($menu ['border_color_tab'], ENT_COMPAT, 'UTF-8') . '!important;}';
			}
			if ($menu ['width_submenu']) {
				$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' .adtm_sub {width:' . htmlentities($menu ['width_submenu'], ENT_COMPAT, 'UTF-8') . 'px!important;}';
			}
			if ($menu ['minheight_submenu']) {
				$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' .adtm_sub {min-height:' . htmlentities($menu ['minheight_submenu'], ENT_COMPAT, 'UTF-8') . 'px!important;}';
				$css [] = '* html .advtm_menu_' . $menu ['id_menu'] . ' .adtm_sub {height:' . htmlentities($menu ['minheight_submenu'], ENT_COMPAT, 'UTF-8') . 'px!important;}';
				$css [] = '#adtm_menu .advtm_menu_' . $menu ['id_menu'] . ' div.adtm_column_wrap {min-height:' . htmlentities($menu ['minheight_submenu'], ENT_COMPAT, 'UTF-8') . 'px!important;}';
				$css [] = '* html #adtm_menu .advtm_menu_' . $menu ['id_menu'] . ' div.adtm_column_wrap {height:' . htmlentities($menu ['minheight_submenu'], ENT_COMPAT, 'UTF-8') . 'px!important;}';
			}
			elseif ($menu ['minheight_submenu'] === '0') {
				$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' .adtm_sub {height:auto!important;min-height:0!important;}';
				$css [] = '#adtm_menu .advtm_menu_' . $menu ['id_menu'] . ' div.adtm_column_wrap {height:auto!important;min-height:0!important;}';
			}
			if ($menu ['position_submenu']) {
				if (intval($menu ['position_submenu']) == 1 || intval($menu ['position_submenu']) == 3)
					$css [] = '#adtm_menu ul#menu li.advtm_menu_' . $menu ['id_menu'] . ':hover, #adtm_menu ul#menu li.advtm_menu_' . $menu ['id_menu'] . ' a.a-niveau1:hover {position:relative!important;}';
				elseif (intval($menu ['position_submenu']) == 2)
					$css [] = '#adtm_menu ul#menu li.advtm_menu_' . $menu ['id_menu'] . ':hover, #adtm_menu ul#menu li.advtm_menu_' . $menu ['id_menu'] . ' a.a-niveau1:hover {position:static!important;}';
				if (intval($menu ['position_submenu']) == 3) {
					$css [] = '#adtm_menu ul#menu li.advtm_menu_' . $menu ['id_menu'] . ':hover div.adtm_sub {left:auto!important;right:0!important;}';
					$css [] = '#adtm_menu ul#menu li.advtm_menu_' . $menu ['id_menu'] . ' a:hover div.adtm_sub {left:auto!important;right:1px!important;}';
				}
			}
			if ($menu ['fnd_color_submenu']) {
				$menu ['fnd_color_submenu'] = explode($this->gradient_separator, $menu ['fnd_color_submenu']);
				if (isset($menu ['fnd_color_submenu'] [1])) {
					$color1 = htmlentities($menu ['fnd_color_submenu'] [0], ENT_COMPAT, 'UTF-8');
					$color2 = htmlentities($menu ['fnd_color_submenu'] [1], ENT_COMPAT, 'UTF-8');
					$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' .adtm_sub {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;}';
				}
				else
					$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' .adtm_sub {background-color:' . htmlentities($menu ['fnd_color_submenu'] [0], ENT_COMPAT, 'UTF-8') . '!important;filter: none!important; background: ' . htmlentities($menu ['fnd_color_submenu'] [0], ENT_COMPAT, 'UTF-8') . '!important;background: ' . htmlentities($menu ['fnd_color_submenu'] [0], ENT_COMPAT, 'UTF-8') . '!important;}';
			}
			if ($menu ['border_color_submenu'])
				$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' div.adtm_sub {border-color:' . htmlentities($menu ['border_color_submenu'], ENT_COMPAT, 'UTF-8') . '!important;}';
			if ($menu ['border_size_submenu']) {
				$css [] = '.advtm_menu_' . $menu ['id_menu'] . ' div.adtm_sub {border-width:' . htmlentities($menu ['border_size_submenu'], ENT_COMPAT, 'UTF-8') . '!important;}';
			}
		}
		foreach ( $columnsWrap as $columnWrap ) {
			if ($columnWrap ['bg_color']) {
				$columnWrap ['bg_color'] = explode($this->gradient_separator, $columnWrap ['bg_color']);
				if (isset($columnWrap ['bg_color'] [1])) {
					$color1 = htmlentities($columnWrap ['bg_color'] [0], ENT_COMPAT, 'UTF-8');
					$color2 = htmlentities($columnWrap ['bg_color'] [1], ENT_COMPAT, 'UTF-8');
					$css [] = '.advtm_column_wrap_td_' . $columnWrap ['id_wrap'] . ' {background-color: ' . $color1 . ';filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'' . $color1 . '\', endColorstr=\'' . $color2 . '\')!important; background: -webkit-gradient(linear, left top, left bottom, from(' . $color1 . '), to(' . $color2 . '))!important;background: -moz-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -ms-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: -o-linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important; background: linear-gradient(top, ' . $color1 . ', ' . $color2 . ')!important;}';
				}
				else
					$css [] = '.advtm_column_wrap_td_' . $columnWrap ['id_wrap'] . ' {background-color:' . htmlentities($columnWrap ['bg_color'] [0], ENT_COMPAT, 'UTF-8') . '!important;filter: none!important; background: ' . htmlentities($columnWrap ['bg_color'] [0], ENT_COMPAT, 'UTF-8') . '!important;background: ' . htmlentities($columnWrap ['bg_color'] [0], ENT_COMPAT, 'UTF-8') . '!important;}';
			}
			if ($columnWrap ['txt_color_column'])
				$css [] = '.advtm_column_wrap_' . $columnWrap ['id_wrap'] . ' span.column_wrap_title, .advtm_column_wrap_' . $columnWrap ['id_wrap'] . ' span.column_wrap_title a {color:' . htmlentities($columnWrap ['txt_color_column'], ENT_COMPAT, 'UTF-8') . '!important;}';
			if ($columnWrap ['txt_color_column_over'])
				$css [] = '.advtm_column_wrap_' . $columnWrap ['id_wrap'] . ' span.column_wrap_title a:hover {color:' . htmlentities($columnWrap ['txt_color_column_over'], ENT_COMPAT, 'UTF-8') . '!important;}';
			if ($columnWrap ['txt_color_element'])
				$css [] = '.advtm_column_wrap_' . $columnWrap ['id_wrap'] . ', .advtm_column_wrap_' . $columnWrap ['id_wrap'] . ' a {color:' . htmlentities($columnWrap ['txt_color_element'], ENT_COMPAT, 'UTF-8') . '!important;}';
			if ($columnWrap ['txt_color_element_over'])
				$css [] = '.advtm_column_wrap_' . $columnWrap ['id_wrap'] . ' a:hover {color:' . htmlentities($columnWrap ['txt_color_element_over'], ENT_COMPAT, 'UTF-8') . '!important;}';
			if ($columnWrap ['width'])
				$css [] = '.advtm_column_wrap_' . $columnWrap ['id_wrap'] . ' {width:' . htmlentities($columnWrap ['width'], ENT_COMPAT, 'UTF-8') . 'px!important;}';
		}
		$advanced_css_file = dirname(__FILE__).'/'.self::ADVANCED_CSS_FILE;
		$old_advanced_css_file_exists = file_exists($advanced_css_file);
		$ids_shop = array(1);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $ids_shop = array_values(Shop::getCompleteListOfShopsID());
		foreach ($ids_shop as $id_shop) {
			$advanced_css_file_shop = str_replace('.css','-'.$id_shop.'.css',$advanced_css_file);
			if (!$old_advanced_css_file_exists && !file_exists($advanced_css_file_shop)) {
				file_put_contents($advanced_css_file_shop, file_get_contents(dirname(__FILE__) . '/' . self::ADVANCED_CSS_FILE_RESTORE));
			} else if ($old_advanced_css_file_exists && sizeof($ids_shop) == 1 && !file_exists($advanced_css_file_shop)) {
				file_put_contents($advanced_css_file_shop, file_get_contents(dirname(__FILE__) . '/' . self::ADVANCED_CSS_FILE));
				@unlink(dirname(__FILE__).'/'.self::ADVANCED_CSS_FILE);
			} else if (!file_exists($advanced_css_file_shop)) {
				file_put_contents($advanced_css_file_shop, file_get_contents(dirname(__FILE__) . '/' . self::ADVANCED_CSS_FILE_RESTORE));
			}
		}
		$ids_shop = array(1);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $ids_shop = array_values(Shop::getCompleteListOfShopsID());
		$specific_css_file = array();
		foreach ($ids_shop as $id_shop)
			$specific_css_file[] = str_replace('.css','-'.$id_shop.'.css',dirname(__FILE__). '/' . self::DYN_CSS_FILE);
		if (sizeof($css) && sizeof($specific_css_file)) {
			foreach ($specific_css_file as $value) file_put_contents($value, implode("\n", $css));
		} else if (!sizeof($css) && sizeof($specific_css_file)) {
			foreach ($specific_css_file as $value) file_put_contents($value, '');
		}
	}
	protected function _hex2rgb($hexstr, $opacity = false) {
		if(strlen($hexstr) < 7) $hexstr = $hexstr.str_repeat(substr($hexstr, -1), 7-strlen($hexstr));
	    $int = hexdec($hexstr);
	    if ($opacity === false)
	    	return 'rgb(' . (0xFF & ($int >> 0x10)) . ', ' . (0xFF & ($int >> 0x8)) . ', ' . (0xFF & $int) . ')';
	    else
	    	return 'rgba(' . (0xFF & ($int >> 0x10)) . ', ' . (0xFF & ($int >> 0x8)) . ', ' . (0xFF & $int) . ', ' . $opacity . ')';
	}
	public function fetchWithCache($file, $template, $cacheid = NULL, $cache_lifetime = 0) {
		$previousTemplate = $this->_smarty->currentTemplate;
		$this->_smarty->currentTemplate = substr(basename($template), 0, - 4);
		$this->_smarty->assign('module_dir', __PS_BASE_URI__ . 'modules/' . basename($file, '.php') . '/');
		$this->_smarty->cache_lifetime = $cache_lifetime;
		if (file_exists(_PS_THEME_DIR_ . 'modules/' . basename($file, '.php') . '/' . $template)) {
			$this->_smarty->assign('module_template_dir', _THEME_DIR_ . 'modules/' . basename($file, '.php') . '/');
			$result = $this->_smarty->fetch(_PS_THEME_DIR_ . 'modules/' . basename($file, '.php') . '/' . $template, $cacheid);
		}
		elseif (file_exists(dirname($file) . '/' . $template)) {
			$this->_smarty->assign('module_template_dir', __PS_BASE_URI__ . 'modules/' . basename($file, '.php') . '/');
			$result = $this->_smarty->fetch(dirname($file) . '/' . $template, $cacheid);
		}
		else
			$result = '';
		$this->_smarty->currentTemplate = $previousTemplate;
		return $result;
	}
	private function _enableCachePM($level = 1) {
		if ((version_compare(_PS_VERSION_, '1.4.0.0', '<') && ! Configuration::get('ATM_CACHE')) || (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && ! Configuration::get('PS_SMARTY_CACHE')))
			return;
		if ($this->_smarty->force_compile == 0 and $this->_smarty->compile_check == 0 and $this->_smarty->caching == $level)
			return;
		self::$_forceCompile = ( int ) ($this->_smarty->force_compile);
		self::$_compileCheck = ( int ) ($this->_smarty->compile_check);
		self::$_caching = ( int ) ($this->_smarty->caching);
		$this->_smarty->force_compile = 0;
		$this->_smarty->compile_check = 0;
		$this->_smarty->caching = ( int ) ($level);
	}
	private function _restoreCacheSettingsPM() {
		if (isset(self::$_forceCompile))
			$this->_smarty->force_compile = ( int ) (self::$_forceCompile);
		if (isset(self::$_compileCheck))
			$this->_smarty->compile_check = ( int ) (self::$_compileCheck);
		if (isset(self::$_caching))
			$this->_smarty->caching = ( int ) (self::$_caching);
	}
	public function clearCache() {
		if (version_compare(_PS_VERSION_, '1.4.0.0', '<') || Configuration::get('PS_FORCE_SMARTY_2')) {
			$this->_smarty->clear_compiled_tpl(dirname(__FILE__) . '/pm_advancedtopmenu.tpl');
			return $this->_smarty->clear_cache(null, 'ADTM');
		} else {
			$this->_smarty->clearCompiledTemplate(dirname(__FILE__) . '/pm_advancedtopmenu.tpl');
			return $this->_smarty->clearCache(null, 'ADTM');
		}
		return true;
	}
	function hookHeader() {
		if($this->_isInMaintenance())
			return;
		$global_css_file = __PS_BASE_URI__ . 'modules/' . $this->name . '/' . self::GLOBAL_CSS_FILE;
		$specific_css_file = __PS_BASE_URI__ . 'modules/' . $this->name . '/' . self::DYN_CSS_FILE;
		$advanced_css_file = __PS_BASE_URI__ . 'modules/' . $this->name . '/' . self::ADVANCED_CSS_FILE;
		$global_css_file_path = dirname(__FILE__).'/'.self::GLOBAL_CSS_FILE;
		$specific_css_file_path = dirname(__FILE__).'/'.self::DYN_CSS_FILE;
		$advanced_css_file_path = dirname(__FILE__).'/'.self::ADVANCED_CSS_FILE;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$current_shop_id = (int)$this->_context->shop->id;
		else
			$current_shop_id = 1;
		$global_css_file = str_replace('.css','-'.$current_shop_id.'.css',$global_css_file);
		$global_css_file_path = str_replace('.css','-'.$current_shop_id.'.css',$global_css_file_path);
		$advanced_css_file = str_replace('.css','-'.$current_shop_id.'.css',$advanced_css_file);
		$advanced_css_file_path = str_replace('.css','-'.$current_shop_id.'.css',$advanced_css_file_path);
		$specific_css_file = str_replace('.css','-'.$current_shop_id.'.css',$specific_css_file);
		$specific_css_file_path = str_replace('.css','-'.$current_shop_id.'.css',$specific_css_file_path);
		$advtmIsSticky = (Configuration::get('ATM_MENU_CONT_POSITION') == 'sticky');
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $this->name . '/css/pm_advancedtopmenu_base.css', 'all');
			$this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $this->name . '/css/pm_advancedtopmenu_product.css', 'all');
			if (file_exists($global_css_file_path) && filesize($global_css_file_path) > 0) $this->context->controller->addCSS($global_css_file, 'all');
			if (file_exists($advanced_css_file_path) && filesize($advanced_css_file_path) > 0) $this->context->controller->addCSS($advanced_css_file, 'all');
			if (file_exists($specific_css_file_path) && filesize($specific_css_file_path) > 0) $this->context->controller->addCSS($specific_css_file, 'all');
			if ($advtmIsSticky) {
				$this->context->controller->addJS(__PS_BASE_URI__ . 'modules/' . $this->name . '/js/jquery.sticky.js');
			}
			$this->context->controller->addJS(__PS_BASE_URI__ . 'modules/' . $this->name . '/js/pm_advancedtopmenu.js');
			if (Configuration::get('ATM_AUTOCOMPLET_SEARCH')) $this->context->controller->addJqueryPlugin("autocomplete");
		} else if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
			Tools::addCSS(__PS_BASE_URI__ . 'modules/' . $this->name . '/css/pm_advancedtopmenu_base.css', 'all');
			Tools::addCSS(__PS_BASE_URI__ . 'modules/' . $this->name . '/css/pm_advancedtopmenu_product.css', 'all');
			if (file_exists($global_css_file_path) && filesize($global_css_file_path) > 0) Tools::addCSS($global_css_file, 'all');
			if (file_exists($advanced_css_file_path) && filesize($advanced_css_file_path) > 0) Tools::addCSS($advanced_css_file, 'all');
			if (file_exists($specific_css_file_path) && filesize($specific_css_file_path) > 0) Tools::addCSS($specific_css_file, 'all');
			if ($advtmIsSticky) {
				Tools::addJS(__PS_BASE_URI__ . 'modules/' . $this->name . '/js/jquery.sticky.js');
			}
			Tools::addJS(__PS_BASE_URI__ . 'modules/' . $this->name . '/js/pm_advancedtopmenu.js');
			if (Configuration::get('ATM_AUTOCOMPLET_SEARCH')) {
				Tools::addJS(_PS_JS_DIR_ . 'jquery/jquery.autocomplete.js');
				Tools::addCSS(_PS_CSS_DIR_ . 'jquery.autocomplete.css', 'all');
			}
		}
		return $this->display(__FILE__, 'pm_advancedtopmenu_header.tpl');
	}
	function hookActionObjectLanguageAddAfter($params) {
		$lang = $params['object'];
		if (Validate::isLoadedObject($lang)) {
			$res = Db::getInstance()->Execute('
				INSERT IGNORE INTO `' . _DB_PREFIX_ . 'pm_advancedtopmenu_elements_lang`
				(
					SELECT `id_element`, "'. (int)$lang->id .'" AS `id_lang`, `link`, `name`, `have_icon`, `image_type`, `image_legend`
					FROM `' . _DB_PREFIX_ . 'pm_advancedtopmenu_elements_lang`
					WHERE `id_lang` = '. (int)$this->_cookie->id_lang .'
				)
			');
			$res &= Db::getInstance()->Execute('
				INSERT IGNORE INTO `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_wrap_lang`
				(
					SELECT `id_wrap`, "'. (int)$lang->id .'" AS `id_lang`, `value_over`, `value_under`
					FROM `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_wrap_lang`
					WHERE `id_lang` = '. (int)$this->_cookie->id_lang .'
				)
			');
			$res &= Db::getInstance()->Execute('
				INSERT IGNORE INTO `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_lang`
				(
					SELECT `id_column`, "'. (int)$lang->id .'" AS `id_lang`, `name`, `value_over`, `value_under`, `link`, `have_icon`, `image_type`, `image_legend`
					FROM `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_lang`
					WHERE `id_lang` = '. (int)$this->_cookie->id_lang .'
				)
			');
			$res &= Db::getInstance()->Execute('
				INSERT IGNORE INTO `' . _DB_PREFIX_ . 'pm_advancedtopmenu_lang`
				(
					SELECT `id_menu`, "'. (int)$lang->id .'" AS `id_lang`, `name`, `value_over`, `value_under`, `link`, `have_icon`, `image_type`, `image_legend`
					FROM `' . _DB_PREFIX_ . 'pm_advancedtopmenu_lang`
					WHERE `id_lang` = '. (int)$this->_cookie->id_lang .'
				)
			');
			$newIsoLang = $lang->iso_code;
			$moduleRoot = _PS_ROOT_DIR_ . '/modules/' . $this->name;
			$elementsList = Db::getInstance()->ExecuteS('SELECT `id_element`, `image_type` FROM `' . _DB_PREFIX_ . 'pm_advancedtopmenu_elements_lang` WHERE `have_icon`=1 AND `id_lang` = '. (int)$this->_cookie->id_lang);
			if (self::_isFilledArray($elementsList)) {
				foreach ($elementsList as $image) {
					$src = $moduleRoot . '/element_icons/' . $image['id_element'] . '-' . $this->_iso_lang . '.' . $image['image_type'];
					$dest = $moduleRoot . '/element_icons/' . $image['id_element'] . '-' . $newIsoLang . '.' . $image['image_type'];
					if (file_exists($src)) {
						$res &= copy($src, $dest);
					}
				}
			}
			$columnsList = Db::getInstance()->ExecuteS('SELECT `id_column`, `image_type` FROM `' . _DB_PREFIX_ . 'pm_advancedtopmenu_columns_lang` WHERE `have_icon`=1 AND `id_lang` = '. (int)$this->_cookie->id_lang);
			if (self::_isFilledArray($columnsList)) {
				foreach ($columnsList as $image) {
					$src = $moduleRoot . '/column_icons/' . $image['id_column'] . '-' . $this->_iso_lang . '.' . $image['image_type'];
					$dest = $moduleRoot . '/column_icons/' . $image['id_column'] . '-' . $newIsoLang . '.' . $image['image_type'];
					if (file_exists($src)) {
						$res &= copy($src, $dest);
					}
				}
			}
			$menusList = Db::getInstance()->ExecuteS('SELECT `id_menu`, `image_type` FROM `' . _DB_PREFIX_ . 'pm_advancedtopmenu_lang` WHERE `have_icon`=1 AND `id_lang` = '. (int)$this->_cookie->id_lang);
			if (self::_isFilledArray($menusList)) {
				foreach ($menusList as $image) {
					$src = $moduleRoot . '/menu_icons/' . $image['id_menu'] . '-' . $this->_iso_lang . '.' . $image['image_type'];
					$dest = $moduleRoot . '/menu_icons/' . $image['id_menu'] . '-' . $newIsoLang . '.' . $image['image_type'];
					if (file_exists($src)) {
						$res &= copy($src, $dest);
					}
				}
			}
		}
	}
	function hookDisplayNav() {
		if (Configuration::get('ATM_MENU_CONT_HOOK') == 'nav') {
			return $this->outputMenuContent();
		}
	}
	function hookTop() {
		if (Configuration::get('ATM_MENU_CONT_HOOK') != 'nav') {
			return $this->outputMenuContent();
		}
	}
	function outputMenuContent() {
		if($this->_isInMaintenance())
			return;
		$return = '';
		$cache = Configuration::get('ATM_CACHE');
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && ! Configuration::get('PS_SMARTY_CACHE'))
			$cache = false;
		if ($cache) {
			if (Configuration::get('ATM_MENU_GLOBAL_ACTIF')) {
				$curUrl = explode('?', $_SERVER ['REQUEST_URI']);
				$curUrl = $curUrl [0] . $this->getKeepVar();
				$strCacheUrl = sha1(preg_replace('#https?://' . preg_quote(htmlspecialchars($_SERVER ['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__,'#') . '#i', '', $curUrl));
			}
			else
				$strCacheUrl = 'global';
			$adtmCacheId = sprintf('ADTM|%s|%d|%s|%d|%s', $strCacheUrl, $this->_cookie->id_lang, $this->_isLogged(), (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive() ? $this->_context->shop->id : 0), implode('-', self::getCustomerGroups()));
			$this->_enableCachePM(2);
		}
		if ((! $cache || (version_compare(_PS_VERSION_, '1.4.0.0', '<') && ! $this->_smarty->is_cached(dirname(__FILE__) . '/pm_advancedtopmenu.tpl', $adtmCacheId)) || (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && ! $this->isCached('pm_advancedtopmenu.tpl', $adtmCacheId)))) {
			$menus = AdvancedTopMenuClass::getMenus($this->_cookie->id_lang, true, false, true);
			if (! sizeof($menus)) {
				$this->_restoreCacheSettingsPM();
				return;
			}
			$columnsWrap = AdvancedTopMenuColumnWrapClass::getMenusColumnsWrap($menus, $this->_cookie->id_lang);
			$columns = AdvancedTopMenuColumnClass::getMenusColums($columnsWrap, $this->_cookie->id_lang, true);
			$elements = AdvancedTopMenuElementsClass::getMenuColumnsElements($columns, $this->_cookie->id_lang, true, true);
			$advtmThemeCompatibility = (bool)Configuration::get('ATM_THEME_COMPATIBILITY_MODE') && ((bool)Configuration::get('ATM_MENU_CONT_HOOK') != 'nav');
			$advtmResponsiveMode = ((bool)Configuration::get('ATM_RESPONSIVE_MODE') && (int)Configuration::get('ATM_RESPONSIVE_THRESHOLD') > 0);
			$advtmResponsiveToggleText = (Configuration::get('ATM_RESP_TOGGLE_TEXT', $this->_cookie->id_lang) !== false && Configuration::get('ATM_RESP_TOGGLE_TEXT', $this->_cookie->id_lang) != '' ? Configuration::get('ATM_RESP_TOGGLE_TEXT', $this->_cookie->id_lang) : $this->l('Menu'));
			$advtmResponsiveContainerClasses = trim(Configuration::get('ATM_RESP_CONT_CLASSES'));
			$advtmInnerClasses = trim(Configuration::get('ATM_INNER_CLASSES'));
			$advtmIsSticky = (Configuration::get('ATM_MENU_CONT_POSITION') == 'sticky');
			$this->_smarty->assign(array ('advtmIsSticky' => $advtmIsSticky, 'advtmInnerClasses' => $advtmInnerClasses, 'advtmResponsiveContainerClasses' => $advtmResponsiveContainerClasses, 'advtmResponsiveToggleText' => $advtmResponsiveToggleText, 'advtmResponsiveMode' => $advtmResponsiveMode, 'advtmThemeCompatibility' => $advtmThemeCompatibility, 'advtm_menus' => $menus, 'advtm_columns_wrap' => $columnsWrap, 'advtm_columns' => $columns, 'advtm_elements' => $elements, 'advtm_obj' => $this, 'isLogged' => $this->_isLogged() ));
		}
		if ($cache) {
			if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
				$return = $this->fetchWithCache(__FILE__, 'pm_advancedtopmenu.tpl', $adtmCacheId, 3600);
			else {
				$this->_smarty->cache_lifetime = 3600;
				$return = $this->display(__FILE__, 'pm_advancedtopmenu.tpl', $adtmCacheId);
			}
			$this->_restoreCacheSettingsPM();
			return $return;
		}
		else {
			$return = $this->display(__FILE__, 'pm_advancedtopmenu.tpl');
			$this->_smarty->caching = 0;
			return $return;
		}
	}
	public function hookCategoryUpdate() {
		$this->clearCache();
	}
	public static function _isFilledArray($array) {
		return ($array && is_array($array) && sizeof($array));
	}
	protected function _displayTitle($title) {
		$this->_html .= '<h2>' . $title . '</h2>';
	}
	protected $pm_lk = 'WQU27UWQV8-2BX3PNNB0O-BYJVIHOGIM';
	private function _getPMdata() {
		$param = array();
		$param[] = 'ver-'._PS_VERSION_;
		$param[] = 'current-'.$this->name;
		$param[] = 'lk-'.$this->pm_lk;
		$result = Db::getInstance()->ExecuteS('SELECT DISTINCT name FROM '._DB_PREFIX_.'module WHERE name LIKE "pm_%"');
		if ($result && self::_isFilledArray($result)) {
			foreach ($result as $module) {
				$instance = Module::getInstanceByName($module['name']);
				if ($instance && isset($instance->version)) $param[] = $module['name'].'-'.$instance->version;
			}
		}
		return urlencode(base64_encode(implode('|', $param)));
	}
	protected function _displayCS() {
		$this->_html .= '<div id="pm_panel_cs_modules_bottom" class="pm_panel_cs_modules_bottom"><br />';
		$this->_displayTitle($this->l('Check all our modules'));
		$this->_html .= '<iframe src="//www.presta-module.com/cross-selling-modules-footer?pm='.$this->_getPMdata().'" scrolling="no"></iframe></div>';
	}
	protected function _displaySupport() {
		$this->_html .= '<div id="pm_footer_container" class="ui-corner-all ui-tabs ui-tabs-panel">';
		$this->_displayCS();
		$this->_html .= '<div id="pm_support_informations" class="pm_panel_bottom"><br />';
		if (method_exists($this, '_displayTitle'))
			$this->_displayTitle($this->l('Information & Support', (isset($this->_coreClassName) ? $this->_coreClassName : false)));
		else
			$this->_html .= '<h2>' . $this->l('Information & Support', (isset($this->_coreClassName) ? $this->_coreClassName : false)) . '</h2>';
		$this->_html .= '<ul class="pm_links_block">';
		$this->_html .= '<li class="pm_module_version"><strong>' . $this->l('Module Version: ', (isset($this->_coreClassName) ? $this->_coreClassName : false)) . '</strong> ' . $this->version . '</li>';
		if (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started))
			$this->_html .= '<li class="pm_get_started_link"><a href="javascript:;" class="pm_link">'. $this->l('Getting started', (isset($this->_coreClassName) ? $this->_coreClassName : false)) .'</a></li>';
		if (self::_isFilledArray($this->_support_link))
			foreach($this->_support_link as $infos)
				$this->_html .= '<li class="pm_useful_link"><a href="'.$infos['link'].'" target="_blank" class="pm_link">'.$infos['label'].'</a></li>';
		$this->_html .= '</ul>';
		if (isset($this->_copyright_link) && $this->_copyright_link) {
			$this->_html .= '<div class="pm_copy_block">';
			if (isset($this->_copyright_link['link']) && !empty($this->_copyright_link['link'])) $this->_html .= '<a href="'.$this->_copyright_link['link'].'"'.((isset($this->_copyright_link['target']) AND $this->_copyright_link['target']) ? ' target="'.$this->_copyright_link['target'].'"':'').''.((isset($this->_copyright_link['style']) AND $this->_copyright_link['style']) ? ' style="'.$this->_copyright_link['style'].'"':'').'>';
			$this->_html .= '<img src="'.str_replace('_PATH_',$this->_path,$this->_copyright_link['img']).'" />';
			if (isset($this->_copyright_link['link']) && !empty($this->_copyright_link['link'])) $this->_html .= '</a>';
			$this->_html .= '</div>';
		}
		$this->_html .= '</div>';
		$this->_html .= '</div>';
		if (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started)) {
			$this->_html .= "<script type=\"text/javascript\">
			$('.pm_get_started_link a').click(function() { $.fancybox([";
			$get_started_image_list = array();
			foreach ($this->_getting_started as $get_started_image)
				$get_started_image_list[] = "{ 'href': '".$get_started_image['href']."', 'title': '".htmlentities($get_started_image['title'], ENT_QUOTES, 'UTF-8')."' }";
			$this->_html .= implode(',', $get_started_image_list);
			$this->_html .= "
					], {
					'padding'			: 0,
					'transitionIn'		: 'none',
					'transitionOut'		: 'none',
					'type'				: 'image',
					'changeFade'		: 0
				}); });
			</script>";
		}
		if (method_exists($this, '_includeHTMLAtEnd')) $this->_includeHTMLAtEnd();
	}
	protected function _addButton($text = '', $href = '', $onclick = false, $icon_class = false, $class = false, $title = '', $rel = false) {
			$curId = 'button_' . uniqid();
			$this->_html .= '<a href="' . htmlentities($href, ENT_COMPAT, 'UTF-8') . '" title="' . htmlentities($title, ENT_COMPAT, 'UTF-8') . '" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' . ($class ? ' ' . htmlentities($class, ENT_COMPAT, 'UTF-8') . '' : '') . '" id="' . $curId . '" ' . ($text ? 'style="padding-right:5px;"' : '') . ' ' . ($rel ? 'rel="' . $rel . '"' : '') . '>
	      ' . ($icon_class ? '<span class="' . htmlentities($icon_class, ENT_COMPAT, 'UTF-8') . '" style="float: left; margin-right: .3em;"></span>' : '') . '
	      ' . $text . '
	      </a>';
			if ($onclick)
				$this->_html .= '<script type="text/javascript">$("#' . $curId . '").unbind("click").bind("click", function() {
	        ' . $onclick . '
	      });</script>';
	}
	protected function _maintenanceButton() {
		$this->_html .= '<a href="' . $this->base_config_url . '&activeMaintenance=1" title="Maintenance" class="ajax_script_load ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id="buttonMaintenance" style="padding-right:5px;">';
		$this->_html .= '<span class="ui-icon ui-icon-wrench" style="float: left; margin-right: .3em;"></span>';
		$this->_html .= $this->l('Maintenance');
		$this->_html .= '<span id="pmImgMaintenance" class="ui-icon ui-icon-' . (Configuration::get('PM_' . self::$_module_prefix . '_MAINTENANCE') ? 'locked' : 'unlocked') . '" style="float: right; margin-left: .3em;">';
		$this->_html .= '</span>';
		$this->_html .= '</a>';
	}
	protected function _maintenanceWarning() {
		$ip_maintenance = Configuration::get('PS_MAINTENANCE_IP');
		$this->_html .= '<div id="maintenanceWarning" class="warning warn clear" ' . ((Configuration::get('PM_' . self::$_module_prefix . '_MAINTENANCE')) ? '' : 'style="display:none"') . '>
								<center>
								<img src="' . $this->_path . 'img/warning.png" style="padding-right:1em;"/>';
		if (! $ip_maintenance || empty($ip_maintenance)) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
				$tab_http_key = 'tab';
				$tab_http_value = 'AdminPreferences';
			} else {
				$tab_http_key = 'controller';
				$tab_http_value = 'AdminMaintenance';
			}
			$this->_html .= '<strong>' . $this->l('You must define a maintenance IP in your') . '
					<a href="index.php?'.$tab_http_key.'='.$tab_http_value.'&token=' . Tools::getAdminToken($tab_http_value . intval(Tab::getIdFromClassName($tab_http_value)) . intval($this->_employee->id)) . '" style="text-decoration:underline;">
					' . $this->l('Preferences Panel.') . '
					</a></strong><br />';
		}
		$this->_html .= $this->l('Module is currently running in Maintenance Mode.') . '';
		$this->_html .= '</center></div>';
		return $this->_html;
	}
	public function _postProcessMaintenance() {
		$return = '';
		$maintenance = Configuration::get('PM_' . self::$_module_prefix . '_MAINTENANCE');
		$maintenance = ($maintenance ? 0 : 1);
		Configuration::updateValue('PM_' . self::$_module_prefix . '_MAINTENANCE', intval($maintenance));
		if ($maintenance) {
			$return .= '$("#pmImgMaintenance").attr("class", "ui-icon ui-icon-locked");';
			$return .= '$("#maintenanceWarning").slideDown();';
			$return .= 'show_info("' . $this->l('Your module is now in maintenance mode.') . '");';
		} else {
			$return .= '$("#pmImgMaintenance").attr("class", "ui-icon ui-icon-unlocked");';
			$return .= '$("#maintenanceWarning").slideUp();';
			$return .= 'show_info("' . $this->l('Your module is now running in normal mode.') . '");';
		}
		$this->clearCache();
		self::_cleanBuffer();
		return $return;
	}
	protected function _isInMaintenance() {
		if(isset($this->_cacheIsInMaintenance))
			return $this->_cacheIsInMaintenance;
		if(Configuration::get('PM_'.self::$_module_prefix.'_MAINTENANCE')){
			$ips = explode(',',Configuration::get('PS_MAINTENANCE_IP'));
			if(in_array($_SERVER['REMOTE_ADDR'],$ips)){
				$this->_cacheIsInMaintenance = false;
				return false;
			}
			$this->_cacheIsInMaintenance = true;
			return true;
		}
		$this->_cacheIsInMaintenance = false;
		return false;
	}
	protected static function _cleanBuffer() {
		if (ob_get_length() > 0) ob_clean();
	}
	private function _isLogged() {
		$isLogged = false;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$isLogged = $this->_context->customer->isLogged();
		} else if (version_compare(_PS_VERSION_, '1.5.0.0', '<') && isset($this->_cookie) && is_object($this->_cookie)) {
			$isLogged = $this->_cookie->isLogged();
		}
		return $isLogged;
	}
	protected function _showRating($show = false) {
		$dismiss = (int)(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Configuration::getGlobalValue('PM_'.self::$_module_prefix.'_DISMISS_RATING') : Configuration::get('PM_'.self::$_module_prefix.'_DISMISS_RATING'));
		if ($show && $dismiss != 1 && self::_getNbDaysModuleUsage() >= 3) {
			$this->_html .= '
			<div id="addons-rating-container" class="ui-widget note">
				<div style="margin-top: 20px; margin-bottom: 20px; padding: 0 .7em; text-align: center;" class="ui-state-highlight ui-corner-all">
					<p class="invite">'
						. $this->l('You are satisfied with our module and want to encourage us to add new features ?')
						. '<br/>'
						. '<a href="http://addons.prestashop.com/ratings.php" target="_blank"><strong>'
						. $this->l('Please rate it on Prestashop Addons, and give us 5 stars !')
						. '</strong></a>
					</p>
					<p class="dismiss">'
						. '[<a href="javascript:void(0);">'
						. $this->l('No thanks, I don\'t want to help you. Close this dialog.')
						. '</a>]
					 </p>
				</div>
			</div>';
		}
	}
	protected static function _getNbDaysModuleUsage() {
		$sql = 'SELECT DATEDIFF(NOW(),date_add)
				FROM '._DB_PREFIX_.'configuration
				WHERE name = \''.pSQL('ATM_LAST_VERSION').'\'
				ORDER BY date_add ASC';
		return (int)Db::getInstance()->getValue($sql);
	}
	protected function _onBackOffice() {
		if (isset($this->_cookie->id_employee) && Validate::isUnsignedId($this->_cookie->id_employee)) return true;
		return false;
	}
	public static function getCustomerGroups() {
		$groups = array();
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Group::isFeatureActive()) {
			if (Validate::isLoadedObject(Context::getContext()->customer))
				$groups = FrontController::getCurrentCustomerGroups();
			else
				$groups = array((int)Configuration::get('PS_UNIDENTIFIED_GROUP'));
		} else if (version_compare(_PS_VERSION_, '1.4.0.2', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
			global $cookie;
			$groups = Customer::getGroupsStatic((int)$cookie->id_customer);
		} else if (version_compare(_PS_VERSION_, '1.4.0.2', '<') && version_compare(_PS_VERSION_, '1.2.5.0', '>=')) {
			global $cookie;
			$result = Db::getInstance()->ExecuteS('SELECT cg.`id_group` FROM '._DB_PREFIX_.'customer_group cg WHERE cg.`id_customer` = '.(int)$cookie->id_customer);
			if ($result && is_array($result))
				foreach ($result AS $group)
					$groups[] = (int)$group['id_group'];
		}
		sort($groups);
		return $groups;
	}
	protected static function getProductsImagesTypes() {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$a = array();
			foreach (ImageType::getImagesTypes('products') as $imageType)
				$a[$imageType['name']] = $imageType['name'] . ' (' . $imageType['width'] .' x ' . $imageType['height'] .' pixels)';
			return $a;
		} else {
			$result = Db::getInstance()->ExecuteS('
				SELECT `id_image_type`, `name`, `width`, `height`
				FROM `' . _DB_PREFIX_ . 'image_type`
				WHERE `products` = 1');
			$return = '';
			if ($result)
				foreach ( $result as $img )
					$return [$img ['name']] = preg_replace('/_default/', '', $img ['name']) . ' ('.$img ['width'].' x '.$img ['height'].') px';
			return $return;
		}
	}
}
?>
