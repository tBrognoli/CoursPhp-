<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 08/02/2018
 * Time: 14:07
//      ",;;*;;;;,
//        .-'``;-');;.
//       /'  .-.  /*;;
//     .'    \d    \;;               .;;;,
//    / o      `    \;    ,__.     ,;*;;;*;,
//    \__, _.__,'   \_.-') __)--.;;;;;*;;;;,
//     `""`;;;\       /-')_) __)  `\' ';;;;;;
//        ;*;;;        -') `)_)  |\ |  ;;;;*;
//        ;;;;|        `---`    O | | ;;*;;;
//        *;*;\|                 O  / ;;;;;*
//       ;;;;;/|    .-------\      / ;*;;;;;
//      ;;;*;/ \    |        '.   (`. ;;;*;;;
//      ;;;;;'. ;   |          )   \ | ;;;;;;
//      ,;*;;;;\/   |.        /   /` | ';;;*;
//       ;;;;;;/    |/       /   /__/   ';;;
//       '"*"'/     |       /    |      ;*;
//            `""""`        `""""`     ;'"
 */


function drawAscii ($filename){
    if (file_exists($filename.".txt")) {
        $fichierPoney = fopen($filename.".txt", 'r');
        while (!feof($fichierPoney)) {
            $line = fgets($fichierPoney);
            echo $line;
        }
        fclose($fichierPoney);
    }

}

function quelCommande($cmd){
    if ($cmd[0] == '--create-file') {
        return 1;
    }
    if ($cmd[0] == "--add-user" ) {
        return 2;
    }
    if ($cmd[0] == "--get-all-users") {
        return 3;
    }
    if ($cmd[0] == "--get-all-cmd") {
        return 4;
    }
    drawAscii("dog");
    $error = "Tapez 'php ex4.php --get-all-cmd' pour connaitre les commandes";
    stop_now($error);
}

function creerFichier($fileName, $create) {
    if (file_exists("csv/".$fileName.".csv")) {
        if ($create) {
            echo "Le fichier ".$fileName.".csv existe deja.\n";
        }else{
            echo "Utilisation du fichier ".$fileName.".csv deja existant.\n";
        }
    }
    else {
        echo "Création du fichier ".$fileName.".csv\n";
    }
    $fichierStudent = fopen("csv/".$fileName.".csv", 'a');
    return $fichierStudent;
}

function verifArgv($tabCom) {
    $tabStudent = array(true, explode('=', $tabCom[2]), explode('=', $tabCom[3]), explode('=', $tabCom[4]));

    if ($tabStudent[1][0] != '--firstname') {
        $tabStudent[0] = false;
    }
    if ($tabStudent[2][0] != '--lastname') {
        $tabStudent[0] = false;
    }
    if ($tabStudent[3][0] != '--note') {
        $tabStudent[0] = false;
    }
    return $tabStudent;
}

function ajtUser($argv) {
    $tabStudent = verifArgv($argv);
    $cmd = explode('=',$argv[1]);

    if (!$tabStudent[0]) {
        $error = "Veuillez respecter '--add-user=NOMDUFICHIER --firstname=FIRSTNAME --lastname=LASTNAME --note=NOTE'";
        stop_now($error);
    }
    $fichierToUse = creerFichier($cmd[1], false);
    if(filesize("csv/".$cmd[1].".csv") > 0) {
        $textToWrite = "\n| ". $tabStudent[1][1]. " | " . $tabStudent[2][1]. " | " .$tabStudent[3][1];
    }
    else {
        $textToWrite = "| ". $tabStudent[1][1]. " | " . $tabStudent[2][1]. " | " .$tabStudent[3][1];
    }

    fwrite($fichierToUse, $textToWrite);
    fclose($fichierToUse);
    echo "Ajout de :".$tabStudent[1][1]." - ".$tabStudent[2][1]." - note: ".$tabStudent[3][1]."\n";
}

function choixFichierAffich($argv) {
    if (isset($argv[2])) {
        $nomFichier = explode("=",$argv[2])[1];
        if (file_exists("csv/".$nomFichier.".csv")) {
            $fichierToUse = fopen("csv/".$nomFichier.".csv", 'r');
        }
        else {
            $error = $nomFichier.".csv n'existe pas";
            stop_now($error);
        }
    }
    else{
        if($dossier = opendir('csv/')) {
            $dossierTab = array();
            while(false !== ($file = readdir($dossier))) {
                $file = readdir($dossier);
                if( $file != '.' && $file != '..' && preg_match('#\.(csv)$#i', $file)) {
                    array_push($dossierTab, $file);
                }
            }
            $nomFichier = $dossierTab[rand(0, count($dossierTab)-1)];
            $fichierToUse = fopen("csv/".$nomFichier, 'r');
            echo "Utilisation de ". $nomFichier."\n";
        }
        else {
            $error = "Pas de fichier csv dans le repertoire";
            stop_now($error);
        }
    }
    return $fichierToUse;
}

function affichAllUser($argv) {
    $fichierToUse = choixFichierAffich($argv);

    if ($fichierToUse) {
        $tabLine = array();
        while (!feof($fichierToUse)) {
            $line = fgets($fichierToUse);
            array_push($tabLine, $line);
        }
        fclose($fichierToUse);

        $tabTailleMot = array(1, 1);
        $i =0;
        while ($i < count($tabLine)) {

            $cutLine = explode('|', $tabLine[$i]);
            if ($i == 0 and empty($cutLine[1])) {
                $error = "Fichier vide";
                stop_now($error);
            }

            if ($tabTailleMot[0] < strlen($cutLine[1])) {
                $tabTailleMot[0] = strlen($cutLine[1]);
            }
            if ($tabTailleMot[1] < strlen($cutLine[2])) {
                $tabTailleMot[1] = strlen($cutLine[2]);
            }
            $i++;


        }
        $newTabLine = array();
        $i =0;
        while ($i < count($tabLine)) {
            $cutLine = explode('|', $tabLine[$i]);
            while (strlen($cutLine[1]) < $tabTailleMot[0]) {
                $cutLine[1] .= " ";
            }
            while (strlen($cutLine[2]) < $tabTailleMot[1]) {
                $cutLine[2] .= " ";
            }
            $newString = "| ". $cutLine[1] ." | ". $cutLine[2] ." | ". $cutLine[3];
            array_push($newTabLine, $newString);
            $i++;
        }
        $i=0;
        while ($i < count($newTabLine)) {
            echo $newTabLine[$i];
            $i++;
        }
    }
}

function affichCommande(){
    echo "La commande pour créer le fichier :\n";
    echo "php ex4.php --create-file=NOMDUFICHIER\n\n";
    echo "La commande permettant d'ajouter un utilisateur :\n";
    echo "php ex4.php --add-user=NOMDUFICHIER --firstname=FIRSTNAME --lastname=LASTNAME --note=NOTE\n\n";
    echo "Affichage du fichier, avec nom du fichier sans extension ou sans\n";
    echo "php ex4.php --get-all-users --fichier=NOMDUFICHIER\n";
}

function stop_now($error) {
    echo $error;
    die();
}

////////////////////////////////////
$cmd = explode('=',$argv[1]);

if (!isset($argv[1])) {
    $error = "Entrer une commande valide\n";
    stop_now($error);
}

$whatToDo = quelCommande($cmd);
if ($whatToDo == 1){
    drawAscii("dog");
    creerFichier($cmd[1], true);
}
if ($whatToDo == 2) {
    drawAscii("dino");
    ajtUser($argv);
}
if ($whatToDo == 3) {
    drawAscii("poney");
    affichAllUser($argv);
}
if ($whatToDo == 4) {
    drawAscii("cartoon");
    affichCommande();
}



