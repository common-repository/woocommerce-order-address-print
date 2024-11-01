<?php
/**
 * Plugin Name:  Woocommerce Order address Print
 * Plugin URI: http://www.uniquesweb.co.in/demo/woocommerce
 * Description: WooCommerce Order Address Print is an extension that allows you to print out shipping / address labels from your WooCommerce orders with QR code. Simply go to your woocommerce order list and choose Address print to get address printed.
 * Author: Bhavik Patel
 * Author URI:   http://www.uniquesweb.co.in/demo/woocommerce
 * Version: 3.2
 * Text Domain: woap
 * Domain Path: /languages/
 *
 * Copyright:
 *
 * License: GPLv2 or later
 * License URI:http://www.opensource.org/licenses/gpl-license.php
 *
 */


defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );


if ( ! class_exists( 'shipping_print' ) )
{
	class shipping_print
	{

		public static $plugin_version;
		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;
		public static $plugin_basefile_path;

		public $writepanel;
		public $settings;
		public $print;
		public $theme;
		var $show_qr;
		var $preview;
		var $doc_export;

		public $setting;

		public function __construct()
		{

			self::$plugin_version = '3.2';
			self::$plugin_prefix = 'shpt_';
			self::$plugin_basefile_path = __FILE__;
			self::$plugin_basefile = plugin_basename( self::$plugin_basefile_path );
			self::$plugin_url = plugin_dir_url( self::$plugin_basefile );
			self::$plugin_path = trailingslashit( dirname( self::$plugin_basefile_path ) );
			add_action( 'init', array($this,'woap_load_textdomain') );
			add_action( 'admin_footer', array( $this, 'bulk_admin_footer' ), 10 );
			add_action( 'load-edit.php', array( $this, 'bulk_action' ) );
			add_action( 'wp_ajax_bulk_shipping_print', array( $this, 'bulk_shipping_print' ));
			add_action( 'admin_notices', array( $this, 'confirm_bulk_actions' ) );
			add_action( 'admin_enqueue_scripts',  array( $this,'shpt_amin_js_include' ));
				$this->include_classes();
				$this->setting = new WooCommerce_shipping_Address_Labels_Settings();

			add_action( 'admin_menu', array( &$this, 'menu' ) ); // Add menu.
			add_action( 'add_meta_boxes_shop_order', array( $this, 'add_box' ) );
			$this->settings = get_option( 'shp_labels_template_settings' );


		}

		function woap_load_textdomain()
		{

				$locale = apply_filters( 'plugin_locale', get_locale(), 'woap' );
				load_textdomain( 'woap', WP_LANG_DIR . '/wc-order-print/woap-' . $locale . '.mo' );
				load_plugin_textdomain( 'woap', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );



		}

		public function include_classes() {
			include_once( 'function.php' );
			include_once( 'shiping_setting.php' );
			include_once( 'qrcode/qrlib.php' );


		}

		public function bulk_admin_footer() {
		global $post_type;

		if ( 'shop_order' == $post_type ) {
			?>
			<script type="text/javascript">
			jQuery(function() {
				jQuery('<option>').val('bulk_shipping_print').text('<?php _e( 'Address Print', 'woap' )?>').appendTo("select[name='action']");
				jQuery('<option>').val('bulk_shipping_print').text('<?php _e( 'Address Print', 'woap' )?>').appendTo("select[name='action2']");


			});
			</script>
			<?php
		}
	}


		public function bulk_action()
		{
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action = $wp_list_table->current_action();

			switch ( $action ) {
				case 'bulk_shipping_print':
						$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );
						$bulk_print_url = admin_url( 'admin-ajax.php' );
						$sendback = add_query_arg( array( 'action'=>'bulk_shipping_print','ids' => join( ',', $post_ids ) ), $bulk_print_url );
						$total = count( $post_ids );

						$referer_args = array();
				parse_str( parse_url( wp_get_referer(), PHP_URL_QUERY ), $referer_args );

				// set the basic args for the sendback
				$args = array(
					'post_type' => $referer_args['post_type']
				);
				if( isset( $referer_args['post_status'] ) ) {
					$args = wp_parse_args( array( 'post_status' => $referer_args['post_status'] ), $args );
				}
				if( isset( $referer_args['paged'] ) ) {
					$args = wp_parse_args( array( 'paged' => $referer_args['paged'] ), $args );
				}
				if( isset( $referer_args['orderby'] ) ) {
					$args = wp_parse_args( array( 'orderby' => $referer_args['orderby'] ), $args );
				}
				if( isset( $referer_args['order'] ) ) {
					$args = wp_parse_args( array( 'orderby' => $referer_args['order'] ), $args );
				}




						$args = wp_parse_args( array( 'shippint_add_print' => 1, 'total' => $total, 'print_url' => urlencode( $sendback ) ), $args );
						$sendback = add_query_arg( $args, '' );
						wp_redirect( $sendback );

						exit();
				break;
				default:
				return;
			}




		}

		public function confirm_bulk_actions()
		{
		if( isset( $_REQUEST['print_url'] ) && isset( $_REQUEST['shippint_add_print'] ) && $_REQUEST['shippint_add_print']==1 )
			{
				?>
				<div id="woocommerce-shipping-addresh-print" class="updated">
						<p><?php _e('Orders Address','woap')?> <a href="<?php echo urldecode( $_REQUEST['print_url'] ); ?>" target="_blank" class="print-preview-button" id="woocommerce-delivery-notes-bulk-print-button"><?php _e( 'Print now', 'woap' ) ?></a> <span class="print-preview-loading spinner"></span></p>
				</div>
				<?php
			}
		}


		public function make_replacements ( $format, $order )
		{



			// get order meta
			$order_meta = get_post_meta( $order->get_id() );

			// flatten values
			foreach ($order_meta as $key => &$value) {
				$value = $value[0];
			}
			// remove reference!
			unset($value);

			// get full countries & states
			$countries = new WC_Countries;
			$shipping_country	= $order_meta['_shipping_country'];
			$billing_country	= $order_meta['_billing_country'];
			$shipping_state		= $order_meta['_shipping_state'];
			$billing_state		= $order_meta['_billing_state'];

			$shipping_state_full	= ( $shipping_country && $shipping_state && isset( $countries->states[ $shipping_country ][ $shipping_state ] ) ) ? $countries->states[ $shipping_country ][ $shipping_state ] : $shipping_state;
			$billing_state_full		= ( $billing_country && $billing_state && isset( $countries->states[ $billing_country ][ $billing_state ] ) ) ? $countries->states[ $billing_country ][ $billing_state ] : $billing_state;
			$shipping_country_full	= ( $shipping_country && isset( $countries->countries[ $shipping_country ] ) ) ? $countries->countries[ $shipping_country ] : $shipping_country;
			$billing_country_full	= ( $billing_country && isset( $countries->countries[ $billing_country ] ) ) ? $countries->countries[ $billing_country ] : $billing_country;
			unset($countries);

			// add 'missing meta'
			$order_meta['shipping_address']			=  ($order->has_shipping_address()) ? $order->get_formatted_shipping_address() :$order->get_formatted_billing_address();
			$order_meta['shipping_country_code']	= $shipping_country;
			$order_meta['shipping_state_code']		= $shipping_state;
			$order_meta['_shipping_country']		= $shipping_country_full;
			$order_meta['_shipping_state']			= $shipping_state_full;

			$order_meta['billing_address']			= $order->get_formatted_billing_address();
			$order_meta['billing_country_code']		= $billing_country;
			$order_meta['billing_state_code']		= $billing_state;
			$order_meta['_billing_country']			= $billing_country_full;
			$order_meta['_billing_state']			= $billing_state_full;

			$order_meta['order_total']				= $order->get_formatted_order_total();
			$order_meta['shipping_method']			= $order->get_shipping_method();
			$order_meta['shipping_notes']			= wpautop( wptexturize( $order->customer_note ) );
			$order_meta['customer_note']			= $order_meta['shipping_notes'];
			$order_meta['order_number']				= ltrim($order->get_order_number(), '#');
			$order_meta['order_date']				= date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) );
			$order_meta['date']						= date_i18n( get_option( 'date_format' ) );

			// create placeholders list
			foreach ($order_meta as $key => $value) {
				// strip leading underscores, add brackets
				$placeholders[$key] = '['.ltrim($key,'_').']';
			}

			 //print_r($placeholders);
			// print_r($order_meta);
			// die();

			// make an index of placeholders
			preg_match_all('/\[.*?\]/', $format, $placeholders_used);
			$placeholders_used = array_shift($placeholders_used); // we only need the first match set

			// unset empty order_meta and remove corresponding placeholder
			foreach ($order_meta as $key => $value) {
				if (empty($value)) {
					unset($order_meta[$key]);
					unset($placeholders[$key]);
				}
			}

			// make replacements




			$formatted_address = str_replace($placeholders, $order_meta, $format);

			// remove empty lines placeholder lines, but preserve user-defined empty lines
			if (isset($this->settings['remove_whitespace'])) {
				// break formatted address into lines
				$formatted_address = explode("\n", $formatted_address);
				// loop through address lines and check if only placeholders (remove HTML formatting first)
				foreach ($formatted_address as $key => $address_line) {
					// strip html tags for checking
					$clean_line = trim(strip_tags($address_line));
					// clean zero-width spaces
					$clean_line = str_replace("\xE2\x80\x8B", "", $clean_line);
					// var_dump($clean_line);
					if (empty($clean_line)) {
						continue; // user defined newline!
					}
					// check without leftover placeholders
					$clean_line = str_replace($placeholders_used, '', $clean_line);

					// remove empty lines
					if (empty($clean_line)) {
						unset($formatted_address[$key]);
					}
				}

				// glue address lines back together
				$formatted_address = implode("\n", $formatted_address);
			}

			// remove leftover placeholders
			$formatted_address = str_replace($placeholders_used, '', $formatted_address);

			return $formatted_address;
		}


		public function bulk_shipping_print(){




			if($this->settings['show_qr'])
			{
				$this->show_qr=1;
			}


								// prepare paper size data for style.css
			if ($this->settings['paper_size'] == 'custom') {
				switch ($this->settings['paper_orientation']) {
					case 'portrait':
						$this->paper_size = $this->settings['custom_paper_size']['width'] .'mm '. $this->settings['custom_paper_size']['height'].'mm';
						break;
					case 'landscape':
						$this->paper_size = $this->settings['custom_paper_size']['height'] .'mm '. $this->settings['custom_paper_size']['width'].'mm';
						break;
					default:
						$this->paper_size = $this->settings['custom_paper_size']['width'] .'mm '. $this->settings['custom_paper_size']['height'].'mm';
						break;
				}
			} else {
				$this->paper_size = $this->settings['paper_size'] .' '. $this->settings['paper_orientation'];
			}

			// Get page size
			switch ($this->settings['paper_size']) {
				case 'a4':
					$this->page_width = 210;
					$this->page_height = 297;
					break;
				case 'letter':
					$this->page_width = 216;
					$this->page_height = 279;
					break;
				case 'custom':
					$this->page_width = $this->settings['custom_paper_size']['width'];
					$this->page_height = $this->settings['custom_paper_size']['height'];
			}

			// Calculate label size
			$cols = $this->settings['cols'];
			$rows = $this->settings['rows'];
			switch ($this->settings['paper_orientation']) {
				case 'portrait':
					$this->label_height = $this->page_height / $rows;
					$this->label_width = $this->page_width / $cols;
					break;
				case 'landscape':
					$this->label_height = $this->page_width / $rows;
					$this->label_width = $this->page_height / $cols;
					break;
				default:
					$this->label_height = $this->page_height / $rows;
					$this->label_width = $this->page_width / $cols;
					break;
			}



			$doc_export = $this->settings['export_doc'];
			if($doc_export)
			{

				include(self::$plugin_path.'/print-template-doc.php');
			}
			else
			{

				include(self::$plugin_path.'/print_template.php');
			}




			wp_die();
		}

		/*admin Include Script*/
		public function shpt_amin_js_include()
		{
			wp_enqueue_script( 'woocommerce-shipping-address-print-link', self::$plugin_url . 'js/admin.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-shipping-address-print-link_note', self::$plugin_url . 'js/jquery.print-link.js', array( 'jquery' ) );



			wp_register_style('shplabels-admin-styles', self::$plugin_url .'/css/shpllabels-admin-styles.css',array(),'3.2','all');
			wp_enqueue_style( 'shplabels-admin-styles' );
			if( isset($this->settings['show_qr']) && $this->settings['show_qr'])
			{
				$this->show_qr=1;
			}

			if( isset($this->settings['preview']) &&  $this->settings['preview'])
			{
				$this->preview=1;
			}
			if( isset($this->settings['export_doc']) && $this->settings['export_doc'])
			{
				$this->doc_export=1;
			}


			$translation_array = array(
													'qr_test_img' =>self::$plugin_url .'/img/test.png',
													'qr_show'=>$this->show_qr,
													'show_prev'=>$this->preview,
													'export_doc'=>$this->doc_export
													);


			wp_register_script('shlabels-admin-scripts', self::$plugin_url.'js/shlabels-admin-scripts.js',array( 'jquery' ),'3.2');
			wp_enqueue_script( 'shlabels-admin-scripts' );
			wp_localize_script( 'shlabels-admin-scripts', 'sh_pl', $translation_array );




	}

		public function menu() {


			$this->options_page_hook = add_submenu_page(
				'woocommerce',
				__( 'Order Address Labels', 'woap' ),
				__( 'Order Address Labels', 'woap' ),
				'manage_woocommerce',
				'sh_labels_options_page',
				array( 'WooCommerce_shipping_Address_Labels_Settings', 'settings_page' )
			);
		}

		public function add_box() {
			add_meta_box( 'shp_labels-box', __( 'Print Shipping  Address Labels', 'woap' ), array( $this, 'create_box_content' ), 'shop_order', 'side', 'default' );
		}

		/**
		 * Create the meta box content on the single order page
		 */
		public function create_box_content() {
			global $post_id;


			$bulk_print_url = admin_url( 'admin-ajax.php' );
			$sendback = add_query_arg( array( 'action'=>'bulk_shipping_print','ids' => join( ',',array($post_id )) ), $bulk_print_url );
			$args = wp_parse_args( array( 'post'=>$post_id,'action'=>'edit','shippint_add_print' =>'true',  'print_url' => urlencode( $sendback ) ), $args );
			$sendback = add_query_arg( $args, '' );

			$alt = esc_attr__( 'Print Shipping  Address Labels', 'woap' );
			$title = __( 'Print Shipping  Address Labels', 'woap' );
			?>
			<ul class="wpo_wclabels-actions">
				<li><a href="<?php echo $sendback; ?>" class="button wclabels-single"  alt="<?php echo $alt; ?>" data-id="<?php echo $post_id; ?>"><?php echo $title; ?></a></li>
			</ul>
			<?php
		}

}

	include('class-wc-dependencies.php');
	if ( sh_is_woocommerce_active() )
	{
		new shipping_print;
	}else
	{
		add_action( 'admin_notices',  'wcop_need_woocommerce' ) ;
	}


		register_activation_hook(__FILE__, 'shipping_label_print_plugin_activation');
		function shipping_label_print_plugin_activation() {
			 wp_schedule_event( current_time( 'timestamp' ), 'daily', 'remove_temp_file_event');

		}
		register_deactivation_hook(__FILE__, 'shipping_label_print_plugin_deactivation');
			function shipping_label_print_plugin_deactivation() {
				wp_clear_scheduled_hook('remove_temp_file_event');
		}

		add_action('remove_temp_file_event', 'shipping_label_print_plugin_temp_file_remove');
		function shipping_label_print_plugin_temp_file_remove() {
			$files = glob(shipping_print::$plugin_path.'temp/*'); // get all file names
			update_option('temp_file',$files);
			foreach($files as $file){ // iterate files
				if(is_file($file))
					unlink($file); // delete file

			}
		}


 }
