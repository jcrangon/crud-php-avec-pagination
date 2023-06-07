<?php 

// Fonction de connexion à la BDD
function getPDO($DBInfo) {
    $dsn = $DBInfo['driver']
        . ":host=" . $DBInfo['server']
        . ";port=" . $DBInfo['port']
        . ";dbname=" . $DBInfo['base']
        . ";charset=" . $DBInfo['charset'];

    $user = $DBInfo['user'];
    $pwd = $DBInfo['pass'];
    $options = $DBInfo['options'];

    try {
        $pdo = new PDO($dsn, $user, $pwd, $options);
    } catch(PDOException $e) {
        afficheErreur($e->getMessage());
    }
    return $pdo;
}

/**
 * Affiche les données recues
 * En début de page afin de pouvoir vérifier le travail correct
 * des procédures de nettoyage des données
 */
function incomingData () {
    
    if ($_POST && !empty($_POST)) {
        echo '<div style="background:#222; font-size:1.1rem; color:orange; border: 1px double orange; padding:15px;">';
        echo '<pre>';
        echo 'Tableau POST:<br>';
        echo print_r($_POST, true);
        echo '</pre>';
        echo '</div>';
    }
    if ($_GET && !empty($_GET)) {
        echo '<div style="background:#222; font-size:1.1rem; color:orange; border: 1px double orange; padding:15px;">';
        echo '<pre>';
        echo 'Tableau GET:<br>';
        echo print_r($_GET, true);
        echo '</pre>';
        echo '</div>';
    }
    if($_FILES && !empty($_FILES)) {
        echo '<div style="background:#222; font-size:1.1rem; color:orange; border: 1px double orange; padding:15px;">';
        echo '<pre>';
        echo 'Tableau FILES:<br>';
        echo print_r($_FILES, true);
        echo '</pre>';
        echo '</div>';
    }

    if($_SESSION && !empty($_SESSION)) {
        echo '<div style="background:#222; font-size:1.1rem; color:orange; border: 1px double orange; padding:15px;">';
        echo '<pre>';
        echo 'Tableau $_SESSION:<br>';
        echo print_r($_SESSION, true);
        echo '</pre>';
        echo '</div>';
    }
}

function afficheErreur ($erreur) {
    echo "<div style='color: red; background: pink; padding: 10px;'>Error:" . (string)$erreur . "</div>";
    die();
}

function showVar($var) {
    $html = '<div style="background:#222; font-size: 1.1rem; color: forestgreen; border: 1px dashed red; padding: 15px">';
    $html .= '<pre>';
    $html .= print_r($var, true);
    $html .= '</pre>';
    $html .= '</div>';   
    echo $html;
}

function checkVar($var) {
    $html = '<div style="background:#222; font-size: 1.1rem; color: forestgreen; border: 1px dashed red; padding: 15px">';
    $html .= '<pre>';
    $html .= print_r($var, true);
    $html .= '</pre>';
    $html .= '</div>';   
    die($html);
}

/**
 * Nettoie une donnée en choisissant le bon FILTRE PHP en fonction
 * de la constante TYPE_* fournie et en appelant ensuite la fonction
 * de nettoyage / validation de PHP: filter_var()
 *
 * @param [type] $var
 * @param [type] $type
 * @return mixed|string
 */
function sanitizeData($var, $type=TYPE_STRING) {
    $flags = null;
    switch($type) {
        case TYPE_URL:
            $filter = FILTER_SANITIZE_URL;
            break;
        case TYPE_INT:
            $filter = FILTER_SANITIZE_NUMBER_INT;
            break;
        case TYPE_FLOAT:
            $filter = FILTER_SANITIZE_NUMBER_FLOAT;
            $flags = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
            break;
        case TYPE_EMAIL:
            $filter = FILTER_SANITIZE_EMAIL;
            $var = substr($var, 0, 254);
            break;
        case TYPE_STRING:
        default:
            // nettoyag%e des données l'ancienne
            $var = htmlspecialchars(strip_tags(trim($var)), ENT_QUOTES | ENT_SUBSTITUTE, null, true);
    }
    if($type !== TYPE_STRING) {
        $output = filter_var($var, $filter, $flags);
        return $output;
    } else {
        return $var;
    }
}

/**
 * Nettoie les tableaux de donnéesz $_GET et $_POST
 * en passan chaque donnée à la fonction sanitizeData puis en
 * réinsérant la donnée nétttoyée dans son tableau d'origine
 *
 * @return void
 */
function sanitizeGetPostData() {
    if(isset($_GET) && !empty($_GET)){
        foreach($_GET as $key => $value){
            $_GET[$key] = sanitizeData($value);
        }
    }
    if(isset($_POST) && !empty($_POST)){
        foreach($_POST as $key => $value){
            $_POST[$key] = sanitizeData($value);
        }
    }
}

/**
 * Valide la donnéee en fonction du TYPE_* fourni/
 * Déclenche une érreur sut le type fourni n'est pas 
 * répertorié.
 *
 * @param [type] $var
 * @param [type] $type
 * @param array $options
 * @return bool|void
 */
function validateData($var, $type = TYPE_STRING, $options= []) {
    switch($type) {
        case TYPE_EMAIL:
            $var = substr($var, 0, 254);
            $filter =  FILTER_VALIDATE_EMAIL;
            break;
        case TYPE_INT:
            $filter = FILTER_VALIDATE_INT;
            break;
        case TYPE_BOOLEAN:
            $filter = FILTER_VALIDATE_BOOL;
            break;
        case TYPE_IP:
            $filter = FILTER_VALIDATE_IP;
            break;
        case TYPE_URL:
            $filter = FILTER_VALIDATE_URL;
            break;
        case TYPE_FLOAT:
            $filter = FILTER_VALIDATE_FLOAT;
            break;
        case TYPE_DOMAIN:
            $filter = FILTER_VALIDATE_DOMAIN;
            break;
        case TYPE_MAC:
            $filter = FILTER_VALIDATE_MAC;
            break;
        case TYPE_STRING:
            return is_string($var);
        default:
    }

    if(false === $filter ) {
        afficheErreur(("Echec validation des données... Le type fourni n'existe pas!"));
    }
    return filter_var($var, $filter, $options) !== false;
}

function validateDateTime($date, $format = "Y-m-d H:i:s") {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
function validateDate($date, $format = "Y-m-d") {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function validateDateFr($date, $format='d-m-Y') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function isImage($fichier, $authorizedFormats) {
    $pos = strrpos($fichier, '.');
    if($pos === false) {
        return false;
    }
    $ext = strtolower(trim(substr($fichier, $pos+1)));
    if(in_array($ext, $authorizedFormats)){
        return true;
    }

    return false;
}

// fonction traitement mysql
function convertDateMysqlToFr($s) {
    $temp = explode('-', $s);
    return $temp[2] . '-' . $temp[1] . '-' . $temp[0];
}

function convertDateFrToMysql($s) {
    $temp = explode('-', $s);
    return $temp[2] . '-' . $temp[1] . '-' . $temp[0];
}

function getFormValues($t) {
    foreach($t as $k){
        if(isset($_POST[$k])){
            $t[$k] = $_POST[$k];
        } elseif (isset($_GET[$k])) {
            $t[$k] = $_GET[$k];
        } else {
            $t[$k] = "";
        }
    }
    return $t;
}

function resetFormValues($t) {
    foreach($t as $k) {
        $_POST[$k] = "";
    }
}