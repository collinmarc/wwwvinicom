{if isset($columns) && is_array($columns) && sizeof($columns) >= 1}
	<select name="id_column">
		<option>-- {l s='Choose' mod='pm_advancedtopmenu'} --</option>
		{foreach from=$columns item=column name=loop}
			<option value="{$column.id_column|intval}" {if $column_selected eq $column.id_column}selected=selected{/if}>{$objADTM->getAdminOutputNameValue($column,false)}</option>
		{foreachelse}
			<option value="">{l s='No column' mod='pm_advancedtopmenu'}</option>
		{/foreach}
	</select>
	{if version_compare($smarty.const._PS_VERSION_, '1.5.0.0', '>=')}
		<script>$('input[name="submitElement"]').removeAttr('disabled').prop('disabled', false);</script>
	{else}
		<script>$('input[name="submitElement"]').removeAttr('disabled');</script>
	{/if}
{else}
	<div class="error inline-alert"><strong><u>{l s='Please select another parent tab!' mod='pm_advancedtopmenu'}</u></strong></div>
	{if version_compare($smarty.const._PS_VERSION_, '1.5.0.0', '>=')}
		<script>$('input[name="submitElement"]').attr('disabled', 'disabled').prop('disabled', true);</script>
	{else}
		<script>$('input[name="submitElement"]').attr('disabled', 'disabled');</script>
	{/if}
{/if}