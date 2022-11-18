<?php
$date=date('Y-m-d');
$debut=0;
$ch = [];
$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer 974f2f2b-9f16-3ad3-8b5b-5cce1a0d3441';
//create the multiple cURL handle
$mh = curl_multi_init();
$i=0;
$j=1200;

while($j>0){
    if($j>=1000){
        $nombre=1000;
    }else{
        $nombre=$j;
    }
    $url='https://api.insee.fr/entreprises/sirene/V3/siret?date='.$date.'&q=(periode(etatAdministratifEtablissement:A))AND%20(trancheEffectifsEtablissement:01%20OR%20trancheEffectifsEtablissement:02%20OR%20trancheEffectifsEtablissement:11)&nombre='.$nombre;
    // create both cURL resources
    $ch[$i] = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch[$i], CURLOPT_URL, $url);
    curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch[$i], CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $headers);
    //add the two handles
    curl_multi_add_handle($mh,$ch[$i]);
    $i++;
    $debut+=1000;
    $j=$j-1000;
}



//execute the multi handle
do {
    $status = curl_multi_exec($mh, $active);
    if ($active) {
        curl_multi_select($mh);
    }
} while ($active && $status == CURLM_OK);



// all of our requests are done, we can now access the results
for ($k=0;$k<$i;$k++){
    $response_1 = curl_multi_getcontent($ch[$k]);
    echo ($response_1 ."\n"); // output results 
    curl_multi_remove_handle($mh, $ch[$k]);

}



//close the handles
curl_multi_close($mh);
?>