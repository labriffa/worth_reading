$("#appbundle_book_bookCoverFile").removeAttr('required');

var text = $('.body-param__example microlight').text();
text.replace('"Unknown Type: ', "");
$('.body-param__example microlight').text(text);

