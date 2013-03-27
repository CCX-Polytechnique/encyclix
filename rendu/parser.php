<?
/******************
Pour toute question s'adresse à cyprien.lecourt@polytechnique.edu
(ajoutez votre mail si vous modifiez le code !)
*******************/

// Entete
header('Content-type: text/html; charset=utf-8');
?>


<?
/*
PARSER de XML pour l'Encyclix
Créé par Cyprien (09)
Sur le modèle d'encyclix de Jean-Eudes (08)
*/
?>

<?
    //$name = "ecx_20110618";
    if (isset($_GET['name'])) { $name = $_GET['name'];}  else die("Sp&eacute;cifier une encyclix &agrave; traiter");
    $path = '../encyclixes/'.$name.'/';
    $EncyclixDOM = new DOMDocument();
    $EncyclixDOM->load($path.$name.'.xml') or die("Erreur DOM");

    $ecx = $EncyclixDOM->getElementsByTagName("encyclix")->item(0);
    //echo $ecx->getElementsByTagName("imagefond")->item(0)->nodeValue;
    function URLimagefond() {
        global $ecx,$path;
        $fond = $ecx->getElementsByTagName("imagefond")->item(0)->nodeValue;
        if ($fond != "") echo($path.$fond);
    }

    function printDate() {
        global $ecx,$path;
        $num = $ecx->getElementsByTagName("numero")->item(0)->nodeValue;
        $d = $ecx->getElementsByTagName("date")->item(0)->nodeValue;
        setlocale(LC_TIME,'fr-FR','fra');date_default_timezone_set('Europe/Paris');
        list($year, $month, $day) = explode('-', $d);
        //$date = strftime("%A %d %B %Y",mktime(0, 0, 0, $month, $day, $year));
        /* Marche pas sur isidore : affiche la date en anglais, berk...*/
        echo('numéro '.$num.' | ');//.$date);
        echo('le '.$day.'/'.$month.'/'.$year);
    }

    function printSemLiturgique() {
        global $ecx,$path;
        $semLit = $ecx->getElementsByTagName("semaineliturgique")->item(0)->nodeValue;
        echo($semLit);
    }


    function printSemaine() {
        global $ecx,$path;
        $semaine = $ecx->getElementsByTagName("agenda")->item(0)->getElementsByTagName("jour");
        $i = 0;
        while($jour = $semaine->item($i)) {
            if ($jour->childNodes->length > 1) {
                 echo('<table>
                        <tr><td colspan="3" class="jour">'.$jour->getAttribute("nom").'</td></tr>');
                        $j = 0;
                        while($evt = $jour->getElementsByTagName("evt")->item($j)) {
                            echo('<tr>
                                    <td class="heure">'.$evt->getElementsByTagName("heure")->item(0)->nodeValue.'</td>
                                    <td class="');
                                    if ($evt->getElementsByTagName("lieu")->item(0)->nodeValue == 'chapelle') echo 'red';
                                    else if ($evt->getElementsByTagName("lieu")->item(0)->nodeValue == 'jeangirette') echo 'green';
                                    else if ($evt->getElementsByTagName("lieu")->item(0)->nodeValue == 'bibliotheque') echo 'blue';
                                    else if ($evt->getElementsByTagName("lieu")->item(0)->nodeValue == 'aquarium') echo 'yellow';
                                    else echo 'black';
                                    echo('" valign="top"></td>
                                    <td class="activite">'.$evt->getElementsByTagName("titre")->item(0)->nodeValue.'</td>
                                </tr>
                            ');
                            $j++;
                        }
                //echo '<tr>'.$jour->getAttribute("nom").$jour->childNodes->length.$i.'<tr>';
                //echo('<img src="IncludeImgs/trait.png" style="padding-top:3px" align="left" /><br /></table>');
                echo('<hr></table>');
            }
            $i++;
        }

    }

    function printPepite() {
        global $ecx,$path;
        $pepite = $ecx->getElementsByTagName("pepite")->item(0);
        $phrase = $pepite->getElementsByTagName("phrase")->item(0)->nodeValue;
        $ref = $pepite->getElementsByTagName("ref")->item(0)->nodeValue;

        echo('<div id="pepite">
                <p><b>À Bible ouverte</b></p>
                <p><i>'.$phrase.'</i></p><p>'.$ref.'</p>'
            .'</div>');
    }

    function printAffiches() {
        global $ecx,$path;
        $affiches = $ecx->getElementsByTagName("affiches")->item(0)->getElementsByTagName("affiche");
        $i = 0;
        echo('<div id="affiches">');
        while($aff = $affiches->item($i)) {
            $i++;
            echo('<img src="'.$path.$aff->nodeValue.'" class="afficheGauche" /><br /> ');
        }
        echo('</div>');
    }

    function printEdito() {
        global $ecx,$path;

        $edito = $ecx->getElementsByTagName("edito")->item(0);
        if($edito == '') return;

        echo('<div id="edito">'.
            $edito->getElementsByTagName("contenu")->item(0)->textContent
            .'<div id="signature">'.$edito->getElementsByTagName("auteur")->item(0)->nodeValue.'</div>'
            .'<img src="IncludeImgs/note.png" style="position:relative; bottom:-30px; left:10px;" />
        </div>');
    }

    function printAnnonces() {
        global $ecx,$path;

        $annonces = $ecx->getElementsByTagName("annonce");
        $i = 0;
        if ($annonces->length <= 0) return;

        $annoncesDemi = null;
        $annoncesFull = null;

           echo('<table id="annonces">');
        while($a = $annonces->item($i)) {
            $i++;
            if ($a->getElementsByTagName("type")->item(0)->nodeValue == "demi")
                $annoncesDemi[] = $a;
            else
                $annoncesFull[] = $a;
        }

        for($n = 0;$n<sizeof($annoncesFull);$n++) {
            echo('
                <tr>
                    <td valign="top" colspan="2" class="annonce longue">
                        <h3>'.$annoncesFull[$n]->getElementsByTagName("titre")->item(0)->nodeValue.'</h3>
                        <p>'.$annoncesFull[$n]->getElementsByTagName("contenu")->item(0)->nodeValue.'</p>
                    </td>
                </tr>
            ');
        }

        for($m = 0;$m < sizeof($annoncesDemi);$m+=2) {
            echo('
                <tr>
                    <td class="annonce courte">
                        <h3>'.$annoncesDemi[$m]->getElementsByTagName("titre")->item(0)->nodeValue.'</h3>
                        <p>'.$annoncesDemi[$m]->getElementsByTagName("contenu")->item(0)->nodeValue.'</p>
                    </td>');
                if ($annoncesDemi[$m+1] != false) {
                    echo('
                    <td class="annonce courte">
                        <h3>'.$annoncesDemi[$m+1]->getElementsByTagName("titre")->item(0)->nodeValue.'</h3>
                        <p>'.$annoncesDemi[$m+1]->getElementsByTagName("contenu")->item(0)->nodeValue.'</p>
                    </td>');
                }
                echo('</tr>');

        }
        echo('</table>');
    }

    function printECXWeb() {
       global $ecx,$path;
       $web = $ecx->getElementsByTagName("web")->item(0);
       if ($web->nodeValue == '') return;

          echo('<table id="surLeWeb">
                <tr><td>
                        <img src="IncludeImgs/web.png" align="left" style="padding-right:20px;" />
                        <span id="titreWeb">sur ccx.polytechnique.org</span><br />
                </td></tr>
                <tr><td>
                    <span id="titreSectionWeb">'.$web->getElementsByTagName("titre")->item(0)->nodeValue.'</span>
                      <p>'.$web->getElementsByTagName("contenu")->item(0)->nodeValue.'</p>
                </td></tr>
               </table>
           ');
   }
?>







<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EnCyCliX</title>
<link rel="stylesheet" type="text/css" href="ecx-style.css" />
<link rel="icon" type="image/png" href="favicon.png" />

</head>

<body>

<div id="conteneur">
    <table>
        <tr>
            <td colspan="2" style="background-image:url(<? URLimagefond() ?>);background-repeat:no-repeat;">
                <table>
                    <tr>
                        <!--<td id="haut" colspan="2">-->
                        <td id="haut" colspan="2" style="background:url(IncludeImgs/surcouche.png); background-repeat:no-repeat;">
                            <div id="dateHaut"><? printDate(); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td id="colonneGauche">
                            <div id="agenda">
                                <span style="font-size:21px; color:#000000;">Cette semaine</span><br />
                                <span id="semaineLiturgique"><? printSemLiturgique(); ?></span>
                                <? printSemaine(); ?>
                                <hr>
                                <table>
                                    <tr>
                                        <td class="red"></td>
                                        <td id="activite">Chapelle</td>
                                    </tr>
                                    <tr>
                                        <td class="green"></td>
                                        <td id="activite">Salle Jean Girette</td>
                                    </tr>
                                    <tr>
                                        <td class="blue"></td>
                                        <td id="activite">Bibliothèque CCX</td>
                                    </tr>
                                    <tr>
                                        <td class="yellow"></td>
                                        <td id="activite">Magnan, Aquarium/Detoeuf</td>
                                    </tr>
                                </table>
                            </div>

                            <? printAffiches(); ?>
                            <? printPepite(); ?>
                        </td>
                        <td id="colonneDroite">
                            <? printEdito(); ?>
                            <? printAnnonces(); ?>
                            <? printECXWeb(); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" height="100" bgcolor="#444444" class="footer" valign="top">
                <p>EnCyCliX - Newsletter hebdomadaire de la Communauté Chrétienne de l'École polytechnique</p>
                <p>Responsables : João Felipe Cabral Moraes - <a href="mailto:joao-felipe.cabral-moraes@polytechnique.edu">joao-felipe.cabral-moraes@polytechnique.edu</a></p>
                <img src="IncludeImgs/logo_footer.png" align="right" />
                <p>Retrouvez cette édition ainsi que les précédentes sur <a href="http://ccx.polytechnique.org">ccx.polytechnique.org</a></p>
            </td>
        </tr>
    </table>
</div>



<script>

    function findPos(obj) {
        //renvoie la position d'un objet
       var curleft = curtop = 0;
       if (obj.offsetParent) {
           do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;
           } while (obj = obj.offsetParent);
        }
        return [curleft,curtop];
    }



    var links = document.links;

    var h = document.getElementById('conteneur').offsetHeight + document.getElementById('conteneur').offsetTop;
    var l = document.getElementById('conteneur').offsetWidth + document.getElementById('conteneur').offsetLeft;

    var rect = document.getElementById('conteneur').getBoundingClientRect();
    x = rect.left;
    y = rect.top;
    w = rect.right - rect.left;
    h = rect.bottom - rect.top; //Attention : faux avec chrome !

    //La partie à mettre dans le HTML du mail
    var texteMap = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n <html xmlns="http://www.w3.org/1999/xhtml">\n<head>\n<title>Encyclix</title>\n</head><body>\n<map name="imagemap" id="Map">\n';

    for(var i = 0; i < links.length; i++) {
        var x1 = findPos(links[i])[0];//links[i].offsetLeft;
        var y1 = findPos(links[i])[1]; //links[i].offsetTop;
        var x2 = links[i].offsetWidth + x1;
        var y2 = links[i].offsetHeight + y1;
        texteMap += '<area shape="rect" coords="' + x1 + ',' + y1 + ',' + x2 + ',' + y2 + '" href="'+links[i]+'" />\n';
    }
    texteMap += '</map>\n<div align="center">\n<i><font size="2">Si l\'encyclix ne s\'affiche pas correctement, <a href="http://ccx.polytechnique.org/site/index.php?page=encyclix">cliquez ici</a> pour acc&eacute;der &agrave; la version en ligne.</font></i><br />\n<img src="<? echo '../encyclixes/'.$name.'/'.$name.'.png';?>" usemap="#imagemap" border="0"/>\n</div>\n</body>\n</html>';

        //Ce qu'on va mettre dans la pop-up pour afficher les infos :
    content = '<div id="Infos"> \
        <b>Pour le Site-Shooter,</b><br /> \
        <ul>';
    content += '<li>Capturer le contenu de la page Encylix, avec pour limites dimensionnelles <br /> Largeur : ' + w + ' px <br /> Hauteur : ' + h + ' px</li>';

        content += '<form method="POST" action="../generateur/generateur.php?name=<? echo $name;?>" target="_blank"><input type="hidden" name="htmlMap" value="' + encodeURIComponent(texteMap) + '"><input type="submit" value="->Telecharger image et test MAP<-"></form>';

    content+= '<li>À mettre dans le corps du mail, <b>en remplaçant l\'adresse de l\'image (le "À REMPLIR vers la fin") : <form id="formf"><textarea id="viewf" rows="20" cols="40">';

    content += texteMap;
    content += '</textarea></form>';
    content += '</li></ul></div>';

    infos = window.open('','Infos pour capture...','width=400,height=600,toolbar=no,scrollbars=no,resizable=no');
    infos.document.write(content);
</script>


</body>
