
<div id="title-product" style="display:none;">{$title}</div>

<div class="shopping_list_block clear">
    <a class="add-shopping-list btn btn-default button button-small" title="{l s='Add to my shopping list' mod='shoppinglist'}">
        <span>
            <img class="icon" alt="{l s='Add to my shopping list' mod='shoppinglist'}" src="{$base_dir}modules/shoppinglist/img/add.png">
            <i class="icon-shopping-cart left"></i>
            {l s='Add to my shopping list' mod='shoppinglist'}
        </span>
    </a>
    {if $shoppingList}
        <ul>
            {foreach from=$shoppingList item=itemList}
                <li>
                    <a data-href="{$link->getModuleLink('shoppinglist', 'ajaxproductshoppinglist', ['id_shopping_list' => $itemList.id_shopping_list, 'static_token' => $static_token])}">{$itemList.title}</a>
                </li>
            {/foreach}
        </ul>
    {/if}
</div>