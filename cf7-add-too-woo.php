<?php
/*
Plugin Name: Woo-add-to-cart via CF7
Plugin URI: https://www.rikjanssen.info
Description: Add a Woo-product to the cart on submitting the Contact Form 7 form.
Author: Rik Janssen (Beta)
Version: 1.20
Author URI: https://betacore.tech
*/

add_action("wpcf7_before_send_mail", "bnADTC_hook_send");  
function bnADTC_hook_send($cf7) {
    $wpcf = WPCF7_ContactForm::get_current();
    if (get_option('bnADTC_formid')==$wpcf->id){
        global $woocommerce;
        $product_id = get_option('bnADTC_productid');
        $woocommerce->cart->add_to_cart($product_id);
    }
    return $wpcf;
}

/* NAV */

function bnADTC_addtocart_nav() {
    
    // add the sub menu page for the plugin
	// https://codex.wordpress.org/Adding_Administration_Menus
    add_submenu_page( 
        'wpcf7', 
        'Add To Cart', 
        'Add To Cart', 
        'manage_options', 
        'bnADTC_addtocart', 
        'bnADTC_addtocart_fb'  // this should correspond with the function name
    ); 
}

add_action( 'admin_menu', 'bnADTC_addtocart_nav' );

/* FORM INPUT FIELD */

function bnADTC_input_field($arg){
?>
<div class="bcSANY_input_wrapper">
	<input type="text"
		   name="bnADTC_<?php echo $arg['name']; ?>"
		   value="<?php echo $arg['selected']; ?>"
		   class="regular-text"
		   />
</div>
<?php	
}

/* FORM PAGE */

function bnADTC_addtocart_fb(){
?>
<div class="wrap">
    <h1>Add to cart</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'bnADTC_addtocart' ); ?>
        <?php do_settings_sections( 'bnADTC_addtocart' ); ?>
        <table class="bcADTC_forms form-table">
            <tr valign="top">
                <th scope="row">
                    <?php _e("Form ID", 'belnedaddtocart'); ?>
                </th>
                 <td>
                    <?php 
                    $input_vars = array( 'name'=>'formid',
                                         'selected'=>get_option('bnADTC_formid')
                                       );
                    bnADTC_input_field($input_vars); ?>
                </td>
            </tr> 
            <tr valign="top">
                <th scope="row">
                    <?php _e("Product ID", 'belnedaddtocart'); ?>
                </th>
                 <td>
                    <?php 
                    $input_vars = array( 'name'=>'productid',
                                         'selected'=>get_option('bnADTC_productid')
                                       );
                    bnADTC_input_field($input_vars); ?>
                </td>
            </tr> 
		</table>
        <?php submit_button(); ?>
     </form>
</div>
<?php
}

/* REGISTER SETTINGS */

function bnADTC_settings_register() {
	
	// this corresponds to some information added at the top of the form
	$setting_name = 'bnADTC_addtocart';
	
	// sanitize settings
    $args_html = array(
            'type' => 'string', 
            'sanitize_callback' => 'wp_kses_post',
            'default' => NULL,
            );	
	
    $args_int = 'intval';
	
    $args_text = array(
            'type' => 'string', 
            'sanitize_callback' => 'sanitize_text_field',
            'default' => NULL,
            );
	
	// adding the information to the database as options
    register_setting( $setting_name, 'bnADTC_productid', $args_text ); // textarea
    register_setting( $setting_name, 'bnADTC_formid', $args_text ); // radio
	
}

add_action( 'admin_init', 'bnADTC_settings_register' );

?>
