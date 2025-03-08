jQuery(document).ready(function ($) {
    // AJAX category filter
    $('.tdn-custom-category-button').on('click', function () {
        var category = $(this).data('category');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_posts',
                category: category
            },
            success: function (response) {
                $('.tdn-custom-posts-container').html(response);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
            }
        });
    });
});