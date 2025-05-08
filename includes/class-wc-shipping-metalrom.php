<?php

if (!defined('ABSPATH')) exit;

class WC_Shipping_Metalrom extends WC_Shipping_Method {

    public function __construct() {
        $this->id                 = 'metalrom_shipping';
        $this->method_title       = __('Metalrom Transport', 'metalrom-step-discounts');
        $this->method_description = __('Metodă de livrare personalizată pentru praguri definite pe produs.', 'metalrom-step-discounts');
        $this->enabled            = 'yes';
        $this->title              = __('Servicii curierat', 'metalrom-step-discounts');
        $this->availability       = 'including';
        $this->countries          = ['RO'];
        $this->init();
    }

    public function init() {
        $this->init_settings();
    }

    public function calculate_shipping($package = []) {
    $use_custom = WC()->session->get('metalrom_use_custom_shipping');
    $shipping_value = WC()->session->get('metalrom_shipping_value');

    if (!$use_custom) return; // nu afișa metoda dacă nu e activă

    $rate = [
        'label' => $this->title,
        'cost'  => max(0, floatval($shipping_value)),
        'calc_tax' => 'per_order',
    ];

    $this->add_rate($rate);
}


}
