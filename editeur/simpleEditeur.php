<?
/******************
Pour toute question s'adresse à cyprien.lecourt@polytechnique.edu
(ajoutez votre mail si vous modifiez le code !)
*******************/
?>
<?


/* Recupération du nom de l'encyclix, et lecture du xml */
if (isset($_GET['name'])) {
    $name = $_GET['name'];
}
else die('<br /><span class="erreur"> > Sp&eacute;cifier une encyclix &agrave; traiter</b>');

$chemin = '../encyclixes/'.$name.'/'.$name.'.xml';
$file = fopen($chemin, 'r+') or die('<span class="erreur"> > Erreur de lecture/ de permissions pour le XML.</span>');
if (filesize($chemin)>0) {
   $ecx = @fread($file, filesize($chemin));
}
else {
   $ecx = '';
}
@fclose($file);

/* Traitement d'une nouvelle validation de modifs */
if(isset($_POST['fichierXML'])) {
    $file = fopen($chemin, 'w');
    @fwrite($file,stripslashes($_POST['fichierXML'])) or die('<span class="erreur"> > Erreur d\'ecriture. Problème de permissions ?</span>');
    //header('Location: ../rendu/parser.php?name='.$name); //=>rendu
}



/* Traitement d'un fichier envoyé */
if(isset($_FILES['fichier'])) {
    if (move_uploaded_file($_FILES['fichier']['tmp_name'], '../encyclixes/'.$name.'/'.$_FILES['fichier']['name'])) {
        echo('<span class="succes"> >' . $_FILES['fichier']['name'] . ' ajouté avec succès. Pour l\'utiliser il suffit d\'insérer ce nom dans le fichier XML.</span>');
    }
    else echo('<span class="erreur"> > Echec de l\'upload. Existe-t-il déjà un fichier avec ce nom ?</span>');
}


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
<title>Editeur d'EnCyCliX</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<link rel="icon" type="image/png" href="favicon.png" />
</head>

<body>

<h3>Édition de : <? echo $name; ?> - <a href="../rendu/parser.php?name=<? echo $name ?>">Voir le résultat</a></h3>
<form method="POST" action="simpleEditeur.php?name=<? echo $name ?>">
<div align="center">
<textarea name="fichierXML" cols="100" rows="40">
<?
    echo htmlentities($ecx,ENT_NOQUOTES, "UTF-8");
?>
</textarea><br /><br />
<input type="Submit" value="Enregistrer les modifications"></div>
</form>

<hr>
<p>Liste des fichiers utilisables actuellement : <br />
<?
    $rep_ecx = @opendir('../encyclixes/' . $name) or die('<span class="erreur">Erreur de permissions</span>');
    $count = 0;
    while($f = readdir($rep_ecx)) {
        if($f != '.' && $f != '..' && $f != $name.'.xml' && $f != $name . '.png') {
            $count++;
            echo('<form action="simpleEditeur.php?name=' . $name . '" method="POST">');

                echo('|- <a href="../encyclixes/' . $name . '/' . $f . '">' . $f . '</a>');
                /* Boutton de suppression */
                echo('<input type="submit" value="Supprimer">
                <input type="hidden" name="nomFichierASupprimer" value="' . $f . '">
            </form> ');
            echo('<br />');
        }
    }
    if ($count <= 0) {
        echo('<hr><span class="erreur">Pas encore de fichier utilisable. Utiliser le formulaire ci dessous pour en ajouter</span>
');
    }

?>
</p>
<hr>
<form method="POST" action="simpleEditeur.php?name=<? echo $name ?>" enctype="multipart/form-data">
<p>Ajouter un fichier : <br />
<input type="file" name="fichier"><input type="submit" value="envoyer"></p>
<span class="erreur">Attention : Enregistrer les modifications ci-dessus avant d'envoyer des fichiers.</span>
</form>
</body>