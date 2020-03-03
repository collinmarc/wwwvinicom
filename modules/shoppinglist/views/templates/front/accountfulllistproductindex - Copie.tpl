 
{**
* AccountFullListProductIndex Template
* 
* Adaptation de la shopping list créée par Olivier Michaud et modifiée par VEZIM
* dans le cadre du projet Vinicom
*
* @author Pascal Véron
* @copyright  VEZIM SARL
*
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My Shopping List' mod='shoppinglist'}</span>{/capture}

<h1 class="page-heading">Catalogue Vinicom</h1>
<p>Utiliser cette page pour ajouter rapidement des références à votre panier.</p><br />

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

{if $fullListProducts}

    <div id="spinner" style="z-index:999; position:absolute; top:0; bottom:0; left:0; right:0; margin:auto; display:none; width:400px;  height:130px; background-color:#A71E4C; color:#FFF; text-align: center; padding-top:30px" ><h2><i class="icon-spinner icon-spin icon-large"></i> Ajout au panier</h2></div>


    <script type="text/javascript" src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">

    <table id="shopping-list" class="table tableDnD">
        <thead>
            <tr>
                <th>R&eacute;f&eacute;rence</th>
                <th>D&eacute;signation</th>
                <th>Couleur</th>
                <th>Contenant</th>
                <th>Millésime</th>
                <th>Opération</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$fullListProducts item=itemList}
                <tr>
                    <td>{$itemList.reference}</td>
                    <td>{$itemList.title}</td>
                    <td>{$itemList.couleur}</td>
                    <td>{$itemList.contenant}</td>
                    <td>{$itemList.millesime}</td>
                    <td>
                        <input type="text" name="qty_{$itemList.id_product}" id="qty_{$itemList.id_product}" value="1" style="width:25px;text-align:center">
                        {if $itemList.minimal_quantity}
                        {$itemList.minimal_quantity}
                        {else}
                        x1
                        {/if}
                        <a class="quickAddToBasket btn btn-default button button-small" id="{$itemList.id_product}" data-attribute="{$itemList.id_product_attribute}">
                            <span>
                                Ajouter au panier
                            </span>
                        </a>
                        
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <p id="no-product">Aucun produit dans le catalogue</p>
{/if}

<!--
attention avec les {} utilisés par Smarty et Javascript
https://datatables.net/forums/discussion/11939/resolved-datatables-smarty
-->
<script type="text/javascript">
    ajaxCart.refresh(); 

    $('.quickAddToBasket').click(function() {
        var id_product = $(this).attr("id");
        var id_product_attribute = $(this).attr("data-attribute");
        var qty = $('#qty_'+id_product).val();
        $('#spinner').show();
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: "action=addOneToCart&add=1&qty="+qty+"&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&fc=module&module=shoppinglist&controller=accountfulllistproduct",   
            success: function(json) {
                ajaxCart.refresh(); 
            }
        }).done(function(){
            $('#spinner').hide();
        });

    } );

    var table = $('#shopping-list').DataTable( {

    "aoColumns": [
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
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


