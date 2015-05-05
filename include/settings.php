<?php
$dir = dirname(__FILE__)."/../";
include_once $dir."config.php";
include_once $dir."class/System.class.php";
include_once $dir."class/Database.class.php";
if ( ! Sys::checkAuth())
    die(header('Location: ../'));

function generateList($type, $sendUpdateService)
{
    echo '<label class="label-name">Сервис уведомлений</label>';
    echo "<select onchange=\"changeDiv('{$type}')\" id=\"{$type}\" name=\"send{$type}Service\">";
    $services = Database::getServiceList($type);
    for ($i=0; $i<count($services); $i++)
    {
        echo '<option value="'.$services[$i]['id'].'"';
        if ($sendUpdateService == $services[$i]['id'])
            echo ' selected';
        echo '>'.$services[$i]['service'].'</option>';
    }
    echo '
    </select>
    <br />';
    for ($i=0; $i<count($services); $i++)
    {
        echo '<div id="'.$services[$i]['service'].'_'.$type.'_label" class="result">';
        echo '<input type="hidden" name="id" value="'.$services[$i]['id'].'">';
        echo '<p>
            <label class="label-name">Адрес</label>
            <input type="text" name="send'.$type.'Address'.$services[$i]['id'].'" value="'.$services[$i]['address'].'">
        </p>
        </div>';
    }
    echo '<span class="subinput-text">Например: korp@bk.ru</span>';
}

$settings = Database::getAllSetting();
foreach ($settings as $row)
{
	extract($row);
}
?>
<script type="text/javascript">
    changeDiv('notification');
    changeDiv('warning');
</script>
<h2 class="settings-title">Настройки монитора</h2>

<form id="setting">
    <p>
        <label class="label-name">Адрес TorrentMonitor</label>
        <input type="text" name="serverAddress" value="<?php echo $serverAddress ?>">
        <span class="subinput-text">Например: http://torrent.test.ru/</span>
    </p>
    <p>
        <label class="label-name"></label>
        <label><input type="checkbox" name="send" <?php if ($send) echo "checked" ?> onclick="expand('sendNotification')"> Отправлять уведомления</label>
    </p>
    <div id="sendNotification" <?php if ( ! $send) echo 'class="result"' ?>>
        <p>
            <label class="label-name"></label>
            <label><input type="checkbox" name="sendUpdate" <?php if ($sendUpdate) echo "checked" ?> onclick="expand('sendUpdate')"> Отправлять уведомления об обновлениях</label>
        </p>
        <div id="sendUpdate" <?php if ( ! $sendUpdate) echo 'class="result"' ?>>
            <?php generateList('notification', $sendUpdateService) ?>
        </div>
        <p>
            <label class="label-name"></label>
            <label><input type="checkbox" name="sendWarning" <?php if ($sendWarning) echo "checked" ?> onclick="expand('sendWarning')"> Отправлять уведомления об ошибках</label>
        </p>
        <div id="sendWarning" <?php if ( ! $sendWarning) echo 'class="result"' ?>>
            <?php generateList('warning', $sendWarningService) ?>
        </div>
    </div>
    <p>
        <label class="label-name"></label>
        <label><input type="checkbox" name="auth" <?php if ($auth) echo "checked" ?>> Включить авторизацию</label>
    </p>
    <p>
        <label class="label-name"></label>
        <label><input type="checkbox" id="proxy" name="proxy" <?php if ($proxy) echo "checked" ?> onclick="expand('proxySettings')"> Использовать прокси</label>
    </p>
    <div id="proxySettings" <?php if ( ! $proxy) echo 'class="result"' ?>>
        <p>
            <label class="label-name">Тип proxy</label>
            <label>
                <select id="proxyType" name="proxyType">
                    <option value="HTTP" <?php if ($proxyType == 'HTTP') echo 'selected';?>>HTTP</option>
                    <option value="SOCKS5" <?php if ($proxyType == 'SOCKS5') echo 'selected';?>>SOCKS5</option>
                </select>
            </label>
            <br />
            <label class="label-name">IP, порт прокси-сервера</label>
            <input type="text" name="proxyAddress" value="<?php echo $proxyAddress ?>">
            <span class="subinput-text">Например: 127.0.0.1:9050</span>
        </p>
    </div>
    <p>
        <label class="label-name"></label>
        <label><input type="checkbox" id="torrent" name="torrent" <?php if ($useTorrent) echo "checked" ?> onclick="expand('torrentSettings')"> Управлять торрент-клиентом</label>
    </p>
    <div id="torrentSettings" <?php if ( ! $useTorrent) echo 'class="result"' ?>>
        <p>
            <label class="label-name">Торрент-клиент</label>
            <label>
                <select id="torrentClient" name="torrentClient">
                    <option value="Deluge" <?php if ($torrentClient == 'Deluge') echo 'selected';?>>Deluge</option>
                    <option value="Transmission" <?php if ($torrentClient == 'Transmission') echo 'selected';?>>Transmission</option>
                </select>
            </label>
        </p>
        <p>
            <label class="label-name">IP, порт торрент-клиента</label>
            <input type="text" name="torrentAddress" value="<?php echo $torrentAddress ?>">
            <span class="subinput-text">Например: 127.0.0.1:58846</span>
        </p>
        <p>
            <label class="label-name">Логин</label>
            <input type="text" name="torrentLogin" value="<?php echo $torrentLogin ?>">
            <span class="subinput-text">Например: KorP</span>
        </p>
        <p>
            <label class="label-name">Пароль</label>
            <input type="password" name="torrentPassword" value="<?php echo $torrentPassword ?>">
            <span class="subinput-text">Например: Pa$$w0rd</span>
        </p>
        <p>
            <label class="label-name">Директория для скачивания</label>
            <input type="text" name="pathToDownload" value="<?php echo $pathToDownload ?>">
            <span class="subinput-text">Например: /var/lib/transmission/downloads</span>
        </p>
        <p>
            <label class="label-name"></label>
            <label><input type="checkbox" name="deleteDistribution" <?php if ($deleteDistribution) echo "checked" ?>> Удалять раздачи из torrent-клиента</label>
        </p>
        <p>
            <label class="label-name"></label>
            <label><input type="checkbox" name="deleteOldFiles" <?php if ($deleteOldFiles) echo "checked" ?>> Удалять файлы старых раздач</label>
            <span class="subinput-text">Только для lostfilm.tv, novafilm.tv, baibako.tv и newstudio.tv</span>
        </p>
    </div>
    <p>
        <label class="label-name"></label>
        <label><input type="checkbox" name="rss" <?php if ($rss) echo "checked" ?>> RSS лента</label>
    </p>
    <p>
        <label class="label-name"></label>
        <label><input type="checkbox" name="debug" <?php if ($debug) echo "checked" ?>> Режим отладки</label>
    </p>
    <button class="form-button">Сохранить</button>
</form>
<br/>
<br/>
<h2 class="settings-title">Смена пароля</h2>
<form id="change_pass">
    <p>
        <label class="label-name">Новый пароль</label>
        <input type="password" name="password">
    </p>
    <p>
        <label class="label-name">Еще раз</label>
        <input type="password" name="password2">
    </p>
    <button class="form-button">Сменить</button>
</form>
<script src="js/user-func.js"></script>