<?
/******************
Pour toute question s'adresse à cyprien.lecourt@polytechnique.edu
(ajoutez votre mail si vous modifiez le code !)
*******************/


function clearDir($dossier) {
    $ouverture=@opendir($dossier);
    if (!$ouverture) return;
    while($fichier=readdir($ouverture)) {
        if ($fichier == '.' || $fichier == '..') continue;
        if (is_dir($dossier."/".$fichier)) {
            $r=clearDir($dossier."/".$fichier);
            if (!$r) return false;
        }
        else {
            $r=@unlink($dossier."/".$fichier);
            if (!$r) return false;
        }
    }
    closedir($ouverture);
    $r=@rmdir($dossier);
    if (!$r) return false;
    return true;
}


?>



<?
    if(isset($_POST['date'])) {
        $date=$_POST['date'];
        //Création nouvelle encyclix
        if (!file_exists('encyclixes/ecx_'.$date)) {
            $nom_ecx = 'ecx_'.$date;
        }
        else {
            $i=2;
            while (file_exists('encyclixes/ecx_'.$date.'_'.$i)) { $i++; }
            $nom_ecx = 'ecx_'.$date.'_'.$i;
        }
        mkdir('encyclixes/'.$nom_ecx);
        $rep_ecx = opendir('./encyclixes/'.$nom_ecx) or die('Erreur de permissions');
        copy('encyclixes/modele/modele.xml','encyclixes/'.$nom_ecx.'/'.$nom_ecx.'.xml');
    };


    if(isset($_GET['action']) && $_GET['action'] == 'supprimer') {
        if (isset($_GET['ecx'])) {
            //archive ?
            clearDir('./encyclixes/'.$_GET['ecx']) or die('<b>Erreur lors de la suppression.</b>');
            //echo('<b>Suppression effectuée</b>');
            header('Location: index.php');
        }
        else { echo('<b>Spécifier une encyclix à supprimer...</b>');}
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>EncycliXML, g&eacute;n&eacute;rateur d'encyclixes</title></head>
<link rel="stylesheet" type="text/css" href="style.css" />
<body>
<h1>EnCyCliX</h1>
<div>

<form action="index.php" method="POST">
<p align="center">
<? @date_default_timezone_set('Europe/Berlin'); ?>
<input type="text" value="<? echo @date('Ymd'); ?>" name="date">
<input type="submit" value="Nouvelle encyclix">
</p>
</form>


<table class="listECX">

<?
    $rep_ecx = @opendir('./encyclixes') or die('<b>Erreur de permissions</b>');
    while($e = @readdir($rep_ecx)) {
        if($e != '.' && $e != '..' && $e != 'modele') {
            $encyclixes_folders[] = $e;
        }
    }
    rsort($encyclixes_folders); //par ordre chronologique inversé.

    for($i=0;$i<sizeof($encyclixes_folders);$i++) {
        $e = $encyclixes_folders[$i];
        echo('<tr>
                <td>'.$e.'</td>
                <td> | </td>
                <td><a href="./rendu/parser.php?name='.$e.'">Rendu</a> | </td>
                <td><a href="./editeur/simpleEditeur.php?name='.$e.'">Edition (XML)</a> | </td>
                <td><a href="./editeur/editeur-dyn.php?name='.$e.'">Edition (Graphique)</a> | </td>
                <td><a href="index.php?action=supprimer&ecx='.$e.'"><span style="color:red;">Supprimer</span></a></td>
                </tr>');
    }

?>
</table>

</div>
<hr>
<? include_once 'modedemploi.php'; ?>
</body>
</html>
