<?php
$result=array();
$link = mysqli_connect('localhost', 'root', '', 'room');
if ($link == false)
    echo 'Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error();
else
{
    mysqli_set_charset($link, "utf8");
        if (!empty($_COOKIE['login'])){
            $login = $_COOKIE['login'];
            $query = "SELECT name FROM users WHERE login='".$login."'";
            $res = mysqli_query($link, $query);
            $res = mysqli_fetch_array($res);
            if (!empty($res)){
                $result=array('name' => $res['name']);
                echo json_encode($result);
            }
            else echo "res is empty";
        }
        else
            echo "not cookie";
}