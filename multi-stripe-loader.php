<?php

function multi_stripe_style_enqueue()
{
    if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'multi-stripe-setting') {
        wp_enqueue_style('cpm_custom_for_css_admin', plugin_dir_url(__FILE__) . '/style.css',array(),rand(),false);
    }
}
add_action('admin_enqueue_scripts', 'multi_stripe_style_enqueue');


if (is_admin()) {
    add_action('admin_menu', 'add_products_menu_entry', 100);
}

function add_products_menu_entry()
{
    add_submenu_page(
        'woocommerce',
        __('Multi Stripe'),
        __('Stripe Setting'),
        'manage_woocommerce', // Required user capability
        'multi-stripe-setting',
        'multi_stripe_setting_page'
    );
}

function multi_stripe_setting_page()
{
    if (isset($_POST['save'])) {
        $stripe_api_key = sanitize_text_field($_POST['stripe_api_key']);
        $stripe_secret_key = sanitize_text_field($_POST['stripe_secret_key']);
        // $stripe_default_payment = $_POST['stripe_default_payment'];
        update_option('stripe_api_key', $stripe_api_key);
        update_option('stripe_secret_key', $stripe_secret_key);
        // update_option('stripe_default_payment', $stripe_default_payment);

        echo '<div id="message" class="updated inline"><p><strong>Your settings have been saved.</strong></p></div>';
    }
?>
    <div class='container'>
        <div class='main'>
            <div class='main__header'>
                <h2>EURO Stripe settings</h2>
            </div>
            <div class='main__content'>
                <div class='main__settings-form'>
                    <form method='post'>
                        <label class='main__input-label'>Publishable Key:</label>
                        <input class='main__input' placeholder='API Key' type='text' name="stripe_api_key" value="<?php echo !empty(stripe_api_key()) ? stripe_api_key() : ''; ?>">
                        <label class='main__input-label'>Secret Key:</label>
                        <input class='main__input' placeholder='Secret Key' type='text' name="stripe_secret_key" value="<?php echo !empty(stripe_secret_key()) ? stripe_secret_key() : ''; ?>">
                        <!-- <div class="default-payment">
                            <label class='main__input-label'>Make USD as Default:</label>
                            <input class='main__input' type='checkbox' name="stripe_default_payment" <?php //checked(stripe_default_payment(), 'on' ); ?>>
                            <label class='main__input-label'>( Woocommerce Integrated Payment )</label>
                        </div> -->
                        <input type="submit" name="save" value="Save" class='btn main__save-button'>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
}




/**
 * The above PHP code adds a filter to modify the Stripe API key used in WooCommerce Stripe payment
 * requests.
 * 
 * @param params The `params` parameter is an array that contains various parameters for the Stripe
 * payment gateway. In this code snippet, it is being passed as an argument to the `wc_stripe_params`
 * filter.
 * 
 * @return The function `bbloomer_conditional_publishable_key_request` is being returned.
 */
add_filter('wc_stripe_params', 'bbloomer_conditional_publishable_key', 9999);

function bbloomer_conditional_publishable_key($params)
{
    
    $currency = $_COOKIE['wmc_current_currency'];
    if($currency != 'USD'){
        $params['key'] = stripe_api_key();
    }

    return $params;
}

/**
 * The function `bbloomer_conditional_publishable_key_request` sets the Stripe Live Publishable Key for
 * the payment request parameters.
 * 
 * @param params The `` parameter is an array that contains the payment request parameters for
 * the Stripe payment gateway. It includes various information such as the payment amount, currency,
 * billing details, and more.
 * 
 * @return The function `bbloomer_conditional_publishable_key_request` is returning the modified
 * `` array.
 */
add_filter('wc_stripe_payment_request_params', 'bbloomer_conditional_publishable_key_request', 9999);

function bbloomer_conditional_publishable_key_request($params)
{
    $currency = $_COOKIE['wmc_current_currency'];
    if($currency != 'USD'){
        $params['stripe']['key'] = stripe_api_key();
    }

    return $params;
}

/**
 * The function adds the Stripe Live Secret Key to the request headers for WooCommerce Stripe
 * integration.
 * 
 * @param headers_args The parameter "headers_args" is an array that contains the headers that will be
 * sent in the request to the Stripe API. In this specific code snippet, it is being used to add an
 * "Authorization" header to the request.
 * 
 * @return the modified  array with the added 'Authorization' header.
 */
add_filter('woocommerce_stripe_request_headers', 'bbloomer_conditional_private_key_headers', 9999);

function bbloomer_conditional_private_key_headers($headers_args)
{
    $currency = $_COOKIE['wmc_current_currency'];
    if($currency != 'USD'){
        $headers_args['Authorization'] = 'Basic ' . base64_encode(stripe_secret_key() . ':');
    }

    return $headers_args;
}

function stripe_api_key()
{
    $api_key = get_option('stripe_api_key');
    return $api_key;
}
function stripe_secret_key()
{
    $secret_key = get_option('stripe_secret_key');
    return $secret_key;
}
// function stripe_default_payment()
// {
//     $stripe_default_payment = get_option('stripe_default_payment');
//     return $stripe_default_payment;
// }
