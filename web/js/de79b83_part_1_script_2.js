/**
 * LK: Ajoute le formulaire d'ajout de catégorie ou étiquette dans le form Edit
 */
$(document).ready(function() {
    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
    var $container = $('div#kvik_adminbundle_post_newTag');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var index = $container.find(':input').length;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $('#add_term').click(function(e) {
        addCategory($container);

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    if (index == 0) {
        addCategory($container);
    } else {
        // S'il existe déjà des catégories, on ajoute un lien de suppression pour chacune d'entre elles
        $container.children('div').each(function() {
            addDeleteLink($(this));
        });
    }

    // La fonction qui ajoute un formulaire CategoryType
    function addCategory($container) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Catégorie n°' + (index+1))
            .replace(/__name__/g,        index)
        ;

        // On crée un objet jquery qui contient ce template
        var $prototype = $(template);

        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addDeleteLink($prototype) {
        // Création du lien
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');

        // Ajout du lien
        $prototype.append($deleteLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        $deleteLink.click(function(e) {
            $prototype.remove();

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });
    }
});


/**
 * LK: Autocomplete to find terms
 *
 */
function split( val ) { return val.split( /,\s*/ ); }
function extractLast( term ) { return split( term ).pop(); }

$(document).ready(function(e) {
    $('.tag_added').click(function () {
        var newchoosen = '',
            choosen = $('.tags_choosen');

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
            var terms = split( this.value );
            terms.pop();
            if( jQuery.inArray(ui.item.value, terms) === -1 ){
                this.value = '';
                $('#tags_choosen').append('<a class="tag_added" href="#" onclick="this.remove(); return false;">'+ ui.item.value +'<i class="fas fa-times"></i></a>');

                var tag_form = $('.tags_choosen');
                if( tag_form.val().length === 0 ) tag_form.val( ui.item.value );
                else tag_form.val( tag_form.val() + ','+ ui.item.value );
            }
            return false;
        },
        response: function(event, ui) {
            if ( !ui.content.length ) {
                var noResult = { value: $('#tags').val(), label:"Ajouter" };
                ui.content.push(noResult);
            }
        }
    });
}
