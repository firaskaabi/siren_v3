<?php

use phpDocumentor\Reflection\PseudoTypes\True_;

$nb=$_GET['nb'];
$curseur=$_GET['curseur'];
$date=date('Y-m-d');
$scociete=[];

$headers = array();
$arraySiren=array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer 974f2f2b-9f16-3ad3-8b5b-5cce1a0d3441';
$TOTALEntreprise=1486958;
$ch = [];
$i=0;

$filename = './resultat.csv';
if(is_null($nb)){
   $k=0;
   $curseur="*";
   if(file_exists($filename)){
    unlink($filename);
   }
   $data = [
     ['N°','Denomination Unite Legale','siren', 'siret', 'nic','Date Creation Etablissement','tranche Effectifs Etablissement','ville','code postale'],
   ];
   $f = fopen($filename, 'a');
   // write each row at a time to a file
   foreach ($data as $row) {
       fputcsv($f, $row);
   }
   fclose($f);
   $nb=intVal($nb)+1;
}else{
    $nb=1;
    $k=intval($nb)*1000*10;
}
$j=0;
var_dump($k);
$f = fopen($filename, 'w');
$ok=true;
while($k<=$TOTALEntreprise  && $ok){
    $j++;
    $url='https://api.insee.fr/entreprises/sirene/V3/siret?date='.$date.'&q=(periode(etatAdministratifEtablissement:A))AND%20(trancheEffectifsEtablissement:01%20OR%20trancheEffectifsEtablissement:02%20OR%20trancheEffectifsEtablissement:11)&nombre=1000&curseur='.$curseur;
    // create both cURL resources
    $ch[$i] = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch[$i], CURLOPT_URL, $url);
    curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch[$i], CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch[$i]);
    echo("*****************************************\n");
    $decoded = json_decode($result, true);

    $x=$decoded['header']['curseurSuivant'];
    if(!is_null($x)){
        $curseur=$x;
    }
    $arraySiren=$decoded['etablissements'];
    print_r($arraySiren);

    foreach ($arraySiren as $val){
        $siret=$val['siret'];
        $siren=$val['siren'];
        $nic=$val['nic'];
        $denomination=$val['uniteLegale']['denominationUniteLegale'];
        $dateCreation=$val['dateCreationEtablissement'];
        $tranchEffectifs=$val['trancheEffectifsEtablissement'];
        $ville=$val['adresseEtablissement']['libelleCommuneEtablissement'];
        $codePostale=$val['adresseEtablissement']['codePostalEtablissement'];

        $scociete=[$k,$denomination,$siren,$siret,$nic,$dateCreation,$tranchEffectifs,$ville,$codePostale];
        print_r($scociete);
        fputcsv($f,$scociete);
        $k++;
    }
    
     var_dump($curseur);
     if($j==2){
        $ok=false;
        echo "<script>
                
        window.open('http://localhost/gouv-fr/test3.php?nb={$nb}&curseur={$curseur}');

        </script>";
     }
    curl_close($ch[$i]);
    //add the two handles

}

fclose($f);

?>