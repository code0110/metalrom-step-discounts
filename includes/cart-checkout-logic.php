<?php

add_action('woocommerce_before_calculate_totals', function($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;
    foreach ($cart->get_cart() as $cart_item) {
        $product = $cart_item['data'];
        $discounts = get_post_meta($product->get_id(), '_metalrom_step_discounts', true);
        if (!is_array($discounts)) continue;
        $qty = $cart_item['quantity'];
        $discount = 0;
        foreach ($discounts as $rule) {
            if ($qty >= $rule['qty']) {
                $discount = floatval($rule['discount']);
            }
        }
        if ($discount > 0) {
            $product->set_price($product->get_regular_price() * (1 - $discount / 100));
        }
    }
});

add_action('woocommerce_before_calculate_totals', function() {
    $max_shipping = 0;
    $use_custom_shipping = false;
    foreach (WC()->cart->get_cart() as $cart_item) {
        $discounts = get_post_meta($cart_item['product_id'], '_metalrom_step_discounts', true);
        if (!is_array($discounts)) continue;
        foreach ($discounts as $rule) {
            if ($cart_item['quantity'] >= $rule['qty']) {
                $shipping = floatval($rule['shipping']);
                if ($shipping > $max_shipping) {
                    $max_shipping = $shipping;
                    $use_custom_shipping = true;
                }
            }
        }
    }
    WC()->session->set('metalrom_use_custom_shipping', $use_custom_shipping);
    WC()->session->set('metalrom_shipping_value', $max_shipping);
}, 8);

add_action('woocommerce_cart_calculate_fees', function($cart) {
    if (WC()->session->get('metalrom_use_custom_shipping')) {
        $value = floatval(WC()->session->get('metalrom_shipping_value'));
        if ($value > 0) {
            $cart->add_fee(__('Servicii curierat', 'metalrom-step-discounts'), $value, true);
        }
    }
});

add_filter('woocommerce_package_rates', function($rates, $package) {
    return WC()->session->get('metalrom_use_custom_shipping') ? [] : $rates;
}, 100, 2);

add_action('woocommerce_cart_updated', function() {
    if (!WC()->session->get('metalrom_use_custom_shipping')) {
        WC()->session->__unset('chosen_shipping_methods');
        WC()->session->__unset('shipping_for_package_0');
    }
});
// ✅ Servicii curieratul personalizat elimină nevoia unei metode de livrare, dar nu și adresa de livrare
add_filter('woocommerce_cart_needs_shipping', function($needs_shipping) {
    if (function_exists('WC') && WC()->session && WC()->session->get('metalrom_use_custom_shipping')) {
        return false; // Nu mai e nevoie de metode standard de livrare
    }
    return $needs_shipping;
});

// ✅ Păstrăm întotdeauna formularul de adresă de livrare
add_filter('woocommerce_cart_needs_shipping_address', '__return_true');

// ✅ Afișăm mențiune în locul adresei doar dacă WooCommerce crede că nu e necesară
add_filter('woocommerce_order_get_shipping_to_display', function($address, $order) {
    if (!$order->needs_shipping()) {
        return __('- (Servicii curierat)', 'metalrom-step-discounts');
    }
    return $address;
}, 10, 2);

add_action('woocommerce_before_calculate_totals', 'metalrom_apply_cart_discounts', 20, 1);

function metalrom_apply_cart_discounts($cart) {
    if (is_admin() || did_action('woocommerce_before_calculate_totals') > 1) return;

    $product_quantities = [];

    // Calculăm cantitatea totală per produs
    foreach ($cart->get_cart() as $cart_item) {
        $pid = $cart_item['product_id'];
        if (!isset($product_quantities[$pid])) {
            $product_quantities[$pid] = 0;
        }
        $product_quantities[$pid] += $cart_item['quantity'];
    }

    foreach ($cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];
        $product = $cart_item['data'];

        // Obține lista de discounturi
        $discounts = get_post_meta($product_id, 'metalrom_discounts', true);
        if (!is_array($discounts)) continue;

        // Cantitate totală pentru produsul respectiv
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
