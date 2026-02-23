<?php
	session_start();
	include("../settings/connect_datebase.php");
	include("../libs/recaptcha/autoload.php");

	$secret = '6LedLnUsAAAAAHpZ_0F10666RPjxxVeWRTusg7ha';

	if (isset($_POST['g-recaptcha-response'])) {
	// создать экземпляр службы recaptcha, используя секретный ключ
	$recaptcha = new \ReCaptcha\ReCaptcha($secret);
	// получить результат проверки кода recaptcha
	$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

	if ($resp->isSuccess()) {
	} 
	} else {
	echo "Нет ответа от RECAPTURE"; //Нет ответа от RECAPTURE
	}
	$login = $_POST['login'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."';");
	
	$id = -1;
	if($user_read = $query_user->fetch_row()) {
		// создаём новый пароль
		$id = $user_read[0];
	}
	
	function PasswordGeneration() {
		// создаём пароль
		$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; // матрица
		$max=10; // количество
		$size=StrLen($chars)-1; // Определяем количество символов в $chars
		$password="";
		
		while($max--) {
			$password.=$chars[rand(0,$size)];
		}
		
		return $password;
	}
	
	if($id != 0) {
		//обновляем пароль
		$password = PasswordGeneration();;
		// проверяем не используется ли пароль 
		$query_password = $mysqli->query("SELECT * FROM `users` WHERE `password`= '".md5($password)."';");
		while($password_read = $query_password->fetch_row()) {
			// создаём новый пароль
			$password = PasswordGeneration();
		}
		// обновляем пароль
		$mysqli->query("UPDATE `users` SET `password`='".md5($password)."' WHERE `login` = '".$login."'");
		// отсылаем на почту
		//mail($login, 'Безопасность web-приложений КГАПОУ "Авиатехникум"', "Ваш пароль был только что изменён. Новый пароль: ".$password);
	}
	
	echo $id;
?>