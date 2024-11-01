(function( $ ) {
	'use strict';
	
	$(document).ready(function($) {

        sendActivationData();

        /**
		 * Send activation data
		 * 
		 * @since 1.0.3
		 */
        function sendActivationData() {
            if(smartaipress_activation_obj.activation_data_sent) {
                return;
            }
            $.ajax({
                type: 'POST',
                url: smartaipress_activation_obj.ajax_url,
                data: {
                    action: 'smartaipress_send_activation_data',
                    nonce: smartaipress_activation_obj.nonce
                }
            }).done(function(response) {
                //
            }).fail(function(jqXHR, textStatus, errorThrown) {
                //
            }).always(function() {
                //
            });
        }

	});

})( jQuery );