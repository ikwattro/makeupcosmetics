/**
 * Show/Hide bloc shipping_info en fonction du checkbox "données livraison identiques"
 */
$('#form_both').click(function(){
    if ($(this).is(':checked')){
        $('#shipping_info_block').hide();
        $('#same_as_billing').show();
    } else {
        $('#shipping_info_block').show();
        $('#same_as_billing').hide();
    }
});
$(document).ready(function() {
    $('#same_as_billing').hide();
    if ($('#form_both').is(':checked')){
        $('#shipping_info_block').hide();
        $('#same_as_billing').show();
    }
});

// --------------

