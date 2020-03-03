$(document).ready(function() {
	$(".temoignages .block_content p:not(:first)").each(function(){
		$(this).hide(); // cache les témoignages
	});
	$(".temoignages .block_content p:first").addClass("current");
	setTimeout("slide()", 3000);
});

function slide(){
	var prev = $(".temoignages .block_content p.current");
	if($(".temoignages .block_content p.current").next('p').size()>0){
		var next = $(".temoignages .block_content p.current").next('p');
	}else{
		var next = $(".temoignages .block_content p:first");
	}
	prev.removeClass("current");
	next.addClass("current");
	prev.fadeOut('500', function(){next.fadeIn('500');});	
	setTimeout("slide()", 3000);
}