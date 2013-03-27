<?
/******************
Pour toute question s'adresse à cyprien.lecourt@polytechnique.edu
(ajoutez votre mail si vous modifiez le code !)
*******************/
?>


<?
//$type = $_POST['mimetype'];
//$xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
$name = $_POST['ecx'];

foreach($_FILES as $file) {
    $n = $file['name'];
    $s = $file['size'];
    if (!$n) continue;
    //echo "File: $n ($s bytes)";
    if (move_uploaded_file($file['tmp_name'], '../encyclixes/'.$name.'/'.$n)) {
        echo('Succès upload');
    }
    else echo('<b>Echec de l\'upload</b>');

}

?>
