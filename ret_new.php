<?php
	include('mysql.php');
	$user = mysqli_fetch_array(mysqli_query($mysqli, "select * from `users` where `id_vk` = '".$id."'"));
	if($_GET['num']) { $num = (int)$_GET['num']; }
	else { $num = 1; }
	$carta = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM `carta` WHERE `num`='".$num."'"));
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
	<div>
		<div class="ret_gl">
			<div class="ret_tab">
				<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/img/game/close.png" onclick="window.location.href = '<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/index.php'.$url; ?>';" class="vibr_close">
				<table class="spt">
					<tr>
						<th>Игрок</th>
						<th>Рейтинг</th>
						<th>Правильных ответов</th>
						<th>Неправильных ответов</th>
					</tr>
					<?php
						$row_num = mysqli_num_rows(mysqli_query($mysqli, "SELECT * FROM `users`"));
						
						$num = 9;
						$page = $_GET['page'];
						$posts = $row_num;
						$total = intval(($posts - 1) / $num) + 1;
						$page = intval($page);
						if(empty($page) or $page < 0) $page = 1;  
						if($page > $total) $page = $total;
						$start = $page * $num - $num;
						
						$sql_retall = mysqli_query($mysqli, "SELECT `id_vk`, `name`, `ret`, `prav`, `neprav` FROM `users` ORDER by `ret` DESC LIMIT $start, $num");
						while($retall = mysqli_fetch_assoc($sql_retall)) {
							?>
								<tr>
									<td <?php if($user['id_vk'] == $retall['id_vk']) { echo ' style="background:#f4a222;" '; } ?> ><a href="http://vk.com/id<?php echo $retall['id_vk'];?>" class="hrefbl" target="_blank"><?php echo $retall['name']; ?></a></td>
									<td <?php if($user['id_vk'] == $retall['id_vk']) { echo ' style="background:#f4a222;" '; } ?> ><?php echo $retall['ret']; ?></td>
									<td <?php if($user['id_vk'] == $retall['id_vk']) { echo ' style="background:#f4a222;" '; } ?> ><?php echo $retall['prav']; ?></td>
									<td <?php if($user['id_vk'] == $retall['id_vk']) { echo ' style="background:#f4a222;" '; } ?> ><?php echo $retall['neprav']; ?></td>
								</tr>
							<?php
						}
					?>
				</table>
				<ul class="pagination">
					<?php
						if($page != 1) {
							$pervpage = '
								<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page=1"> << </a></li>
								<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page='.($page-1).'"> < </a></li>
							';
						}
						if($page != $total) {
							$nextpage = '
								<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page='.($page+1).'"> > </i></a></li>
								<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page='.$total.'"> >> </a></li>
							';
						}
						if($page - 2 > 0) {
							$page2left = '<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page='.($page-2).'">'.($page-2) .'</a></li>';
						}
						if($page - 1 > 0) {
							$page1left = '<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page='.($page-1).'">'.($page-1).'</a></li>'; 
						}
						if($page + 2 <= $total) {
							$page2right = '<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page='.($page+2).'">'.($page+2).'</a></li>';
						}
						if($page + 1 <= $total) {
							$page1right = '<li><a href="https://'.$_SERVER['HTTP_HOST'].'/ret_new.php'.$url.'&page='.($page+1).'">'.($page+1).'</a></li>';
						}
						echo $pervpage.$page2left.$page1left.'<li class="active"><a>'.$page.'</a></li>'.$page1right.$page2right.$nextpage;
					?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>