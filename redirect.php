<?php
    $link = new mysqli('localhost', 'root', '', 'room');
    $query = 'SELECT * FROM users WHERE login=?';
    $query = $link->prepare($query);
    $query->bind_param("s",$_COOKIE['login']);
    $query->execute();

    $result = $query->get_result()->fetch_assoc();
    
    if(isset($_COOKIE['login']) && isset($_COOKIE['token'])){
        if($_COOKIE['login'] == $result['login'] && $_COOKIE['token'] == $result['token']){
            echo "true";
            $_SESSION['auth'] = true;
            $_SESSION['login'] = $_COOKIE['login'];
            exit();
        }else{
            echo "false";
            exit();
        }
    }else{
        echo "false";
        exit();
    }
