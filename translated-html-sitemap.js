jQuery(document).ready(function(){
	jQuery( "#translatedhs-sortable" ).sortable({
		placeholder: "translatedhs-ui-state-highlight",

		update: function(event, ui) {
				var fruitOrder = jQuery("#translatedhs-sortable").sortable('toArray').toString();
				jQuery('#translatedhs-sortorder').val(fruitOrder);
				//$.get('update-sort.cfm', {fruitOrder:fruitOrder});
			}
	});
	
	jQuery('.translatedhs_changename').click(function(){
		jQuery(this).next().fadeIn();
		return false;
	});
	
	jQuery('a.translated-save-newname').click(function(){
		if( jQuery(this).prev().val() == '' ){
			originalname = jQuery(this).parent().parent().next().next().html();
			jQuery(this).prev().val(originalname);
			jQuery(this).parent().prev().prev().html(originalname);
		} else {
			jQuery(this).parent().prev().prev().html( jQuery(this).prev().val() );
		}
		jQuery(this).parent().fadeOut();
		return false;
	});
	
	jQuery('a.translated-cancel-newname').click(function(){
		jQuery(this).prev().prev().val( jQuery(this).parent().prev().prev().html() );
		jQuery(this).parent().fadeOut();
		return false;
	});
	
	
	
	
	//jQuery( "#translatedhs-sortable" ).disableSelection();
});
