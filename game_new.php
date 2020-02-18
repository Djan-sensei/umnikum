<?php
	include('mysql.php');	
	$user = mysqli_fetch_array(mysqli_query($mysqli, "select * from `users` where `id_vk` = '".$id."'"));
	if($_GET['num']) { $num = (int)$_GET['num']; }
	else { $num = 1; }
	$carta = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `carta` WHERE `num`='".$num."'"));
	$sql_vikt = mysqli_query($mysqli, "SELECT * FROM `games` WHERE `carta_lvl`='".(int)$_GET['lvln']."'");
	$sql_prl = mysqli_query($mysqli, "SELECT * FROM `games2` WHERE `carta_lvl`='".(int)$_GET['lvln']."'");
	if((int)$_GET['lnum'] == 0) {
		mysqli_query($mysqli, "UPDATE `users` SET `live`=`live`-1 WHERE `id`='".$user['id']."'");
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
	
	<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/'.$carta['img']; ?>" class="monstr">
	
	<div class="podffg">
		<?php
		
			$mass = array();
		
			while($vikt = mysqli_fetch_assoc($sql_vikt)) {
				$mass[] = $vikt['id'].'_v';					
			}
			while($prl = mysqli_fetch_assoc($sql_prl)) {
				$mass[] = $prl['id'].'_p';
			}
			
			rsort($mass);
			
			$lnum = $mass[ (int)$_GET['lnum'] ];
			
			$ob = explode('_', $lnum);
		
			// если викторина
			if($ob[1] == 'v') {
				include('ob_game.php');
			}
			
			// если правда/ложь
			if($ob[1] == 'p') {
				include('ob_game2.php');
			}
		
			if(empty($ob[1])) {
				mysqli_fetch_array(mysqli_query($mysqli, "UPDATE `game_finish` SET `end`='1' WHERE `id_user`='".$user['id']."' and `carta_lvl`='".(int)$_GET['lvln']."'"));
				?>
					<div class="gam2">
						<div class="gam_end">
							<div class="gam_endf">
								<?php
									$gfi = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `game_finish` WHERE `id_user`='".$user['id']."' and `carta_lvl`='".(int)$_GET['lvln']."' ORDER by `id` DESC LIMIT 0,1 "));
									
									$vikt_sum = mysqli_fetch_array(mysqli_query($mysqli, "SELECT sum(`lvl`) as `sum` FROM `games` WHERE `carta_lvl`='".(int)$_GET['lvln']."'"));
									$prl_sum = mysqli_fetch_array(mysqli_query($mysqli, "SELECT count(`id`) as `sum` FROM `games2` WHERE `carta_lvl`='".(int)$_GET['lvln']."'"));
									$gf_sum = $vikt_sum['sum'] + $prl_sum['sum'];
								?>
								
								<div class="gam_endtxt">УРОВЕНЬ ПРОЙДЕН</div>
								
								<div class="gam_endbal">Вы набрали <b><?php echo $gfi['och']; ?></b> баллов</div>
								
								<?php
									if(!$gfi['och'] or $gfi['och'] <= 0) {
										$imr1 = 'zvzd_big0';
										$imr2 = 'zvzd_big0';
										$imr3 = 'zvzd_big0';
									} else if($gfi['och'] <= ($gf_sum/3)) {
										$imr1 = 'zvzd_big';
										$imr2 = 'zvzd_big0';
										$imr3 = 'zvzd_big0';
									} else if($gfi['och'] > ($gf_sum/3) and $gfi['och'] <= ($gf_sum/3)*2) {
										$imr1 = 'zvzd_big';
										$imr2 = 'zvzd_big';
										$imr3 = 'zvzd_big0';
									} else if($gfi['och'] > ($gf_sum/3)*2) {
										$imr1 = 'zvzd_big';
										$imr2 = 'zvzd_big';
										$imr3 = 'zvzd_big';
									}
								?>
								<div class="gam_endret">
									<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr1; ?>.png">
									<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr2; ?>.png">
									<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/<?php echo $imr3; ?>.png">
								</div>
								
								<div class="btn_finish" onclick="window.location.href = '<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/index.php'.$url; ?>';">завершить</div>
								
							</div>
						</div>
					</div>
				<?php
			}
		?>
	</div>

</body>
</html>