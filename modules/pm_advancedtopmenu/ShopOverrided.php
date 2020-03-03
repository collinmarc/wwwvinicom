<?php
if (version_compare(_PS_VERSION_, '1.5', '>=') && version_compare(_PS_VERSION_, '1.5.2.0', '<=') && !class_exists ("ShopPrestaModule")) {
	class ShopPrestaModule extends ShopCore {
		// Abilty to add module shop asso table
		public static function PrestaModule_setAssoTable($table, $type = 'shop') {
			Shop::$asso_tables[$table] = array('type' => $type);
		}
	}
}
?>