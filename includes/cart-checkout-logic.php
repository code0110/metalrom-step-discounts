<?php
function metalrom_prepare_shipping_data() {
    if (!WC()->cart) return;

    $max_shipping = 0;
    $use_custom_shipping = false;

    foreach (WC()->cart->get_cart() as $cart_item) {
        $discounts = get_post_meta($cart_item['product_id'], '_metalrom_step_discounts', true);
        if (!is_array($discounts)) continue;
        foreach ($discounts as $rule) {
            if ($cart_item['quantity'] >= $rule['qty']) {
                $shipping = floatval($rule['shipping']);
                $max_shipping = max($max_shipping, $shipping);
                $use_custom_shipping = true;
            }
        }
    }

    WC()->session->set('metalrom_use_custom_shipping', $use_custom_shipping);
    WC()->session->set('metalrom_shipping_value', $max_shipping);
}

add_action('woocommerce_shipping_init', 'metalrom_prepare_shipping_data');
add_action('woocommerce_before_cart', 'metalrom_prepare_shipping_data');
add_action('woocommerce_before_checkout_process', 'metalrom_prepare_shipping_data');

// Aplică discounturi pe produse conform cantităților
add_action('woocommerce_before_calculate_totals', 'metalrom_apply_cart_discounts', 20, 1);
function metalrom_apply_cart_discounts($cart) {
    if (is_admin() || did_action('woocommerce_before_calculate_totals') > 1) return;

    $product_quantities = [];

    foreach ($cart->get_cart() as $cart_item) {
        $pid = $cart_item['product_id'];
        $product_quantities[$pid] = ($product_quantities[$pid] ?? 0) + $cart_item['quantity'];
    }

    foreach ($cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];
        $product = $cart_item['data'];
        $discounts = get_post_meta($product_id, 'metalrom_discounts', true);
        if (!is_array($discounts)) continue;

        $total_qty = $product_quantities[$product_id];
        $regular_price = $product->get_regular_price();
        $matched_discount = null;

        foreach ($discounts as $entry) {
            if (!isset($entry['qty'], $entry['discount'])) continue;
            if ((int)$total_qty >= (int)$entry['qty']) {
                $matched_discount = $entry;
            }
        }

        if ($matched_discount && isset($matched_discount['discount'])) {
            $discount_pct = floatval($matched_discount['discount']);
            $new_price = round($regular_price * (1 - $discount_pct / 100), wc_get_price_decimals());
            $product->set_price($new_price);
        }
    }
}



// Asigură că WooCommerce consideră că este necesară o metodă de livrare
add_filter('woocommerce_cart_needs_shipping', '__return_true');
add_filter('woocommerce_cart_needs_shipping_address', '__return_true');

// Notificare suplimentară în checkout pentru transparență
add_action('woocommerce_review_order_before_shipping', function () {
    $use = WC()->session->get('metalrom_use_custom_shipping');
    $val = WC()->session->get('metalrom_shipping_value');
    if ($use) {
        echo '<div class="woocommerce-info">Transportul este tratat prin serviciul Metalrom — <strong>' . wc_price($val) . '</strong></div>';
    }
});

// Suprascrie afișarea adresei de livrare în comandă dacă nu este necesară
add_filter('woocommerce_order_get_shipping_to_display', function ($address, $order) {
    if (!$order->needs_shipping()) {
        return __('- (Servicii curierat)', 'metalrom-step-discounts');
    }
    return $address;
}, 10, 2);


add_filter('woocommerce_package_rates', function($rates, $package) {
    $use_custom = WC()->session->get('metalrom_use_custom_shipping');

    if ($use_custom && isset($rates['metalrom_shipping'])) {
        // Păstrăm DOAR transportul Metalrom
        return [
            'metalrom_shipping' => $rates['metalrom_shipping']
        ];
    }

    // În celelalte cazuri (fără prag atins), păstrăm metodele clasice
    return $rates;
}, 100, 2);
