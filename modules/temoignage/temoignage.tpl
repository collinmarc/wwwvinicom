<div class="block temoignages">
	<h4>{l s='Témoignages clients' mod='temoignage'}</h4>
	<div class="block_content">
	{foreach from=$temoignages item=temoignage name=loop}
		<p class="temoignage">{$temoignage}</p>
	{/foreach}
		<div class="clear"></div>
	</div>
</div>