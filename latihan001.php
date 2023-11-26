<?php
    //--------------------------------------------------
    // Step-1: Baca dataset dari file
    //--------------------------------------------------
    $filename = "datatraining.csv";
    $file = fopen($filename, "r");
    $dataset = array(array());
    $i = 0;
    while (!feof($file)) {
        $data = fgetcsv($file, null, ';');
        $dataset[$i][0]=$data[3];//judul penelitian
        $dataset[$i][1]=$data[4];//nama dosen pembimbing 1
        $i++;
    }
    fclose($file);

    if(sizeof($dataset)>0){
        //--------------------------------------------------
        // Step-2: Case Folding
        //--------------------------------------------------
        for($i=0;$i<sizeof($dataset);$i++){
            $dataset[$i][0] = strtolower($dataset[$i][0]);
        }

        //--------------------------------------------------
        // Step-3: Filtering
        //--------------------------------------------------
        $stopSymbols = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "?", "@","(",")",".","[","]",":");
        $stopWords = array("di","dan", "untuk", "atau", "dengan");
        //replace all stop symbols
        for($i=0;$i<sizeof($stopSymbols);$i++){
            $symbol = $stopSymbols[$i];
            for($j=0;$j<sizeof($dataset);$j++){
                $dataset[$j][0] = str_replace($symbol, " ", $dataset[$j][0]);
            }
        }

        //replace all stop words
        for($i=0;$i<sizeof($stopWords);$i++){
            $word = $stopWords[$i];
            for($j=0;$j<sizeof($dataset);$j++){
                $pattern = "/\b(".$word.")\b/";
                $dataset[$j][0] = preg_replace($pattern, " ", $dataset[$j][0]);
            }
        }

        //replace all spasi ganda dan trim
        for($j=0;$j<sizeof($dataset);$j++){
            $dataset[$j][0] = preg_replace("/\s+/", " ", $dataset[$j][0]);
            $dataset[$j][0] = trim($dataset[$j][0]);
        }

        //--------------------------------------------------
        // Step-4: Tokenizing
        //--------------------------------------------------
        for($j=0;$j<sizeof($dataset);$j++){
            $tokens = explode(" ",$dataset[$j][0]);
            echo "| ";
            for($i=0;$i<sizeof($tokens);$i++){
                echo $tokens[$i]." | ";
            }
            echo "<br>";
        }




        //print dataset
        for($i=0;$i<sizeof($dataset);$i++){
            echo $dataset[$i][0]." : ".$dataset[$i][1]."<br>";
        }

    }//end of if(sizeof($dataset)>0)   

?>