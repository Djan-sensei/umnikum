<?php

	include('../mysql.php');
	
	$otv = mysqli_real_escape_string($mysqli, $_POST['otv']);
	
	$gamid = mysqli_real_escape_string($mysqli, $_POST['gamid']);
	$gamid = (int)$gamid;
	
	$gamlvl = mysqli_real_escape_string($mysqli, $_POST['gamlvl']);
	$gamlvl = (int)$gamlvl;
	
	$gam = mysqli_fetch_array(mysqli_query($mysqli, "select `id`, `otv`, `pod` from `games2` where `id`='".$gamid."'"));
	
	$user = mysqli_fetch_array(mysqli_query($mysqli, "select `id` from `users` where `id_vk` = '".$id."'"));
	
	$gf = mysqli_fetch_array(mysqli_query($mysqli, "select * from `game_finish` where `id_user` = '".$user['id']."' and `carta_lvl` = '".$gamlvl."' and `end`='0'"));
	
	if($gam['otv'] == $otv) {
		$mes = '<b class="prav_otv">Правильный ответ</b>';		
		$pr = 1;		
		if($gf['id']) {
			mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `game_finish` SET `och`=`och`+1 WHERE `id`='".$gf['id']."'"));
		} else {
			mysqli_fetch_array(mysqli_query($mysqli, "INSERT INTO `game_finish`(`id_user`, `carta_lvl`, `och`) VALUES ('".$user['id']."', '".$gamlvl."', '1')"));
		}
		$otvpn = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `prav`=`prav`+1 WHERE `id`='".$user['id']."'"));
	} else {
		$mes = '<b class="non_otv">Вы ответили не верно</b>';
		$pr = 0;
		$otvpn = mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `users` SET `neprav`=`neprav`+1 WHERE `id`='".$user['id']."'"));
	}
	
	$mes .= '<br><p>'.$gam['pod'].'</p>';
	
	$result = array('mes' => $mes, 'rez' => $otv, 'pr' => $pr);
	echo json_encode($result); 

?>