<?php

add_filter('woocommerce_product_data_tabs', function($tabs) {
    $tabs['metalrom_discounts'] = array(
        'label'    => __('Discounturi cantitative', 'metalrom-step-discounts'),
        'target'   => 'metalrom_discounts_data',
        'class'    => array(),
        'priority' => 80,
    );
    return $tabs;
});

add_action('woocommerce_product_data_panels', function() {
    global $post;
    $discounts = get_post_meta($post->ID, '_metalrom_step_discounts', true);
    if (!is_array($discounts)) $discounts = [];

    echo '<div id="metalrom_discounts_data" class="panel woocommerce_options_panel">';
    echo '<div class="options_group">';
    echo '<h3>' . __('Discounturi și transport personalizat', 'metalrom-step-discounts') . '</h3>';
    echo '<table class="widefat" id="metalrom-discount-table">';
    echo '<thead><tr>
            <th>' . __('Cant. Min.', 'metalrom-step-discounts') . '</th>
            <th>' . __('Unitate', 'metalrom-step-discounts') . '</th>
            <th>' . __('Discount %', 'metalrom-step-discounts') . '</th>
            <th>' . __('Transport (lei) fără TVA', 'metalrom-step-discounts') . '</th>
            <th></th>
        </tr></thead><tbody>';

    foreach ($discounts as $index => $rule) {
        echo '<tr>';
        echo '<td><input type="number" name="metalrom_discounts['.$index.'][qty]" value="'.esc_attr($rule['qty']).'" min="1" class="small-text" required /></td>';
        echo '<td><input type="text" name="metalrom_discounts['.$index.'][unit]" value="'.esc_attr($rule['unit']).'" class="small-text" /></td>';
        echo '<td><input type="number" name="metalrom_discounts['.$index.'][discount]" value="'.esc_attr($rule['discount']).'" min="0" max="100" step="0.01" class="small-text" required /></td>';
        echo '<td><input type="number" name="metalrom_discounts['.$index.'][shipping]" value="'.esc_attr($rule['shipping']).'" min="0" step="0.01" class="small-text" /></td>';
        echo '<td><button type="button" class="button remove-row">✕</button></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<p><button type="button" class="button add-discount-row">+ ' . __('Adaugă rând', 'metalrom-step-discounts') . '</button></p>';
    echo '</div></div>';
});

add_action('woocommerce_process_product_meta', function($post_id) {
    if (isset($_POST['metalrom_discounts']) && is_array($_POST['metalrom_discounts'])) {
        $data = array_values(array_filter($_POST['metalrom_discounts'], function($row) {
            return isset($row['qty'], $row['discount']) && $row['qty'] !== '' && $row['discount'] !== '';
        }));

        foreach ($data as &$row) {
            $row['qty'] = (int) $row['qty'];
            $row['unit'] = sanitize_text_field($row['unit']);
            $row['discount'] = (float) $row['discount'];
            $row['shipping'] = isset($row['shipping']) ? (float) $row['shipping'] : 0;
        }

        if (!empty($data)) {
            update_post_meta($post_id, '_metalrom_step_discounts', $data);
        } else {
            delete_post_meta($post_id, '_metalrom_step_discounts'); 
        }
    } else {
        delete_post_meta($post_id, '_metalrom_step_discounts');
    }
});
