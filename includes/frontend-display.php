<?php
// Afișează tabelul cu praguri de discount înainte de câmpul de cantitate
add_action('woocommerce_before_add_to_cart_quantity', function () {
    if (isset($_REQUEST['metalrom_context']) && $_REQUEST['metalrom_context'] === 'sticky') return;

    global $product;
    $discounts = get_post_meta($product->get_id(), '_metalrom_step_discounts', true);
    if (empty($discounts) || !is_array($discounts)) return;

    echo '<div id="metalrom-step-discounts" data-context="main" data-product_id="' . esc_attr(get_the_ID()) . '"><h3>' . __('Discounturi cantitative', 'metalrom-step-discounts') . '</h3>';
    echo '<table class="metalrom-discount-table"><thead><tr><th>Cantitate</th><th>Discount</th><th>Transport</th></tr></thead><tbody>';

    foreach ($discounts as $row) {
        $shipping = floatval($row['shipping']);
        $transp = $shipping === 0.0 ? '<strong class="gratuit">Gratuit</strong>' : wc_price($shipping);
        echo '<tr class="metalrom-row" 
                data-qty="' . esc_attr($row['qty']) . '" 
                data-discount="' . esc_attr($row['discount']) . '" 
                data-shipping="' . esc_attr($row['shipping']) . '">';
        echo '<td>' . esc_html($row['qty']) . ' ' . esc_html($row['unit']) . '</td>';
        echo '<td>' . esc_html($row['discount']) . '%</td>';
        echo '<td>' . $transp . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<div id="metalrom-prag-activ" class="metalrom-summary-box" style="display:none;"></div>';
    echo '<input type="hidden" id="metalrom-regular-price" value="' . esc_attr(wc_get_price_including_tax($product)) . '">';
    echo '</div>';
});



// Include JS și CSS doar în pagina produsului
add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_script('metalrom-frontend', plugin_dir_url(__FILE__) . '../assets/frontend.js', ['jquery'], '1.0.0', true);
        wp_enqueue_style('metalrom-frontend-style', plugin_dir_url(__FILE__) . '../assets/frontend.css');
        wp_localize_script('metalrom-frontend', 'metalrom_ajax', [
            'url' => admin_url('admin-ajax.php')
        ]);
    }
});
add_action('wp_ajax_metalrom_get_cart_qty', 'metalrom_get_cart_qty');
add_action('wp_ajax_nopriv_metalrom_get_cart_qty', 'metalrom_get_cart_qty');

if (!function_exists('metalrom_get_cart_qty')) {
    function metalrom_get_cart_qty() {
        if (!isset($_POST['product_id']) || !function_exists('WC')) {
            wp_send_json(0);
        }

        $product_id = absint($_POST['product_id']);
        $qty = 0;

        if (WC()->cart) {
            foreach (WC()->cart->get_cart() as $cart_item) {
                if (isset($cart_item['product_id']) && (int)$cart_item['product_id'] === $product_id) {
                    $qty += (int)$cart_item['quantity'];
                }
            }
        }

        wp_send_json($qty);
    }
}
