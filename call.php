<?php
header("Content-Type: application/json; encoding=utf-8");

$secret_key = '*******'; // Защищенный ключ приложения

$input = $_POST;

// Проверка подписи
$sig = $input['sig'];
unset($input['sig']);
ksort($input);
$str = '';
foreach ($input as $k => $v) {
  $str .= $k.'='.$v;
}

if ($sig != md5($str.$secret_key)) {
	$response['error'] = array(
		'error_code' => 10,
		'error_msg' => 'Несовпадение вычисленной и переданной подписи запроса.',
		'critical' => true
	);
} else {
	// Подпись правильная
	switch ($input['notification_type']) {
	
		case 'get_item':
			// Получение информации о товаре
			$item = $input['item'];
			if($item == 'item_1coin') {
				$response['response'] = array(
					'item_id' => 1,
					'title' => 'Бесконечные игры на 1 час',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 2
				);
			} else if($item == 'item_2coin') {
				$response['response'] = array(
					'item_id' => 2,
					'title' => 'Бесконечные игры на 1 день',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 8
				);
			} else if($item == 'item_3coin') {
				$response['response'] = array(
					'item_id' => 3,
					'title' => 'Бесконечные игры на 3 дня',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 22
				);
			} else if($item == 'item_4coin') {
				$response['response'] = array(
					'item_id' => 4,
					'title' => 'Бесконечные игры на 7 дней',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 48
				);
			}  else if($item == 'item_5coin') {
				$response['response'] = array(
					'item_id' => 5,
					'title' => '3 подсказки',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 1
				);
			}  else if($item == 'item_6coin') {
				$response['response'] = array(
					'item_id' => 6,
					'title' => '12 подсказок',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 4
				);
			}  else if($item == 'item_7coin') {
				$response['response'] = array(
					'item_id' => 7,
					'title' => '24 подсказки',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 8
				);
			}  else if($item == 'item_8coin') {
				$response['response'] = array(
					'item_id' => 8,
					'title' => '50 подсказок',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 15
				);
			} else {
				$response['error'] = array(
					'error_code' => 20,
					'error_msg' => 'Товара не существует.',
					'critical' => true
				);
			}
		break;

		case 'get_item_test':
			// Получение информации о товаре в тестовом режиме
			$item = $input['item'];
			if($item == 'item_1coin') {
				$response['response'] = array(
					'item_id' => 1,
					'title' => 'Бесконечные игры на 1 час',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 2
				);
			} else if($item == 'item_2coin') {
				$response['response'] = array(
					'item_id' => 2,
					'title' => 'Бесконечные игры на 1 день',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 8
				);
			} else if($item == 'item_3coin') {
				$response['response'] = array(
					'item_id' => 3,
					'title' => 'Бесконечные игры на 3 дня',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 22
				);
			} else if($item == 'item_4coin') {
				$response['response'] = array(
					'item_id' => 4,
					'title' => 'Бесконечные игры на 7 дней',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 48
				);
			}  else if($item == 'item_5coin') {
				$response['response'] = array(
					'item_id' => 5,
					'title' => '3 подсказки',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 1
				);
			}  else if($item == 'item_6coin') {
				$response['response'] = array(
					'item_id' => 6,
					'title' => '12 подсказок',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 4
				);
			}  else if($item == 'item_7coin') {
				$response['response'] = array(
					'item_id' => 7,
					'title' => '24 подсказки',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 8
				);
			}  else if($item == 'item_8coin') {
				$response['response'] = array(
					'item_id' => 8,
					'title' => '50 подсказок',
					'photo_url' => 'https://era-p.ru/img/img150.jpg',
					'price' => 15
				);
			} else {
				$response['error'] = array(
					'error_code' => 20,
					'error_msg' => 'Товара не существует.',
					'critical' => true
				);
			}
		break;

		case 'order_status_change':
			// Изменение статуса заказа
			if($input['status'] == 'chargeable') {
				$order_id = intval($input['order_id']);
				$item_id = intval($input['item_id']);
				$user_id = intval($input['user_id']);
				
				$mysqli = mysqli_connect("localhost", "*******", "*******", "*******");
				mysqli_query($mysqli, "set names utf8");
				
				$date_now = date("Y-m-d H:i:s");
				
				if($item_id == 1) {
					$com = 'Бесконечные игры на 1 час';
					$dat = date('Y-m-d H:i:s', strtotime("+1 hour", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 2) {
					$com = 'Бесконечные игры на 1 день';
					$dat = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 3) {
					$com = 'Бесконечные игры на 3 дня';
					$dat = date('Y-m-d H:i:s', strtotime("+3 day", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 4) {
					$com = 'Бесконечные игры на 7 дней';
					$dat = date('Y-m-d H:i:s', strtotime("+7 day", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 5) {
					$com = '3 подсказки';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+3 WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 6) {
					$com = '12 подсказок';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+12 WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 7) {
					$com = '24 подсказки';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+24 WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 8) {
					$com = '50 подсказок';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+50 WHERE `id_vk`='".$user_id."'"));
				}
				
				$up2 = mysqli_fetch_array(mysqli_query($mysqli, "INSERT INTO `buy`(`id_users`, `com`) VALUES ('".$user_id."', '".$com."')"));
				
				// Код проверки товара, включая его стоимость
				$app_order_id = 1; // Получающийся у вас идентификатор заказа.
				$response['response'] = array(
					'order_id' => $order_id,
					'app_order_id' => $app_order_id,
				);
			} else {
				$response['error'] = array(
					'error_code' => 100,
					'error_msg' => 'Передано непонятно что вместо chargeable.',
					'critical' => true
				);
			}
		break;

		case 'order_status_change_test':
			// Изменение статуса заказа в тестовом режиме
			if($input['status'] == 'chargeable') {
				$order_id = intval($input['order_id']);
				$item_id = intval($input['item_id']);
				$user_id = intval($input['user_id']);
				
				$mysqli = mysqli_connect("localhost", "*******", "*******", "*******");
				mysqli_query($mysqli, "set names utf8");
				
				$date_now = date("Y-m-d H:i:s");
				
				if($item_id == 1) {
					$com = 'Бесконечные игры на 1 час';
					$dat = date('Y-m-d H:i:s', strtotime("+1 hour", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 2) {
					$com = 'Бесконечные игры на 1 день';
					$dat = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 3) {
					$com = 'Бесконечные игры на 3 дня';
					$dat = date('Y-m-d H:i:s', strtotime("+3 day", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 4) {
					$com = 'Бесконечные игры на 7 дней';
					$dat = date('Y-m-d H:i:s', strtotime("+7 day", strtotime($date_now)));
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `game_live`='".$dat."' WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 5) {
					$com = '3 подсказки';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+3 WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 6) {
					$com = '12 подсказок';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+12 WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 7) {
					$com = '24 подсказки';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+24 WHERE `id_vk`='".$user_id."'"));
				} else if($item_id == 8) {
					$com = '50 подсказок';
					$up1 = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `help`=`help`+50 WHERE `id_vk`='".$user_id."'"));
				}
				
				$up2 = mysqli_fetch_array(mysqli_query($mysqli, "INSERT INTO `buy`(`id_users`, `com`) VALUES ('".$user_id."', '".$com."')"));
				
				// Код проверки товара, включая его стоимость
				// Код проверки товара, включая его стоимость
				$app_order_id = 1; // Получающийся у вас идентификатор заказа.
				$response['response'] = array(
					'order_id' => $order_id,
					'app_order_id' => $app_order_id,
				);
			} else {
				$response['error'] = array(
					'error_code' => 100,
					'error_msg' => 'Передано непонятно что вместо chargeable.',
					'critical' => true
				);
			}
		break;
		
	}
	echo json_encode($response);	
}
?>