{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- THEME/MODULE Block cart -->
{*** Suppression du block Cart, Remplacement pas un lien vers la shoppingList **}
<div class="">
<a class="shopping_cart btn btn-default button button-medium exclusive" 
href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['id_shopping_list' => $id_shopping_list])}">
<i class="icon-heart" padding-right=100px></i>
{l s='  Ma commande pré-établie'}
</a>
</div>
<!-- /MODULE Block cart -->
