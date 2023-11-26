<?php
//START OF PROSES TRANING NAIVE BAYES------------------------------------------------------------

//----------------------------
//step-1: Baca dataset
//----------------------------
$namefile ="datatraining.csv";
$file =fopen($namefile, "r");
$dataset =[[]];//array (array());
$i=0;
while (!feof($file)){
    $kolom=fgetcsv($file,null, ";");
    $dataset[$i][0]=$kolom[3];//judul
    $dataset[$i][1]=$kolom[4];// pembimbing
    $i++;
}
fclose($file);

//----------------------------
//step-2: Case Folding
//----------------------------
for ($i=0; $i<sizeof($dataset); $i++){
    $dataset[$i][0]=strtolower($dataset[$i][0]);
}

//----------------------------
//step-3: Filterting
//----------------------------
$stopSymbols =["[0-9]","\?","\:","\(","\)","\[","\]"];
$stopword= ["dan","di","dengan","untuk","yang"];
//Filtering stop symbols
for ($i=0; $i<sizeof($stopSymbols); $i++){
    $simbol=$stopSymbols[$i];
    for ($j=0; $j<sizeof($dataset); $j++){
        $dataset[$j][0]=preg_replace ('/'.$simbol.'/', " ",$dataset[$j][0]);
    }
}
//Filtering stop words
for ($i=0; $i<sizeof($stopword); $i++){
    $word=$stopword[$i];
    for ($j=0; $j<sizeof($dataset); $j++){
        $dataset[$j][0]=preg_replace ('/\b'.$word.'\b/', " ",$dataset[$j][0]);
    }
}
//Hapus spasi ganda dan Trim untuk menghapus spasi di awal dan akhir string
for ($i=0; $i<sizeof($dataset); $i++){
    $dataset[$i][0]=preg_replace ('/\s+/', " ",$dataset[$i][0]);
    $dataset[$i][0]=trim($dataset[$i][0]);
}

//print dataset--------------------------------
for($i=0;$i<sizeof($dataset);$i++){
    echo $dataset[$i][1]." : ".$dataset[$i][0]."<br>";
}

//----------------------------
//step-4: Tokenizing
//----------------------------
$tokens=[[]];//array(array());
for ($i=0; $i<sizeof($dataset); $i++) {
    $token_baris=explode(" ", $dataset[$i][0]);
    $tokens[$i]=$token_baris;
}

//print tokens--------------------------------
echo "----------------------------------------------<br>";
echo "Tokens<br>";
for($i=0;$i<sizeof($tokens);$i++){
    echo "| ";
    for($j=0;$j<sizeof($tokens[$i]);$j++){
        echo $tokens[$i][$j]." | ";
    }
    echo "<br>";
}

//----------------------------
//step-5: Ektraksi class dan fitur
//----------------------------
$stringKelas = [];
$stringFitur = [];
for($i=0;$i<sizeof($dataset);$i++){
    //ektraksi class
    $dosenpembimbing = $dataset[$i][1];
    $size = sizeof($stringKelas);
    $databaru = true;
    for($j=0;$j<$size;$j++){
        if($dosenpembimbing==$stringKelas[$j]){
            $databaru = false;
            break;
        }
    }
    if($databaru==true){
        $stringKelas[$size]=$dosenpembimbing;
    }
    //ektraksi fitur
    $token_baris = $tokens[$i];
    for($j=0;$j<sizeof($token_baris);$j++){
        $token = $token_baris[$j];//kata = word
        $size = sizeof($stringFitur);
        $databaru = true;
        for($k=0;$k<$size;$k++){
            if($token==$stringFitur[$k]){
                $databaru = false;
                break;
            }
        }
        if($databaru == true){
            $stringFitur[$size]= $token;
        }
    }
}

echo "----------------------------------------------<br>";
//print kelas
echo "n_kelas: ".sizeof($stringKelas)."<br>";
for($i=0;$i<sizeof($stringKelas);$i++){
    echo $stringKelas[$i]."<br>";
}

//print fitur
echo "n_fitur: ".sizeof($stringFitur)."<br>";
for($i=0;$i<sizeof($stringFitur);$i++){
    echo $stringFitur[$i].";";
}

//----------------------------
//step-6: Hitung frekuensi class dan fitur
//----------------------------
$nKelas = sizeof($stringKelas);//banyaknya Class
$nFitur = sizeof($stringFitur);//banyaknya feature
$frekuensiKelas = array_fill(0, $nKelas, 0);//inisialisasi frekuensi kelas
$frekuensiFitur = [[]];//array(array());//frekuensi fitur di kelas
for($i=0;$i<$nKelas;$i++){
    $frekuensiFitur[$i]=array_fill(0, $nFitur, 0);//inisialisasi frekuensi fitur di kelas
}

for ($d=0; $d<sizeof($dataset); $d++){
    $namaDosenPembimbing = $dataset[$d][1];
    $indexKelas = -1;
    //hitung frekuensi class
    for($i=0;$i<$nKelas;$i++){
        if($namaDosenPembimbing==$stringKelas[$i]){
            $indexKelas = $i;
            $frekuensiKelas[$indexKelas]++;//increment frekuensi kelas
            break;
        }
    }
    //hitung frekuensi fitur di class
    $token_baris = $tokens[$d];
    for($k=0;$k<sizeof($token_baris);$k++){
        $token = $token_baris[$k];//word-k atau token-k atau kata-k
        for ($j = 0; $j < $nFitur; $j++) {
            if($token==$stringFitur[$j]){
                $frekuensiFitur[$indexKelas][$j]++;//incremen frekuensi fitur-j di kelas-indexKelas
                break;
            }
        }
    }    
}//end of hitung frekuensi


echo "----------------------------------------------<br>";
//print frekuensi kelas
// echo "n_kelas: ".$nKelas."<br>";
// for($i=0;$i<$nKelas;$i++){
//     echo $stringKelas[$i]." : ".$frekuensiKelas[$i]."<br>";
//     //echo $frekuensiKelas[$i]."<br>";
// }

//print tabel frekuensi
echo "----------------------------------------------<br>";
echo "TABEL FREKUENSI<br>";
//header
echo "kelas;"."frekuensi kelas;";
for($i=0;$i<sizeof($stringFitur);$i++){
    echo $stringFitur[$i].";";
}
echo "<br>";
//row
for($i=0;$i<$nKelas;$i++){
    echo $stringKelas[$i].";".$frekuensiKelas[$i].";";
    for($j=0;$j<$nFitur;$j++){
        echo $frekuensiFitur[$i][$j].";";
    }
    echo "<br>";
}
//----------------------------
//step-7: Proses Training untuk membangun model Naive Bayes
//----------------------------
$pC = array_fill(0, $nKelas, 0);//peluang kelas ci
$pWC = [[]];//array(array());//peluang fitur-k di kelas ci
for($i=0;$i<$nKelas;$i++){
    $pWC[$i]=array_fill(0, $nFitur, 0);//inisialisasi pWC
}

$sumFrekuensiFiturTiapKelas = array_fill(0, $nKelas, 0);//sum of frekuensi fitur-k di tiap kelas
$nData = sizeof($dataset);//banyaknya elemen data di dataset
//hitung probabilitas kelas-i
for($i=0;$i<$nKelas;$i++){
    $f = $frekuensiKelas[$i];//frekuensi class-i
    $probabilityKelas = $f/$nData;
    $pC[$i] = $probabilityKelas;
    for ($k = 0; $k < $nFitur; $k++) {
         $sumFrekuensiFiturTiapKelas[$i] += $frekuensiFitur[$i][$k];
    }
}
//hitung probabilitas fitur-k di kelas c-i = pWkCi
$wSize = sizeof($stringFitur);
for ($i = 0; $i < $nKelas; $i++) {
    for ($k = 0; $k < $nFitur; $k++) {
        $probabilityFitur_k_di_kelas_i = ($frekuensiFitur[$i][$k] + 1) / ($sumFrekuensiFiturTiapKelas[$i] + $wSize);
        $pWC[$i][$k] = $probabilityFitur_k_di_kelas_i ;
    }
}

//print tabel model
echo "----------------------------------------------<br>";
//header
echo "kelas;"."pCi;";
for($i=0;$i<sizeof($stringFitur);$i++){
    echo "p_".$stringFitur[$i]."_Ci;";
}
echo "<br>";
//row
for($i=0;$i<$nKelas;$i++){
    echo $stringKelas[$i].";".$pC[$i].";";
    for($j=0;$j<$nFitur;$j++){
        echo $pWC[$i][$j].";";
    }
    echo "<br>";
}
//END OF PROSES TRANING NAIVE BAYES------------------------------------------------------------

//PROSES TESTING NAIVE BAYES----------------------------------------------------
$judulBaru = "PENERAPAN USER GENERATED CONTENT UNTUK PENGEMBANGAN PLATFORM PELATIHAN DAN KURSUS MENGGUNAKAN METODE AGILE";
$dosenPembimbingJudulBaru  = "NULL";

$frekuensiFiturBaru = array_fill(0, $nFitur, 0);
$probabilitasNB = array_fill(0, $nKelas, 0);
$judulBaru = strtolower($judulBaru);
$tokensBaru = explode(" ", $judulBaru);
for ($i = 0; $i < sizeof($tokensBaru); $i++) {
    for ($k = 0; $k < $nFitur; $k++) {
        if ($tokensBaru[$i]==$stringFitur[$k]) {
            $frekuensiFiturBaru[$k]++;
            break;
        }
    }
}

//hitung peluang judul baru
$arg_max = -999999;
$iMAX = -1;
for ($i = 0; $i < $nKelas; $i++) {
    $probabilitasNB[$i] = $pC[$i];
    for ($k = 0; $k < $nFitur; $k++) {
        if ($frekuensiFiturBaru[$k] > 0) {
            $probabilitasNB[$i] *= $pWC[$i][$k];
        }
    }
    //evaluasi arg_max
    if($probabilitasNB[$i]>$arg_max){
        $arg_max = $probabilitasNB[$i];
        $iMAX = $i;
    }  
}

//penentuan class
if($iMAX>-1){
    $hasilKlasifikasi = $stringKelas[$iMAX];
    $dosenPembimbingJudulBaru = $hasilKlasifikasi;
    echo "Hasil Klasifikasi: " .$hasilKlasifikasi;
}

//END OF TESTING-----------------------------------------------------------------
?>