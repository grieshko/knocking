<?PHP

// ********************************************
// Affichage de l'entete html
// ********************************************
echo
    '
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

    <html>

    <head>

    <LINK REL="StyleSheet" HREF="../style.css" TYPE="text/css">

    <title>FrameIP, Pour ceux qui aiment IP - Script Masque</title>

    <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <META NAME="AUTHOR" CONTENT="www.frameip.com">
    <META NAME="COPYRIGHT" CONTENT="Copyright (c) 2003 by framip">
    <META NAME="KEYWORDS" CONTENT="masque, ip, classe, A, B, C, D, E, 255, 0, 128, bits, binaire, routeur, wan, reseaux, lan, comaraison, adresse, segment, convertion, hote, passerelle, route, calcul, sousreseau">
    <META NAME="DESCRIPTION" CONTENT="Frameip, pour ceux qui aiment IP - Script Masque">
    <META NAME="ROBOTS" CONTENT="INDEX, FOLLOW">
    <META NAME="REVISIT-AFTER" CONTENT="1 DAYS">
    <META NAME="RATING" CONTENT="GENERAL">
    <META NAME="GENERATOR" CONTENT="powered by frameip.com - webmaster@frameip.com">

    </head>

    <body>
    ';

// ********************************************
// Initiation des variables
// ********************************************
$calcul_adresse_ip=$_GET['ip'];
$calcul_mask=$_GET['mask'];

if(substr(strtolower($calcul_mask), 0, 1) == "/") $calcul_mask = substr($calcul_mask, 1);if(substr(strtolower($calcul_mask), 0, 1) == "/") $calcul_mask = substr($calcul_mask, 1);
if(preg_match("/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/",$calcul_mask))
{
	$table_correspondance = array(
		"255.255.255.255" => "32",
		"255.255.255.254" => "31",
		"255.255.255.252" => "30",
		"255.255.255.248" => "29",
		"255.255.255.240" => "28",
		"255.255.255.224" => "27",
		"255.255.255.192" => "26",
		"255.255.255.128" => "25",
		"255.255.255.0" => "24",
		"255.255.254.0" => "23",
		"255.255.252.0" => "22",
		"255.255.248.0" => "21",
		"255.255.240.0" => "20",
		"255.255.224.0" => "19",
		"255.255.192.0" => "18",
		"255.255.128.0" => "17",
		"255.255.0.0" => "16",
		"255.254.0.0" => "15",
		"255.252.0.0" => "14",
		"255.248.0.0" => "13",
		"255.240.0.0" => "12",
		"255.224.0.0" => "11",
		"255.192.0.0" => "10",
		"255.128.0.0" => "9",
		"255.0.0.0	" => "8",
		"254.0.0.0	" => "7",
		"252.0.0.0	" => "6",
		"248.0.0.0	" => "5",
		"240.0.0.0	" => "4",
		"224.0.0.0	" => "3",
		"192.0.0.0	" => "2",
		"128.0.0.0" => "1",
		"0.0.0.0" => "0");
		
		$calcul_mask = $table_correspondance[$calcul_mask];
}
// **********************************************
// Récupération de la date et heure
// **********************************************
/*$annee=date("Y");
$mois=date("m");
$jour=date("d");
$heure=date("H");
$minute=date("i");
$seconde=date("s");*/

// **********************************************
// Récupération de l'IP cliente
// **********************************************
$ip_client=getenv("REMOTE_ADDR");

// **********************************************
// Récupération du Ptr de l'IP cliente
// **********************************************
$ptr=gethostbyaddr("$ip_client");
if ($ptr==$ip_client)
$ptr="Pas de ptr";

// **********************************************
// Récupération du port TCP source
// **********************************************
$port_source=getenv("REMOTE_PORT");

// **********************************************
// Récupération de l'IP du browser
// **********************************************
$ip_browser=getenv("HTTP_X_FORWARDED_FOR");

// ********************************************
// Validation du champs IP
// ********************************************
$calcul_inetaddr=ip2long($calcul_adresse_ip);
$calcul_adresse_ip=long2ip($calcul_inetaddr);

// ********************************************
// Vérification de la saisie
// ********************************************
$erreur=0; // Initialisation
if (($calcul_inetaddr==0)||($calcul_inetaddr==-1))
    masque_erreur(1);
if (($calcul_mask<1)||($calcul_mask>32))
    masque_erreur(2);

// ********************************************
// Conversion du masque
// ********************************************
// Optimisation fournit par Pascal de Serveurperso.com
$calcul_chaine_mask = (string) long2ip(256*256*256*256 - pow(2, 32 - $calcul_mask)); 

// ********************************************
// Calcul du nombre de HOST
// ********************************************
if ($calcul_mask==32)
    $calcul_host=1;
else
    $calcul_host=pow(2,32-$calcul_mask)-2;

// ********************************************
// Calcul de la route
// ********************************************
$calcul_route=$calcul_inetaddr&ip2long($calcul_chaine_mask); // Ajoute l'IP et le masque en binaire
$calcul_route=long2ip($calcul_route); // Convertit l'adresse inetaddr en IP

// ********************************************
// Calcul de la premiere adresse
// ********************************************
if ($calcul_mask==32)
    $offset=0;
else
    $offset=1;

if ($calcul_mask==31)
    $calcul_premiere_ip="N/A";
else
    {
    $calcul_premiere_ip=ip2long($calcul_route)+$offset;
    $calcul_premiere_ip=long2ip($calcul_premiere_ip);
    }

// ********************************************
// Calcul de la dernière adresse
// ********************************************
if ($calcul_mask==32)
    $offset=-1;
else
    $offset=0;

if ($calcul_mask==31)
    $calcul_derniere_ip="N/A";
else
    {
    $calcul_derniere_ip=ip2long($calcul_route)+$calcul_host+$offset;
    $calcul_derniere_ip=long2ip($calcul_derniere_ip);
    }

// ********************************************
// Calcul du broadcast
// ********************************************
if ($calcul_mask==32)
    $offset=0;
else
    $offset=1;
$calcul_broadcast=ip2long($calcul_route)+$calcul_host+$offset;
$calcul_broadcast=long2ip($calcul_broadcast);

// ********************************************
// Présentation des résultats
// ********************************************
echo '
    <p align="center">
        <font size="4" color="#008000">
            <b>
                Masque de sous réseaux
            </b>
        </font>
    </p>
    ';

echo '
    <table border="0" width="100%" id="table1">
        <tr>
            <td width="50%" ><b>Les saisies</b></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="50%" >Adresse IP :</td>
            <td>'.$calcul_adresse_ip.'</td>
        </tr>
        <tr>
            <td width="50%" >Subnet Mask :</td>
            <td>'.$calcul_mask.'</td>
        </tr>
        <tr>
            <td width="50%" >&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="50%" ><b>Les résultats</b></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="50%" >Subnet Mask :</td>
            <td>'.$calcul_chaine_mask.'</td>
        </tr>
        <tr>
            <td width="50%" >Nombre max d\'ip :</td>
            <td>'.$calcul_host.'</td>
        </tr>
        <tr>
            <td width="50%" >L\'adresse de réseau :</td>
            <td>'.$calcul_route.'</td>
        </tr>
        <tr>
            <td width="50%" >Première adresse :</td>
            <td>'.$calcul_premiere_ip.'</td>
        </tr>
        <tr>
            <td width="50%" >Dernière adresse :</td>
            <td>'.$calcul_derniere_ip.'</td>
        </tr>
        <tr>
            <td width="50%" >Broadcast :</td>
            <td>'.$calcul_broadcast.'</td>
        </tr>
    </table>
    ';

// ********************************************
// Fin du script général
// ********************************************
fin_du_script();

// ********************************************
// Fonction d'affichage de l'erreur de saisie
// ********************************************
function masque_erreur($erreur) // $erreur représente le numéro d'erreur.
    {
    // ********************************************
    // Affichage de titre d'erreur
    // ********************************************
    echo
        '
        <p align="center">
            <b>
                <font size="5" color="#008000">
                    Erreur
                </font>
            </b>
        </p>
    ';
    echo "<BR>";

    // ********************************************
    // Message personnalisé
    // ********************************************
    switch ($erreur)
        {
        case 1:
            echo'Le calcul ne peux pas avoir lieu car le champ IP est vide ou non valide.';
        break;
        case 2:
            echo'Le calcul ne peux pas avoir lieu car le champ MASK est vide ou non valide.';
        break;
        }

    // ********************************************
    // Fin du script général
    // ********************************************
    fin_du_script();
    }

function fin_du_script()
    {
    // ********************************************
    // Fin de la page Html
    // ********************************************
    echo
        '
        </body>

        </html>
        ';

    // ********************************************
    // Fin du script général
    // ********************************************
    exit(0);
    }

?>


