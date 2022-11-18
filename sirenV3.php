<?php


$ch = curl_init();
$date=date('Y-m-d');
$url='https://api.insee.fr/entreprises/sirene/V3/siret?date='.$date.'&q=(periode(etatAdministratifEtablissement:A))AND%20(trancheEffectifsEtablissement:01%20OR%20trancheEffectifsEtablissement:02%20OR%20trancheEffectifsEtablissement:11)&nombre=1';
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer 974f2f2b-9f16-3ad3-8b5b-5cce1a0d3441';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' ;
}else{
    $decoded = json_decode($result);
    $decoded = json_decode($result, true);
}
curl_close($ch);
$key = array_search('statut', $decoded);

$TOTALEntreprise=0;
$TOTALEntreprise=$decoded['header']['total'];
$j=10;
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
}





function call_curl($date,$debut,$nombre,$headers,$List_siret)
{

    $url='https://api.insee.fr/entreprises/sirene/V3/siret?date='.$date.'&q=(periode(etatAdministratifEtablissement:A))AND%20(trancheEffectifsEtablissement:01%20OR%20trancheEffectifsEtablissement:02%20OR%20trancheEffectifsEtablissement:11)&nombre='.$nombre.'&debut='.$debut;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec ($ch);
  
    if (curl_errno($ch)) {
        echo 'Error:';
    }else{
        $decoded = json_decode($result, true);
        $arraySiren=[];
        $arraySiren=$decoded['etablissements'];
        foreach ($arraySiren as $val){
            $siret=$val['siret'];
            array_push($List_siret,$siret);
           
         }
    }

    curl_close($ch);
}

write_csv();
function write_csv(){
    $data = [
        ['Title', 'image1', 'image2'],
        ['GOOG', 'Google Inc.', '800'],
        ['AAPL', 'Apple Inc.', '500'],
        ['AMZN', 'Amazon.com Inc.', '250'],
        ['YHOO', 'Yahoo! Inc.', '250'],
        ['FB', 'Facebook, Inc.', '30'],
    ];
    $filename = './resultat.csv';
    $f = fopen($filename, 'ac');
    if ($f === false) {
        die('Error opening the file ' . $filename);
    }
    // write each row at a time to a file
    foreach ($data as $row) {
        fputcsv($f, $row);
    }
    // close the file
    fclose($f);
}
?>