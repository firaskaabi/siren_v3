<?php
  $data = [
    ['siren', 'siret', 'nic','Date Creation Etablissement','tranche Effectifs Etablissement','ville'],
  ];
  $filename = './resultat.csv';
  $f = fopen($filename, 'a');
  // write each row at a time to a file
  foreach ($data as $row) {
      fputcsv($f, $row);
  }
fclose($f);
$date=date('Y-m-d');
$scociete=[];
$debut=0;
$ch = [];
$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer 974f2f2b-9f16-3ad3-8b5b-5cce1a0d3441';
//create the multiple cURL handle
$mh = curl_multi_init();
$i=0;
$j=120000;
$curseur="*";
$f = fopen($filename, 'w');

while($j>0){

    if($j>=1000){
        $nombre=1000;
    }else{
        $nombre=$j;
    }
    $url='https://api.insee.fr/entreprises/sirene/V3/siret?date='.$date.'&q=(periode(etatAdministratifEtablissement:A))AND%20(trancheEffectifsEtablissement:01%20OR%20trancheEffectifsEtablissement:02%20OR%20trancheEffectifsEtablissement:11)&nombre='.$nombre."&debut=".$debut."&curseur=".$curseur;
    // create both cURL resources
    $ch[$i] = curl_init();
    // set URL and other appropriate options
    curl_setopt($ch[$i], CURLOPT_URL, $url);
    curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch[$i], CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch[$i]);
    usleep(20000);
    echo("firas");
    echo("*****************************************\n");
    $decoded = json_decode($result, true);
    $curseur=$decoded['header']['curseurSuivant'];
    $arraySiren=$decoded['etablissements'];
    
    foreach ($arraySiren as $val){
        $siret=$val['siret'];
        $siren=$val['siren'];
        $nic=$val['nic'];
        $dateCreation=$val['dateCreationEtablissement'];
        $tranchEffectifs=$val['trancheEffectifsEtablissement'];
        $ville=$val['adresseEtablissement']['libelleCommuneEtablissement'];
        $scociete=[$siren,$siret,$nic,$dateCreation,$tranchEffectifs,$ville];
        print_r($scociete);
        fputcsv($f, $scociete);
    }
     var_dump($curseur);
    curl_close($ch[$i]);
    //add the two handles
    $i++;
    $debut+=1000;
    $j=$j-1000;
}
fclose($f)

/*
//execute the multi handle
do {
    $status = curl_multi_exec($mh, $active);
    if ($active) {
        curl_multi_select($mh);
    }
   

   
} while ($active && $status == CURLM_OK);



// all of our requests are done, we can now access the results
for ($k=0;$k<$i;$k++){
    echo("firas kaabi");
    $response_1 = curl_multi_getcontent($ch[0]);
    echo ($response_1 ."\n"); // output results 
    curl_multi_remove_handle($mh, $ch[$k]);

}



//close the handles
curl_multi_close($mh);
*/
?>