<?php
/**
 * Review Order
 */

global $woocommerce;
$checked = get_option('woocommerce_enable_guest_checkout');

$executepayment = new WC_Gateway_PayPal_Plus_AngellEYE;
$get_orderid = '';
if (isset(WC()->session->result) && !empty(WC()->session->result)) {
	$get_orderid = maybe_unserialize(WC()->session->result);
}
if (isset($get_orderid) && !empty($get_orderid)) { 
		$currentorder_id = $get_orderid['INVNUM'];
		$is_terms_on = get_post_meta($currentorder_id,'paypal_for_woocommerce_terms_on',true);
		$is_create_on = get_post_meta($currentorder_id,'paypal_for_woocommerce_create_act',true);
}
//Add hook to show login form or not
$show_login = apply_filters('paypal-for-woocommerce-show-login', !is_user_logged_in() && $checked==="no" && isset($_REQUEST['pp_action']));
$show_act = apply_filters('paypal-for-woocommerce-show-login', !is_user_logged_in() && $checked==="yes" && isset($_REQUEST['pp_action']) && isset($is_create_on) && empty($is_create_on));


if (is_user_logged_in()) {
	$is_userloggedin = 1;
}elseif (!is_user_logged_in()) {
	$is_userloggedin = 0;
}



### After PayPal payment method confirmation, user is redirected back to this page with token and Payer ID ###
if (isset($_GET["token"]) && isset($_GET["PayerID"]) && isset($_GET["paymentId"])) {

    $frm_act = "";
    //$order = new WC_Order( $order_id );

    //$inputhtml = '<input type="submit" name ="btn_placeorder" class="button" value="' . __('Place Order', 'paypal-for-woocommerce') . '" /></p>';
} else {
    $frm_act = add_query_arg('pp_action', 'payaction', add_query_arg('wc-api', 'WC_Gateway_PayPal_Express_AngellEYE', home_url('/')));
   // $inputhtml = '<input type="submit" onclick="jQuery(this).attr(\'disabled\', \'disabled\').val(\'Processing\'); jQuery(this).parents(\'form\').submit(); return false;" class="button" value="' . __('Place Order', 'paypal-for-woocommerce') . '" /></p>';
}



if( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) && isset($is_terms_on) && empty($is_terms_on) && empty($_GET["paymentId"])) {
	
	$inputhtml = '<input type="button" class="button cls_place_order_own" value="' . __( 'Place Order','paypal-for-woocommerce') . '" /></p>';
	$term_off =0;
}
else if (wc_get_page_id( 'terms' ) < 0 && $is_userloggedin == 0 && $checked == 'yes') {
	$inputhtml = '<input type="button" class="button cls_place_order_own" value="' . __( 'Place Order','paypal-for-woocommerce') . '" /></p>';
	$term_off =1;	
}

else if (isset($_GET["token"]) && isset($_GET["PayerID"]) && isset($_GET["paymentId"])) {
	 $inputhtml = '<input type="submit" name ="btn_placeorder" class="button" value="' . __('Place Order', 'paypal-for-woocommerce') . '" /></p>';
}
else {
	
	$inputhtml = '<input type="submit" onclick="jQuery(this).attr(\'disabled\', \'disabled\').val(\'Processing\'); jQuery(this).parents(\'form\').submit(); return false;" class="button" value="' . __( 'Place Order','paypal-for-woocommerce') . '" /></p>';

}

if (isset($_POST["btn_placeorder"]) && empty($_SESSION['execute_payment'])) {

	
	$_SESSION['payment_args'] = array('token' => $_GET["token"], 'PayerID' => $_GET["PayerID"], 'paymentId' => $_GET["paymentId"]);

    $address = array(
        'first_name' => WC()->customer->shiptoname,
        'company' => WC()->customer->company,
        'address_1' => WC()->customer->get_address(),
        'address_2' => "",
        'city' => WC()->customer->get_city(),
        'state' => WC()->customer->get_state(),
        'postcode' => WC()->customer->get_postcode(),
        'country' => WC()->customer->get_country()
            );
    $executepayment->executepay($_SESSION['payment_args']);
    
	
}

?>
<script type="text/javascript">
jQuery(window).bind("load", function() {

	var txt_act_password1 = jQuery('.cls_txt_act_password').val();
	
	jQuery('.cls_txt_act_password').val('');
 	jQuery('body').on('click','.cls_place_order_own',function(){
     	
   		var ischecked = jQuery('.terms_own').is(':checked') ;
   		var ischecked_crete_act = jQuery('.chkcreate_act').is(':checked');
   		var txt_act_password = jQuery('.cls_txt_act_password').val();
   		var is_userloggedin = jQuery('.isuserlogin').val();
   		var term_off = jQuery('.term_off').val();
   		
   		if (ischecked == false && term_off == 0 ) {
   			
   			jQuery('.wp_notice_own').html('');
   			jQuery('.wp_notice_own').html('<div class="woocommerce-error">You must accept our Terms & Conditions.</div>');
   		
    		jQuery('html, body').animate({
    			scrollTop: "0px"
    		}, 800);
   			
   			
   			return false;
   		}else if (ischecked == true && is_userloggedin == 1) {
   			
   			jQuery('.wp_notice_own').html('');
   			jQuery(this).attr('disabled','disabled').val('Processing'); 
      
        	jQuery(this).parents('form').submit(); 
        	return true;
   		}else if (ischecked_crete_act == true && txt_act_password == ""  && is_userloggedin == 0 && term_off == 1) {
   			
   			jQuery('.wp_notice_own').html('');
   			jQuery('.wp_notice_own').html('<div class="woocommerce-error">Account Password field is required.</div>');
   		
    		jQuery('html, body').animate({
    			scrollTop: "0px"
    		}, 800);
   			
   			
   			return false;	
   		}
   		
   		else if (ischecked == true && ischecked_crete_act == true && txt_act_password == "") {
   			
   			jQuery('.wp_notice_own').html('');
   			jQuery('.wp_notice_own').html('<div class="woocommerce-error">Account Password field is required.</div>');
   		
    		jQuery('html, body').animate({
    			scrollTop: "0px"
    		}, 800);
   			
   			
   			return false;	
   			
   		}else if (ischecked == true && ischecked_crete_act == false && txt_act_password == "") {
   			
   			jQuery('.wp_notice_own').html('');
   			jQuery(this).attr('disabled','disabled').val('Processing'); 
      
        	jQuery(this).parents('form').submit(); 
        	return true;
   		}
   		else if (ischecked == true && ischecked_crete_act == true && txt_act_password != "") {
   			
   			jQuery('.wp_notice_own').html('');
   			jQuery(this).attr('disabled','disabled').val('Processing'); 
      
        	jQuery(this).parents('form').submit(); 
        	return true;
   		}
   		else if (ischecked_crete_act == true && txt_act_password != ""  && is_userloggedin == 0 && term_off == 1) {
   			 jQuery('.wp_notice_own').html('');
   			 jQuery(this).attr('disabled','disabled').val('Processing'); 
        	 jQuery(this).parents('form').submit(); 
        	return true;
   		}
   		 if (ischecked_crete_act == false && txt_act_password == "" && is_userloggedin == '0' && term_off == '1') {
   			
   			jQuery('.wp_notice_own').html('');
   		    jQuery(this).attr('disabled','disabled').val('Processing'); 
      
        	jQuery(this).parents('form').submit(); 
        	return true;
   		}
   		
    });
  
     jQuery(".chkcreate_act").click(function(){
     	   var ischecked_act = jQuery('.chkcreate_act').is(':checked') ;
     	   
   		if (ischecked_act == false) {
     		jQuery('.create_account_child').toggle();
     		jQuery('.cls_txt_act_password').val('');
     		
   		}else if(ischecked_act == true) {
   				jQuery('.create_account_child').toggle();
   				jQuery('.cls_txt_act_password').val('');
   				
   		}
     	
     });
     
            
    });
</script>
<style type="text/css">
    #payment{
        display:none;
    }
   .lbl_terms{    
    
    display: inline-block !important;
    margin-right: 5px !important;
   }
   .terms_own 
   {
   	float: none;
    margin-top: 0px !important;
    display: inline-block !important; }
</style>


<form class="angelleye_checkout" method="POST" action="<?php echo $frm_act;?>">
<div class="wp_notice_own">

</div>
<input type="hidden" class="isuserlogin" value="<?php echo $is_userloggedin;?>"/>
<input type="hidden" class="term_off" value="<?php echo $term_off;?>"/>
<div id="paypalexpress_order_review">
        <?php woocommerce_order_review();?>
    
</div>

<?php if ( WC()->cart->needs_shipping()  ) : ?>


    <div class="title">
        <h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>
    </div>

    <div class="col2-set addresses">

        <div class="col-1">

            <div class="title">
                <h3><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
            </div>
            <div class="address">
                <p>
                    <?php
                    // Formatted Addresses
                    $address = array(
                    'first_name' 	=> WC()->customer->shiptoname,
                    'company'		=> WC()->customer->company,
                    'address_1'		=> WC()->customer->get_address(),
                    'address_2'		=> "",
                    'city'			=> WC()->customer->get_city(),
                    'state'			=> WC()->customer->get_state(),
                    'postcode'		=> WC()->customer->get_postcode(),
                    'country'		=> WC()->customer->get_country()
                    ) ;

                    echo WC()->countries->get_formatted_address( $address );
                    ?>
                </p>
            </div>

        </div><!-- /.col-1 -->
        <div class="col-2">
        	<?php 
        	$woocommerce_paypal_express_settings = maybe_unserialize(get_option('woocommerce_paypal_express_settings'));
        	if( isset($woocommerce_paypal_express_settings['billing_address']) && $woocommerce_paypal_express_settings['billing_address'] == 'yes') :
        	// Formatted Addresses
        	$user_submit_form = maybe_unserialize(WC()->session->checkout_form);

        	if( (isset($user_submit_form) && !empty($user_submit_form)) && is_array($user_submit_form) ) {
        		if( isset($user_submit_form['ship_to_different_address']) && $user_submit_form['ship_to_different_address'] == true ) {
        			$billing_address = array(
        			'first_name' 	=> $user_submit_form['billing_first_name'],
        			'last_name'		=> $user_submit_form['billing_last_name'],
        			'company'		=> $user_submit_form['billing_company'],
        			'address_1'		=> $user_submit_form['billing_address_1'],
        			'address_2'		=> $user_submit_form['billing_address_2'],
        			'city'			=> $user_submit_form['billing_city'],
        			'state'			=> $user_submit_form['billing_state'],
        			'postcode'		=> $user_submit_form['billing_postcode'],
        			'country'		=> $user_submit_form['billing_country']
        			) ;
        		}
        	} else {

        		$billing_address = array(
        		'first_name' 	=> WC()->customer->shiptoname,
        		'company'		=> WC()->customer->company,
        		'address_1'		=> WC()->customer->get_address(),
        		'address_2'		=> "",
        		'city'			=> WC()->customer->get_city(),
        		'state'			=> WC()->customer->get_state(),
        		'postcode'		=> WC()->customer->get_postcode(),
        		'country'		=> WC()->customer->get_country()
        		) ;
        	}

        	if( isset($billing_address) && !empty($billing_address) ) :
        		?>
        	<div class="col-1">

	            <div class="title">
	                <h3><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>
	            </div>
	            <div class="address">
	                <p>
	                    
	                    <?php 
	                    echo WC()->countries->get_formatted_address( $billing_address );

	                    ?>
	                </p>
	            </div>

        </div><!-- /.col-1 -->
        	<?php endif; endif; ?>
        </div><!-- /.col-2 -->
    </div><!-- /.col2-set -->
<?php endif; ?>
<?php if ( $show_login ):  ?>
</form>
    <style type="text/css">

        .woocommerce #content p.form-row input.button,
        .woocommerce #respond p.form-row input#submit,
        .woocommerce p.form-row a.button,
        .woocommerce p.form-row button.button,
        .woocommerce p.form-row input.button,
        .woocommerce-page p.form-row #content input.button,
        .woocommerce-page p.form-row #respond input#submit,
        .woocommerce-page p.form-row a.button,
        .woocommerce-page p.form-row button.button,
        .woocommerce-page p.form-row input.button{
            display: block !important;
        }
    </style>
    <div class="title">
        <h2><?php _e( 'Login', 'woocommerce' ); ?></h2>
    </div>
    <form name="" action="" method="post">
        <?php
        function curPageURL() {
        	$pageURL = 'http';
        	if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        	$pageURL .= "://";
        	if ($_SERVER["SERVER_PORT"] != "80") {
        		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        	} else {
        		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        	}
        	return $pageURL;
        }

        woocommerce_login_form(
        array(
        'message'  => 'Please login or create an account to complete your order.',
        'redirect' => curPageURL(),
        'hidden'   => true
        )
        );
        $result = unserialize(WC()->session->RESULT);
        $email = (!empty($_POST['email']))?$_POST['email']:$result['EMAIL'];
        ?>
    </form>
    <div class="title">
        <h2><?php _e( 'Create A New Account', 'woocommerce' ); ?></h2>
    </div>
    
    <form action="" method="post">
        <p class="form-row form-row-first">
            <label for="paypalexpress_order_review_username">Username:<span class="required">*</span></label>
            <input style="width: 100%;" type="text" name="username" id="paypalexpress_order_review_username" value="" />
        </p>
        <p class="form-row form-row-last">
            <label for="paypalexpress_order_review_email">Email:<span class="required">*</span></label>
            <input style="width: 100%;" type="email" name="email" id="paypalexpress_order_review_email" value="<?php echo $email; ?>" />
        </p>
        <div class="clear"></div>
        <p class="form-row form-row-first">
            <label for="paypalexpress_order_review_password">Password:<span class="required">*</span></label>
            <input type="password" name="password" id="paypalexpress_order_review_password" class="input-text" />
        </p>
        <p class="form-row form-row-last">
            <label for="paypalexpress_order_review_repassword">Re Password:<span class="required">*</span></label>
            <input type="password" name="repassword" id="paypalexpress_order_review_repassword" class="input-text"/>
        </p>
        <div class="clear"></div>
        <p>
            <input class="button" type="submit" name="createaccount" value="Create Account" />
            <input type="hidden" name="address" value="<?php echo WC()->customer->get_address(); ?>">
        </p>
    </form>
<?php else:
    if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) && isset($is_terms_on) && empty($is_terms_on) && empty($_GET["paymentId"])) : ?>
    
	<p class="form-row terms">
				<label for="terms" class="checkbox lbl_terms"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" class="terms_chkbox" target="_blank">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?></label>
				<input type="checkbox" class="input-checkbox terms_own" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
	</p>
		<?php endif;
		
		
	if ($show_act) {?>
	
	<div class="create-account" class="div_create_act" >
	<p class="form-row form-row-wide create-account div_create_act_para" style="cursor:pointer;">
				<input class="input-checkbox chkcreate_act" id="createaccount" type="checkbox" name="createaccount" value="1"> 
				<label for="createaccount" style="cursor:pointer;" class="checkbox lbl_chkcreate_act">Create an account?</label>
			</p>
				<div class="create_account_child" style="display:none;">
				<p>Create an account by entering the information below. If you are a returning customer please login at the top of the page.</p>

				
					<p class="form-row form-row validate-required woocommerce-validated" id="account_password_field">
					<label for="account_password" class="">Account password
					</label>
					<input type="password" class="input-text" placeholder="Password" value="" name="create_act"/>
					</p>
				
				<div class="clear"></div>
</div>
			</div>
	
		
	<?php }
global $pp_settings;
$cancel_url = isset( $pp_settings['cancel_page'] ) ? get_permalink( $pp_settings['cancel_page'] ) : $woocommerce->cart->get_cart_url();
$cancel_url = apply_filters( 'angelleye_review_order_cance_url', $cancel_url );
echo '<div class="clear"></div>';
echo '<p><a class="button angelleye_cancel" href="' . $cancel_url . '">'.__('Cancel order', 'paypal-for-woocommerce').'</a> ';
echo $inputhtml;
    ?>
    </form><!--close the checkout form-->
<?php endif; ?>

<div class="clear"></div>