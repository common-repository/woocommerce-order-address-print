jQuery(document).ready(function($) {

	function set_table_size() {
		var paper_size = $('#paper_size').val();
		var paper_orientation = $('#paper_orientation').val();

		switch(paper_size) {
		case 'a4':
			$('#custom_paper_size_width').val('210');
			$('#custom_paper_size_height').val('297');
			break;
		case 'letter':
			$('#custom_paper_size_width').val('216');
			$('#custom_paper_size_height').val('279');
			break;
		}

		var paper_width = $('#custom_paper_size_width').val() != '' ? $('#custom_paper_size_width').val() : 100;
		var paper_height = $('#custom_paper_size_height').val() != '' ? $('#custom_paper_size_height').val() : 100;


		if (paper_orientation == 'landscape') {
			portrait_width = paper_width;
			paper_width = paper_height;
			paper_height = portrait_width;
		}

		// $('.wclabels-preview').width(paper_width*2+10);
		$('.wclabels-preview table').width(paper_width*2);
		$('.wclabels-preview table').height(paper_height*2);
	}

	function create_table() {
		var rows = $('#rows').val();
		var cols = $('#cols').val();
		var table = $('<table><tbody>');
		for(var r = 0; r < rows; r++)
		{
		    var tr = $('<tr>');
		    for (var c = 0; c < cols; c++)
		        $('<td><div class="label-wrapper"><div class="address-block"><div class="qr_show"><img src="'+sh_pl.qr_test_img+'"></div><div class="addrress_show">Cynthia G. Hensley<br>1465 Sycamore Road<br>Eugene, OR 97404 <br>						</div><div class="clearb"></div></div></div></td>').appendTo(tr);
		    tr.appendTo(table);
		}
		
		
		$('.wclabels-preview table').replaceWith(table);
		if(sh_pl.qr_show)
		{
			
			jQuery('.qr_show').show();
		}else
		{
			jQuery('.qr_show').hide();
		}
	}

	function check_size() {
		var paper_size = $('#paper_size').val();
		if (paper_size == 'custom') {
			$( 'label[for=custom_paper_size_width], #custom_paper_size_width').show();
			$( 'label[for=custom_paper_size_height], #custom_paper_size_height').show();
		} else {
			// $( '#custom_paper_size_width').val('');
			$( 'label[for=custom_paper_size_width], #custom_paper_size_width').hide();

			// $( '#custom_paper_size_height').val('');
			$( 'label[for=custom_paper_size_height], #custom_paper_size_height').hide();			
		}
	}

	create_table();
	set_table_size();
	check_size();

	$( '#paper_size, #paper_orientation' ).change(function() {
		set_table_size();
		check_size();
	});

	$( '#rows, #cols, #custom_paper_size_width, #custom_paper_size_height' ).change(function() {
		create_table();
		set_table_size();
	});


$('#show_qr').click(function(){
	
	if(jQuery(this).is(':checked'))
	{
			jQuery('.qr_show').show();
		
	}else
	{jQuery('.qr_show').hide();
		}
	})
	create_table();
	set_table_size();
	check_size();

});
