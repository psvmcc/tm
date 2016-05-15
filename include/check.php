<?php 
$dir = dirname(__FILE__)."/../";
include_once $dir."class/System.class.php";
include_once $dir."class/Database.class.php";

if ( ! Sys::checkAuth())
    die(header('Location: ../'));
    
?>
<table class="test">
    <thead>
        <tr>
            <th>Основные настройки</th>
        </tr>
    </thead>
    <tbody>
<?php
    
if (Sys::checkInternet())
{  
?>
		<tr>
			<td>Подключение к интернету установлено.</td>
		</tr>
	<?php
	if (Sys::checkConfigExist())
	{
	?>
		<tr>
			<td>Конфигурационный файл существует.</td>
		</tr>
		<?php
		if (Sys::checkCurl())
		{
		?>
		<tr>
			<td>Расширение cURL установлено.</td>
		</tr>
			<?php
			$torrentPath = str_replace('class/../', '', $dir).'torrents/';
			if (Sys::checkWriteToPath($torrentPath))
			{
			?>
		<tr>
			<td>Запись в директорию для torrent-файлов <?php echo $torrentPath?> разрешена.</td>
		</tr>
			<?php
			}
			else
			{
			?>
		<tr>
			<td class="test-error">Запись в директорию для torrent-файлов <?php echo $torrentPath?> запрещена.</td>
		</tr>			
			<?php	
			}
			$dir = str_replace('include', '', dirname(__FILE__));
			if (Sys::checkWriteToPath($dir))
			{
			?>
		<tr>
			<td>Запись в системную директорию <?php echo $dir?> разрешена.</td>
		</tr>
			<?php	
			}
			else
			{
			?>
		<tr>
			<td class="test-error">Запись в системную директорию <?php echo $dir?> запрещена.</td>
		</tr>
			<?php	
			}
			
			$trackers = Database::getTrackersList();
			foreach ($trackers as $tracker)
			{
				if (file_exists($dir.'trackers/'.$tracker.'.engine.php'))
				{
				?>
		<tr>
			<td>Основной файл для работы с трекером <?php echo $tracker?> найден.</td>
		</tr>
				<?php	
				}
				else
				{
				?>
		<tr>
			<td class="test-error">Основной файл для работы с трекером <?php echo $tracker?> не найден.</td>
		</tr>
				<?php	
				}
				if ($tracker == 'nnm-club.me' || $tracker == 'pornolab.net' || $tracker == 'rutracker.org' || $tracker == 'tapochek.net' || $tracker == 'tfile.co')
				{
					if (file_exists($dir.'trackers/'.$tracker.'.search.php'))
					{
					?>
		<tr>
			<td>Дополнительный файл для работы с трекером <?php echo $tracker?> найден.</td>
		</tr>
					<?php	
					}
					else
					{
					?>
		<tr>
			<td class="test-error">Дополнительный файл для работы с трекером <?php echo $tracker?> не найден.</td>
		</tr>
					<?php	
					}
				}
				
				if ($tracker == 'lostfilm-mirror' || $tracker == 'rutor.org' || $tracker == 'tfile.co')
				{
				?>
		<tr>
			<td>Учётные данные для работы с трекером <?php echo $tracker?> не требуются.</td>
		</tr>
				<?php
				}
				elseif (Database::checkTrackersCredentialsExist($tracker))
				{
                ?>
		<tr>
			<td>Учётные данные для работы с трекером <?php echo $tracker?> найдены.</td>
		</tr>                
                <?php
				}
				else
				{
				?>
		<tr>
			<td class="test-error">Учётные данные для работы с трекером <?php echo $tracker?> не найдены.</td>
		</tr>
				<?php	
				}
				if ($tracker == 'lostfilm.tv')
					$page = 'https://www.lostfilm.tv/';
				elseif ($tracker == 'rutracker.org')
				    $page = 'http://rutracker.org/forum/index.php';
				elseif ($tracker == 'rutor.org')
				    $page = 'http://rutor.info/';
				elseif ($tracker == 'lostfilm-mirror')
				    $page = 'http://korphome.ru/lostfilm.tv/rss.xml';
				elseif ($tracker == 'nnm-club.me')
				    $page = 'http://nnmclub.to/forum/index.php';
				else
					$page = 'http://'.$tracker;
				if (Sys::checkavAilability($page))
				{
				?>
		<tr>
			<td>Трекер <?php echo $tracker?> доступен.</td>
		</tr>
				<?php	
				}
				else
				{
				?>
		<tr>
			<td class="test-error">Трекер <?php echo $tracker?> не доступен.</td>
		</tr>
				<?php	
				}
			}
		}
		else
		{
		?>
		<tr>
			<td class="test-error">Для работы системы необходимо включить <a href="http://php.net/manual/en/book.curl.php">расширение cURL</a>.</td>
		</tr>
		<?php	
		}
	}
	else
	{
	?>
		<tr>
			<td class="test-error">Для корректной работы необходимо внести изменения в конфигурационный файл.</td>
		</tr>
	<?php 
	}	
}
else
{
?>
		<tr>
			<td class="test-error">Отсутствует подключение к интернету.</td>
		</tr>
<?php
}    
?>
	</tbody>
</table>
<div class="clear-both"></div>