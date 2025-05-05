jQuery(function($) {
    const inputQty = $('input.qty');
    const priceBox = $('.summary .price .woocommerce-Price-amount').first();
    let container = $('#metalrom-step-discounts[data-context="main"]').first();

    if (!container.length) {
        container = $('#metalrom-step-discounts').first();
    }

    const productId = container.data('product_id');

    let regularPrice = parseFloat($('#metalrom-regular-price').val());

    if (isNaN(regularPrice)) {
        const priceText = $('.price del .woocommerce-Price-amount').first().text().replace(/[^0-9.,]/g, '').replace(',', '.');
        regularPrice = parseFloat(priceText);
    }

    function getCartQty(callback) {
        if (!productId) {
            console.warn('⚠️ productId invalid');
            callback(0);
            return;
        }

        $.ajax({
            url: metalrom_ajax.url,
            method: 'POST',
            data: {
                action: 'metalrom_get_cart_qty',
                product_id: productId
            },
            success: function(response) {
                const cartQty = parseInt(response) || 0;
                callback(cartQty);
            },
            error: function(xhr) {
                console.error('❌ Eroare AJAX:', xhr.responseText);
                callback(0);
            }
        });
    }

    function updateDisplay(totalQty) {
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
    const qty = matched.data('qty');

    const tvaRate = 0.19;
    const shippingWithTva = ship * (1 + tvaRate);

    const msg = `
    <strong>💯 Prag activ:</strong> ${qty} buc — <strong>${disc}%</strong> reducere<br>
    <span style="display:inline-block;margin-top:4px;">
        🚚 Transport: ${ship === 0
            ? '<strong style="color:green;">Gratuit</strong>'
            : shippingWithTva.toFixed(2) + ' lei cu TVA'}
    </span>
`;


    msgBox.html(msg).show();

    const finalPrice = (regularPrice * (1 - disc / 100)).toFixed(2);
    priceBox.html(finalPrice + ' lei');
}
 else {
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

    function setInitialQtyFromCart() {
        getCartQty(function(cartQty) {
            if (cartQty > 0) {
                inputQty.val(cartQty);              // 🟢 setăm inputul
                updateDisplay(cartQty);             // 🟢 afișăm pragul direct
            }
        });
    }

    // Inițializăm
    inputQty.on('input change', update);
    setInitialQtyFromCart(); // setăm cantitatea dacă există în coș
});
