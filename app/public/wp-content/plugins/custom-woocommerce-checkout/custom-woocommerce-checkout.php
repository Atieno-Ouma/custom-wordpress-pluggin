<?php
/*
Plugin Name: Custom WooCommerce Checkout
Description: Adds a custom checkout page to WooCommerce page for my technical interview.
Version: 1.0
Author: Atieno Ouma
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add a custom checkout page to WooCommerce.
 */
function custom_woocommerce_checkout_page()
{
    // Check if the current page is the checkout page
    if (is_checkout() && !is_wc_endpoint_url('order-received')) {
        // Load the custom checkout template
        include_once plugin_dir_path(__FILE__) . 'custom-checkout.php';
        exit();
    }
}
add_action('template_redirect', 'custom_woocommerce_checkout_page');