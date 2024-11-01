<?php
if ( !class_exists( 'WooCommerce_shipping_Address_Labels_Settings' ) ) {

	class WooCommerce_shipping_Address_Labels_Settings {
		public function __construct() {

			add_action( 'admin_init', array( &$this, 'init_settings' ) ); // Registers settings


		}




		/**
		 * Add settings link to plugins page
		 */

		public static function settings_page() {
			?>
				<div class="wrap">
					<div class="icon32" id="icon-options-general"><br /></div>
					<h2><?php _e( 'WooCommerce Order address Print', 'woap' ); ?></h2>

					<div class="wclabels-settings">
						<form method="post" action="options.php">

						<?php
							settings_fields( 'shp_labels_template_settings' );
							do_settings_sections( 'shp_labels_template_settings' );

							submit_button();
						?>

						</form>
					</div>
					<div class="wclabels-preview">
						<h3><?php _e( 'Page layout preview', 'woap' ); ?></h3>
						<table>
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
						<div class="promote" style="margin-top:15px;" >
							<ul>

						<li>
							<a target="_blank" href="https://yithemes.com?refer_id=1056403">
								<img src="<?php echo  shipping_print::$plugin_url ?>img/yith-01_300x250.jpg">
						</a>
					</li>
					<li>
						<a target="_blank" href="https://yithemes.com?refer_id=1056403">
							<img src="<?php echo  shipping_print::$plugin_url ?>img/yith-02_300x250.jpg">
					</a>
				</li>

					</ul>
					<ul>
						<li>
							<a target="_blank" href="https://codecanyon.net/user/wp-uniques/portfolio?ref=WP-UNIQUES">
								<img src="<?php echo  shipping_print::$plugin_url ?>img/promote.png">
						</a>
					</li>
				</ul>
						</div>
					</div>
				</div>
			<?php
		}

		/**
		 * User settings.
		 */

		public function init_settings() {
			$option = 'shp_labels_template_settings';

			// Create option in wp_options.
			if ( false === get_option( $option ) ) {
				$this->default_settings( $option );
			}

			// Section.
			add_settings_section(
				'template_settings',
				__( 'Template settings', 'woap' ),
				array( &$this, 'section_options_callback' ),
				$option
			);

			add_settings_field(
				'paper_size',
				__( 'Paper format', 'woap' ),
				array( &$this, 'select_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'paper_size',
					'options' 		=> array(
						'a4'		=> __( 'A4' , 'woap' ),
						'letter'	=> __( 'Letter' , 'woap' ),
						'custom'	=> __( 'Custom size (enter below)' , 'woap' ),
					),
					'custom'		=> array(
						'type'		=> 'multiple_text_element_callback',
						'args'		=> array(
							'menu'			=> $option,
							'id'			=> 'custom_paper_size',
							'fields'		=> array(
								'width'		=> array(
									'label'			=> __( 'Width (mm):' , 'woap' ),
									'label_width'	=> '100px',
									'size'			=> '5',
								),
								'height'	=> array(
									'label'			=> __( 'Height (mm):' , 'woap' ),
									'label_width'	=> '100px',
									'size'			=> '5',
								),
							),
						),
					),
				)
			);

			add_settings_field(
				'paper_orientation',
				__( 'Paper orientation', 'woap' ),
				array( &$this, 'select_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'paper_orientation',
					'options' 		=> array(
						'portrait'	=> __( 'Portrait' , 'woap' ),
						'landscape'	=> __( 'Landscape' , 'woap' ),
					),
				)
			);

			add_settings_field(
				'cols',
				__( 'Columns', 'woap' ),
				array( &$this, 'text_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'cols',
					'size'			=> '5',
					// 'description'	=> __( 'Number of columns on the label sheet', 'woap' ),
				)
			);

			add_settings_field(
				'rows',
				__( 'Rows', 'woap' ),
				array( &$this, 'text_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'rows',
					'size'			=> '5',
					// 'description'	=> __( 'Number of rows on the label sheet', 'woap' ),
				)
			);


			add_settings_field(
				'show_qr',
				__( 'Show Qr Code', 'woap' ),
				array( &$this, 'checkbox_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'show_qr',
					'description'	=>  __( 'This option enables QR Code Show On Print', 'woap' ),
				)
			);


			add_settings_field(
				'ecc',
				__( 'ECC', 'woap' ),
				array( &$this, 'select_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'ecc',
					'options' 		=> array(
						'l'	=> __( 'L - smallest' , 'woap' ),
						'M'	=> __( 'M' , 'woap' ),
						'Q'	=> __( 'Q' , 'woap' ),
						'H'	=> __( 'H - best' , 'woap' ),
					),
				)
			);

			add_settings_field(
				'code_size',
				__( 'Code Size', 'woap' ),
				array( &$this, 'select_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'code_size',
					'options' 		=> array(
						'1'	=> __( '1' , 'woap' ),
						'2'	=> __( '2' , 'woap' ),
						'3'	=> __( '3' , 'woap' ),
						'4'	=> __( '4' , 'woap' ),
						'5'	=> __( '5' , 'woap' ),
						'6'	=> __( '6' , 'woap' ),
						'7'	=> __( '7' , 'woap' ),
						'8'	=> __( '8' , 'woap' ),
						'9'	=> __( '9' , 'woap' ),
						'10'	=> __( '10' , 'woap' ),


					),
				)
			);



			add_settings_field(
				'preview',
				__( 'Enable preview', 'woap' ),
				array( &$this, 'checkbox_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'preview',
					'description'	=> __( 'Open the address labels in a new browser tab instead of printing directly', 'woap' ),
				)
			);

			// Section.
			add_settings_section(
				'label_contents',
				__( 'Label contents', 'woap' ),
				array( &$this, 'section_options_callback' ),
				$option
			);

			add_settings_field(
				'address_data',
				__( 'Address/order data', 'woap' ),
				array( &$this, 'textarea_element_callback' ),
				$option,
				'label_contents',
				array(
					'menu'			=> $option,
					'id'			=> 'address_data',
					'width'			=> '42',
					'height'		=> '8',
					'default'		=> '[shipping_address]',
					'description'	=> __( 'You can use the following placeholders: [shipping_address], [shipping_first_name], [shipping_last_name], [shipping_company], [shipping_address_1], [shipping_address_2], [shipping_city], [shipping_postcode], [shipping_country], [shipping_state], [billing_email], [billing_phone] & [order_number]', 'woap' ),
				)
			);

			add_settings_field(
				'remove_whitespace',
				__( 'Remove empty lines', 'woap' ),
				array( &$this, 'checkbox_element_callback' ),
				$option,
				'label_contents',
				array(
					'menu'			=> $option,
					'id'			=> 'remove_whitespace',
					'description'	=> __( 'Enable this option if you want to remove empty lines left over from empty address/placeholder replacements', 'woap' ),
				)
			);

			add_settings_field(
				'font_size',
				__( 'Font size', 'woap' ),
				array( &$this, 'select_element_callback' ),
				$option,
				'label_contents',
				array(
					'menu'			=> $option,
					'id'			=> 'font_size',
					'options' 		=> array(
						'8'		=> '8pt',
						'9'		=> '9pt',
						'10'	=> '10pt',
						'11'	=> '11pt',
						'12'	=> '12pt',
						'14'	=> '14pt',
						'16'	=> '16pt',
						'18'	=> '18pt',
						'20'	=> '20pt',
						'24'	=> '24pt',
					),
					'default'		=> '10',
				)
			);

			add_settings_field(
				'block_width',
				__( 'Address block width', 'woap' ),
				array( &$this, 'text_element_callback' ),
				$option,
				'label_contents',
				array(
					'menu'			=> $option,
					'id'			=> 'block_width',
					'size'			=> '5',
					'default'		=> '5cm',
					'description'	=> __( 'Enter any value in cm, mm, px or in - use a dot (and not a comma!) as the decimal separator!', 'woap' ),
				)
			);

			add_settings_field(
				'export_doc',
				__( 'Export Doc', 'woap' ),
				array( &$this, 'checkbox_element_callback' ),
				$option,
				'template_settings',
				array(
					'menu'			=> $option,
					'id'			=> 'export_doc',
					'description'	=>  __( 'This option Doc File Export', 'woap' ),
				)
			);


			add_settings_field(
				'custom_styles',
				__( 'Custom styles', 'woap' ),
				array( &$this, 'textarea_element_callback' ),
				$option,
				'label_contents',
				array(
					'menu'			=> $option,
					'id'			=> 'custom_styles',
					'width'			=> '42',
					'height'		=> '8',
					'description'	=> __( 'Enter custom CSS styles for the address labels here', 'woap' ),
				)
			);

			// Register settings.
			register_setting( $option, $option, array( &$this, 'validate_options' ) );

			// Register defaults if settings empty (might not work in case there's only checkboxes and they're all disabled)
			$option_values = get_option($option);
			if ( empty( $option_values ) ) {
			}
		}

		/**
		 * Set default settings.
		 */
		public function default_settings( $option ) {
			switch ( $option ) {
				case 'shp_labels_template_settings':
					$default = array(
						'cols'				=> '3',
						'rows'				=> '5',
						'paper_size'		=> 'a4',
						'paper_orientation'	=> 'portrait',
						'address_data'		=> '[shipping_address]',
						'font_size'			=> '12',
						'block_width'		=> '15cm',
						'ecc'		=> 'l',
						'code_size'=>'3'
					);
					break;
				default:
					$default = array();
					break;
			}

			if ( false === get_option( $option ) ) {
				add_option( $option, $default );
			} else {
				update_option( $option, $default );

			}
		}

		/**
		 * Text element callback.
		 * @param  array $args Field arguments.
		 * @return string	   Text input field.
		 */
		public function text_element_callback( $args ) {
			$menu = $args['menu'];
			$id = $args['id'];
			$size = isset( $args['size'] ) ? $args['size'] : '25';

			$options = get_option( $menu );

			if ( isset( $options[$id] ) ) {
				$current = $options[$id];
			} else {
				$current = isset( $args['default'] ) ? $args['default'] : '';
			}

			$html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" size="%4$s"/>', $id, $menu, $current, $size );

			// Displays option description.
			if ( isset( $args['description'] ) ) {
				$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
			}

			echo $html;
		}

		/**
		 * Multiple text element callback.
		 * @param  array $args Field arguments.
		 * @return string	   Text input field.
		 */
		public function multiple_text_element_callback( $args ) {
			$menu = $args['menu'];
			$id = $args['id'];
			$fields = $args['fields'];
			$options = get_option( $menu );

			foreach ($fields as $name => $field) {
				$label = $field['label'];
				$size = $field['size'];

				if (isset($field['label_width'])) {
					$style = sprintf( 'style="display:inline-block; width:%1$s;"', $field['label_width'] );
				} else {
					$style = '';
				}

				// output field label
				printf( '<label for="%1$s_%2$s" %3$s>%4$s</label>', $id, $name, $style, $label );

				// die(var_dump($options));

				if ( isset( $options[$id][$name] ) ) {
					$current = $options[$id][$name];
				} else {
					$current = isset( $args['default'] ) ? $args['default'] : '';
				}

				// output field
				printf( '<input type="text" id="%1$s_%3$s" name="%2$s[%1$s][%3$s]" value="%4$s" size="%5$s"/><br/>', $id, $menu, $name, $current, $size );

			}


			// Displays option description.
			if ( isset( $args['description'] ) ) {
				printf( '<p class="description">%s</p>', $args['description'] );
			}

		}

		// Text area element callback.
		public function textarea_element_callback( $args ) {
			$menu = $args['menu'];
			$id = $args['id'];
			$width = $args['width'];
			$height = $args['height'];

			$options = get_option( $menu );

			if ( isset( $options[$id] ) ) {
				$current = $options[$id];
			} else {
				$current = isset( $args['default'] ) ? $args['default'] : '';
			}

			printf( '<textarea id="%1$s" name="%2$s[%1$s]" cols="%4$s" rows="%5$s"/>%3$s</textarea>', $id, $menu, $current, $width, $height );

			// Displays option description.
			if ( isset( $args['description'] ) ) {
				printf( '<p class="description">%s</p>', $args['description'] );
			}
		}

		/**
		 * Select element callback.
		 *
		 * @param  array $args Field arguments.
		 *
		 * @return string	  Select field.
		 */
		public function select_element_callback( $args ) {
			$menu = $args['menu'];
			$id = $args['id'];

			$options = get_option( $menu );

			if ( isset( $options[$id] ) ) {
				$current = $options[$id];
			} else {
				$current = isset( $args['default'] ) ? $args['default'] : '';
			}

			printf( '<select id="%1$s" name="%2$s[%1$s]">', $id, $menu );

			foreach ( $args['options'] as $key => $label ) {
				printf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
			}

			echo '</select>';

			if (isset($args['custom'])) {
				$custom = $args['custom'];

				echo '<br/><br/>';

				switch ($custom['type']) {
					case 'text_element_callback':
						$this->text_element_callback( $custom['args'] );
						break;
					case 'multiple_text_element_callback':
						$this->multiple_text_element_callback( $custom['args'] );
						break;
					default:
						break;
				}
			}

			// Displays option description.
			if ( isset( $args['description'] ) ) {
				printf( '<p class="description">%s</p>', $args['description'] );
			}

		}

		/**
		 * Checkbox field callback.
		 *
		 * @param  array $args Field arguments.
		 *
		 * @return string	  Checkbox field.
		 */
		public function checkbox_element_callback( $args ) {
			$menu = $args['menu'];
			$id = $args['id'];

			$options = get_option( $menu );

			if ( isset( $options[$id] ) ) {
				$current = $options[$id];
			} else {
				$current = isset( $args['default'] ) ? $args['default'] : '';
			}

			printf( '<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1"%3$s />', $id, $menu, checked( 1, $current, false ) );

			// Displays option description.
			if ( isset( $args['description'] ) ) {
				printf( '<p class="description">%s</p>', $args['description'] );
			}

		}

		/**
		 * Section null callback.
		 *
		 * @return void.
		 */
		public function section_options_callback() {
		}

		/**
		 * Validate options.
		 *
		 * @param  array $input options to valid.
		 *
		 * @return array		validated options.
		 */
		public function validate_options( $input ) {
			// Create our array for storing the validated options.
			$output = array();

			// Loop through each of the incoming options.
			foreach ( $input as $key => $value ) {

				// Check to see if the current option has a value. If so, process it.
				if ( isset( $input[$key] ) ) {

					// Strip all HTML and PHP tags and properly handle quoted strings.
					if ( is_array( $input[$key] ) ) {
						foreach ( $input[$key] as $sub_key => $sub_value ) {
							$output[$key][$sub_key] = $input[$key][$sub_key];
						}

					} else {
						$output[$key] = $input[$key];
					}
				}
			}

			// Return the array processing any additional functions filtered by this action.
			return apply_filters( 'shp_labels_validate_input', $output, $input );
		}

	} // end class
} // end class_exists
