function smartaipressInjectImageButton() {
    const buttonElement = document.createElement('button');

    buttonElement.textContent = 'Generate AI image';
    buttonElement.setAttribute("id", "smartaipress-openai-show-generate-image-popup");
    buttonElement.classList.add("smartaipress-content-generator-button", "gutenberg-environment");

    const iconElement = document.createElement('img');
    iconElement.src = smartaipress.logo_without_text;
    iconElement.alt = 'Generate AI image';

    buttonElement.prepend(iconElement);

    const container = document.querySelector('.editor-post-featured-image');

    if (container) {
        container.appendChild(buttonElement);
    }
}

/**
 * Inject the SmartAIPress button into the Gutenberg editor toolbar.
 */
function smartaipressInjectButton() {
    const buttonElement = document.createElement('button');
    buttonElement.textContent = 'SmartAIPress';
    buttonElement.classList.add('smartaipress-btn-generate-gutenberg');

    const iconElement = document.createElement('img');
    iconElement.src = smartaipress.logo_without_text;
    iconElement.alt = 'SmartAIPress';

    buttonElement.prepend(iconElement);

    const toolbarContainer = document.querySelector('.edit-post-visual-editor__post-title-wrapper');

    if (toolbarContainer) {
        toolbarContainer.appendChild(buttonElement);
    }
}

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

document.addEventListener("DOMContentLoaded", (event) => {
    
    setTimeout(() => {
       
        smartaipressInjectButton();

    }, 1000);

    setTimeout(() => {
       
        smartaipressInjectImageButton();

    }, 4000);

    setTimeout(() => {
        // Select the button element with the class '.smartaipress-btn-generate-gutenberg'
        const buttonElement = document.querySelector('.smartaipress-btn-generate-gutenberg');
        
        // Select the post title element with the selector 'h1.wp-block-post-title'
        const postTitle = document.querySelector('h1.wp-block-post-title');
    
        // Add a keyup event listener to the post title element
        postTitle.addEventListener('keyup', () => {
            // Select the element with the ID 'smartaipress-meta-input-post-title'
            const smartaipressMetaTitle = document.getElementById('smartaipress-meta-input-post-title');
    
            // If the smartaipressMetaTitle element exists, set its value to the post title content
            if (smartaipressMetaTitle) {
                smartaipressMetaTitle.value = postTitle ? postTitle.textContent : '';
            }
        });
        
        // Add a click event listener to the button element
        buttonElement.addEventListener('click', (event) => {

            // Show a SweetAlert popup with the title 'SmartAIPress' and HTML content
            swal.fire({
                title: 'SmartAIPress',
                showCloseButton: true,
                allowOutsideClick: false,
                html: document.getElementById('smartaipress-gutenberg-tooltip').innerHTML,
                showConfirmButton: false,
            });
    
            // Select the element with the ID 'sap-input-post-title'
            const smartaipressPostTitle = document.getElementById('sap-input-post-title');
            
            // If the smartaipressPostTitle element exists, set its value to the post title content
            if (smartaipressPostTitle) {
                smartaipressPostTitle.value = postTitle ? postTitle.textContent : '';
            }

            // Click on the generate content button
            const generateContentButton = document.querySelector('.smartaipress-openai-swal .smartaipress-btn-generate');
            generateContentButton.addEventListener('click', (event) => { 
                const Button = event.target;
                const loadIconEl = Button.querySelector('i.smartaipress-display-none');
                const openaiData = {
                    post_title: Button.closest('form').querySelector('input[name="smartaipress[post_title]"]').value,
                    focus_keyword: Button.closest('form').querySelector('input[name="smartaipress[focus_keyword]"]').value,
                    model: Button.closest('form').querySelector('select[name="smartaipress[openai_model]"]').value,
                    language: Button.closest('form').querySelector('select[name="smartaipress[openai_default_language]"]').value,
                    maximum_length: Button.closest('form').querySelector('input[name="smartaipress[maximum_length]"]').value,
                    creativity: Button.closest('form').querySelector('select[name="smartaipress[creativity]"]').value,
                    tone_of_voice: Button.closest('form').querySelector('select[name="smartaipress[tone_of_voice]"]').value,
                    post_type: document.querySelector("input[name=post_type]").value,
                };

                // Check if the <i> element exists and has the class before removing it
                if (loadIconEl) {
                    loadIconEl.classList.remove('smartaipress-display-none');
                }

                Button.disabled = true;

                jQuery.ajax({
                    type: 'POST',
                    url: smartaipress.ajax_url,
                    data: {
                        action: 'smartaipress_openai_send_prompt',
                        nonce: smartaipress.nonce,
                        smartaipress: openaiData
                    }, 
                    success: function(response) {
    
                        // Errors handler.
                        if ( response.data.error && response.data.error.label !== undefined ) {
                            swal.fire({
                                title: response.data.error.label,
                                text: response.data.error.message,
                                icon: 'error',
                                confirmButtonText: 'Ok',
                            }).then(($result) => {
                                // Enable buttons
                                Button.disabled = false;
                                loadIconEl.classList.add('smartaipress-display-none');
                            });
                        }
    
                        // Classic editor handler
                        if ( response.success && response.data.content && response.data.content !== undefined ) {
                            swal.fire({
                                title: openaiData.post_title,
                                html: '<div class="smartaipress-html-content">'+response.data.content+'</div>',
                                footer: '<a href="https://smartaipress.com/pricing/?utm_source=smartaipress-wordpress-plugin&utm_medium=generate-gutenberg-editor-popup&utm_campaign=sweetalert2" target="_blank">Upgrade to PRO</a>',
                                icon: 'success',
                                confirmButtonText: smartaipress.insert_to_txteditor_label,
                                showCancelButton: true,
                                cancelButtonText: smartaipress.cancel_btn_label,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const htmlContent = document.querySelector(".smartaipress-html-content").innerHTML;

                                    var insertedBlock = wp.blocks.createBlock('core/freeform', {
                                        content: htmlContent,
                                    });

                                    wp.data.dispatch('core/block-editor').insertBlocks(insertedBlock);
                                }
    
                                loadIconEl.classList.add('smartaipress-display-none');
                                Button.disabled = false;
                            });
                        }
                    
                    },
                    error: function(response) {
                        swal.fire({
                            title: 'Error',
                            text: 'Error occured during the Ajax request.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            loadIconEl.classList.add('smartaipress-display-none');
                            Button.disabled = false;
                        });
                    }
                });
            });
        });
    }, 1500);
    
});
