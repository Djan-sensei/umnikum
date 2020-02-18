<?php
	include('mysql.php');	
	
	$user = mysqli_fetch_array(mysqli_query($mysqli, "select * from `users` where `id_vk` = '".$id."'"));
	
	if(!$user['id']) {
		$request_params = array(
			'user_id' => $id,
			'fields' => 'first_name, last_name',
			'v' => '5.60'
		);
		$get_params = http_build_query($request_params);
		$result = json_decode(file_get_contents('https://api.vk.com/method/users.get?'. $get_params.'&access_token=*******'));
		$vk_login = $result -> response[0] -> first_name .' '.  $result -> response[0] -> last_name;
	
		mysqli_query($mysqli, "INSERT INTO `users`(`name`, `id_vk`, `live`, `help`) VALUES ('".$vk_login."', '".$id."', '5', '10')");
		echo '<script> window.location.href="'.$this_url.'";</script>';
		exit;
	}
	
	if($_GET['num']) { $num = (int)$_GET['num']; }
	else { $num = 1; }
	
	$carta = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `carta` WHERE `num`='".$num."'"));
	
	$carta_next = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `carta` WHERE `num`='". ($num+15) ."'"));
	$carta_prev = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `carta` WHERE `num`='". ($num-15) ."'"));
	
	// Последний пройденный уровень
	$end_lvl = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `game_finish` WHERE `id_user`='".$user['id']."' and `end`='1' ORDER by `carta_lvl` DESC LIMIT 0,1"));
	
	// Считаем рейтинг
	$sql_ret_all = mysqli_query($mysqli, "SELECT * FROM `game_finish` WHERE `id_user`='".$user['id']."' and `end`='1' GROUP by `carta_lvl` ORDER by `och` DESC");
	$ret_all = 0;
	while($mret_all = mysqli_fetch_assoc($sql_ret_all)) {
		$ret_all += $mret_all['och'];
	}
	
	
	if($ret_all != $user['ret']) {
		mysqli_query($mysqli, "UPDATE `users` SET `ret`='".$ret_all."' WHERE `id`='".$user['id']."'");
		echo '<script>window.location.href="/index.php'.$url.'";</script>';
		exit;
	}
		
	mysqli_query($mysqli, "DELETE FROM `game_finish` WHERE `end`='0' and `id_user`='".$user['id']."'");
	
	if($user['live'] < 0) {
		mysqli_query($mysqli, "UPDATE `users` SET `live`='0', `live_time`='".date('Y-m-d H:i:s')."' WHERE `id`='".$user['id']."'");
		echo '<script> window.location.href="/'.$url.'";</script>';
		exit;
	}
	
	// Бесконечная игра
	if(date('Y-m-d H:i:s') >= $user['game_live'] and $user['game_live'] != '0000-00-00 00:00:00') {
		mysqli_query($mysqli, "UPDATE `users` SET `live`='5', `game_live`='0000-00-00 00:00:00' WHERE `id`='".$user['id']."'");
		echo '<script>window.location.href="/'.$url.'";</script>';
		exit;
	}
	
	if($user['live'] < 5 and $user['live_time'] == '0000-00-00 00:00:00') {
		mysqli_query($mysqli, "UPDATE `users` SET `live_time`='".date('Y-m-d H:i:s')."' WHERE `id`='".$user['id']."'");
		echo '<script> window.location.href="/'.$url.'";</script>';
		exit;
	}
	
	if($user['live'] > 5) {
		mysqli_query($mysqli, "UPDATE `users` SET `live`='5' WHERE `id`='".$user['id']."'");
		echo '<script> window.location.href="/'.$url.'";</script>';
		exit;
	}
	
	if($user['live'] >= 5 and $user['live_time'] != '0000-00-00 00:00:00') {
		mysqli_query($mysqli, "UPDATE `users` SET `live`='5', `live_time`='0000-00-00 00:00:00' WHERE `id`='".$user['id']."'");
		echo '<script> window.location.href="/'.$url.'";</script>';
		exit;
	}
	
	$dat1 = date_create($user['live_time']);
	$dat2 = date_create(date("Y-m-d H:i:s"));
	$int = date_diff($dat1, $dat2);

	$minutes = $int->format('%i');
	$hours = $int->format('%H');
	$day = $int->format('%d');
	$month = $int->format('%m');
	
	$nach = round((($minutes + ($hours*60) + ($day*1440) + ($month*43200)))/20);
	
	$dob = new DateTime($user['live_time']);
	$dob->modify("+20 minutes");
	$izm = $dob->format("Y-m-d H:i:s");
	
	if($nach > 5) { $nach = 5; }
	
	if($user['live'] < 5 and $nach > 0) {
		mysqli_query($mysqli, "UPDATE `users` SET `live_time`='". $izm ."', `live`=`live`+'".$nach."' WHERE `id`='".$user['id']."'");
		echo '<script> window.location.href="/'.$url.'";</script>';
		exit;
	}
	
	
?>
<!doctype html>
<html lang="ru">

<head>
	<?php include('head_new.php'); ?>	
</head>

<body style="background:url('<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/'.$carta['fon']; ?>');">
	
	<div id="loading">
		<img id="loading-image" src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/ajax-loader.gif" alt="Загрузка..." />
	</div>
	
	<div class="podl"></div>
	<div class="podl2"></div>
	
	<div id="bby">
		<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/close.png" onclick="buy_cl();" class="vibr_close">
		<div class="blk">
			<div id="callbacks"></div>
			
			<div class="divbl" <?php if($user['game_live'] == '0000-00-00 00:00:00') { ?>onclick="order(1)"<?php } ?>>
				<div class="txt_buy">Бесконечные игры на <u>1 час</u></div>
				<div class="btn_buy">2 голоса</div>
			</div>
			<div class="divbl" <?php if($user['game_live'] == '0000-00-00 00:00:00') { ?>onclick="order(2)"<?php } ?>>
				<div class="txt_buy">Бесконечные игры на <u>1 день</u></div>
				<div class="btn_buy">8 голосов</div>
			</div>
			<div class="divbl" <?php if($user['game_live'] == '0000-00-00 00:00:00') { ?>onclick="order(3)"<?php } ?>>
				<div class="txt_buy">Бесконечные игры на <u>3 дня</u></div>
				<div class="btn_buy">22 голоса</div>
			</div>
			<div class="divbl" <?php if($user['game_live'] == '0000-00-00 00:00:00') { ?>onclick="order(4)"<?php } ?>>
				<div class="txt_buy">Бесконечные игры на <u>неделю</u></div>
				<div class="btn_buy">48 голосов</div>
			</div>
			
			<div class="razd"></div>
			
			<div class="divbl backj" onclick="order(5)">
				<div class="txt_buy"><u>3</u> подсказки</div>
				<div class="btn_buy">1 голос</div>
			</div>
			<div class="divbl backj" onclick="order(6)">
				<div class="txt_buy"><u>12</u> подсказок</div>
				<div class="btn_buy">4 голоса</div>
			</div>
			<div class="divbl backj" onclick="order(7)">
				<div class="txt_buy"><u>24</u> подсказки</div>
				<div class="btn_buy">8 голосов</div>
			</div>
			<div class="divbl backj" onclick="order(8)">
				<div class="txt_buy"><u>50</u> подсказок</div>
				<div class="btn_buy">15 голосов</div>
			</div>
			
		</div>
	</div>

	<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/'.$carta['img']; ?>" class="monstr">
	
	<div>
	
		<?php if($id == '19321708') { ?>
			<div style="position:absolute; background:#edeef0; top:114px; left:0px; padding:5px; z-index:1;">
				<a href="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/send.php<?php echo $url; ?>&id=1">попытки восстановлены</a><br>
				<a href="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/send.php<?php echo $url; ?>&id=2&pp=1">новое в игре</a>
			</div>
		<?php } ?>
	
		<?php if($carta_prev['num']) { ?>
			<a href="<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/index.php'.$url.'&num='.$carta_prev['num']; ?>">
		<?php } ?>	
			<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php if($num != 1) { echo 'prev.png'; } else { echo 'prev0.png'; } ?>" class="prev">
		<?php if($carta_prev['num']) { ?>
			</a>
		<?php } ?>
			
		<?php if($carta_next['num']) { ?>
			<a href="<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/index.php'.$url.'&num='.$carta_next['num']; ?>">
		<?php } ?>
			<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php if($carta_next['num']) { echo 'next.png'; } else { echo 'next0.png'; } ?>" class="next">
		<?php if($carta_next['num']) { ?>
			</a>
		<?php } ?>
		
		
		<div class="nast_pp" onclick="VK.callMethod('showSettingsBox', 256);">добавить в меню</div>
		<?php
			$frand = rand(1,3);
			if($frand == 1) { $ffim = 'photo-165761369_456239017'; }
			else if($frand == 2) { $ffim = 'photo-165761369_456239018'; }
			else if($frand == 3) { $ffim = 'photo-165761369_456239019'; }
		?>
		<div class="nast_pp nast_pp2" onclick="VK.api('wall.post', {message: 'Умникум - Правда или Ложь, Викторины. Заходите в игру - будем соревноваться! \n https://vk.com/app6457915', attachments: 'photo-165761369_456239560'}, function(data) {});">рассказать друзьям</div>
		
		
		<div class="block_tim" style="background:url('<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/tim.png');">
			<?php if($user['game_live'] == '0000-00-00 00:00:00') { ?>
			
				<?php if($user['live_time'] != '0000-00-00 00:00:00') { ?>
					<?php
						$dat2 = date_create(date('Y-m-d H:i:s', strtotime("+1205 seconds", strtotime($user['live_time']))));
						$int = date_diff(date_create(date('Y-m-d H:i:s')), $dat2);
					?>																		
					<div id="int"><?php echo $int->format('%I:%S'); ?></div>
					<script>
						function int() {
							var time = document.getElementById("int");
							var times = time.innerHTML;
							var arr = times.split(":");
							var m = arr[0];
							var s = arr[1];
							if (s == 0) {
								if (m == 0) {
									window.location.reload();
									return;
								}
								m--;
								if (m < 10) m = "0" + m;
								s = 59;
							}
							else s--;
							if (s < 10) s = "0" + s;
							document.getElementById("int").innerHTML = m+":"+s;																
							setTimeout(int, 1000);
						} int();
					</script>		
				<?php } ?>
			
				<div class="tim_pop">
					попытки: <span><?php echo $user['live'].'/5'; ?></span>
				</div>
			<?php } else { ?>
				<div class="tim_pop2">
					<span>безлимит до</span> <?php echo date_format(date_create($user['game_live']), 'd.m.Y H:i'); ?>
				</div>
			<?php } ?>
		</div>
		
		<div class="block_pod" style="background:url('<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/podsk.png');">
			<div class="pod_t">
				подсказки: <span id="kpod"><?php echo $user['help']; ?></span>
			</div>
			<div onclick="buy();" class="but_buy"></div>
		</div>
		
		
		
		<div style="position:absolute; top:112px; left:172px; width:640px; height:400px;">
			
			<?php for ($i = $num; $i < ($num+15); $i++) { ?>
			
				<?php
					$vikt_col = mysqli_fetch_array(mysqli_query($mysqli, "SELECT count(`id`) as `count` FROM `games` WHERE `carta_lvl`='".$i."'"));
					$prl_col = mysqli_fetch_array(mysqli_query($mysqli, "SELECT count(`id`) as `count` FROM `games2` WHERE `carta_lvl`='".$i."'"));
					$sum_col = $prl_col['count'] + $vikt_col['count'];
					
					$vikt_sum = mysqli_fetch_array(mysqli_query($mysqli, "SELECT sum(`lvl`) as `sum` FROM `games` WHERE `carta_lvl`='".$i."'"));
					$prl_sum = mysqli_fetch_array(mysqli_query($mysqli, "SELECT count(`id`) as `sum` FROM `games2` WHERE `carta_lvl`='".$i."'"));
					$gf_sum = $vikt_sum['sum'] + $prl_sum['sum'];
					
					$gf = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `game_finish` WHERE `id_user`='".$user['id']."' and `carta_lvl`='".$i."' and `end`='1' ORDER by `och` DESC LIMIT 0,1"));
					
					if(!$gf['och'] or $gf['och'] <= 0) {
						$imr1 = 'zvzd_big0';
						$imr2 = 'zvzd_big0';
						$imr3 = 'zvzd_big0';
					} else if($gf['och'] <= ($gf_sum/3)) {
						$imr1 = 'zvzd_big';
						$imr2 = 'zvzd_big0';
						$imr3 = 'zvzd_big0';
					} else if($gf['och'] > ($gf_sum/3) and $gf['och'] <= ($gf_sum/3)*2) {
						$imr1 = 'zvzd_big';
						$imr2 = 'zvzd_big';
						$imr3 = 'zvzd_big0';
					} else if($gf['och'] > ($gf_sum/3)*2) {
						$imr1 = 'zvzd_big';
						$imr2 = 'zvzd_big';
						$imr3 = 'zvzd_big';
					} else {
						$imr1 = 'zvzd_big0';
						$imr2 = 'zvzd_big0';
						$imr3 = 'zvzd_big0';
					}	
	
				?>
				
				<div class="btn_vib" onclick="$('.podl').fadeIn('fast'); $('#vibr_<?php echo $i; ?>').fadeIn('fast');" <?php if($gf['carta_lvl'] == $i) { echo ' style="background:url(/img/game/btn.png);" '; } ?> >
					<div class="vib_div">
						<?php echo $i; ?>
						
						<?php if($gf['carta_lvl'] == $i) { ?>
							<div class="vib_div_ret">
								<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr1; ?>.png">
								<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr2; ?>.png">
								<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr3; ?>.png">
							</div>
						<?php } ?>
						
					</div>
				</div>
				<div id="vibr_<?php echo $i; ?>" class="vibr">
					<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/close.png" onclick="$('.podl').fadeOut('fast'); $('#vibr_<?php echo $i; ?>').fadeOut('fast');" class="vibr_close">
					
					<div class="vibr_lvl">Уровень <?php echo $i; ?></div>
					
					<div class="vibr_vop">
						<b><?php echo $prl_col['count']; ?></b> вопросов - <b>правда или ложь</b><br>
						<b><?php echo $vikt_col['count']; ?></b> вопросов - <b>викторины</b>
					</div>
					
					<div class="vibr_rt">
						<div class="vibr_rtz">
							<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr1; ?>.png">
							<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr2; ?>.png">
							<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr3; ?>.png">
						</div>
						<div class="vibr_rtu">ваш рейтинг: <span><?php echo 0+$gf['och'];?></span> из <?php echo $gf_sum;?></div>
						
						<?php if($i <= ($end_lvl['carta_lvl']+1)) { ?>
						
							<?php if($gf_sum > 0) { ?>
								<?php if($user['live'] <= 0 and $user['game_live'] == '0000-00-00 00:00:00') { ?>
								<div class="vibr_rtg" style="font-size:18px;" onclick="buy();">кончились попытки</div>
								<?php } else { ?>
									<div class="vibr_rtg" onclick="window.location.href = '<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/game_new.php'.$url.'&num='.$num; ?>&lvln=<?php echo $i; ?>&lnum=0';">начать игру</div>
								<?php } ?>
							<?php } ?>
							
						<?php } else { ?>
						
							<div class="pred_lvl">Предыдущий уровень не пройден</div>
							
						<?php } ?>
					</div>
					
					<div class="vibr_rtj">
						<table>
							<?php
								$ir = 1;
								$s_ret = mysqli_query($mysqli, "SELECT MAX(`och`) as `och`, `id_user`, `carta_lvl` FROM `game_finish` WHERE `end`='1' and `carta_lvl`='".$i."' GROUP BY `id_user` ORDER BY `och` DESC LIMIT 0,3");
								while($rr = mysqli_fetch_assoc($s_ret)) {
									$rr_user = mysqli_fetch_array(mysqli_query($mysqli, "SELECT `id`, `name` FROM `users` WHERE `id`='".$rr['id_user']."'"));
									echo '<tr><td><b>'. $ir++ .'.</b> '. $rr_user['name'].'</td><td><b>'.$rr['och'].'</b></td></tr>';
								}
							?>
						</table>
					</div>
					
				</div>				
				
			<?php } ?>
			
		</div>
		
		
		
		
		<div class="block_ret" style="background:url('<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/podl.png');">
			<div class="ret_div">
				<div class="text_center"><?php echo '<u>'.$user['name'].'</u> - ID '.$user['id_vk']; ?><br>&nbsp;</div>
				<table style="width:100%;">
					<tr><td>Рейтинг:<br>&nbsp;</td><td><b><?php echo $user['ret']; ?><br>&nbsp;</b></td></tr>
					<tr><td>Правильных ответов: &nbsp;</td><td><b><?php echo $user['prav']; ?></b></td></tr>
					<tr><td>Неверных ответов: &nbsp;</td><td><b><?php echo $user['neprav']; ?></b></td></tr>
					<tr><td>Использовано подсказок: &nbsp;</td><td><b><?php echo $user['help_is']; ?></b></td></tr>
				</table>
			</div>
		</div>
		
		<div class="menu menu1">
			<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/dost.png">
			<div class="mt10_m">Достижения</div>
		</div>
		<div class="menu menu2" onclick="window.location.href = '<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/friends.php'.$url; ?>';">
			<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/frnd.png">
			<div class="mt10_m">Друзья</div>
		</div>
		<div class="menu menu3"  onclick="window.location.href = '<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url; ?>';">
			<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/reit.png">
			<div class="mt10_m">Рейтинг</div>
		</div>
		
	</div>

</body>
</html>