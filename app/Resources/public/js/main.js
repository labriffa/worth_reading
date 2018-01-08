$(document).ready(function() {

    if(matchMedia) {
        var mqNav = window.matchMedia( "(min-width: 960px)" );
        mqNav.addListener(widthChange);
    }

    function widthChange(mq) {

        $('.hamburger-menu').on('click', function() {
            if (mq.matches) {
                $('.mobile-menu').hide();
            } else {
                $('.mobile-menu').stop().slideToggle();
            }
        });
    }

    widthChange(mqNav);

    $('.close-icon-js').on('click', function() {
        $('.alert').slideUp();
    });

    // handle ajax request for wishlist button
    $('.wishlist-btn-js').on('click', function() {

        // resolve the current url
        var wishlistUrl = document.location.protocol + '//' + document.location.host + '/wishlist';
        var wishlistAction = 'add';
        $_this = $(this);

        // check if this user has already added this item to their wishlist
        if($_this.hasClass('btn--yellow-fill')) {
            wishlistAction = 'remove';
        }

        // disable the wishlist button and enable the loader
        $_this.attr('disabled', 'disabled');
        $_this.next().css('display', 'inline-block');

        // handle the wishlist ajax request
        $.ajax({
          method: 'POST',
          url: wishlistUrl + '/' + wishlistAction + '/' + $_this.data('id'),
          data: {
              id: $_this.data('id'),
          },
            success: function(data) {
              // remove and add styling based on wishlist request
              if(wishlistAction === 'add') {
                  $_this.removeClass('btn--blue');
                  $_this.addClass('btn--yellow-fill');
                  $_this.text('Remove from Reading List');
              } else {
                  $_this.removeClass('btn--yellow-fill');
                  $_this.addClass('btn--blue');
                  $_this.text('Add to Reading List');
              }
            }
       }).always(function() {
           // re-enable the wishlist button and hide the loader
           $_this.removeAttr('disabled', 'disabled');
           $_this.next().hide();
        });
    });

    // handle the search autosuggest
    $('.js-search-form input').keyup(function(event) {

        var searchTerm = $(this).val();

        // infer the current url
        var searchUrl = document.location.protocol + '//' + document.location.host + '/books/search';

        // empty the current search suggestions
        $('.search-suggestions > li, .search-suggestions p, .search-suggestions__books').remove();

        // only show the auto suggest dropdown if we have more than 3 characters
        if('' !== searchTerm && searchTerm.length >= 3) {
            searchUrl += '?q=' + searchTerm;

            // retrieve the HTML source for the search results page
            $.get(searchUrl, function(data){

                // get all the books from the html
                $booksHtml = $(data).find('.book').slice(0, 3);

                $('.search-suggestions > li, .search-suggestions p, .search-suggestions__books').remove();

                if($booksHtml.length > 0) {

                    // add books

                    $('.search-suggestions').append('<p>Books</p>');

                    $('.search-suggestions').append('<div class="search-suggestions__books">');

                    $booksHtml.each(function (index) {

                        $('.search-suggestions__books').append(
                            '<li class="search-suggestions__book">' +
                                '<a href="' + $(this).find('.book__link').attr('href') + '">' +
                                    '<img src="'+ $(this).find('.book__image').attr('src') +'"/>'
                                        + '<span class="search-suggestions__book-info">' + $(this).find('.book__title').html() + '</span>'
                                            + '<span class="author">' + $(this).find('.book__author').html() + '</span>'
                                        +
                                '</a>' +
                            '</li>');
                    });
                }

                // get all the books from the html
                $authorHtml = $(data).find('.author').slice(0, 3);

                if($authorHtml.length > 0) {

                    // add books

                    $('.search-suggestions').append('<p class="search-suggestions__header--author">Authors</p>');

                    $authorHtml.each(function (index) {

                        $('.search-suggestions').append(
                            '<li>' +
                            '<a href="' + $(this).find('.book__link').attr('href') + '">' +
                            '<img src="' + $(this).find('.grid-image-author').attr('src') + '"/>'
                            + '<span class="search-suggestions__author-info">' + $(this).find('.author__name-title').html() + '</span>' +
                            '</a>'
                            + '</li>');
                    });
                }

                // check if there were no search results
                if($('.search-suggestions li').length === 0) {

                    $('.search-suggestions').append('<p>No Results</p>');

                    $('.search-suggestions').append('<li class="search-suggestions__no-results">' + '<small style="font-style: italic">search for</small> <span style="margin-left: 10px; display: inline-block">"' + searchTerm + '"</span>' + '</li>');
                }

                $('.search-suggestions').show();
                $('.search-suggestions').addClass('search-suggestions-awake');

                $('.overlay').show();
            });
        } else {
            $('.overlay').hide();
            $('.search-suggestions').removeClass('search-suggestions-awake');
        }
    });

    $('.overlay').click(function(){
        $('.search-suggestions').hide();
        $(this).hide();
    });

    $('.role-change-js').on('click', function(event) {

        event.preventDefault();

        var adminUrl = document.location.protocol + '//' + document.location.host + '/admin';
        var $_this = $(this);

        $.ajax({
            method: 'POST',
            url: adminUrl + '/change-role/' + $_this.prev().val() + '/' + $_this.data('id'),
            data: {
                roleId: 0,
                userId: $_this.data('id'),
            },
            success: function(data) {
                console.log(data);
                document.location.reload();
            }
        });
    });
});