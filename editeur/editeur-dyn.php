<?
/******************
Pour toute question s'adresse à cyprien.lecourt@polytechnique.edu
(ajoutez votre mail si vous modifiez le code !)
*******************/
?>

<?
/* Recupération du nom de l'encyclix, et lecture du xml */

    if (isset($_GET['name'])) { $name = $_GET['name'];}  else die("<br /><br /><b>Sp&eacute;cifier une encyclix &agrave; traiter</b>");
    $chemin = '../encyclixes/'.$name.'/'.$name.'.xml';
    $file = fopen($chemin, 'r+');
    $ecx = fread($file, filesize($chemin));
    fclose($file);
/* Traitement d'une demande de suppression de fichier */
if(isset($_POST['nomFichierASupprimer'])) {
    if (@unlink('../encyclixes/' . $name . '/' . $_POST['nomFichierASupprimer'])) {
        echo('<span class="succes"> > Suppression de '. $_POST['nomFichierASupprimer'].' effectuée avec succès</span>');
    }
    else {
        echo('<span class="erreur"> > Erreur lors de la suppression...</span>');
    }

}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EnCyCliX du futur : Génerateur</title>
<link rel="stylesheet" type="text/css" href="editeur-dyn.css" />

<script src="../include/jquery-1.6.1.min.js" type="text/javascript"></script>
<script src="../include/jquery.form.js" type="text/javascript"></script>
<script src="../include/encyclix.fonctions.js" type="text/javascript"></script>
<script type="application/javascript">

var name = "<? echo $name; ?>";
var ecx;

$(document).ready(function() {
    load();


    var options = {
        target:        '#output1',   // target element(s) to be updated with server response
        beforeSubmit:  showRequest,  // pre-submit callback
        success:       showResponse
    }
    $('#formFile').ajaxForm(options);

    function showRequest(formData, jqForm, options) {
        var queryString = $.param(formData);
        $('#output1').html('<p>Uploading...</p>');
        //alert('About to submit: \n\n' + queryString);
        return true;
    }
    function showResponse(responseText, statusText, xhr, $form)  {
        //alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
        //'\n\nThe output div should have already been updated with the responseText.');
        window.location.reload();
    }

    $('#logCheckbox').change(function() {
        //alert($('#logCheckbox').attr('checked'));
        if($('#logCheckbox').attr('checked') != 'checked') { //La case est décochée. On efface le log !
            $('#trash').html('');
        }
    });
});




//Upload


</script>

</head>
<body id="myBody">
    <div id="trash"></div>
    <h1>Édition de l'EnCyCliX "<? echo $name; ?>"</h1>
    <form><p><input type="checkbox" name="logCheckbox" id="logCheckbox">Afficher le log des requetes au serveur (debug)</p></form>
    <form method="POST">
    <div id="Editor">

        <table>
            <tr>
                <td colspan="2">
                    <div id="basicprops">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="agenda">
                    </div>
                    <div id="affiches">
                    </div>
                    <div id="pepite">
                    </div>
                </td>
                <td>
                    <div id="edito">
                    </div>
                    <div id="annonces">
                    </div>
                    <div id="web">
                    </div>
                </td>
            </tr>
        </table>

    </div>
    </form>

<?
    echo('<div id="listeFichiers">');
    echo('<p>Liste des fichiers utilisables actuellement : <br />');
    $rep_ecx = @opendir('../encyclixes/' . $name) or die('Erreur de permissions');
    $count = 0;
    while($f = @readdir($rep_ecx)) {
        if($f != '.' && $f != '..' && $f != $name.'.xml' && $f != $name . '.png') {
            $count++;
            echo('<form action="editeur-dyn.php?name=' . $name . '" method="POST">');

                echo('|- <a href="../encyclixes/' . $name . '/' . $f . '">' . $f . '</a>');
                /* Boutton de suppression */
                echo('<input type="submit" value="Supprimer">
                <input type="hidden" name="nomFichierASupprimer" value="' . $f . '">
            </form> ');
            echo('<br />');
        }
    }
    @closedir($rep_ecx);
    if ($count <= 0) {
        echo('<hr><span class="erreur">Pas encore de fichier utilisable. Utiliser le formulaire ci dessous pour en ajouter</span>
');
    }
    echo('</div>');

?>
</p>
<form id="formFile" action="fileUpload.php" method="POST" enctype="multipart/form-data">
    <p>Upload : <input type="file" name="file" />
                <input type="hidden" name="ecx" value="<? echo $name; ?>">
                <input type="submit" value="Envoyer">
    </p>
</form>
<div id="output1"></div>
</body>