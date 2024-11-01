jQuery(document).ready(function($) {
    $('iframe[name=give-embed-form]').on('load', function() {
        var bank_input = $(this).contents().find('select[name=valuepay_bank]');

        if (bank_input.length) {
            // Remove Giving Frequency in donation summary
            $(this).contents().find('.give-donation-summary-table-wrapper tr:nth-child(2)').remove();
        }
    });
});
