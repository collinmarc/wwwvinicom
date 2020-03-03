
{**
* AccountShoppingListDelete Template
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My Shopping List' mod='shoppinglist'}</span>{/capture}

<h1 class="page-heading">{l s='My Shopping List' mod='shoppinglist'}</h1>
<p>{l s='Are you sure, you want to delete this shopping list' mod='shoppinglist'} : {$shoppingListObj->title}?</p>

<div id="action-shopping-list">
    <a class="cancel-shopping-list btn btn-default button button-medium exclusive" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'index'])}">
        <span>{l s='Cancel' mod='shoppinglist'}</span>
    </a>
    <a class="validate-shopping-list btn btn-default button red button-medium" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'deleteConfirm', 'id_shopping_list' => $shoppingListObj->id_shopping_list])}">
        <span>{l s='Validate' mod='shoppinglist'}</span>
    </a>
</div>