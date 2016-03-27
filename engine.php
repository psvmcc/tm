<?php
////////////////////////////////////
///////////TorrentMonitor///////////
////////////////////////////////////
$dir = dirname(__FILE__).'/';
include_once $dir.'config.php';
include_once $dir.'class/System.class.php';
include_once $dir.'class/Database.class.php';
include_once $dir.'class/Errors.class.php';
include_once $dir.'class/Notification.class.php';

header('Content-Type: text/html; charset=utf-8');

$debug = Database::getSetting('debug');
$time_start_full = microtime(true);
if (Sys::checkCurl())
{
	$torrentsList = Database::getTorrentsList('name');
	$count = count($torrentsList);
	echo 'Опрос новых раздач на трекерах:'."\r\n".'<br />';
    $time_start_overall = microtime(true);
	for ($i=0; $i<$count; $i++)
	{
		$tracker = $torrentsList[$i]['tracker'];
		if (Database::checkTrackersCredentialsExist($tracker))
		{
			$engineFile = $dir.'trackers/'.$tracker.'.engine.php';
			if (file_exists($engineFile))
			{
				Database::clearWarnings('system');
				
				$functionEngine = include_once $engineFile;
				$class = explode('.', $tracker);
				$class = $class[0];
				$functionClass = str_replace('-', '', $class);
				
				if ($tracker == 'tracker.0day.kiev.ua')
				    $functionClass = 'kiev';
				    
                if ($tracker == 'tv.mekc.info')
				    $functionClass = 'mekc';

				echo $torrentsList[$i]['name'].' на трекере '.$tracker."\r\n".'<br />';
				if ($tracker == 'baibako.tv' || $tracker == 'hamsterstudio.org' || $tracker == 'lostfilm.tv' || $tracker == 'lostfilm-mirror' || $tracker == 'newstudio.tv' || $tracker == 'novafilm.tv')
				{
				    $time_start = microtime(true);
				    call_user_func($functionClass.'::main', $torrentsList[$i]['id'], $tracker, $torrentsList[$i]['name'], $torrentsList[$i]['hd'], $torrentsList[$i]['ep'], $torrentsList[$i]['timestamp'], $torrentsList[$i]['hash']);
				    $time_end = microtime(true);
				    $time = $time_end - $time_start;
				    if ($debug)
				        echo 'Время выполнения: '.$time."\r\n".'<br />';
				}
				if ($tracker == 'anidub.com' || $tracker == 'animelayer.ru' || $tracker == 'casstudio.tv' || $tracker == 'kinozal.tv' || $tracker == 'nnmclub.to' || $tracker == 'pornolab.net' || $tracker == 'rustorka.com' || $tracker == 'rutor.org' ||    $tracker == 'rutracker.org' || $tracker == 'tfile.me' || $tracker == 'tracker.0day.kiev.ua' || $tracker == 'tv.mekc.info')
				{
				    $time_start = microtime(true);
					call_user_func($functionClass.'::main', $torrentsList[$i]['id'], $tracker, $torrentsList[$i]['name'], $torrentsList[$i]['torrent_id'], $torrentsList[$i]['timestamp'], $torrentsList[$i]['hash'], $torrentsList[$i]['auto_update']);
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					if ($debug)
				        echo 'Время выполнения: '.$time."\r\n".'<br />';
				}
				$functionClass = NULL;
				$functionEngine = NULL;
			}
			else
				Errors::setWarnings('system', 'missing_files');				
		}
		else
			Errors::setWarnings('system', 'credential_miss');
	}
    $time_end_overall = microtime(true);
    $time = $time_end_overall - $time_start_overall;
    if ($debug)
        echo 'Общее время опроса трекеров: '.$time."\r\n".'<br />';
			
	$usersList = Database::getUserToWatch();
	$count = count($usersList);
    echo 'Опрос новых раздач пользователей на трекерах:'."\r\n".'<br />';
	$time_start_overall = microtime(true);
	for ($i=0; $i<$count; $i++)
	{
		$tracker = $usersList[$i]['tracker'];
		if (Database::checkTrackersCredentialsExist($tracker))
		{
			$serchFile = $dir.'trackers/'.$tracker.'.search.php';
			if (file_exists($serchFile))
			{
				Database::clearWarnings('system');

				$functionEngine = include_once $serchFile;
				$class = explode('.', $tracker);
				$class = $class[0];
				$class = str_replace('-', '', $class);
				$functionClass = $class.'Search';
                echo 'Пользователь '.$usersList[$i]['name'].' на трекере '.$tracker."\r\n".'<br />';
                $time_start = microtime(true);
				call_user_func($functionClass .'::mainSearch', $usersList[$i]['id'], $tracker, $usersList[$i]['name']);
				$time_end = microtime(true);
				$time = $time_end - $time_start;
				if ($debug)
				    echo 'Время выполнения: '.$time."\r\n".'<br />';

				$functionClass = NULL;
				$functionEngine = NULL;
			}
			else
				Errors::setWarnings('system', 'missing_files');
		}
		else
			Errors::setWarnings('system', 'credential_miss');
	}
    $time_end_overall = microtime(true);
    $time = $time_end_overall - $time_start_overall;
    if ($debug)
        echo 'Общее время опроса пользователей на трекерах: '.$time."\r\n".'<br />';		
	echo '=================='."\r\n".'<br />';
	echo 'Выполение служебных функций:'."\r\n".'<br />';
	echo 'Добавляем темы из Temp.'."\r\n".'<br />';
	$time_start = microtime(true);
	$tempList = Database::getAllFromTemp();
	if (count($tempList) > 0)
	    Sys::AddFromTemp($tempList);
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	if ($debug)
	    echo 'Время выполнения: '.$time."\r\n".'<br />';
	echo 'Обновление новостей.'."\r\n".'<br />';
	$time_start = microtime(true);
	Sys::getNews();
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	if ($debug)
        echo 'Время выполнения: '.$time."\r\n".'<br />';
	echo 'Запись времени последнего запуска ТМ.'."\r\n".'<br />';
	Sys::lastStart();
}	
else
	Errors::setWarnings('system', 'curl');
	
$time_end_full = microtime(true);
$time = $time_end_full - $time_start_full;
if ($debug)
    echo 'Общее время работы скрипта: '.$time."\r\n".'<br />';
?>