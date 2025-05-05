<?php
/**
 * Plugin Name: Metalrom Step Discounts
 * Plugin URI: https://botezatucatalin.ro
 * Description: Adaugă discounturi progresive la produse în funcție de cantitate și cost transport personalizat.
 * Version: 1.0.1
 * Author: Botezatu Catalin
 * Author URI: https://botezatucatalin.ro
 * License: GPL2
 * Text Domain: metalrom-step-discounts
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/admin-fields.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend-display.php';
require_once plugin_dir_path(__FILE__) . 'includes/cart-checkout-logic.php';

add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_style('metalrom-frontend-style', plugin_dir_url(__FILE__) . 'assets/frontend.css');
        wp_enqueue_script('metalrom-frontend', plugin_dir_url(__FILE__) . 'assets/frontend.js', ['jquery'], null, true);
        wp_localize_script('metalrom-frontend', 'metalrom_ajax', [
            'url' => admin_url('admin-ajax.php')
        ]);
    }
        wp_localize_script('metalrom-frontend-js', 'metalrom_ajax', [
            'url' => admin_url('admin-ajax.php'),
        ]);
    

    if (is_cart() || is_checkout()) {
        wp_enqueue_script('metalrom-step-discounts-js', plugin_dir_url(__FILE__) . 'assets/metalrom-ui.js', ['jquery'], '1.0.0', true);
        wp_localize_script('metalrom-step-discounts-js', 'metalrom_data', [
            'use_custom_shipping' => WC()->session->get('metalrom_use_custom_shipping') ? true : false,
        ]);
    }
});

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_style('metalrom-admin-style', plugin_dir_url(__FILE__) . 'assets/metalrom-admin.css');
        wp_enqueue_script('metalrom-admin-js', plugin_dir_url(__FILE__) . 'assets/metalrom-admin.js', ['jquery'], null, true);
    }
});


