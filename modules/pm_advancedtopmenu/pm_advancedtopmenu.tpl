<!-- MODULE PM_AdvancedTopMenu || Presta-Module.com -->
{if !isset($advtmThemeCompatibility) || (isset($advtmThemeCompatibility) && $advtmThemeCompatibility)}</div><div class="clear"></div>{/if}
<div id="adtm_menu"{if isset($advtmIsSticky) && $advtmIsSticky} data-sticky="1"{/if}{if isset($advtmResponsiveContainerClasses) && !empty($advtmResponsiveContainerClasses)} class="{$advtmResponsiveContainerClasses}"{/if}>
<div id="adtm_menu_inner"{if isset($advtmInnerClasses) && !empty($advtmInnerClasses)} class="{$advtmInnerClasses}"{/if}>
<ul id="menu">
{if isset($advtmResponsiveMode) && $advtmResponsiveMode}
	<li class="li-niveau1 advtm_menu_toggle">
		<a class="a-niveau1 adtm_toggle_menu_button"><span class="advtm_menu_span adtm_toggle_menu_button_text">{if isset($advtmResponsiveToggleText) && !empty($advtmResponsiveToggleText)}{$advtmResponsiveToggleText}{else}Menu{/if}</span></a>
	</li>
{/if}
{foreach from=$advtm_menus item=menu name=loop}
{if ($menu.privacy eq 2 && $isLogged) || ($menu.privacy eq 1 && !$isLogged) || (!$menu.privacy)}
{assign var='menuHaveSub' value=$advtm_columns_wrap[$menu.id_menu]|count}
{assign var='menuIsSearchBox' value=($menu.type == 6)}

{assign var='menuHaveAtLeastOneMobileSubMenu' value=0}
{foreach from=$advtm_columns_wrap[$menu.id_menu] item=column_wrap name=loop2}
	{if $column_wrap.active_mobile|intval}
		{assign var='menuHaveAtLeastOneMobileSubMenu' value=1}
		{break}
	{/if}
{/foreach}
<li class="li-niveau1 advtm_menu_{$menu.id_menu|intval}{if $menuHaveSub} sub{/if}{if $menuIsSearchBox} advtm_search{/if}{if !$menu.active_mobile|intval} advtm_hide_mobile{/if}{if !$menuHaveAtLeastOneMobileSubMenu} menuHaveNoMobileSubMenu{/if}">{$advtm_obj->getLinkOutputValue($menu,'menu',true,$menuHaveSub,true)}{if $menuHaveSub}<!--<![endif]-->
<!--[if lte IE 6]><table><tr><td><![endif]-->
	<div class="adtm_sub">
		{if trim($advtm_obj->realStripTags4Smarty($menu.value_over,'<object><img>'))}
			{$menu.value_over}
		{/if}
		<table class="columnWrapTable"><tr>
		{foreach from=$advtm_columns_wrap[$menu.id_menu] item=column_wrap name=loop2}
			{if ($column_wrap.privacy eq 2 && $isLogged) || ($column_wrap.privacy eq 1 && !$isLogged) || (!$column_wrap.privacy)}
				<td class="adtm_column_wrap_td advtm_column_wrap_td_{$column_wrap.id_wrap|intval}{if !$column_wrap.active_mobile|intval} advtm_hide_mobile{/if}">
				<div class="adtm_column_wrap advtm_column_wrap_{$column_wrap.id_wrap|intval}">
				{if trim($advtm_obj->realStripTags4Smarty($column_wrap.value_over,'<object><img>'))}
					{$column_wrap.value_over}
				{/if}
				<div class="adtm_column_wrap_sizer">&nbsp;</div>
				{foreach from=$advtm_columns[$column_wrap.id_wrap] item=column name=loop3}
					{if ($column.privacy eq 2 && $isLogged) || ($column.privacy eq 1 && !$isLogged) || (!$column.privacy)}
						{assign var='menuColumnWrapValue' value=$advtm_obj->getLinkOutputValue($column,'column',true)}
						{if trim($advtm_obj->realStripTags4Smarty($column.value_over,'<object><img>'))}
							{$column.value_over}
						{/if}
						<div class="adtm_column adtm_column_{$column.id_column|intval}{if !$column.active_mobile|intval} advtm_hide_mobile{/if}">
						{if $column.type == 8}
							{include file="./pm_advancedtopmenu_product.tpl" products=$column.productInfos}
						{else}
							{if $menuColumnWrapValue}<span class="column_wrap_title">{$menuColumnWrapValue}</span>{/if}
							{assign var='columnHaveElement' value=$advtm_elements[$column.id_column]|count}
							{if $columnHaveElement}
								<ul class="adtm_elements adtm_elements_{$column.id_column|intval}">
								{foreach from=$advtm_elements[$column.id_column] item=element name=loop3}
									{if ($element.privacy eq 2 && $isLogged) || ($element.privacy eq 1 && !$isLogged) || (!$element.privacy)}
										<li{if !$element.active_mobile|intval} class="advtm_hide_mobile"{/if}>{$advtm_obj->getLinkOutputValue($element,'element',true)}</li>
									{/if}
								{/foreach}
								</ul>
							{/if}
						{/if}
						</div>
						{if trim($advtm_obj->realStripTags4Smarty($column.value_under,'<object><img>'))}
							{$column.value_under}
						{/if}
					{/if}

				{/foreach}
				{if  trim($advtm_obj->realStripTags4Smarty($column_wrap.value_under,'<object><img>'))}
					{$column_wrap.value_under}
				{/if}
				</div>
				</td>
			{/if}
		{/foreach}
		</tr></table>
		{if trim($advtm_obj->realStripTags4Smarty($menu.value_under,'<object><img>'))}
			{$menu.value_under}
		{/if}
	</div>
<!--[if lte IE 6]></td></tr></table></a><![endif]-->
{/if}</li>
{/if}
{/foreach}
</ul>
</div>
</div>
{if !isset($advtmThemeCompatibility) || (isset($advtmThemeCompatibility) && $advtmThemeCompatibility)}<div>{/if}
<!-- /MODULE PM_AdvancedTopMenu || Presta-Module.com -->