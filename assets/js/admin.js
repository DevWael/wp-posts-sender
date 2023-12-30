(function ($) {
    'use strict';

    $(document).on('click', '.wp-posts-sender-button', function (e) {
        e.preventDefault();
        let button = $(this),
            siteUrl = $(this).data('site-url'),
            nonce = $(this).data('nonce'),
            postId = $(this).data('post-id');

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
                button.append('<span class="dashicons dashicons-update"></span>');
            },
            success: function (response) {
                console.log(response);
                if (response.success) {
                    // remove loading indicator
                    button.find('span').remove();
                    button.append('<span class="dashicons dashicons-yes"></span>');
                }
            }
        });
    });

})(jQuery);