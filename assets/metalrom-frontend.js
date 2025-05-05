jQuery(function($) {
    function hideShippingErrors() {
        if (!metalrom_data.use_custom_shipping) return;

        $('tr.woocommerce-shipping-totals.shipping').each(function () {
            if ($(this).text().toLowerCase().includes('nu am găsit') || $(this).text().toLowerCase().includes('nu este disponibilă')) {
                $(this).hide();
            }
        });

        $('ul.woocommerce-error li, ul.woocommerce-info li').each(function () {
            const txt = $(this).text().toLowerCase();
            if (txt.includes('nu a fost selectată') || txt.includes('nu este disponibilă') || txt.includes('nu am găsit')) {
                $(this).hide();
            }
        });
    }

    hideShippingErrors();

    setInterval(hideShippingErrors, 500);

    $(document.body).on("updated_checkout wdUpdateCart", function() {
        setTimeout(hideShippingErrors, 300);
    });
});
