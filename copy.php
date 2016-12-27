#!/usr/bin/php

<?
/* предопределенные настройки */
$bank_dir = "/mnt/share/UNITY/Mail/Down/";             // папка с банковскими файлами 
$mail_dir = "/var/flexshare/shares/postudksu/";        // каталог, куда выкладываются файлы для отправки

/* подключение к базе данных */
$mysqli = new mysqli('10.21.1.5', 'root', 'P@ssw0rd', 'trs_scripts');
if (mysqli_connect_error())
{
    die('Ошибка подключения (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}
else
$query = "SELECT * FROM ndi_udksu";
$result = $mysqli->query($query);
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
// $bank_dir.$row['udksu_kod']."/OUT/";
chdir ($bank_dir.$row['udksu_kod']."/OUT/");
$handle=opendir($bank_dir.$row['udksu_kod']."/OUT/");
while (false !== ($fl = readdir($handle)))
{
if ($fl != "." && $fl != "..")
{
echo $fl." ";
copy($fl, $mail_dir."04".$row['udksu_email']."/".$fl); 
}
}
closedir($handle);
}
$result->close();
$mysqli->close();
?>