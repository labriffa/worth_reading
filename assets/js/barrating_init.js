$(document).ready(function() {

    var barrating = require('jquery-bar-rating');

    // the overall single book rating
    var bookAvgRating = $('#book-avg-rating-js').data('rating');

    $('#book-avg-rating-js').barrating({
        theme: 'fontawesome-stars-o',
        readonly: true,
        initialRating: $('#book-avg-rating-js').data('rating')
    });

    if(bookAvgRating === 0) {
        $('#book-avg-rating-js + .br-widget a').removeClass('br-selected');
    }

    // list of books e.g. grid of books
    $('.book-list-avg-rating-js').each(function() {

        var bookAvgRating = $(this).data('rating');

        $(this).barrating({
            theme: 'fontawesome-stars-o',
            initialRating: bookAvgRating,
            readonly: true,
        });

        if(bookAvgRating === 0) {
            $(this).find('+ .br-widget a').removeClass('br-selected');
        }
    });

    // single book review form
    var singleBookReviewInitialRating = 1;

    if((1 === $('#user-reviewed-js').length)) {
        singleBookReviewInitialRating = $('#user-reviewed-js').data('rating');
    }

    $('.book_review_form_stars-js').barrating({
        theme: 'fontawesome-stars',
        initialRating: singleBookReviewInitialRating,
        onSelect: function (value, text, event) {
            $('.book_review_form_stars-js option').removeAttr('selected');
            $('.book_review_form_stars-js option[value='+ value +']').attr('selected', 'selected');
        },
    });
    

    // single book list of reviews
    $('.review-rating').each(function(value, text, event) {
        $(this).barrating({
            theme: 'fontawesome-stars-o',
            readonly: true,
            initialRating: $(this).data('rating'),
        });
    });
});