<?
header("Content-Type: text/html; charset=utf-8");
echo "<table border=1><tr>";
$mysqli = new mysqli('10.21.1.5', 'root', 'P@ssw0rd', 'trs_scripts');
$mysqli->set_charset("utf8");
if (mysqli_connect_error())
{
    die('Ошибка подключения (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}
$query = "SELECT mail_script_log.archive_name, mail_script_log.date_time, mail_script_log.files_names, ndi_udksu.udksu_name FROM mail_script_log,ndi_udksu WHERE mail_script_log.udksu_id=ndi_udksu.udksu_id;";
$result = $mysqli->query($query);
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
$fl_in_arch = explode(",", $row['files_names']);

echo "<td>".$row['archive_name']."</td><td>".$row['date_time']."</td><td>";
for ($i=0; $i<count($fl_in_arch); $i++)
{
echo $fl_in_arch[$i]."<br>";
}
echo "</td>";
echo "<td>".$row['udksu_name']."</td>";
echo "</tr>";
}

$result->close();
$mysqli->close();
echo "</tr></table>";
?>