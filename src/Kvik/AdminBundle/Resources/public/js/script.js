/**
 * LK: Autocomplete to find terms
 *
 */
function split( val ) { return val.split( /,\s*/ ); }
function extractLast( term ) { return split( term ).pop(); }

$(document).ready(function(e) {
    /**
     * Ajout et suppression des étiquettes
     */
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
     * Coloration des onglets dans la page de login
     */
    $('.login-box .nav-item a').on('click', function (e) {
        e.preventDefault();
        $(this).parent().addClass('active');
        $(this).parent().siblings().removeClass('active');
        target = $(this).attr('href');
        $('.tab-content > div').not(target).hide();
        $(target).fadeIn(600);
    });

    /**
     * Gestion des menus dynamiquement
     */
    //: Stop envoie du form dans Menulink : liens personnalisés + Affichage du link dans le bloc du menu
    var form        = $("form#add-link"),
        menulinks   = $( "#menu-links" ),
        mlinks      = $(".menulinks")
    ;
    $("#link-item-button").on("click", function(e){
        e.preventDefault();
        var title       = form .find('[name=link-item-title]').val(),
            url         = form .find('[name=link-item-url]').val(),
            type        = form .find('[name=link-item-type]').val(),
            numero      = $("div#menu-links .card").length,
            container   = $('div#kvik_adminbundle_menu_links')
        ;

        if( title.length > 0 && url.length > 0 ){
            if( type === 'custom') var type_shown = 'Lien personnalisé';
            menulinks.append( '<div class="card" id="link-'+numero+'"><div class="card-header"><button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse'+numero+'" aria-expanded="false" aria-controls="collapse'+numero+'">'+title+'</button><span class="link-type">'+type_shown+'</span></div><div id="collapse'+numero+'" class="collapse" aria-labelledby="link'+numero+'" data-parent="#menu-links"><div class="card-body row"><div class="col-12 menu-input"><input type="hidden" id="kvik_adminbundle_menu_links_'+numero+'_linktype" name="kvik_adminbundle_menu[links]['+numero+'][linktype]" value="'+type+'"> <input class="form-control" id="kvik_adminbundle_menu_links_'+numero+'_name" type="text" name="kvik_adminbundle_menu[links]['+numero+'][name]" value="'+title+'" placeholder="Titre du lien"><input class="form-control" id="kvik_adminbundle_menu_links_'+numero+'_url" type="text" name="kvik_adminbundle_menu[links]['+numero+'][url]" value="'+url+'"placeholder="Adresse URL"></div><div class="col-12 btns"><a href="#" class="text-danger" id="retirer" title="Retirer ce lien du menu">Retirer</a> <a class="text-primary float-right" href="#" data-toggle="collapse" data-target="#collapse'+numero+'" aria-expanded="false" aria-controls="#collapse'+numero+'">Annuler</a></div></div></div></div>')
            ;
            form.get(0).reset();
            makeItDraggable();
            makeItDroppable();
        }
        else{
            $("#custom-link").prepend('<span class="text-danger">Vous devez remplir les deux champs</span>');
        }
        return false;
    });

    //---- Ce qu'il faut faire quand on déplace un élément ----//
    $(".card").on("drag", function () {
        var elemnt      = $(this),
            elemnTop    = parseInt(elemnt.css('top'))
        ;
        if( elemnt.prev().length > 0 ){
            var former = (elemnTop / -50);
            $('#menu-links').each(function () {
                $("div.form-items h3").html( $(':nth-child('+former+')', $(this)).attr('id') );
            });
        }
    });


    //---- Supprime le message d'erreur quand le champ est vide ----//
    mlinks.on("focus", "input.form-control", function () {
        $("span.text-danger").remove();
    });
    //---- Clic sur le bouton retirer menulink ajouté ----//
    $("div#menu-links").on("click", "a#retirer", function(){
        $(this).parent().parent().parent().parent().remove();
        return false;
    });
});
    
function makeItDroppable() {
    $(".form-items").droppable();
}

function makeItDraggable() {
    var card = $(".card");
    card.draggable({
        grid: [ 50, 50 ]
    });
}

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
