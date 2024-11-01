<?php
/**
 * Print order header
 *
 * @package WooCommerce Print Invoice & Delivery Note/Templates
 */

if ( !defined( 'ABSPATH' ) ) exit;

?>

<!DOCTYPE html>
<html>

<head>
	<title><?php _e('Shipping Adress','woap')?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?php
		do_action( 'shpt_head' ); ?>
	<style type="text/css">
	<?php include('shplabels-styles.css.php' ) ; ?>
	</style>
	<?php
	 if ( isset($this->settings['custom_styles']) ) { ?>
	<style type="text/css">
	<?php echo $this->settings['custom_styles']; ?>
	</style>
	<?php } ?>
</head>
<?php


$PNG_WEB_DIR = plugin_dir_path( __FILE__ ).'temp/';

wp_mkdir_p($PNG_WEB_DIR);

$cols = $this->settings['cols'];
$rows = $this->settings['rows'];
$label_number = 0;

// die($_GET['order_ids']);
$order_ids = explode(',',$_GET['ids']);
$order_count = count($order_ids);
$labels_per_page = $cols*$rows;
$page_count = ceil(($order_count)/$labels_per_page);

for ($page=0; $page < $page_count; $page++) {
	echo '<table class="address-labels" width="100%" height="100%" border="0" cellpadding="0">';
	$last_height = 0;
	$current_height = $current_width = 0;
	$current_row = 0;

	for ($label=0; $label < $labels_per_page; $label++) {
		$label_number++;
		$current_col = (($label_number-1) % $cols)+1;

		if ($current_col == 1) {
			$last_height = $current_height;
			$last_width = 0;
			$current_row++;
			echo '<tr class="label-row">';
		}

		if ( $label_number > $this->offset && isset($order_ids[$label_number - $this->offset - 1]) ) {
			$order_id = $order_ids[$label_number - $this->offset - 1];
		} else {
			$order_id = '';
		}

		$current_width = round( $current_col * (100/$cols) );
		$width = $current_width - $last_width;
		$last_width = $current_width;

		$current_height = round( $current_row * (100/$rows) );
		$height = $current_height - $last_height;

		printf('<td width="%s%%" height="%s%%" class="label"><div class="label-wrapper">', $width, $height);
		// because we are also looping through the empty labels,
		// we need to check if there's an order for this label
		if (!empty($order_id)) {
			// get label data from order
			$order = new WC_Order( $order_id );
			



			// replace placeholders
			$label_data = isset($this->settings['address_data'])? nl2br( $this->settings['address_data'] ): '[shipping_address]';
			$label_data = $this->make_replacements( $label_data, $order );

			echo '<div class="address-block">';
			if($this->show_qr)
			{
				$errorCorrectionLevel = $this->settings['ecc'] ? $this->settings['ecc'] : 'L';
				$matrixPointSize = $this->settings['code_size'] ? $this->settings['code_size'] :4;

				$label_data_qr = preg_replace('#<br\s*/?>#i', "\n", $label_data);
				$filename = $PNG_WEB_DIR.'test'.md5($label_data_qr.'|'.$errorCorrectionLevel.'|'.$matrixPointSize.'|'.time()).'.png';
				QRcode::png($label_data_qr, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
				$url = plugin_dir_url(  __FILE__ );
				$imagesfolswr=str_replace(plugin_dir_path( __FILE__ ),$url,$filename);
				echo "<div class='qr_show'>";
					echo '<img src="'.$imagesfolswr.'" />';
				echo "</div>"	;

			}
			echo '<div class="addrress_show">';

			// process label template to display content
			echo $label_data;
			echo '</div>';
			echo '<div class="clearb"></div>';
			echo '<hr/></div>';
		} else {
			echo '&nbsp;';
		}
		echo '</div></td>';

		if ($current_col == $cols) {
			echo '</tr>';
		}

	}
	echo '</table>';
}
		// shpt_after_page hook
				do_action( 'shpt_after_page' );
		?>



</body>
</html>

<?php


if($this->settings['preview']!=1)
{
?>
<script>
	window.print();

</script>
<?php }?>
