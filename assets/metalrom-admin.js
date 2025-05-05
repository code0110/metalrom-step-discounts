jQuery(function($){
    $('.add-discount-row').on('click', function(){
        const index = $('#metalrom-discount-table tbody tr').length;
        const row = `
            <tr>
                <td><input type="number" name="metalrom_discounts[${index}][qty]" class="small-text" min="1" required /></td>
                <td><input type="text" name="metalrom_discounts[${index}][unit]" class="small-text" /></td>
                <td><input type="number" name="metalrom_discounts[${index}][discount]" class="small-text" min="0" max="100" step="0.01" required /></td>
                <td><input type="number" name="metalrom_discounts[${index}][shipping]" class="small-text" min="0" step="0.01" /></td>
                <td><button type="button" class="button remove-row">✕</button></td>
            </tr>`;
        $('#metalrom-discount-table tbody').append(row);
    });

    $(document).on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
    });
});
