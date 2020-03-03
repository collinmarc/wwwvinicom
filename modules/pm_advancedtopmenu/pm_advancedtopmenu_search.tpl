<form method="get" action="{$atm_form_action_link}" class="searchboxATM">
<label for="{$atm_search_id}">{if $atm_withExtra && $atm_have_icon}<img src="{$atm_icon_image_source}" alt="" title="" class="adtm_menu_icon" />{/if}</label>
{if version_compare($smarty.const._PS_VERSION_, '1.5.0.0', '>=')}<input type="hidden" name="controller" value="search" />{/if}
<input type="hidden" name="orderby" value="position" />
<input type="hidden" name="orderway" value="desc" />
<input type="text" class="{if version_compare($smarty.const._PS_VERSION_, '1.6.0.0', '>=')}form-control {/if}search_query_atm" id="{$atm_search_id}" name="search_query" value="{$atm_search_value}" {if isset($atm_search_value) && !empty($atm_search_value)}onfocus="javascript:if(this.value=='{$atm_search_value}')this.value='';" onblur="javascript:if(this.value=='')this.value='{$atm_search_value}';"{/if} />
<input type="submit" name="submit_search" value="{l s='OK' mod='pm_advancedtopmenu'}" class="{if version_compare($smarty.const._PS_VERSION_, '1.6.0.0', '<')}button_mini{else}btn btn-default{/if}" />
</form>
{if $atm_is_autocomplete_search}
	{if version_compare($smarty.const._PS_VERSION_, '1.4.0.0', '<')}
		<script type="text/javascript">
			function formatSearch(row) {ldelim} return row[2] + " > " + row[1]; {rdelim}
			function redirectSearch(event, data, formatted) {ldelim} $("#{$atm_search_id}").val(data[1]); document.location.href = data[3]; {rdelim}
			$("document").ready( function() {ldelim}
				$("#{$atm_search_id}").autocomplete(
					"{$atm_form_action_link}", {ldelim}
					minChars: 3,
					max:10,
					selectFirst:false,
					width:500,
					scroll: false,
					formatItem:formatSearch,
					extraParams:{ldelim}
						ajaxSearch:1,
						id_lang: {$atm_cookie_id_lang}
					{rdelim}
				{rdelim}).result(redirectSearch)
			{rdelim});
		</script>
	{else}
		<script type="text/javascript">
			$("#{$atm_search_id}").autocomplete(
				"{$atm_pagelink_search}", {ldelim}
					minChars: 3,
					max: 10,
					width: 500,
					selectFirst: false,
					scroll: false,
					dataType: "json",
					formatItem: function(data, i, max, value, term) {ldelim} return value;	{rdelim},
					parse: function(data) {ldelim}
						var mytab = new Array();
						for (var i = 0; i < data.length; i++)
						mytab[mytab.length] = {ldelim} data: data[i], value: data[i].cname + ' > ' + data[i].pname {rdelim};
						return mytab;
					{rdelim},
					extraParams: {ldelim}
						ajaxSearch: 1,
						id_lang: {$atm_cookie_id_lang}
					{rdelim}
				{rdelim}
			).result(function(event, data, formatted) {ldelim}
				$('{$atm_search_id}').val(data.pname);
				document.location.href = data.product_link;
			{rdelim});
		</script>
	{/if}
{/if}