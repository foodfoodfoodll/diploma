<?php

$link = mysqli_connect('localhost', 'root', '', 'room');
if ($link == false)
    echo 'Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error();
else
{
$ventInRoom = 50;
$ventOutRoom = 50;

$startWork = mktime(8);
$endWork = mktime(17);
$res = array();

$valves = false;
$humidifier = 0;
$refrigerator = 0;
$calorifier = 0;
$airCooler = 0;
$alarm = false;
$calorifierRef = 0;

$pVent = 30;
$pCalorifier = 40;
$pAirCooler = 40;
$V = 100;

if (!empty($_GET)){
    $date = strval(date("Y-m-d")). " " . $_GET["currentTime"] . ":00";
    $query = "SELECT temp, wet, dPressure FROM variables WHERE date='".$date."'";
    $response = mysqli_query($link, $query);
    $response = mysqli_fetch_array($response);
    $adjastedWet = $_GET['adjastedWet'];
    $adjastedTemp = $_GET['adjastedTemp'];
    $adjastedPressure = $_GET['adjastedPressure'];
    $adjastedPressureFilter1 = $_GET['adjastedPressureFilter1'];
    $adjastedPressureFilter2 = $_GET['adjastedPressureFilter2'];
    $adjastedPressureFilter3 = $_GET['adjastedPressureFilter3'];

    $constTemp = $_GET['constTemp'];
    $constWet = $_GET['constWet'];
    $constPressure = $_GET['constPressure'];

    $tempIn = $_GET['tempIn'];
    $wet = $_GET['wet'];
    $dPressure = $_GET['dPressure'];
    $dPressureFilter1 = $_GET['dPressureFilter1'];
    $dPressureFilter2 = $_GET['dPressureFilter2'];
    $dPressureFilter3 = $_GET['dPressureFilter3'];

    $fireAlarm = $_GET['fireAlarm'];

    $res["fireAlarm"] = $fireAlarm;
    $res["wet"] = $wet;
    $res["tempIn"] = $tempIn;
    $res["dPressure"] = $dPressure;
    $res["dPressureFilter1"] = $dPressureFilter1;
    $res["dPressureFilter2"] = $dPressureFilter2;
    $res["dPressureFilter3"] = $dPressureFilter3;
    $res["filterMessage"] = "";
    $res["alarm"] = "";
{
    $time = explode(":", $_GET["currentTime"]);
    $time[1]++;
    if ($time[1] >= 60){
        $time[1] %= 60;
        $time[0]++;
    }
    if ($time[0] >= 24){
        $time[0] %= 24;
    }

    $currentTime = sprintf("%02d", $time[0]) . ":" . sprintf("%02d", $time[1]);
}
$res["currentTime"] = $currentTime;
if ($_GET['allWorking']=='false'){
    $res["tempIn"] = $tempIn;
    $res["wet"] = $wet;
    $res["dPressure"] = $dPressure;
    $res["dPressureFilter1"] = $dPressureFilter1;
    $res["dPressureFilter2"] = $dPressureFilter2;
    $res["dPressureFilter3"] = $dPressureFilter3;
    $res["currentTime"] = $currentTime;
    $res["ventInRoom"] = 0;
    $res["ventOutRoom"] = 0;
    $res["calorifier"] = 0;
    $res["airCooler"] = 0;
    $res["humidifier"] = 0;
    $res["refrigerator"] = 0;
    $res["filterMessage"] = "";
    $res["alarm"] = "Работа остановлена";
    $res["valves"] = "Открыты";
    echo json_encode($res);
    exit();
}
//isWorkTime
{
    $tcurrentTime = mktime($time[0], $time[1]);
    if ($tcurrentTime > $startWork && $tcurrentTime < $endWork){
        $isWorkTime = true;
        $res["isWorkTime"] =  "Рабочее время";
    }
    else {
        $isWorkTime = false;
        $res["isWorkTime"] =  "Нерабочее время";
    }
}
//const'ы
{
    if ($isWorkTime==true){
        $constTemp = true;
        $constWet = true;
        $constPressure = true;
        $tmp = array('isWorkTime' => $isWorkTime, 'constTemp' => $constTemp, 'constWet' => $constWet, 'constPressure' => $constPressure);

    }
    else{
        if ($constTemp == 'true' && $isWorkTime==false)
            $constTemp = true;
        else $constTemp = false;
        if ($constWet == 'true' && $isWorkTime==false)
            $constWet = true;
        else $constWet = false;
        if ($constPressure == 'true' && $isWorkTime==false)
            $constPressure = true;
        else $constPressure = false;
    }
}
    if($fireAlarm=='true'){
        $ventInRoom = 0;
        $ventOutRoom = 0;
        $humidifier = 0;
        $refrigerator = 0;
        $calorifier = 0;
        $airCooler = 0;
        $alarm = true;
        $valves = true;
        $res["alarm"] = $res["alarm"] . "Пожарная тревога<br>";
    }
    else
//фильтры
{
    if ($dPressureFilter1 <= $adjastedPressureFilter1*1.1 && $dPressureFilter1 > $adjastedPressureFilter1)
        $res["filterMessage"] = $res["filterMessage"] . "Фильтр грубой очистки приточного воздуха засорен <br>";
    else if ($dPressureFilter1 < $adjastedPressureFilter1 || $dPressureFilter1 > $adjastedPressureFilter1*1.1){
        $ventInRoom = 0;
        $ventOutRoom = 0;
        $humidifier = 0;
        $refrigerator = 0;
        $calorifier = 0;
        $airCooler = 0;
        $alarm = true;
        $res["alarm"] = $res["alarm"] . "Неисправность фильтра грубой очистки приточного воздуха<br>";
    }
    if ($dPressureFilter2 <= $adjastedPressureFilter2*1.1 && $dPressureFilter2 > $adjastedPressureFilter2)
        $res["filterMessage"] = $res["filterMessage"] . "Фильтр тонкой очистки приточного воздуха засорен<br>";
    else if ($dPressureFilter2 < $adjastedPressureFilter2 || $dPressureFilter2 > $adjastedPressureFilter2*1.1){
            $ventInRoom = 0;
            $ventOutRoom = 0;
            $humidifier = 0;
            $refrigerator = 0;
            $calorifier = 0;
            $airCooler = 0;
            $alarm = true;
            $res["alarm"] = $res["alarm"] . "Неисправность фильтра тонкой очистки приточного воздуха<br>";
    }
    if ($dPressureFilter3 <= $adjastedPressureFilter3*1.1 && $dPressureFilter3 > $adjastedPressureFilter3)
        $res["filterMessage"] = $res["filterMessage"] . "Задерживающий фильтр вытяжного воздуха засорен<br>";
    else if ($dPressureFilter3 < $adjastedPressureFilter3 || $dPressureFilter3 > $adjastedPressureFilter3*1.1){
            $ventInRoom = 0;
            $ventOutRoom = 0;
            $humidifier = 0;
            $refrigerator = 0;
            $calorifier = 0;
            $airCooler = 0;
            $alarm = true;
            $res["alarm"] = $res["alarm"] . "Неисправность задерживающего фильтра вытяжного воздуха\n";
    }
}
    if($alarm!=true){
        if($dPressure<$adjastedPressure)
        {
            $ventInRoom = 100;
            $dPressure+=1;
        }
        else if($dPressure>$adjastedPressure)
        {
            $ventInRoom = 25;
            $dPressure-=0.5;
        }


        if ($wet > $adjastedWet && $constWet==true){       //осушение
            $humidifier = 0;
            $refrigerator = 100;
            $wet -= 1;
            $calorifierRef = ceil(((($adjastedTemp - 3)*$V)/($ventInRoom*$pVent*$pCalorifier))*1000);
        }
        if ($tempIn < $adjastedTemp){   //нагрев
            if ($constTemp==true){
                $airCooler = 0;
                if ($adjastedTemp-$tempIn > $pCalorifier)
                    $calorifier = 100;
                else if ($adjastedTemp-$tempIn > 90*$pCalorifier/200)
                    $calorifier = 90;
                else if ($adjastedTemp-$tempIn > 80*$pCalorifier/200)
                    $calorifier = 80;
                else if ($adjastedTemp-$tempIn > 70*$pCalorifier/200)
                    $calorifier = 70;
                else if ($adjastedTemp-$tempIn > 60*$pCalorifier/200)
                    $calorifier = 60;
                else if ($adjastedTemp-$tempIn > 50*$pCalorifier/200)
                    $calorifier = 50;
                else if ($adjastedTemp-$tempIn > 40*$pCalorifier/200)
                    $calorifier = 40;
                else if ($adjastedTemp-$tempIn > 30*$pCalorifier/200)
                    $calorifier = 30;
                else if ($adjastedTemp-$tempIn > 20*$pCalorifier/200)
                    $calorifier = 20;
                else
                    $calorifier = ceil(((($adjastedTemp - $tempIn)*$V)/($ventInRoom*$pVent*$pCalorifier))*1000);
                $tempIn = ($tempIn * $V + (($calorifier*$pCalorifier)/100)*($ventInRoom*$pVent)/100)/$V;
            }
        }
        else if ($tempIn > $adjastedTemp){      //охлаждение
            if ($constTemp==true) {
                $calorifier = 0;
                if ($tempIn - $adjastedTemp > $pCalorifier)
                    $airCooler = 100;
                else if ($tempIn - $adjastedTemp > 90*$pAirCooler/200)
                    $airCooler = 90;
                else if ($tempIn - $adjastedTemp > 80*$pAirCooler/200)
                    $airCooler = 80;
                else if ($tempIn - $adjastedTemp > 70*$pAirCooler/200)
                    $airCooler = 70;
                else if ($tempIn - $adjastedTemp > 60*$pAirCooler/200)
                    $airCooler = 60;
                else if ($tempIn - $adjastedTemp > 50*$pAirCooler/200)
                    $airCooler = 50;
                else if ($tempIn - $adjastedTemp > 40*$pAirCooler/200)
                    $airCooler = 40;
                else if ($tempIn - $adjastedTemp > 30*$pAirCooler/200)
                    $airCooler = 30;
                else if ($tempIn - $adjastedTemp > 20*$pAirCooler/200)
                    $airCooler = 20;
                else
                    $airCooler = ceil(((($tempIn - $adjastedTemp)*$V)/($ventInRoom*$pVent*$pAirCooler))*1000);
                $tempIn = ($tempIn * $V - (($airCooler*$pAirCooler)/100)*($ventInRoom*$pVent)/100)/$V;
            }
        }
        if ($calorifier<$calorifierRef)
            $calorifier=$calorifierRef;
        if ($wet < $adjastedWet && $constWet==true){        //увлажнение
            $humidifier = 100;
            $refrigerator = 0;
            $wet += 1;
        }
        if (!empty($response)){
            if ($adjastedTemp!=$tempIn && $response["temp"]==$tempIn && $calorifier!=0 && ($isWorkTime == false && $constTemp==true))
                $res["alarm"] = $res["alarm"] . "Неисправность калорифера<br>";
            if ($adjastedTemp!=$tempIn && $response["temp"]==$tempIn && $airCooler!=0 && ($isWorkTime == false && $constTemp==true))
                $res["alarm"] = $res["alarm"] . "Неисправность охладителя воздуха<br>";
            if ($adjastedWet!=$wet && $response["wet"]==$wet && $humidifier!=0 && ($isWorkTime == false && $constWet==true))
                $res["alarm"] = $res["alarm"] . "Неисправность увлажнителя воздуха<br>";
            if ($adjastedWet!=$wet && $response["wet"]==$wet && $refrigerator!=0 && ($isWorkTime == false && $constWet==true))
                $res["alarm"] = $res["alarm"] . "Неисправность осушителя воздуха<br>";
            if ($adjastedPressure!=$dPressure && $response["dPressure"]==$dPressure && $ventInRoom!=0  && ($isWorkTime == false && $constPressure==true))
                $res["alarm"] = $res["alarm"] . "Неисправность вентилятора<br>";
        }
    }
    else{
        $ventInRoom = 0;
        $ventOutRoom = 0;
        $humidifier = 0;
        $refrigerator = 0;
        $calorifier = 0;
        $airCooler = 0;
        $alarm = true;
    }
$res["wet"] = $wet;
$res["tempIn"] = sprintf("%01.01f", $tempIn);
$res["dPressure"] = $dPressure;

//текст
{

    $res["ventInRoom"] = $ventInRoom;
    $res["ventOutRoom"] = $ventOutRoom;
    $res["calorifier"] = $calorifier;
    $res["airCooler"] = $airCooler;
    $res["refrigerator"] = $refrigerator;
    $res["humidifier"] = $humidifier;

    if ($valves)
        $res["valves"] = "Закрыты";
    else
        $res["valves"] = "Открыты";
}
    $tempIn = sprintf("%01.01f", $tempIn);
    $date = strval(date("Y-m-d")). " " . $currentTime . ":00";
    $query = "SELECT * FROM variables WHERE date='".$date."'";
    $response = mysqli_query($link, $query);
    if ($response!=false){
        $query = "DELETE FROM variables WHERE date = '".$date."'";
    }
    $query = "INSERT INTO variables SET date = '".$date."', temp = '".$tempIn."', wet = '".$wet."', dPressure = '".$dPressure."'";
    $response = mysqli_query($link, $query);


    echo json_encode($res);
}
else echo "error";
}
?>