(function($) {
    'use strict';

    $(document).ready(function($) {

        /**
         * JavaScript code for handling multiple tab groups on a web page.
         * This code allows for the independent switching of tabs and their associated content
         * within each tab group.
         *
         * The HTML structure assumes that each tab group is enclosed within a container
         * with the class "smartaipress-tabs." Tabs are marked with the class "smartaipress-tab,"
         * and their associated content is represented by elements with the class "smartaipress-tab-content."
         * Each tab and its content should have the same data-tab attribute to link them.
         *
         * When a tab is clicked, the code identifies its associated tab content within the same group
         * and manages the display of the active tab and content while hiding others.
         */
        $('.smartaipress-tab').on('click', function() {
            var $this = $(this);
            var tabId = $this.data('tab');
            
            // Find the tab content elements within the same tab group
            var $tabContents = $this.closest('.smartaipress-tabs').nextAll('.smartaipress-tab-content');
        
            // Remove the "active" class from all tabs and hide their tab content within the same group
            $this.siblings('.smartaipress-tab').removeClass('smartaipress-tab-active');
            $tabContents.css('display', 'none');
        
            // Add the "active" class to the clicked tab and show the corresponding tab content
            $this.addClass('smartaipress-tab-active');
            $('#' + tabId).css('display', 'block');
        });

        // Listen for changes in the post title input
        $('input[name=post_title]').on('keyup', function() {
            handlePostTitleInput($(this));
        });

        // Function to handle changes in the post title input
        function handlePostTitleInput($input) {
            var text = $input.val().trim();
            $('input[name="smartaipress[post_title]"]').val(text);
        }

        $(document).on('click', function(event) {
            var $tooltip = $('.smartaipress-openai-small-popup');
        
            if (!$tooltip.is(event.target) && $tooltip.has(event.target).length === 0) {
                $tooltip.addClass('sap-hidden');
            }
        });
        
        $('.smartaipress-content-generator-button').on('click', function(event) {
            event.stopPropagation(); // Prevent the click event from propagating to the document
            var $tooltip = $('.smartaipress-openai-small-popup');
            $tooltip.toggleClass('sap-hidden');
        });

        $('.smartaipress-openai-small-popup').on('click', function(event) {
            event.stopPropagation(); // Prevent the click event from reaching this element
        });
        
    });

    /**
     * Send prompt request to the OpenAI
     * 
     * @since 1.0.0
     */
    $(document).ready(function($) {
        var $button = $('.smartaipress-btn-generate');

        /**
         * Disable generate buttons.
         * 
         * @since 1.0.0
         */
        function smartaipressDisableButtons() {
            var $buttons = $(document).find('.smartaipress-btn-generate');
            $buttons.addClass('disabled').attr("disabled", "disabled");
            $buttons.children('i').removeClass('smartaipress-display-none');
        }

        /**
         * Enable generate buttons.
         * 
         * @since 1.0.0
         */
        function smartaipressEnableButtons() {
            var $buttons = $(document).find('.smartaipress-btn-generate');
            $buttons.removeClass('disabled').removeAttr("disabled");
            $buttons.children('i').addClass('smartaipress-display-none');
        }

        /**
         * Start Timers for Multiple Elements
         *
         * Initializes and starts timers for a collection of HTML elements with the class "smartaipress-timer."
         * Each timer counts up from the moment of initialization, displaying elapsed time in the "00:00" format.
         * The interval IDs for each timer are stored in an array for later management.
         *
         * @returns {Array} An array of interval IDs, allowing control and synchronization of the timers.
         */
        function startTimers() {
            const timerElements = document.querySelectorAll(".smartaipress-timer");
            const timerIntervals = [];
        
            timerElements.forEach(function (timerElement) {
                let startTime = new Date().getTime();

                const timerInterval = setInterval(function () {
                    const currentTime = new Date().getTime();
                    const elapsedTime = currentTime - startTime;
            
                    // Calculate minutes and seconds
                    const minutes = Math.floor(elapsedTime / 60000);
                    const seconds = Math.floor((elapsedTime % 60000) / 1000);
            
                    // Format minutes and seconds to display as "00:00"
                    const formattedTime = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
            
                    timerElement.textContent = formattedTime;
                }, 1000);

                // Store the interval ID in the array
                timerIntervals.push(timerInterval);
            });

            return timerIntervals;
        }

        /**
         * Stop Timers
         *
         * Stops multiple timers based on the provided array of interval IDs.
         *
         * @param {Array} timerIntervals - An array of interval IDs to be cleared, effectively halting the associated timers.
         */
        function stopTimers(timerIntervals) {
            timerIntervals.forEach(function (interval) {
                clearInterval(interval);
            });
        }

        $button.on('click', function(event) {
            const timerIntervals = startTimers();
            var openaiData = {
                post_title: $(this).closest('form').find('input[name="smartaipress[post_title]"]').val(),
                focus_keyword: $(this).closest('form').find('input[name="smartaipress[focus_keyword]"]').val(),
                model: $(this).closest('form').find('select[name="smartaipress[openai_model]"]').val(),
                language: $(this).closest('form').find('select[name="smartaipress[openai_default_language]"]').val(),
                maximum_length: $(this).closest('form').find('input[name="smartaipress[maximum_length]"]').val(),
                creativity: $(this).closest('form').find('select[name="smartaipress[creativity]"]').val(),
                tone_of_voice: $(this).closest('form').find('select[name="smartaipress[tone_of_voice]"]').val(),
                post_type: $('input[name=post_type]').val(),
            };

            // Disable buttons.
            smartaipressDisableButtons();

            $.ajax({
                type: 'POST',
                url: smartaipress.ajax_url,
                data: {
                    action: 'smartaipress_openai_send_prompt',
                    nonce: smartaipress.nonce,
                    smartaipress: openaiData
                }, 
                success: function(response) {

                    // Stop timers
                    stopTimers(timerIntervals);

                    // Errors handler.
                    if ( response.data.error && response.data.error.message !== undefined ) {
                        swal.fire({
                            title: response.data.error.label ?? response.data.error.label,
                            text: response.data.error.message,
                            icon: 'error',
                            confirmButtonText: 'Ok',
                        }).then(($result) => {
                            // Enable buttons
                            smartaipressEnableButtons();
                        });
                    }

                    // Insert content to editor
                    if ( response.success && response.data.content && response.data.content !== undefined ) {
                        swal.fire({
                            title: openaiData.post_title,
                            html: '<div class="smartaipress-html-content">'+response.data.content+'</div>',
                            footer: '<a href="https://smartaipress.com/pricing/?utm_source=smartaipress-wordpress-plugin&utm_medium=classic-editor-popup&utm_campaign=sweetalert2" target="_blank">Upgrade to PRO</a>',
                            icon: 'success',
                            confirmButtonText: smartaipress.insert_to_txteditor_label,
                            showCancelButton: true,
                            cancelButtonText: smartaipress.cancel_btn_label,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var htmlContent = $(".smartaipress-html-content").html();
                                var editor = tinymce.get('content');
                                var $tooltip = $('.smartaipress-openai-small-popup');

                                // Gutenberg editor
                                if (smartaipress.is_block_editor) {
                                    var insertedBlock = wp.blocks.createBlock('core/freeform', {
                                        content: htmlContent,
                                    });

                                    wp.data.dispatch('core/block-editor').insertBlocks(insertedBlock);
                                } else {
                                    if (editor) {
                                        editor.setContent(htmlContent);
                                    } else {
                                        // Handle the case where the editor is not found or not initialized
                                        console.error('TinyMCE editor not found or not initialized');
                                    }
                                }

                                $tooltip.addClass('sap-hidden');
                            }

                            smartaipressEnableButtons();
                        });
                    }
                
                },
                error: function(response) {
                    
                    // Stop timers
                    stopTimers(timerIntervals);

                    swal.fire({
                        title: 'Error',
                        text: 'Error occured during the Ajax request.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        smartaipressEnableButtons();
                    });
                }
            });
        });

         /**
         * IMAGE GENERATION
         * ------------------------------------------------------------------------ *
         */

        /**
         * Adds a SmartAIPress button to the Featured Image section.
         *
         * This function retrieves an HTML button template from the element with the ID 'smartaipress-openai-generate-image-btn-template'
         * and appends it to the 'postimagediv' section after a short delay (1000 milliseconds). This feature is designed to enhance
         * the user interface and functionality of the featured image section in a web application.
         */
        function addDalleLinkToFeaturedImage() {
            let generateImageBtn = $('#smartaipress-openai-generate-image-btn-template').html();
            setTimeout(function(){
                $("#postimagediv .inside").append(generateImageBtn);
            }, 1000);
        }

        addDalleLinkToFeaturedImage();

        $('#postimagediv').on("click", "#remove-post-thumbnail", function() {
            addDalleLinkToFeaturedImage();
        });

        function resizeImageUrl(originalUrl, size) {
            // Check if the original URL contains a file extension (e.g., .png, .jpg)
            const fileExtension = originalUrl.split('.').pop();
            if (!fileExtension) {
                return originalUrl; // URL doesn't seem to have a file extension
            }
        
            // Extract the base URL (excluding the file extension)
            const baseUrl = originalUrl.slice(0, -(fileExtension.length + 1));
        
            // Construct the new URL with the desired size parameter
            const resizedUrl = `${baseUrl}-${size}.${fileExtension}`;
            return resizedUrl;
        }

        // The generate image popup
        $(document).on("click", "#smartaipress-openai-show-generate-image-popup", function() {
            let dallePopupTemplate = $('#smartaipress-swal-dalle-popup-template').html();
            swal.fire({
                title: 'SmartAIPress Image',
                html: dallePopupTemplate,
                showConfirmButton: false,
                showCloseButton: true,
                allowOutsideClick: false,
            });
        });

        let selectedImageModel = 'dall-e-2';
        let selectedImageResolution = '256x256';
        let selectedImageStyle = 'classic';

        $(document).on('change', 'select[name=smartaipress-dalle-image-model]', function() {
            selectedImageModel = this.value;
            if ("dall-e-3" == selectedImageModel || "dall-e-3-hd" == selectedImageModel) {
                $('#smartaipress-openai-image-form .sap-maybe-hide').hide();
                $('#smartaipress-openai-image-form .sap-maybe-show').show();
            } else {
                $('#smartaipress-openai-image-form .sap-maybe-show').hide();
                $('#smartaipress-openai-image-form .sap-maybe-hide').show();
            }
        });

        $(document).on('change', 'select[name=smartaipress-dalle-image-style]', function() {
            selectedImageStyle = this.value;
        });

        $(document).on('change', 'select[name=smartaipress-dalle-image-resolution]', function() {
            selectedImageResolution = this.value;
        });

        /* #################################################################################################
         * # SEND REQUEST IMAGE GENERATION TO OPENAI SERVER
         * ################################################################################################# */
        $(document).on('click', '#smartaipress-send-image-request-btn', function(e) {
            const theButton = $(this);
            const setAsFeaturedBTN = $(this).closest('.smartaipress-openai-swal-content').find('#smartaipress-set-featured-image');
            const imagePlaceholder = $(this).closest('.smartaipress-openai-swal-content').find('.smartaipress-image-placeholder');

            let imagePromptLength = $('#smartaipress-image-prompt').val().length;
            let validationError = false;

            // Validate the prompt field
            if (!imagePromptLength) {
                $("#smartaipress-openai-image-form").find('.smartaipress-error-message').html( smartaipress.promptImageRequiredMsg );
                validationError = true;
            } else if(selectedImageModel == 'dall-e-2' && imagePromptLength > 1000) {
                $("#smartaipress-openai-image-form").find('.smartaipress-error-message').html( smartaipress.dall_e_2_max_length );
                validationError = true;
            } else {
                $("#smartaipress-openai-image-form").find('.smartaipress-error-message').empty();
                validationError = false;
            }

            if(validationError) {
                return;
            }

            // Disable the button.
            imagePlaceholder.removeClass('smartaipress-display-none');
            theButton.addClass('disabled').attr("disabled", "disabled");
            theButton.children('i').removeClass('smartaipress-display-none');
            setAsFeaturedBTN.addClass('disabled').attr("disabled", "disabled");

            $.ajax({
                type: 'POST',
                url: smartaipress.ajax_url,
                data: {
                    action: 'smartaipress_openai_generate_image',
                    nonce: smartaipress.nonce,
                    image: $('#smartaipress-image-prompt').val(),
                    resolution: selectedImageResolution,
                    model: selectedImageModel,
                    style: selectedImageStyle,
                    post_type: $('input#post_type').val(),
                }
            }).done(function(response) {
                // Clear error messages
                $('.smartaipress-js-error').remove();

                $("#smartaipress-openai-image-form").find('.smartaipress-error-message').empty();

                setAsFeaturedBTN.removeClass('disabled').removeAttr('disabled');
                
                // Errors handler.
                if(response.data.error && response.data.error.message !== undefined) {
                    Swal.fire({
                        title: response.data.error.label ?? '',
                        text: response.data.error.message ?? '',
                        icon: 'error',
                        confirmButtonText: "Ok",
                    });
                }

                // Success response.
                if(response.success && response.data) {
                    let imagePlaceholder = document.querySelector('.smartaipress-image-animated-background');
                    let imageURL = response.data.data[0].url;
                    let imageHTML = "<img src=" + imageURL + "  style='width: 100%;'>";
                    let setAsFeaturedBTN = document.getElementById('smartaipress-set-featured-image');

                    if (imageURL) {
                        imagePlaceholder.innerHTML = imageHTML;
                        setTimeout(function(){
                            setAsFeaturedBTN.classList.remove('smartaipress-display-none');
                        }, 1000);
                    }
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                swal.fire({
                    title: 'Error',
                    text: errorThrown,
                    icon: 'error',
                    confirmButtonText: 'Ok',
                });
            }).always(function() {
                theButton.removeClass('disabled').removeAttr('disabled');
                theButton.children('i').addClass('smartaipress-display-none');
            });
        });

        /* #################################################################################################
         * # SET FEATURED IMAGE
         * ################################################################################################# */
        $(document).on('click', '#smartaipress-set-featured-image', function(e) {
            const theButton = $(this);
            const imageURL = $('.smartaipress-image-animated-background > img').attr('src');

            theButton.addClass('disabled').attr('disabled', 'disabled');

            $.ajax({
                type: 'POST',
                url: smartaipress.ajax_url,
                data: {
                    action: 'smartaipress_openai_upload_and_set_featured_image',
                    nonce: smartaipress.nonce,
                    imageurl: imageURL,
                    post_id: $('input[name=post_ID]').val(),
                }
            })
            .done(function(response) {
                
                // Clear error messages
                $('.smartaipress-js-error').remove();

                // Handle errors
                if(response.data.error && response.data.error.message !== undefined) {
                    $('.smartaipress-openai-swal-content').prepend('<code class="smartaipress-js-error">' + response.data.error.message + '</code>');
                    return;
                }

                // Success response
                if(!response.data.error) {
                    const originalURL = response.data.url;
                    const resizedURL = resizeImageUrl(originalURL, '150x150');
                    const imageId = response.data.image_id;
                    const postId = response.data.post_id;

                    if(smartaipress.is_block_editor) {

                        wp.data.dispatch('core/editor').editPost({ featured_media: imageId }, postId);

                    } else {

                        let featuredImageHTML = $('#smartaipress-featured-image-tmpl').html();

                        featuredImageHTML = featuredImageHTML
                            .replace(/\{postId}/g, postId)
                            .replace(/\{originalUrl}/g, originalURL)
                            .replace(/\{resizedUrl}/g, resizedURL)
                            .replace(/\{imageId}/g, imageId);

                        wp.media.featuredImage.set(imageId);
                        addDalleLinkToFeaturedImage();

                    }

                    swal.fire({
                        'title': response.data.label ?? '',
                        'text': response.data.message ?? '',
                        icon: 'success',
                        confirmButtonText: 'Ok',
                    });
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                swal.fire({
                    title: 'Error',
                    text: errorThrown,
                    icon: 'error',
                    confirmButtonText: 'Ok',
                });
            })
            .always(function() {
                theButton.removeClass('disabled').removeAttr('disabled');
            });
        });

    });

})(jQuery);
