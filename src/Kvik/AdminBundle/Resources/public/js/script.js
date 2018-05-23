/**
 * LK: Autocomplete to find terms
 *
 */
function split( val ) { return val.split( /,\s*/ ); }
function extractLast( term ) { return split( term ).pop(); }
$(document).ready(function(e) {
    $('#tags_choosen').on('click', 'a', function () {
        var newchoosen = '',
            choosen = $('.tags_choosen')
        ;
        choosen.val( choosen.val().replace($(this).text(), '') );
        this.remove();
        if( choosen.val().charAt(0) === ',' ){
            newchoosen = choosen.val().replace(',','');
            choosen.val(newchoosen);
        }
        if( choosen.val().charAt(choosen.val().length-1) === ',' ){
            newchoosen = choosen.val().replace(',','');
            choosen.val(newchoosen);
        }
        choosen.val(choosen.val().replace(',,',','));
        return false;
    });


    /**
     * LK: forms coloration
     */
    $('.login-box .nav-item a').on('click', function (e) {
        e.preventDefault();
        $(this).parent().addClass('active');
        $(this).parent().siblings().removeClass('active');

        target = $(this).attr('href');

        $('.tab-content > div').not(target).hide();

        $(target).fadeIn(600);

    });

});
function completeTags(tags){
    $( "#tags" ).autocomplete({
        minLength: 1,
        source: function( request, response ) {
            response( $.ui.autocomplete.filter(
                tags, extractLast( request.term ) ) );
        },
        focus: function() {
            return false;
        },
        select: function( event, ui ) {
            var tag_form = $('.tags_choosen');
            var terms = split( this.value );
            terms.pop();

            this.value = '';
            if( tag_form.val().indexOf(ui.item.value) > -1 ){
                $('#tags_error').append('<div class="alert alert-danger alert-dismissible fade show" role="alert">Tag déjà ajouté !<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
            else{
                $('#tags_choosen').append('<a class="tag_added" href="#">'+ ui.item.value +'<i class="fas fa-times"></i></a>');
                if( tag_form.val().length === 0 ) tag_form.val( ui.item.value );
                else tag_form.val( tag_form.val() + ','+ ui.item.value );
                return false;
            }
        },
        response: function(event, ui) {
            if ( !ui.content.length ) {
                var noResult = { value: $('#tags').val(), label:"Ajouter" };
                ui.content.push(noResult);
            }
        }
    });
}


