function log(msg) {
    if ($('#logCheckbox').is(':checked')) {
        $('<p></p>').html(msg).appendTo('#trash');
    }
}

function getXPath( element ) //Retourne le XPATH d'un élément DOM. Permet de définir de façon unique quel noeud du XML est affecté par une modif.
{
    var xpath = '';
    for ( ; element && element.nodeType == 1; element = element.parentNode )
    {
        var id = $(element.parentNode).children(element.tagName).index(element) + 1;
        id > 1 ? (id = '[' + id + ']') : (id = '');
        xpath = '/' + element.tagName.toLowerCase() + id + xpath;
    }
    return xpath;
}

////////////////


function load() { //Télécharge le XML et remplis les formulaires
    viderChamps();
    $.ajax({
           type: "GET",
           url: "../encyclixes/"+name+"/"+name+".xml",
           async: false,
           dataType: "xml",
           success: function(xml) {
               ecx = $(xml).find('encyclix:first');
               remplirChamps();
           }
    });
}

function remplirChamps() {
    //Basic props
    afficherEditeur($('#basicprops'),ecx.find('numero:first'),'Numéro : ','text',6);
    afficherEditeur($('#basicprops'),ecx.find('date:first'),'Date (A-M-J): ','text',10);
    afficherEditeur($('#basicprops'),ecx.find('semaineliturgique:first'),'Semaine Liturgique : ','text');
    afficherEditeur($('#basicprops'),ecx.find('imagefond:first'),'image de fond : ','text');

    //Edito
    afficherEditeur($('#edito'),ecx.find('edito:first').find('contenu:first'),'','textarea',20);
    //Web
    afficherEditeur($('#web'),ecx.find('web:first').find('titre:first'),'titre :','text',10);
    afficherEditeur($('#web'),ecx.find('web:first').find('contenu:first'),'','textarea',10);


    //Agenda
    ecx.find('agenda:first').find('jour').each(function() {
        var j = $('<div class="jour"></div>');
        j.append($(this).attr('nom'));
        $(this).find('evt').each(function() {
            afficherEvenement($(this)).appendTo(j);
        });
        addButton(j,$(this),'+Évènement');
        //j.append('<div class="evenement">Ajouter un évènement</div>');
        j.appendTo($('#agenda'));

    });

    //Annonces
    ecx.find('annonces:first').find('annonce').each(function() {
        var annonce = $('<div class="annonce"></div>');
        afficherEditeur(annonce,$(this).find('titre:first'),'t:','text',30);
        afficherEditeur(annonce,$(this).find('type:first'),'t:','menu',['demi','large']);
        afficherEditeur(annonce,$(this).find('contenu:first'),'','textarea',7);
        supprButton(annonce,$(this),'Supprimer l\'annonce');
        annonce.appendTo($('#annonces'));
    });
    addButton($('#annonces'),ecx.find('annonces:first'),'+Annonce');

    //Pepite de la bible
    afficherEditeur($('#pepite'),ecx.find('pepite:first').find('phrase:first'),'Pepite :','text',37);
    afficherEditeur($('#pepite'),ecx.find('pepite:first').find('ref:first'),'ref : ','text',10);

    //Affiches à gauche
    ecx.find('affiche').each(function() {
        afficherEditeur($('#affiches'),$(this),'Affiche : ','text',20);
        supprButton($('#affiches'),$(this),'Suppr');
    });
    addButton($('#affiches'),ecx.find('affiches'),'+Affiche');

}

function viderChamps() {
    $('#basicprops').html('');
    $('#edito').html('');
    $('#web').html('');
    $('#agenda').html('');
    $('#affiches').html('');
    $('#pepite').html('');
    $('#annonces').html('');

}

function afficherEditeur($divParent, $objXML, titreChamp, type, options) { //options = liste élements pour un menu | taille pour un input text | hauteur our un textarea
    if (!options) {var options=10;}
    switch(type){
        case 'text':
            var aaa = $('<input type="text" size="'+options+'"value="' + $objXML.text() + '">');

            aaa.change(function() {
                path = getXPath($objXML.get(0));
                content = aaa.val();
                log('MODIF '+path + ' | '+ content);
                a = $.ajax({
                    type:"POST",
                    url: "updateXML.php",
                    dataType:"text",
                    async: false,
                    data:{ecx:name,action:'modifXMLNode',path:path,content:content},
                    success: function(xml) {},
                    error: function(xml) { alert('Echec d\'envoi de la modification');}
                });
                log(a.responseText);

            });
            break;
        case 'textarea':

            var aaa = $('<textarea rows="'+options+'" cols="60">'+$objXML.text()+'</textarea>');
            aaa.change(function() {
                path = getXPath($objXML.get(0));
                content = aaa.text();
                log('MODIF '+path + ' | '+ content);
                a = $.ajax({
                    type:"POST",
                    url: "updateXML.php",
                    dataType:"text",
                    async: false,
                    data:{ecx:name,action:'modifXMLNode',path:path,content:content},
                    success: function(xml) {},
                    error: function(xml) { alert('Echec d\'envoi de la modification');}
                });
                log(a.responseText);

            });
            break;
        case 'menu':
            //à refaire avec un joli menu déroulant js ?
            var aaa = $('<select></select>');
            var selected = $objXML.text();
            $.each(options, function(i,obj) {
                obj == selected ? aaa.append('<option name="'+obj+'" selected="selected">'+obj+'</option>')  : aaa.append('<option name="'+obj+'">'+obj+'</option>');
            });
            aaa.change(function() {
                path = getXPath($objXML.get(0));
                content = aaa.val();
                log('MODIF '+path + ' | '+ content);
                a = $.ajax({
                    type:"POST",
                    url: "updateXML.php",
                    dataType:"text",
                    async: false,
                    data:{ecx:name,action:'modifXMLNode',path:path,content:content},
                    success: function(xml) {},
                    error: function(xml) { alert('Echec d\'envoi de la modification');}
                });
                log(a.responseText);

            });

            break;
        default:
            break;
    }

    $('<div style="display:inline;"></div>').append(titreChamp+' ').append(aaa).appendTo($divParent);
}

function afficherEvenement($evt) {
   var e = $('<div class="evenement"></div>');
   afficherEditeur(e,$evt.find('lieu:first'),'l:','menu',['jeangirette','chapelle','bibliotheque','aquarium','autre']);
   afficherEditeur(e,$evt.find('heure:first'),'h:','text',5);
   afficherEditeur(e,$evt.find('titre:first'),'T:','text',20);
   eventToDelete = $evt;
   supprButton(e,$evt);
   return e;
}





function supprButton($divParent,$objXML,titreButton) {
    //Affiche un bouton supprimer dans $divParent, qui supprime l'objet $objXML
    if (!titreButton) {titreButton = 'Supprimer';}
    var b = $('<p style="color:red;border:1px black dotted;margin:0.5em;">'+titreButton+'</p>')

    b.click(function(){
        if(window.confirm('Supprimer l\'élément ? '+getXPath($objXML.get(0)))) {
            del = $.ajax({
                type:"POST",
                url: "updateXML.php",
                dataType:"text",
                async: false,
                data:{ecx:name,action:'deleteNode',path:getXPath($objXML.get(0))},
                success: function(xml) {load();},
                error: function(xml) { alert('Echec d\'envoi de la modification');}
            });
            log(del.responseText);
        }
    }).appendTo($divParent);

}

function addButton($divParent,$objXML,titreButton) {
    //Affiche un bouton ajouter dans $divParent, qui duplique le dernier enfant de $objXML
    if (!titreButton) {titreButton = 'Ajouter';}
    var b = $('<p style="color:green;display:block;margin:0;">'+titreButton+'</p>')
    b.click(function(){
        del = $.ajax({
            type:"POST",
            url: "updateXML.php",
            dataType:"text",
            async: false,
            data:{ecx:name,action:'addNode',path:getXPath($objXML.get(0))},
            success: function(xml) {load();},
            error: function(xml) { alert('Echec d\'envoi de la modification');}
        });
        log(del.responseText);
    }).appendTo($divParent);

}
