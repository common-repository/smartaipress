(function( $ ) {
	'use strict';
	
	$(document).ready(function($) {

        $(document).on("click", "#deactivate-smartaipress", function(event) {

            if(!smartaipress_deactivation_obj.deactivation_data_sent) {
                event.preventDefault();
                
                let deactivationFeedbackTemplate = $("#smartaipress-swal-deactivation-feedback-template").html();

                swal.fire({
                    title: 'Deactivation Feedback',
                    html: deactivationFeedbackTemplate,
                    showConfirmButton: false,
                    showCloseButton: true,
                    allowOutsideClick: false,
                });
            }
        });

        $(document).on("click", "#send-deactivation-data-btn", function(event) {
            if(!smartaipress_deactivation_obj.deactivation_data_sent) {
                
                event.preventDefault();

                const theButton = $(this);

                theButton.addClass('disabled').attr("disabled", "disabled");
                theButton.children('i').removeClass('smartaipress-display-none');

                setTimeout(function() {
                    $.ajax({
                        type: 'POST',
                        url: smartaipress_deactivation_obj.ajax_url,
                        data: {
                            action: 'smartaipress_send_deactivation_data',
                            nonce: smartaipress_deactivation_obj.nonce,
                            reason: $("#smartaipress-deactivation-feedback-reason").val(),
                            message: $("#smartaipress-deactivation-feedback-message").val()
                        }
                    }).done(function(response) {
                        //
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        //
                    }).always(function() {
                        theButton.removeClass('disabled').removeAttr('disabled');
                        theButton.children('i').addClass('smartaipress-display-none');
                        location.reload();
                    });
                }, 1000);
            }
        });

	});

})( jQuery );