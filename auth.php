<?php
$link = new mysqli('localhost', 'root', '', 'room');
if ($link == false)
	echo 'Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error();
else {
	mysqli_set_charset($link, "utf8");
	//echo implode('', $_POST);
	if (!empty($_POST['login']) && !empty($_POST['password'])) {
		$login = $_POST['login'];
		$password = $_POST['password'];
		//////////////////////////////////////////////////////////////////////////////////////
		$query = "SELECT * FROM users WHERE login=?";
		$query = $link->prepare($query);
		$query->bind_param("s", $login);
		$query->execute();
		$user = $query->get_result()->fetch_assoc(); // Преобразуем ответ из БД в нормальный массив PHP

		if (empty($user))
			echo "Такого пользователя не существует";
		else {
			if (password_verify($password, $user['password'])) {
				session_start();
				echo "Вход выполнен";
				$_SESSION['auth'] = true;	//Пишем в сессию информацию о том, что мы авторизовались
				$_SESSION['login'] = $user['login']; 	//Пишем в сессию логин пользователя


				$key = random_int(1, 1000); 	//Сформируем случайную строку для куки
				//Пишем куки (имя куки, значение, время жизни - сейчас+месяц)
				setcookie('login', $user['login'], time() + 60 * 60 * 24 * 30, '/'); //логин
				setcookie('token', $key, time() + 60 * 60 * 24 * 30, '/'); //случайная строка


				$query = 'UPDATE users SET token=? WHERE login=?'; //Пишем эту же куку в базу данных для данного юзера.
				$query = $link->prepare($query);
				$query->bind_param("is",$key,$login);
				$query->execute();

				mysqli_query($link, $query);


				header("Location:http://localhost/main.html");
				//}
			} else echo "Неверный пароль";
		}
	} else echo 'пусто';
}