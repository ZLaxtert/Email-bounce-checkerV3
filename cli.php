<?php

// DONT CHANGE THIS
/*==========> INFO 
 * CODE     : BY ZLAXTERT
 * SCRIPT   : EMAIL BOUNCE CHECKER
 * VERSION  : V3.5
 * TELEGRAM : t.me/zlaxtert
 * BY       : DARKXCODE
 */


//========> REQUIRE

require_once "function/function.php";
require_once "function/settings.php";

//========> BANNER

echo banner();
echo banner2();

//========> GET FILE

enterlist:
echo "$WH [$GR+$WH] Your file ($YL example.txt $WH) $GR>> $BL";
$listname = trim(fgets(STDIN));
if (empty($listname) || !file_exists($listname)) {
    echo PHP_EOL . PHP_EOL . "$WH [$YL!$WH] $RD FILE NOT FOUND$WH [$YL!$WH]$DEF" . PHP_EOL . PHP_EOL;
    goto enterlist;
}
$lists = array_unique(explode("\n", str_replace("\r", "", file_get_contents($listname))));


//=========> COUNT

$live = 0;
$die = 0;
$rto = 0;
$unknown = 0;
$limit = 0;
$no = 0;
$total = count($lists);
echo "\n\n$WH [$YL!$WH] TOTAL $GR$total$WH LISTS [$YL!$WH]$DEF\n\n";

//========> LOOPING

foreach ($lists as $list) {
    $no++;
    // EXPLODE
    $email = multiexplode(array(":", "|", "/", ";", ""), $list)[0];
    $pass = multiexplode(array(":", "|", "/", ";", ""), $list)[1];
    //API
    $api = $APIs . "validator/bounceV3/?list=$email&proxy=$Proxies&proxyAuth=$proxy_Auth&type_proxy=$type_proxy&apikey=$apikey";
    //CURL

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //RESPONSE
    $x = curl_exec($ch);
    curl_close($ch);
    $js = json_decode($x, TRUE);
    $msg = $js['data']['msg'];
    $jam = Jam();
    //============> RESPONSE


    if (strpos($x, '"status":"live"')) {
        $live++;
        save_file("result/live.txt", "$email");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$GR LIVE$DEF =>$BL $email$DEF | [$YL MSG$DEF: $WH$msg$DEF ] | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;
    } else if (strpos($x, '"msg":"Incorrect APIkey!"')) {

        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$RD DIE$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG Incorrect APIkey!$DEF ] | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;

    } else if (strpos($x, '"status":"die"')) {
        $die++;
        save_file("result/die.txt", "$email");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$RD DIE$DEF =>$BL $email$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;
    } else if (strpos($x, '"status":"unknown"')) {
        $limit++;
        save_file("result/limit.txt", "$email");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$CY LIMIT$DEF =>$BL $email$DEF | [$YL MSG$DEF: $MG$msg$DEF ] | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;
    } else if ($x == "") {
        $rto++;
        save_file("result/RTO.txt", "$email");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;
    } else if (strpos($x, 'Request Timeout')) {
        $rto++;
        save_file("result/RTO.txt", "$email");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;
    } else if (strpos($x, 'Service Unavailable')) {
        $rto++;
        save_file("result/RTO.txt", "$email");
        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$DEF TIMEOUT$DEF =>$BL $email$DEF | [$YL MSG$DEF:$MG REQUEST TIMEOUT!$DEF ] | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;
    } else {
        $unknown++;
        save_file("result/unknown.txt", "$email");

        echo "[$RD$no$DEF/$GR$total$DEF][$CY$jam$DEF]$YL UNKNOWN$DEF =>$BL $email$DEF | BY$CY DARKXCODE$DEF (V3.5)" . PHP_EOL;
    }



}



//============> END

echo PHP_EOL;
echo "================[DONE]================" . PHP_EOL;
echo " DATE          : " . $date . PHP_EOL;
echo " LIVE          : " . $live . PHP_EOL;
echo " DIE           : " . $die . PHP_EOL;
echo " TIMEOUT       : " . $rto . PHP_EOL;
echo " UNKNOWN       : " . $unknown . PHP_EOL;
echo " TOTAL         : " . $total . PHP_EOL;
echo "======================================" . PHP_EOL;
echo "[+] RATIO VALID => $GR" . round(RatioCheck($live, $total)) . "%$DEF" . PHP_EOL . PHP_EOL;
echo "[!] NOTE : CHECK AGAIN FILE 'unknown.txt' or 'RTO.txt' [!]" . PHP_EOL;
echo "This file '" . $listname . "'" . PHP_EOL;
echo "File saved in folder 'result/' " . PHP_EOL . PHP_EOL;

// ==========> FUNCTION

function collorLine($col)
{
    $data = array(
        "GR" => "\e[32;1m",
        "RD" => "\e[31;1m",
        "BL" => "\e[34;1m",
        "YL" => "\e[33;1m",
        "CY" => "\e[36;1m",
        "MG" => "\e[35;1m",
        "WH" => "\e[37;1m",
        "DEF" => "\e[0m"
    );
    $collor = $data[$col];
    return $collor;
}
function multiexplode($delimiters, $string)
{
    $one = str_replace($delimiters, $delimiters[0], $string);
    $two = explode($delimiters[0], $one);
    return $two;
}

?>