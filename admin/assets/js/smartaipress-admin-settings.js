(function( $ ) {
	'use strict';
	
	$(document).ready(function($) {

		/**
		 * Sidenav toggle
		 * 
		 * @since 1.0.0
		 */
		$(function() {
			let $body = $('body');
			let $hideIcon = $('.sap-hide-sidenav');
			let $showIcon = $('#sap-sidenav-toggler-init');
		
			// Toggle side navigation on icon click
			$hideIcon.click(function() {
				$body.removeClass('sap-sidenav-show');
			});
		
			$showIcon.click(function() {
				$body.toggleClass('sap-sidenav-show');
			});

			$(window).on('resize', function() {
				if($(window).width() >= 960) {
					$('body').removeClass('sap-sidenav-show');
				}
			});
		});

		/**
		 * Save plugin settings
		 * 
		 * @since 1.0.0
		 */
		$(function() {

			$('#smartaipress-btn-submit').on('click', function() {
				let $loader = $('.sap-loader-box');
				let settingsData = {
					openai_api_key: $('[name="settings[openai_api_key]"]').val(),
					openai_default_model: $('[name="settings[openai_default_model]"]').val(),
					openai_default_language: $('[name="settings[openai_default_language]"]').val(),
					openai_default_tone_of_voice: $('[name="settings[openai_default_tone_of_voice]"]').val(),
					openai_default_creativity: $('[name="settings[openai_default_creativity]"]').val(),
					openai_max_input_length: $('[name="settings[openai_max_input_length]"]').val(),
					openai_max_output_length: $('[name="settings[openai_max_output_length]"]').val(),
					openai_response_log: ($('[name="settings[openai_response_log]"]:checked').val() === undefined) ? '0' : $('[name="settings[openai_response_log]"]:checked').val(),
				};

				$loader.css('display', 'flex');

				setTimeout(function() {
					$.ajax({
						type: 'POST',
						url: smartaipress.ajax_url,
						data: {
							action: 'smartaipress_save_settings',
							nonce: $('#smartaipress_nonce').val(),
							settings: settingsData
						}
					})
					.done(function(response) {
						if (response.data.error && response.data.error.message !== "undefined") {
							Swal.fire({
								title: response.data.error.label ?? '',
								text: response.data.error.message ?? '',
								icon: 'error',
								confirmButtonText: "Ok",
							});
						}
						if (response.data.success && response.data.success.message !== "undefined") {
							Swal.fire({
								position: 'center',
								icon: 'success',
								title: response.data.success.message ?? '',
								showConfirmButton: false,
								timer: 1500
							})
						}
					})
					.fail(function() {
						// Handle AJAX errors (if any)
					})
					.always(function() {
						$loader.css('display', 'none');
					});
				}, 2000);

			});
		});

	});

})( jQuery );
