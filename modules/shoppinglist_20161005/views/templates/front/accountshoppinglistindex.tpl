
{**
* AccountShoppingListIndex Template
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My Shopping List' mod='shoppinglist'}</span>{/capture}

<h1 class="page-heading">Mon r&eacute;f&eacute;rencement</h1>
<p>{l s='You find here a page who permit to manage all your shopping list' mod='shoppinglist'}</p><br />

{if $errors}
    <div class="error alert alert-danger">
        {foreach from=$errors item=error}
            <p>{$error}</p>
        {/foreach} 
    </div>
{/if}

{if $messages}
    <div class="warning alert alert-warning">
        {foreach from=$messages item=message}
            <p>{$message}</p>
        {/foreach} 
    </div>
{/if}

    
{if $shoppingList}
    <table id="shopping-list" class="std table table-bordered footab default footable-loaded footable">
        <thead>
            <tr>
                <!--<th>{l s='Reference' mod='shoppinglist'}</th>-->
                <th>{l s='Title' mod='shoppinglist'}</th>
                <th>{l s='Date Add' mod='shoppinglist'}</th>
                <th>{l s='Action' mod='shoppinglist'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$shoppingList item=itemList}
                <tr>
                   <!-- <td>{$itemList.id_shopping_list}</td>-->
                    <td>{$itemList.title}</td>
                    <td>{$itemList.date_add}</td>
                    <td>
                        <a class="btn btn-default button button-small" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['id_shopping_list' => $itemList.id_shopping_list])}">
                            <span>
                                {l s='See Products' mod='shoppinglist'}
                                <i class="icon-chevron-right right"></i>
                            </span>
                        </a>&nbsp;&nbsp;
                        <a class="btn btn-default button button-small" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'update', 'id_shopping_list' => $itemList.id_shopping_list])}">
                            <span>
                                {l s='Update' mod='shoppinglist'}
                                <i class="icon-chevron-right right"></i>
                            </span>
                        </a>&nbsp;&nbsp;
                        <a class="btn btn-default button button-small" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'delete', 'id_shopping_list' => $itemList.id_shopping_list])}">
                            <span>
                                {l s='Delete' mod='shoppinglist'}
                                <i class="icon-chevron-right right"></i>
                            </span>
                        </a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
            
    <a class="add-shopping-list btn btn-default button button-medium" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['action' => 'add'])}">
        <img class="icon" src="{$base_dir}modules/shoppinglist/img/add.png" alt="{l s='Add a shopping list' mod='shoppinglist'}">
        <span>Cr&eacute;er mon r&eacute;f&eacute;rencement<i class="icon-chevron-right right"></i></span>
    </a>
{/if}
