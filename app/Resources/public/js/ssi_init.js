$(document).ready(function() {

    if($('#book-cover-path-js').length > 0) {
        var image = $('#book-cover-path-js').attr("value");
        createFilePreviewer(image, 'book-preview-image');
    }

    $('#appbundle_author_avatarFile').on('change', function() {
        var reader = new FileReader();

        reader.onload = function() {
            var url = reader.result;
            createFilePreviewer('#appbundle_author_avatarFile', url, 'author-avatar-preview-image');
        };

        var files = document.getElementById('appbundle_author_avatarFile').files;
        reader.readAsDataURL(files[0]);
    });

    $('#appbundle_book_bookCoverFile').on('change', function() {
        var reader = new FileReader();

        reader.onload = function() {
          var url = reader.result;
          createFilePreviewer('#appbundle_book_bookCoverFile', url, 'book-preview-image');
        };

        var files = document.getElementById('appbundle_book_bookCoverFile').files;
        reader.readAsDataURL(files[0]);
    });

    $('#appbundle_author_signatureFile').on('change', function() {
        var reader = new FileReader();

        reader.onload = function() {
            var url = reader.result;
            createFilePreviewer('#appbundle_author_signatureFile', url, 'author-signature-preview-image');
        };

        var files = document.getElementById('appbundle_author_signatureFile').files;
        reader.readAsDataURL(files[0]);
    });

    function createFilePreviewer(fileInput, value, className) {
        if($('.'+className).length === 0) {
            $(fileInput).after(
                '<img class="' + className + '" src="'
                + value
                +'"'+'</img>');
        } else {
            $('.'+className).attr('src', value);
        }
    }
});
