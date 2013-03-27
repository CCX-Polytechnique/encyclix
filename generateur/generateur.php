<?
/******************
Pour toute question s'adresse à cyprien.lecourt@polytechnique.edu
(ajoutez votre mail si vous modifiez le code !)
*******************/
?>

<?
    if(isset($_GET['name'])) {
        $ecx = $_GET['name'];
        echo exec('bash ./captureECX '.$ecx);

        if(isset($_POST['htmlMap'])) {
            echo(urldecode($_POST['htmlMap']));
        }
    }
    else echo "spécifier une encyclix à générer";
?>
