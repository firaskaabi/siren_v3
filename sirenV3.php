<?php

use PhpParser\Node\Stmt\Foreach_;

$ch = curl_init();
$date=date('Y-m-d');
$url='https://api.insee.fr/entreprises/sirene/V3/siret?date='.$date.'&q=(periode(etatAdministratifEtablissement:A))AND%20(trancheEffectifsEtablissement:01%20OR%20trancheEffectifsEtablissement:02%20OR%20trancheEffectifsEtablissement:11)&nombre=1';
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer token';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}else{
    $decoded = json_decode($result);
    $decoded = json_decode($result, true);
}
curl_close($ch);
$key = array_search('statut', $decoded);

$TOTALEntreprise=0;
$TOTALEntreprise=$decoded['header']['total'];
$j=6000;
echo("\n".$j."\n");
$debut=0;
$List_siret = array();

while($j>0){
        if($j>=1000){
            $nombre=1000;
            call_curl($date,$debut,$nombre,$headers,$List_siret);
        }else{
            $nombre=$j;
            call_curl($date,$debut,$nombre,$headers,$List_siret);

        }
    $debut+=1000;
    $j=$j-1000;
    echo("\n".$j);    
}


//var_dump($TOTALEntreprise);
echo("---------------------------------------------\n\n\n");
print_r($List_siret);
//print_r($decoded);

function call_curl($date,$debut,$nombre,$headers,$List_siret)
{
    $url='https://api.insee.fr/entreprises/sirene/V3/siret?date='.$date.'&q=(periode(etatAdministratifEtablissement:A))AND%20(trancheEffectifsEtablissement:01%20OR%20trancheEffectifsEtablissement:02%20OR%20trancheEffectifsEtablissement:11)&nombre='.$nombre.'&debut='.$debut;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec ($ch);
    curl_close($ch);
    echo($result);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }else{
        $decoded = json_decode($result);
        $decoded = json_decode($result, true);
        $arraySiren=[];
        $arraySiren=$decoded['etablissements'];
        foreach ($arraySiren as $val){
            $siret=$val['siret'];
            array_push($List_siret,$siret);
         }

    }
    print_r($List_siret);
}
?>
