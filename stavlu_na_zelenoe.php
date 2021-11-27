<?php
//если файл данных с десяткой лидеров не создан
if (!file_exists('liders.dat'))
{
$fp = fopen('liders.dat','w'); //создаем файл
/* записываем начальную десятку лидеров с результатом 0% */
for($i=0;$i<10;$i++) 
fwrite($fp,'NaMe'.($i+1).'|'.'0'.'|'.'20.03.06'.chr(13).chr(10));
fclose($fp);
}
//генерируем случайный цвет (красный, зеленый или синий)
$num_color = mt_rand (0,2);
if ($num_color == 0) $color = 'red';
if ($num_color == 1) $color = 'green';
if ($num_color == 2) $color = 'blue';
setcookie('color', $color, time()+3600, "");
/* если куки kolvo не создано или там записаны неверные данные, то обнуляем значение */
if(!isset($_COOKIE['kolvo']) || $_COOKIE['kolvo']>10)
setcookie('kolvo', 0, time()+3600, "");
//иначе увеличиваем его значение на 1
else setcookie('kolvo', $_COOKIE['kolvo']+1, time()+3600, "");
//если не создано куки verno, то создаем — со значением 0
if(!isset($_COOKIE['verno']))
setcookie('verno', 0, time()+3600, "");
//если игрок дал вариант ответа
if (isset($_POST['u_color']) && ($_COOKIE['kolvo']<10))
//если цвет угадан
if ($_POST['u_color'] === $color)
//пополняем значение verno, в котором содержится количество угадываний
setcookie('verno', $_COOKIE['verno']+1, time()+3600, "");
//если игра окончена
if (($_COOKIE['kolvo'] >= 10) && isset($_POST['imya']))
{
$procent = (($_COOKIE['verno'] * 100) / 10); //вычисляем процент верных ответов
$liders = file('liders.dat'); //подгружаем файл с десяткой лидеров
//определяем кол-во строк в файле
if (count($liders)<10) $N = count($liders);
else $N = 10;
/* выделяем из каждой строки имя игрока, процент угадывания и дату записи */
for($i=0;$i<$N;$i++)
if (isset($liders[$i]))
list($name[$i],$proc[$i],$data[$i]) = explode('|',trim($liders[$i]));
for($i=0;$i<($N — 1);$i++)
// проверяем — если текущий результат превышает результат из таблицы лидеров…
if ($proc[$i] < $procent)
{
//…добавляем в список текущий результат
$name[$N — 1] = $_POST['imya'];
$data[$N — 1] = date('d.m.y');
$proc[$N — 1] = $procent;
$j = $i;
//заново сортируем список лидеров
for($k=$j;$k<$N;$k++)
for($j=0;$j<($N — 1);$j++)
{
if ($proc[$j]<$proc[$j+1])
{
$buf = $proc[$j];
$proc[$j] = $procent;
$proc[$j+1] = $buf;
$buf = $name[$j+1];
$name[$j+1] = $name[$j];
$name[$j] = $buf;
$buf = $data[$j+1];
$data[$j+1] = $data[$j];
$data[$j] = $buf;
}
}
break;
}
//перезаписываем файл с лучшими результатами
$fp = fopen('liders.dat','w');
for($i=0;$i<$N;$i++)
{
$str = $name[$i].'|'.$proc[$i].'|'.$data[$i].chr(13).chr(10);
fwrite($fp, $str);
}
fclose($fp);
//обнуляем временные данные, записанные в cookie
 setcookie('kolvo', 0, time()+3600, "");
 setcookie('verno', 0, time()+3600, "");
//выводим список лидеров в таблице на страницу 
$liders = file('liders.dat');
for($i=0;$i<$N;$i++)
if (isset($liders[$i]))
list($name[$i],$proc[$i],$data[$i]) = explode('|',$liders[$i]);
echo '<table border=1 bordercolor=navy align=center width=50%>';
echo '<tr align=center><td><b>Позиция</b></td><td><b>Имя</b></td><td><b>Процент угадывания</b></td>
<td><b>Дата</b></td></tr>';
for($i=0;$i<$N;$i++)
{
if (isset($liders[$i]))
if (($i % 2)>0) echo '<tr bgcolor=yellow align=center>';
else echo "<tr bgcolor=gold align=center>";
echo '<td>'.($i+1).'</td><td>'.$name[$i].'</td>
<td>'.$proc[$i].'%</td><td>'.$data[$i].'</td></tr>';
}
echo '</table>';
echo '<p align=center>';
echo '<a href=colors.php>Попробовать еще раз?</a><p>';
}

//спрашиваем имя пользователя
 if (($_COOKIE['kolvo'] == 10) && !isset($_POST['imya']))
echo "
<b>Вы угадали ".(($_COOKIE['verno'] * 100) / 10)." процентов</b>
<br> Введите ваше имя:
<form action=colors.php method=POST>
<input type='text' name=imya value=noname>
<input type='submit' value='Ok'>
</form>
";
//предлагаем выбрать цвет
if (($_COOKIE['kolvo'] < 10) && (!isset($_POST['imya'])) )
echo "
<b>Угадайте цвет:</b>
<form action='colors.php' method=POST>
<table border=0>
<tr><td>
<input type=radio name=u_color checked value=red>
</td><td>
<hr style='background-color:red; width:100px; height:20px'>
</td></tr>
<tr><td>
<input type=radio name=u_color value=red>
</td><td>
<hr style='background-color:green; width:100px; height:20px'>
</td></tr>
<tr><td>
<input type=radio name=u_color value=blue>
</td><td>
<hr style='background-color:blue; width:100px; height:20px'>
</td></tr>
</table>  <p>
<input type=submit value='Дать ответ'>
</form>
";
?>
