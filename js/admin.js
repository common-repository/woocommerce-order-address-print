jQuery(document).ready(function($) {
	
	/*
	 * Print button
	 */	 
	 	// Button on list and edit screen
	if(!sh_pl.show_prev && !sh_pl.export_doc )
	{
		
		$('.print-preview-button').printLink();
		
	}else
	{
		var bulkButton = $('#woocommerce-shipping-addresh-print a');
		if( bulkButton.length > 0 ) {
		var print_href=jQuery('#woocommerce-shipping-addresh-print a').attr('href');
		window.open(print_href);
		jQuery('#woocommerce-shipping-addresh-print a').trigger('click');
	}
		
	}	
	
	$('.print-preview-button').on('printLinkInit', function(event) {
		$(this).parent().find('.print-preview-loading').show();
	});
	$('.print-preview-button').on('printLinkComplete', function(event) {
		$('.print-preview-loading').hide();
	});
	$('.print-preview-button').on('printLinkError', function(event) {
		$('.print-preview-loading').hide();
		tb_show('', $(this).attr('href') + '&amp;TB_iframe=true&amp;width=800&amp;height=500');
	});

	/*
	 * Bulk actions print button in the confirm message
	 */	
	$(window).on('load', function(event) {
		var bulkButton = $('#woocommerce-shipping-addresh-print a');
		if( bulkButton.length > 0 ) {
			bulkButton.trigger('click');
			
		}
	});

	/*
	 * Settings
	 */	 
	 
	// Media managment
	
	
});

