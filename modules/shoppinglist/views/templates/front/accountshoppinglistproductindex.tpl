 
{**
* AccountShoppingListProductIndex Template
* 
* @author Marc Collin
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<!-- Module/ShoppingList/view/accountshoppinglistproductindex.tpl -->
<!-- Detail d'une shoppingList -->
{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='Mon Compte'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='Ma commande pré-établie' mod='shoppinglist'}</span>{/capture}

<!--<h1 class="page-heading">{l s='Ma commande pré-établie' mod='shoppinglist'}</h1>-->




    <script type="text/javascript" src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">
	{* fonction qui permet de modifider l'action à prendre en compte par le controleur *}
	<script type="text/javascript">
		function changeAction(value){
		document.getElementsByName("action")[0].value= value;
		}
	</script>

<Form id="ShoppingList" method="post" action="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct')}"> 

	{* Données cachées *}
	<P class = "hidden">
		<input  type="text" name="action" id="action" value="addAllToCart" />
		<!-- <input type="hidden" name="action" value="gotoCatalog" /> -->
		<input  name="id_shopping_list" value="{$shoppingListObj->id_shopping_list}" />
  </p>
{if $shoppingListProducts}
    <table id="shopping-list" class="table tableDnD">
        <thead>
            <tr>
                <th>{l s="Référence"}</th>
                <th>{l s= "Désignation"}</th>
                <th>{l s= "Couleur"}</th>
                <th>{l s= "Contenant"}</th>
                <th>{l s= "Millésime"}</th>
                <th>{l s= "U.C."}</th>
                <th>{l s= "cmd"}</th>
                <th align="center">{l s='Quantité'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$shoppingListProducts item=product}
                <tr>
                    <!--<td>{$product.id_product}</td>-->
                    <!--<td>{$product.id_product_attribute}</td>-->
                    <td>{$product.reference}</td>
                    <td>{$product.designation}</td>
                    <td>{$product.couleur}</td>
                    <td>{$product.contenant}</td>
                    <td>{$product.millesime}</td>
                    <td>{$product.conditionnement}</td>
                    <td>{$product.quantity}</td>
                    <td align="center">
                        {* création de la clé *}
                        {assign var='Key' value=$product.id_product|string_format:"Q%d"}
						{if isset($Quantities[$Key])}
							{* affectation de la quantité *}
							<input type="text" name="qty_{$product.id_product}" id="qty_{$product.id_product}" value='{$Quantities[$Key]}' style="width:50px;text-align:center">
						{else}
							<input type="text" name="qty_{$product.id_product}" id="qty_{$product.id_product}" value='' style="width:50px;text-align:center">
						{/if}

                        
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <p id="no-product">{l s='Pas de produit référencé' }</p>
{/if}

<ul class="action">
    {if $shoppingListProducts}
        <li>
			<!--Affichage de toutes les lignes avant de valider le forumlaires avec l'action addAllToCart-->
            <a class="add-all btn btn-default button button-medium" 
		   onclick="$('#shopping-list').DataTable().search('').draw();changeAction('addAllToCart');$(this).closest('form').submit()">
                <span>
                    <img class="icon" src="{$base_dir}modules/shoppinglist/img/add-product.png" alt="{l s='Visualiser la commande' mod='shoppinglist'}">{l s='Visualiser la commande'}<i class="icon-shopping-cart right"></i>
                </span>
            </a>
        </li>
    {/if}
			<!--Affichage de toutes les lignes avant de valider le forumlaires avec l'action gotocatalog-->
    <li>
       <a class="back-shopping-list btn btn-default button button-medium exclusive" 
	   onclick="$('#shopping-list').DataTable().search('').draw();changeAction('gotoCatalog'); $(this).closest('form').submit()" >
            <span>
                {l s='Accès au catalogue'}<i class="icon-chevron-left right"></i>
            </span>
        </a>
     </li>
</ul>
	</form>


<!--
attention avec les {} utilisés par Smarty et Javascript
https://datatables.net/forums/discussion/11939/resolved-datatables-smarty
-->
<script type="text/javascript">

    var table = $('#shopping-list').DataTable( {
	"autowidth":true,
	"paging":false, 
	"scrollY": 550, 
	"searching":true,
    "aoColumns": [
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":false },
    { "bSortable":false }
    ],

    "oLanguage": {
            "sProcessing":     "<div class=loading>Traitement en cours...</div>",
            "sSearch":         "Rechercher&nbsp;:",
            "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
            "sInfo":           "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "sInfoPostFix":    "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
            "oPaginate": {
              "sFirst":      "Premier",
              "sPrevious":   "Pr&eacute;c&eacute;dent",
              "sNext":       "Suivant",
              "sLast":       "Dernier"
            },
         "oAria": {
                "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
            }
    }

    });  
</script>


