/**
 * LK: Autocomplete to find terms
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
        $(target).fadeIn(500);
    });

    /**
     * Gestion des menus dynamiquement
     */
    var form        = $("form#add-link"),
        mlinks      = $(".menulinks"),
        sortablelinks = $( "#sortablelinks" ),
        hidesort    = $('input.sorts'),
        li_cards = $("ul#sortablelinks li.card"),
        
        btn_ajouter = $("#link-item-button"),
        btn_retirer = $("a#retirer")
    ;
    
    sortablelinks.sortable({
        //Submenus
        out: function(e, ui) {
            if( ui.position.left > 10 ){
                if( ui.item.prev().get(0) !== undefined ){
                    $('.ui-state-highlight').addClass('sublist-1');
                    ui.item.addClass('sublist-1');
                }
            }
            if( ui.position.left < 10 )ui.item.removeClass('sublist-1');
        },
        placeholder: "ui-state-highlight"
    });
    sortablelinks.disableSelection();
    
    /**
     * Clic sur le bouton pour ajouter menulink
     */
    btn_ajouter.on("click", function(e){
        e.preventDefault();
        sortablelinks.disableSelection();
        var title   = form .find('[name=link-item-title]').val(),
            url     = form .find('[name=link-item-url]').val(),
            type    = form .find('[name=link-item-type]').val(),
            numero  = li_cards.length
        ;
        if( title.length > 0 && url.length > 0 ){
            if( type === 'custom') var type_shown = 'custom';
            sortablelinks.append(
                '<li class="card" id="link-'+numero+'">' +
                    '<div class="card-header">' +
                        '<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse'+numero+'" aria-expanded="false" aria-controls="collapse'+numero+'">'+title+'</button>' +
                        '<span class="link-type">'+type_shown+'</span>' +
                    '</div>' +
                    '<div id="collapse'+numero+'" class="collapse" aria-labelledby="link'+numero+'" data-parent="#sortablelinks">' +
                        '<div class="card-body row">' +
                            '<div class="col-12 menu-input">' +
                                '<input type="hidden" id="kvik_adminbundle_menu_links_'+numero+'_linktype" name="kvik_adminbundle_menu[links]['+numero+'][linktype]" value="'+type+'">' +
                                '<input type="hidden" id="kvik_adminbundle_menu_links_'+numero+'_linktype" name="kvik_adminbundle_menu[links]['+numero+'][position]" value="'+numero+'">' +
                                '<input class="form-control linkname" id="kvik_adminbundle_menu_links_'+numero+'_name" type="text" name="kvik_adminbundle_menu[links]['+numero+'][name]" value="'+title+'" placeholder="Titre du lien">' +
                                '<input type="hidden" id="kvik_adminbundle_menu_links_'+numero+'_parent" name="kvik_adminbundle_menu[links]['+numero+'][parent]">'+
                                '<input class="form-control" id="kvik_adminbundle_menu_links_'+numero+'_url" type="text" name="kvik_adminbundle_menu[links]['+numero+'][url]" value="'+url+'"placeholder="Adresse URL">' +
                            '</div>' +
                            '<div class="col-12 btns">' +
                                '<a href="#" class="text-danger" id="retirer" title="Retirer ce lien du menu">Retirer</a>' +
                                '<a class="text-primary float-right" href="#" data-toggle="collapse" data-target="#collapse'+numero+'" aria-expanded="false" aria-controls="#collapse'+numero+'">Annuler</a>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</li>')
            ;
            form.get(0).reset();
        }
        else $("#custom-link").prepend('<span class="text-danger">Vous devez remplir les deux champs</span>');
        return false;
    });
    /**
     * Clic sur le bouton pour retirer menulink ajouté
     */
    btn_retirer.on("click", function(e){
        var li = $(this).parent().parent().parent().parent();
        li.fadeOut(500);
        li.remove();
        return false;
    });
    /**
     * Supprime le message d'erreur quand le champ est vide
     */
    mlinks.on("focus", "input.form-control", function () {
        $("span.text-danger").remove();
    });
    /**
     * En modifiant l'input on modifie aussi le title du menulink
     */
    $(".card").on('keyup', 'input', function(){
        var that        = $(this),
            idParent    = that.prev().parent().parent().parent().parent()
        ;
        if( that.hasClass('linkname') ){
            idParent.find("button").text( that.val() );
        }
    });
    /** Tris les menus dans l'orde avant l'envoie du formulaire*/
    $('form.sortmenus').on('submit', function (e) {
        e.preventDefault();
        var lis = sortablelinks.find('li'),
            menus = [],
            submenus = []
        ;
        for(var i=0; i<lis.length; i++ ){
            var link = lis.get(i);
            menus.push(lis.get(i).id);
            if( link.classList.contains('sublist-1') ){
                var parent = link.previousElementSibling;
                while( parent.classList.contains('sublist-1') ){
                    parent = parent.previousElementSibling;
                }
                submenus.push({
                    'element': link.id,
                    'parent': parent.id
                });
            }
        }
        hidesort.val( [ JSON.stringify(menus) +';'+ JSON.stringify(submenus) ]);
        console.log(hidesort.val());
        $(this).unbind('submit').submit();
    });

    /**
     * Type de menus a chercher
     */
    var btn_content = $('.content-button button.btn');
    btn_content.on('click', function() {
        if( !$(this).hasClass('active') ){
            $('.content-button button.active').removeClass('active');
            $(this).addClass('active');
        }
    });
    $('#content-search').on('focus', function() {
        var type = $('.content-button button.active').attr('id');
        $(this).autocomplete({
            minLength: 3,
            source: '/autocomplete/content-'+type
        });
    });
});

function completeTags(tags){
    $( "#tags" ).autocomplete({
        minLength: 1,
        source: function( request, response ) {
            response($.ui.autocomplete.filter(tags, extractLast(request.term)));
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
