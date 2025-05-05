jQuery(function($) {
    const inputQty = $('input.qty');
    const priceBox = $('.summary .price .woocommerce-Price-amount').first();
    let regularPrice = parseFloat($('#metalrom-regular-price').val());

    if (isNaN(regularPrice)) {
        const priceText = $('.price del .woocommerce-Price-amount').first().text().replace(/[^0-9.,]/g, '').replace(',', '.');
        regularPrice = parseFloat(priceText);
    }

    let container = $('#metalrom-step-discounts[data-context="main"]').first();
if (!container.length) {
    container = $('#metalrom-step-discounts').first();
}
const productId = container.data('product_id');


    function getCartQty(callback) {
    $.ajax({
        url: metalrom_ajax.url,
        method: 'POST',
        data: {
            action: 'metalrom_get_cart_qty',
            product_id: productId
        },
        success: function(response) {
            callback(parseInt(response) || 0);
        },
        error: function(xhr) {
            console.error('❌ Eroare AJAX:', xhr.responseText);
        }
    });
}


    function updateDisplay(totalQty) {
        let container = $('#metalrom-step-discounts[data-context="main"]').first();
if (!container.length) {
    container = $('#metalrom-step-discounts').first();
}
        const rows = container.find('.metalrom-row');
        let matched = null;

        rows.removeClass('highlight');

        rows.each(function () {
            const row = $(this);
            const minQty = parseInt(row.data('qty'));
            if (totalQty >= minQty) {
                matched = row;
            }
        });

        const msgBox = $('#metalrom-prag-activ');
        if (matched) {
            matched.addClass('highlight');
            const disc = parseFloat(matched.data('discount'));
            const ship = parseFloat(matched.data('shipping'));
            const msg = `<strong>✅ Prag activ:</strong> ${matched.data('qty')} buc — ${disc}% reducere — ` +
                (ship === 0 ? '<strong style="color:green;">Gratuit</strong>' : ship.toFixed(2) + ' lei');
            msgBox.html(msg).show();
            const finalPrice = (regularPrice * (1 - disc / 100)).toFixed(2);
            priceBox.html(finalPrice + ' lei');
        } else {
            msgBox.hide().html('');
            priceBox.html(regularPrice.toFixed(2) + ' lei');
        }
    }

    function update() {
    const enteredQty = parseInt(inputQty.val()) || 0;
    getCartQty(function(existingQty) {
        const totalQty = enteredQty + existingQty;
        updateDisplay(totalQty);
    });
}


    inputQty.on('input change', update);
    update();
});
