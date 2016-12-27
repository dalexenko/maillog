<?
$mysqli = new mysqli('10.21.1.5', 'root', 'P@ssw0rd', 'trs_scripts');

if (mysqli_connect_error())
{
    die('Ошибка подключения (' . mysqli_connect_errno() . ') '.mysqli_connect_error());
}
$query = "SELECT udksu_kod FROM ndi_udksu";
$result = $mysqli->query($query);

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
echo "mail/".$row['udksu_kod']."<br>";
mkdir ("mail/".$row['udksu_kod'], 0777, true);
}
$result->free();
$mysqli->close();
?>