<?php
//скрипт считывает из текстового файла адреса страниц на яндекс афише 

ini_set('max_execution_time', '180'); 
include ("../mod/conf_mysql.php");
$db=mysql_connect($db_adr, $db_login, $db_pass);
mysql_query("SET CHARACTER SET 'cp1251'");
mysql_select_db($db_name,$db);

//подключаем функции для работы с CURL
function data_encode ( $data , $keyprefix = "" , $keypostfix = "" ) {
assert ( is_array ( $data ) );
$vars = null ;
foreach ( $data as $key => $value )
{
if ( is_array ( $value )) $vars .= data_encode ( $value , $keyprefix . $key . $keypostfix . urlencode ( "[" ), urlencode ( "]" ));
else
$vars .= $keyprefix . $key . $keypostfix . "=" . urlencode ( $value ). "&" ;
}
return $vars ;
}
function CurlPage ($path, $agent, $header, $referer, $arr_cookie, $cookie_file, $postdata) {
$ch = curl_init( $path );
@curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1 );
@curl_setopt( $ch , CURLOPT_VERBOSE , 0 );
@curl_setopt( $ch , CURLOPT_HEADER , 0 );
@curl_setopt( $ch , CURLOPT_USERAGENT , $agent );
@curl_setopt( $ch , CURLOPT_REFERER , $referer );
@curl_setopt( $ch , CURLOPT_HTTPHEADER , $header );
@curl_setopt( $ch , CURLOPT_FOLLOWLOCATION , 1 );
@curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER, 0 );
@curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST, 0 );

if ( @is_array ($arr_cookie))
{
while (list($key, $val) = @each ($arr_cookie)){
$COKKIES .= trim ($key)."=". trim ($val)."; ";
}
// var_dump($COKKIES);
@curl_setopt ( $ch , CURLOPT_COOKIE , $COKKIES." expires=Mon, 14-Apr-09 10:34:13 GMT" );
}
// если с сервера пришло cookie, то запишем его в файл $cookie_file
@curl_setopt ( $ch , CURLOPT_COOKIEJAR , $cookie_file );
@curl_setopt ( $ch , CURLOPT_COOKIEFILE , $cookie_file );
// если мы послали данные из формы, которая стоит
 // на страничке $path, добавляем метод $_POST
if (is_array($postdata)){
@curl_setopt( $ch , CURLOPT_POST , 1 );
@curl_setopt( $ch , CURLOPT_POSTFIELDS , substr ( data_encode ($postdata), 0 , - 1 ) );
}

$tmp = @curl_exec( $ch );

//print_r(curl_getinfo($ch));
//echo " cURL error number:" .curl_errno($ch);
//echo " cURL error:" . curl_error($ch);

curl_close($ch);
return $tmp;
}



$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7" ;
$header[] = "Accept: text/html;q=0.9, text/plain;q=0.8, image/png, */*;q=0.5" ;
$header[] = "Accept_charset: windows-1251, utf-8, utf-16;q=0.6, *;q=0.1";
$header[] = "Accept_encoding: identity";
$header[] = "Accept_language: en-us,en;q=0.5";
$header[] = "Connection: close";
$header[] = "Cache-Control: no-store, no-cache, must-revalidate";
$header[] = "Keep_alive: 300";
$header[] = "Expires: Thu, 01 Jan 2009 00:00:01 GMT";
$cookie_file = "cookie.txt";
$referer="http://pda.afisha.yandex.ru/";
//$arr_cookie[my] = "YxsDAIDVAAA=";
$arr_cookie[yandexuid]="642160621220906690";
/////////


///очищаем все устаревшие события из БД
$today=getdate();  
$mon=$today['mon'];
$year_1=$today['year'];

$deadlinetime=time()+43200;
$result=mysql_query("DELETE FROM afisha WHERE bot='1' AND unixtime>'$deadlinetime'",$db);



$base_club = file("base_club.dat");
for ( $ic = count($base_club) - 1; $ic>=0; $ic--)
{



$clubRow=explode("|",$base_club[$ic]);
$name_club=$clubRow[2];
$name_club_id=$clubRow[0];
$club_city=$clubRow[3];

print "<br><b>".$name_club.":</b> ";
$ot4et.="\n".$name_club.": ";

if ($clubRow[1]=="" or $clubRow[1]=="0") {print "id клуба не указан"; $ot4et.="id клуба не указан";}


if ($clubRow[1]!="")
{
if ($clubRow[3]=="moskva") $yandexgorod="msk";
if ($clubRow[3]=="sanktpeterburg") $yandexgorod="spb";
$url="http://m.afisha.yandex.ru/" . $yandexgorod . "/places/" . $clubRow[1] . "/?date=" . date("Y-n-j") . "&days=100";



for($popytka=0; $popytka<2;$popytka++)
{
sleep(2);
$text=CurlPage($url, $agent, $header, $referer, $arr_cookie, $cookie_file, false);
$text=@iconv("koi8-r","windows-1251",@iconv("UTF-8","koi8-r",$text));


if($text=="" or stristr($text, "К сожалению, у нас нет") or stristr($text, "сервис временно недоступен") or stristr($text, "Доступ к нашему сервису запрещен") or stristr($text, "404@yandex-team.ru")) 
{
if(stristr($text, "сервис временно недоступен")) $yanderror=" сервис временно недоступен"; 
if(stristr($text, "Доступ к нашему сервису запрещен")) $yanderror=" доступ к сервису запрещен";
if(stristr($text, "нет данных о репертуаре")) $yanderror=" нет данных о репертуаре";
if(stristr($text, "404@yandex-team.ru")) $yanderror=" Страница не найдена";
if($text=="") $yanderror="Невозможно получить информацию с яндекса"; 
$udacha=false;
} 
else 
{
$udacha=true; 
$popytka=4;
}
}



//если неудачно считали - выводим ошибку
if($udacha==false) 
{
print $yanderror;
$ot4et.=$yanderror;
}
else
{
//считали удачно, парсим страничку	
$text=str_replace("\r","",$text);
$text=str_replace("\n","",$text);
eregi('(<table class="place" cellspacing="0">)(.*)(<\/table><div class="clear">)',$text,$text_temp);
$text=$text_temp[2];


//чистим от тегов и пробелов
while (stristr($text, "  ") or stristr($text, "> <"))
{
$text=str_replace("  "," ",$text);
$text=str_replace("> <","><",$text);
}
$text=str_replace('<tr><th id="selected" width="50%">название</th><th width="20%">время</th></tr>',"",$text);
$text=str_replace("</tr>","%%%&&&",$text);
$text=preg_replace ("/(events\/)(\d*)/", "\">\$2_<a href=\"", $text);
$text=strip_tags($text,'<b>');



$text=str_replace("place","text",$text);
$text=str_replace("%%%&&&<b>","<b>",$text);
$text=str_replace("<b>","%&%&%&",$text);
$text=str_replace("</b>","",$text);
$text=$text."@@@";
$text=str_replace("%%%&&&@@@","",$text);



$arraySobitiya=explode("%&%&%&",$text);



for ( $i = (count($arraySobitiya))-1 ; ($i> 0); $i--)
{
$id_event="%%%&&&[0-9]_";
eregi('(%%%&&&)([0-9]+)(_)',$arraySobitiya[$i],$id_event);
$id_event=$id_event[2];
$arraySobitiya[$i]=str_replace($id_event."_","",$arraySobitiya[$i]);




$data_sobit=explode("%%%&&&",$arraySobitiya[$i]);
$data_temp=explode(",",$data_sobit[0]); 


$dataArray=explode(" ",$data_temp[0]);
if ($dataArray[1]=="января" or $dataArray[1]=="Январь") $dataArray[1]="01";
if ($dataArray[1]=="февраля" or $dataArray[1]=="Февраль") $dataArray[1]="02";
if ($dataArray[1]=="марта" or $dataArray[1]=="Март") $dataArray[1]="03";
if ($dataArray[1]=="апреля" or $dataArray[1]=="Апрель") $dataArray[1]="04";
if ($dataArray[1]=="мая" or $dataArray[1]=="Май")$dataArray[1]="05";
if ($dataArray[1]=="июня" or $dataArray[1]=="Июнь") $dataArray[1]="06";
if ($dataArray[1]=="июля" or $dataArray[1]=="Июль") $dataArray[1]="07";
if ($dataArray[1]=="августа" or $dataArray[1]=="Август") $dataArray[1]="08";
if ($dataArray[1]=="сентября" or $dataArray[1]=="Сентябрь") $dataArray[1]="09";
if ($dataArray[1]=="октября" or $dataArray[1]=="Октябрь") $dataArray[1]="10";
if ($dataArray[1]=="ноября" or $dataArray[1]=="Ноябрь") $dataArray[1]="11";
if ($dataArray[1]=="декабря" or $dataArray[1]=="Декабрь") $dataArray[1]="12";
if ($dataArray[1]<$mon) $year=$year_1+1; else $year=$year_1;
####
$party_day=$dataArray[0];
$party_mon=$dataArray[1];
$party_year=$year;




for ( $ii2=1 ; $ii2<(count($data_sobit)); $ii2++)
{
eregi('([0-9]{2}):([0-9]{2})',$data_sobit[$ii2],$time_temp);  //вычисляем время
$data_sobit[$ii2]= preg_replace('/([0-9]{2}):([0-9]{2})/','', $data_sobit[$ii2]);

#######

$party_hour=$time_temp[1];
$party_min=$time_temp[2];
$party_logo=$data_sobit[$ii2];
$unik_id=$paty_unix_time.$name_club_id.rand (0,3000);
$paty_unix_time=mktime($party_hour,$party_min,0,$party_mon,$party_day,$party_year);
//Дtнь недели
$days = array (0 => 'Воскресенье',1 => 'Понедельник',2 => 'Вторник',3 => 'Среда',4 => 'Четверг',5 => 'Пятница',6 => 'Суббота');
$day = (int) date('w', $paty_unix_time);
$party_day_of_week=$days[$day];

//передача в БД

$party_time=$party_hour.":".$party_min;
if ($party_day<10) $party_day="0".($party_day*1);
$party_logo=str_replace("  ","",$party_logo);
$party_logo=mysql_escape_string(trim($party_logo));
$id_event_36=$id_event;
$result=mysql_query("INSERT INTO afisha (id, clubid, clubname, logo, text, unixtime, time, data, dayofweek, bot, clubcity, afisharuid) VALUES ('$id_event_36','$name_club_id','$name_club','$party_logo','','$paty_unix_time','$party_time','$party_day.$party_mon.$party_year','$party_day_of_week','1','$club_city','$id_event')",$db);
$a=mysql_error();
print $a;

}


}


print "загружен удачно";
$ot4et.="загружен удачно";

}
}


}

//отчёт на почту
$mail_logo="Отчёт по обновлению базы данных афишы за ".date("d.m.Y",time());
$headers = "From:   Робот тусовщик <afisha-bot@mail.ru>
";
$headers.= "Content-type: text/plain; charset=windows-1251
";
@mail("test@mail.ru", $mail_logo, $mail_logo.$ot4et, $headers);


mysql_close($db);
?>