<?php
$link = mysqli_connect('localhost', 'root', '', 'room');
if ($link == false)
    echo 'Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error();
else
{
    mysqli_set_charset($link, "utf8");
        $query = "SELECT * FROM customisation";
        $res = mysqli_query($link, $query);
        if ($res != false){
            $res = mysqli_fetch_array($res);
            $result = array('adjastedTemp' => $res['adjastedTemp'],
            'adjastedWet' => $res['adjastedWet'],
            'adjastedPressure' => $res['adjastedPressure'],
            'constTemp' => $res['constTemp'],
            'constWet' => $res['constWet'],
            'constPressure' => $res['constPressure'],
            'adjastedPressureFilter1' => $res['adjastedPressureFilter1'],
            'adjastedPressureFilter2' => $res['adjastedPressureFilter2'],
            'adjastedPressureFilter3' => $res['adjastedPressureFilter3']);
            echo json_encode($result);
        }
}