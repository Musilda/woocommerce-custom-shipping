/**
 * @wordpress-plugin
 * Plugin Name:       Custom shipping
 * Plugin URI:        
 * Description:       
 * Version:           1.0
 * Author:            
 * Author URI:        
 * Text Domain:       custom-shipping
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

function woocommerce_custom_shipping_init(){
	
	if ( !class_exists( 'WC_Shipping_Method' ) ) {
      		return;
	}

	if ( ! class_exists( 'WC_Custom_Shipping_Method' ) ) {
	
		class WC_Custom_Shipping_Method extends WC_Shipping_Method {
			
			/**
			 * Constructor for your shipping class
			 *
			 * @access public
			 * @return void
			 */
			public function __construct( $instance_id = 0 ) {

				$this->id                 = 'custom'; 
				$this->instance_id        = absint( $instance_id );
				$this->method_title       = __( 'Custom shipping', 'custom-shipping' );
				$this->method_description = __( 'Custom shipping for WooCommerce', 'custom-shipping' );
				
				$this->supports           = array(
											'shipping-zones',
											'instance-settings',
											'instance-settings-modal',
				);

				$this->init_form_fields();

				$this->enabled            = "yes";
				$this->title              = $this->get_option( 'title' );
				$this->description        = $this->get_option( 'description' );
				$this->availability       = $this->get_option( 'availability' );
				
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		
			}
			
			/**
			 * Init form fields
			 *
			 * @access public
			 */
			public function init_form_fields() {
					
				$this->instance_form_fields = array(
					'enabled'     => array(
						'title'       => __( 'Enable', 'custom-shipping' ),
						'type' 	      => 'checkbox',
						'label'       => __( 'Enable custom shippng', 'custom-shipping' ),
						'default'     => 'yes',
						),
					'title'       => array(
						'title'       => __( 'Title', 'custom-shipping' ),
						'type'        => 'text',
						'description' => __( 'Method title', 'custom-shipping' ),
						'default'     => __( 'Custom shipping', 'custom-shipping' ),
						),
					'description' => array(
						'title'       => __( 'Description', 'custom-shipping' ),
						'type'        => 'text',
						'description' => __( 'Shipping description', 'custom-shipping' ),
						'default'     => __( 'Custom shipping for WooCommerce', 'custom-shipping' ),
					),
					'cost'        => array(
						'title'       => __( 'Price', 'custom-shipping' ),
						'type'        => 'text'
					),
				);

			}

			/**
			 * Calculate_shipping function.
			 *
			 * @access public
			 */
			public function calculate_shipping( $package = array() ) {
								
				$cost = $this->get_option( 'cost' );
		
				$rate = array(
					'id' 		=> $this->id,
					'label' 	=> $this->title,
					'calc_tax'	=> 'per_order',
					'cost' 		=> $cost
				);

				$this->add_rate( $rate );           
		
			}                
	
			/**
			 * Is available shipping
			 *
			 * @access public
			 */                    
			public function is_available( $package ) {
	
				if ( 'no' == $this->enabled ) {
					return false;
				}

				return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	
			}
	
		}
	
	}

}
add_action('plugins_loaded', 'woocommerce_custom_shipping_init');

function add_woo_custom_shipping_method( $methods ) {

  	$methods['custom'] = 'WC_Custom_Shipping_Method';
	return $methods;

}
add_filter( 'woocommerce_shipping_methods', 'add_woo_custom_shipping_method' );
