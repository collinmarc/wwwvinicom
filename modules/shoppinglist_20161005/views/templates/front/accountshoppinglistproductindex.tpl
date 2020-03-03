 
{**
* AccountShoppingListProductIndex Template
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My Shopping List' mod='shoppinglist'}</span>{/capture}

<h1 class="page-heading">Ma Pr&eacute;-commande</h1>
<p>{l s='You find here all your product' mod='shoppinglist'} r&eacute;f&eacute;renc&eacute;s</p><br />

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

{if $shoppingListProducts}
    <table id="shopping-list" class="std table table-bordered footab footable-loaded footable tablet breakpoint">
        <thead>
            <tr>
                <th>N&deg; produit</th>
                <!--<th>{l s='Itemisation' mod='shoppinglist'}</th>-->
                <th>D&eacute;signation</th>
                <th>{l s='Action' mod='shoppinglist'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$shoppingListProducts item=itemList}
                <tr>
                    <td>{$itemList.id_product}</td>
                    <!--<td>{$itemList.id_product_attribute}</td>-->
                    <td>{$itemList.title}</td>
                    <td>
                        <a class="btn btn-default button button-small" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addOneToCart', 'add' => '1', 'id_shopping_list' => $itemList.id_shopping_list, 'id_product' => $itemList.id_product, 'id_product_attribute' => $itemList.id_product_attribute])}">
                            <span>
                                Commander le produit
                                <i class="icon-chevron-right right"></i>
                            </span>
                        </a>

                        <!--<a class="btn btn-default button button-small" href="{$link->getProductLink($itemList.id_product, null, null, null, null, null, $itemList.id_product_attribute)}" target="_blank">
                            <span>
                                {l s='See' mod='shoppinglist'}
                                <i class="icon-chevron-right right"></i>
                            </span>-->
                        </a>
                        <a class="btn btn-default button button-small" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'delete', 'id_shopping_list' => $itemList.id_shopping_list, 'id_product' => $itemList.id_product, 'id_product_attribute' => $itemList.id_product_attribute])}?id_product={$itemList.id_product}">
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
{else}
    <p id="no-product">{l s='No product in this shopping list' mod='shoppinglist'}</p>
{/if}

<ul class="action">
    {if $shoppingListProducts}
        <li>
            <a class="add-all btn btn-default button button-medium" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addAllToCart', 'id_shopping_list' => $shoppingListObj->id_shopping_list])}">
                <span>
                    <img class="icon" src="{$base_dir}modules/shoppinglist/img/add-product.png" alt="{l s='Add all products to cart' mod='shoppinglist'}">
                    Commander tous les produits<i class="icon-shopping-cart right"></i>
                </span>
            </a>
        </li>
    {/if}
    <li>
        <a class="back-shopping-list btn btn-default button button-medium exclusive" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglist', ['id_shopping_list' => $shoppingListObj->id_shopping_list])}">
            <span>
                <img class="icon" src="{$base_dir}modules/shoppinglist/img/back.png" alt="{l s='Back to list' mod='shoppinglist'}">
                {l s='Back to list' mod='shoppinglist'}<i class="icon-chevron-left right"></i>
            </span>
        </a>
    </li>
</ul>


<script type="text/javascript">
    ajaxCart.refresh();
</script>
