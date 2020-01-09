/**
 * Reset CodeManas themes Customizer Options
 */
jQuery(document).ready(function ($) {

    var actionContainer = $('#customize-header-actions'),
        button = $('<input type="submit" name="codemanas-customizer-reset" id="codemanas-customizer-reset" class="button-secondary button">')
            .attr('value', cmCustomizerReset.reset.stringReset)
            .css({
                'float': 'right',
                'margin-top': '9px'
            });

    // Process on click.
    button.on('click', function (event) {
        event.preventDefault();

        // Reset all confirm?
        if (confirm(cmCustomizerReset.reset.stringConfirm)) {

            // Enable loader.
            actionContainer.find('.spinner').addClass('is-active');

            var data = {
                wp_customize: 'on',
                action: 'codemanas_customizer_reset',
                nonce: cmCustomizerReset.reset.security
            };

            // Disable button.
            button.attr('disabled', 'disabled');

            // Process AJAX.
            $.post(ajaxurl, data, function (result) {

                // If pass then trigger the state 'saved'.
                if ('pass' === result.data) {
                    wp.customize.state('saved').set(true);
                }

                var Url = window.location.href;
                Url = Url.split("?")[0];
                window.location.href = Url;

            });
        }
    });

    actionContainer.append(button);
});
