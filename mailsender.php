<?
/* предопределенные настройки */
$mail_dir = "/var/flexshare/shares/postudksu/";           // каталог, куда выкладываются файлы для отправки
$from = "d_alexenko@dp.trs";                              // адрес отправителя
// $to = "d_alexenko@dp.trs";                             // адрес получателя, по умолчанию переопределяется динамически
// $subj = '=?UTF-8?B?'.base64_encode('тест').'?=';       // тема письма, по умолчанию переопределяется динамически
$text = "Это я тестирую скрипт :-)";                      // текст письма

/* функция отправки почты */
function XMail( $from, $to, $subj, $text, $filename) 
{
    $f         = fopen($filename,"rb");
    $un        = strtoupper(uniqid(time()));
    $head      = "From: $from\n";
	$head     .= "To: $to\n";
    $head     .= "Subject: $subj\n";
    $head     .= "X-Mailer: PHPMail Tool\n";
    $head     .= "Reply-To: $from\n";
    $head     .= "Mime-Version: 1.0\n";
    $head     .= "Content-Type:multipart/mixed;";
    $head     .= "boundary=\"----------".$un."\"\n\n";
    $zag       = "------------".$un."\nContent-Type:text/html; charset=utf-8\n";
    $zag      .= "Content-Transfer-Encoding: 8bit\n\n$text\n\n";
    $zag      .= "------------".$un."\n";
    $zag      .= "Content-Type: application/octet-stream;";
    $zag      .= "name=\"".basename($filename)."\"\n";
    $zag      .= "Content-Transfer-Encoding:base64\n";
    $zag      .= "Content-Disposition:attachment;";
    $zag      .= "filename=\"".basename($filename)."\"\n\n";
    $zag      .= chunk_split(base64_encode(fread($f,filesize($filename))))."\n";

    return @mail("$to", "$subj", $zag, $head);
}
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
chdir ($mail_dir."04".$row['udksu_email']);
$to = "office.".$row['udksu_kod_name']."@dp.trs";
$subj = '=?UTF-8?B?'.base64_encode($row['udksu_kod']).'?='; // в теме письма позывной района
$archive_name = date("mdHi").".zip";
$handle=opendir($mail_dir."04".$row['udksu_email']);
$files_in_archive = "";

while (false !== ($fl = readdir($handle)))
{
if ($fl != "." && $fl != "..")
{
  $files_in_archive .= " ".$fl.",";
  $zip = new ZipArchive(); //Создаём объект для работы с ZIP-архивами
  $zip->open($mail_dir."04".$row['udksu_email']."/".$archive_name, ZIPARCHIVE::CREATE); //Открываем (создаём) архив
  $zip->addFile($fl); //Добавляем в архив файл
  $zip->renameName($fl, iconv('UTF-8', 'CP866', $fl));
  $zip->close(); //Завершаем работу с архивом
  unlink($fl);
}
}
closedir($handle);
if (file_exists($archive_name))
{
$today = date("Y-m-d H:i:s");
$files_in_archive = substr($files_in_archive, 0, strlen($files_in_archive)-1);
$query_insert = "insert into mail_script_log (udksu_id, date_time, archive_name, files_names) values ('".$row['udksu_id']."', '".$today."', '".$archive_name."', '".$files_in_archive."')";
$mysqli->set_charset("utf8");
$result_insert = $mysqli->query($query_insert);
if (XMail($from, $to, $subj, $text, $archive_name))
{
echo 'Mail Отправлен';
unlink($archive_name);
}
else
{
echo 'Произошла ошибка';
}

}
}
$result->close();
$mysqli->close();
?>