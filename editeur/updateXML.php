<?
/******************
Pour toute question s'adresse à cyprien.lecourt@polytechnique.edu
(ajoutez votre mail si vous modifiez le code !)
*******************/
?>


<?
// Reçoit les actions "ajax" de l'editeur dynamique, et modifier le XML en conséquence

if (isset($_POST['action'])) {
    //echo('Action : '.$_POST[action]);
    if ($_POST['action'] == 'modifXMLNode') {
        if (isset($_POST['ecx']) && isset($_POST['path']) && isset($_POST['content'])) {
            $encyclix = $_POST['ecx'];
            $path = $_POST['path'];
            $content = $_POST['content'];

            echo('Modif :'.$encyclix.' | '. $path . ' | ' .$content);
            $EncyclixDOM = new DOMDocument();
            $EncyclixDOM->load('../encyclixes/'.$encyclix.'/'.$encyclix.'.xml') or die("Erreur DOM");
            $ecx = $EncyclixDOM->getElementsByTagName('encyclix')->item(0);
            $xpath = new DOMXPath($EncyclixDOM);
            $entries = $xpath->evaluate($path,$ecx);

            $entries->item(0)->nodeValue = stripslashes($content);

            $b = $EncyclixDOM->save('../encyclixes/'.$encyclix.'/'.$encyclix.'.xml');
            echo('<span style="color:green;">Réussi ! ('.$b.')</span>');
        }

    }
    if($_POST['action'] == 'deleteNode') {
        if (isset($_POST['ecx']) && isset($_POST['path'])) {
            $encyclix = $_POST['ecx'];
            $path = $_POST['path'];
            echo('Deleting node '.$path);
            $EncyclixDOM = new DOMDocument();
            $EncyclixDOM->load('../encyclixes/'.$encyclix.'/'.$encyclix.'.xml') or die("Erreur DOM");
            $ecx = $EncyclixDOM->getElementsByTagName('encyclix')->item(0);
            $xpath = new DOMXPath($EncyclixDOM);

            $entries = $xpath->evaluate($path,$ecx);

            if($entries->length >0) $entries->item(0)->parentNode->removeChild($entries->item(0));

            $b = $EncyclixDOM->save('../encyclixes/'.$encyclix.'/'.$encyclix.'.xml');
            echo('<span style="color:green;">Réussi ! ('.$b.')</span>');
        }
    }
    if($_POST['action'] == 'addNode') {
        if (isset($_POST['ecx']) && isset($_POST['path'])) {
            $encyclix = $_POST['ecx'];
            $path = $_POST['path'];
            echo('Adding node '.$path);
            $EncyclixDOM = new DOMDocument();
            $EncyclixDOM->load('../encyclixes/'.$encyclix.'/'.$encyclix.'.xml') or die("Erreur DOM");
            $ecx = $EncyclixDOM->getElementsByTagName('encyclix')->item(0);
            $xpath = new DOMXPath($EncyclixDOM);
            $entries = $xpath->evaluate($path,$ecx);

            if ($entries->item(0)->childNodes->length > 1) {
                //$entries->item(0)->appendChild($entries->item(0)->getElementsByTagName('evt')->item(0)->cloneNode(true));
                $node = $entries->item(0)->childNodes->item(1)->cloneNode(true);
                echo('->'.$node->tagName);
                $entries->item(0)->appendChild($node);
            }
            else { //Noeud jour vide, on crée un nouveau eveneent...pas très fiable
                if(strstr($path,'agenda')) {
                    $evt = new DOMElement('evt');
                    $evt = $EncyclixDOM->createElement('evt','');
                    $evt->appendChild($EncyclixDOM->createElement('titre','Mon super évènement'));
                    $evt->appendChild($EncyclixDOM->createElement('heure','20:00'));
                    $evt->appendChild($EncyclixDOM->createElement('lieu','autre'));
                    $entries->item(0)->appendChild($evt);
                }
                else if(strstr($path,'annonce')) {
                    $evt = new DOMElement('annonce');
                    $evt = $EncyclixDOM->createElement('annonce','');
                    $evt->appendChild($EncyclixDOM->createElement('titre','Mon super titre'));
                    $evt->appendChild($EncyclixDOM->createElement('contenu','Ma super annonce'));
                    $evt->appendChild($EncyclixDOM->createElement('type','demi'));
                }
            }
            $b = $EncyclixDOM->save('../encyclixes/'.$encyclix.'/'.$encyclix.'.xml');
            echo('<span style="color:green;">Réussi ! ('.$b.')</span>');
        }
    }
}
?>
