
{**
* AccountShoppingListForm Template
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My Shopping List' mod='shoppinglist'}</span>{/capture}

<h1 class="page-heading">{l s='My Shopping List' mod='shoppinglist'}</h1>
<p>{$introduction}</p>

<div class="box">
    <form class="std" action="{$link->getModuleLink('shoppinglist', 'accountshoppinglist')}" method="post">
        <fieldset>
            <h1 class="page-subheading">{l s='Your List' mod='shoppinglist'}</h1>
            <input type="hidden" name="action" value="{$action}" />
            <input type="hidden" name="id_shopping_list" value="{$shoppingListObj->id_shopping_list}" />

            <p class="required text">
                <label>{l s='Title' mod='shoppinglist'}</label>
                <input type="text" name="title" value="{$shoppingListObj->title}" />
            </p>

        </fieldset>
            
        <button class="btn btn-default button button-medium" type="submit">
            <span>
                {$submit} <i class="icon-chevron-right right"></i>
            </span>
        </button>
    </form> 
</div>
            
