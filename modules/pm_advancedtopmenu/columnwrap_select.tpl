{if isset($columnsWrap) && is_array($columnsWrap) && sizeof($columnsWrap) >= 1}
	<select name="id_wrap">
		<option>-- {l s='Choose' mod='pm_advancedtopmenu'} --</option>
		{foreach from=$columnsWrap item=columnWrap name=loop}
			<option value="{$columnWrap.id_wrap|intval}" {if $columnWrap_selected eq $columnWrap.id_wrap}selected=selected{/if}>{$columnWrap.internal_name}</option>
		{foreachelse}
			<option value="">{l s='No column' mod='pm_advancedtopmenu'}</option>
		{/foreach}
	</select>
	{if version_compare($smarty.const._PS_VERSION_, '1.5.0.0', '>=')}
		<script>$('input[name="submitColumn"]').removeAttr('disabled').prop('disabled', false);</script>
	{else}
		<script>$('input[name="submitColumn"]').removeAttr('disabled');</script>
	{/if}
{else}
	<div class="error inline-alert"><strong><u>{l s='Please select another parent tab!' mod='pm_advancedtopmenu'}</u></strong></div>
	{if version_compare($smarty.const._PS_VERSION_, '1.5.0.0', '>=')}
		<script>$('input[name="submitColumn"]').attr('disabled', 'disabled').prop('disabled', true);</script>
	{else}
		<script>$('input[name="submitColumn"]').attr('disabled', 'disabled');</script>
	{/if}
{/if}