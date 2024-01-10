(function ($) {
    'use strict';

    $(document).on('click', '.wp-posts-sender-button', function (e) {
        e.preventDefault();
        let button = $(this),
            siteUrl = button.data('site-url'),
            nonce = button.data('nonce'),
            postId = button.data('post-id');

        if (button.hasClass('disabled')) {
            return;
        }

        $.ajax({
            url: wp_posts_sender.ajax_url,
            type: 'POST',
            data: {
                action: 'wp_posts_sender',
                site_url: siteUrl,
                nonce: nonce,
                post_id: postId
            },
            // display loading indicator
            beforeSend: function () {
                button.find('span').remove();
                button.parent().find('p').remove();
                button.append('<span class="dashicons dashicons-update" style="margin-top: 4px"></span>');
            },
            success: function (response) {
                if (response.success) {
                    // remove loading indicator
                    button.find('span').remove();
                    button.append('<span class="dashicons dashicons-yes" style="margin-top: 4px"></span>');
                    button.addClass('disabled');
                } else {
                    button.find('span').remove();
                    button.parent().find('p').remove();
                    button.append('<span class="dashicons dashicons-no" style="margin-top: 4px"></span>');
                    button.parent().append('<p class="wp-posts-sender-error" style="color: red">' + response.data.message + '</p>');
                }
            }
        });
    });

})(jQuery);