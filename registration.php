<?php

$link = new mysqli('localhost', 'root', '', 'room');
	if ($link == false)
		echo 'Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error();
	else {
		mysqli_set_charset($link, "utf8");	//кодировка
		if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['name'])){
			$login = $_POST['login'];
			$password = $_POST['password'];
			$name = $_POST['name'];
			$passwordHash = password_hash($password, PASSWORD_DEFAULT);
			$query = "SELECT * FROM users WHERE login=?";
			$query = $link->prepare($query);
			$query->bind_param("s",$login);
			$query->execute();
			$user = $query->get_result()->fetch_assoc();

			if (empty($user)){
				$query = "INSERT INTO users (login, name, password) VALUES (?, ?, ?)";
				$query = $link->prepare($query);
				$query->bind_param("sss",$login, $name, $passwordHash);
				$result = $query->execute();

				if ($result == false) 
					echo "Произошла ошибка при выполнении запроса";				
				else {
					echo "Успешная регистрация";
					$_SESSION['auth'] = true;
					$_SESSION['login'] = $login;
					$_SESSION['name'] = $name;

					$key = random_int(1, 1000); 	//Сформируем случайную строку для куки 
					//Пишем куки (имя куки, значение, время жизни - сейчас+месяц)
					setcookie('login', $user['login'], time() + 60 * 60 * 24 * 30); //логин
					setcookie('token', $key, time() + 60 * 60 * 24 * 30); //случайная строка
					setcookie('token',  $user['name'], time() + 60 * 60 * 24 * 30); //случайная строка



					header("Location:http://localhost/main.html");
				}
			}
			else echo "Этот логин уже занят";
		}
		else echo "оно пустое";
	}
