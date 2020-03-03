
/**
* JS File of module
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

$(document).ready(function() {
    //Display shopping list on hover (plus utilisé)
    $(".shopping_list_block").hover(function() {
        $(this).find('ul').slideDown();
    }, function() {
        $(this).find('ul').slideUp();
    })
    
//Click sur les boutons AddOrRemoveShoppingList 
$(".addOrRemove-shopping-list").click(function() {
		// Récupération des données
        $id = $(this).attr('id');
		$id_product = $id.split("_")[1];
		$action = $id.split("_")[2];
        $href = $(this).attr('data-href');
        $id_shoppinglist = $('#id_shoppinglist_'+$id_product).val();
        $token = $('#token_'+$id_product).val();
        $reference = $('#product_reference_'+$id_product).val();
        $attribute = $('#id_product_attribute_'+$id_product).val();
        $title = $('#product_title_'+$id_product).val();

			if ($action == "add")
			{
				$('#shoppinglist_'+$id_product+'_add').slideToggle();
			}
			if ($action == "remove")
			{
				$('#shoppinglist_'+$id_product+'_remove').slideToggle();
			}
//		$("html,body").css("cursor", "wait");
//				$('#addShoppinglist_'+$id_product).slideToggle();
		//Appel de l'url passé dans data-href
        $.ajax({
            url: $href,
            type: 'POST',
            dataType: 'json',
            data: {
                id_product: $id_product,
                id_product_attribute: $attribute,
                title: $title + $reference,
                idShoppingList: $id_shoppinglist,
                token: $token,
                ajax: true
            },
            success: function(msg){
			if ($action == "add")
			{
				$('#shoppinglist_'+$id_product+'_remove').slideToggle();
			}
			if ($action == "remove")
			{
				$('#shoppinglist_'+$id_product+'_add').slideToggle();
			}
				$('#iconInShoppingList_'+$id_product).slideToggle();
                alert(msg.result);
            },
            error: function(msg){
                alert(msg.result);
            },
        });

		/*
		
				$('#addShoppinglist_'+$id_product).slideToggle();
				$('#removeShoppinglist_'+$id_product).slideToggle();
				$('#iconInShoppingList_'+$id_product).slideToggle();
		*/
    }); // add-shopping-list.click
	
});