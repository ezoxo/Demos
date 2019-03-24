<?php
ini_set('max_execution_time', '180'); 
@session_start();
if(($_FILES['fupload']['tmp_name']=="")) $error[]="Файл не выбран";
if(filesize($_FILES['fupload']['tmp_name']) > 1048576) $error[]="Размер файла больше одного мегабайта";
if(!($_POST['mag']=="playboy" OR $_POST['mag']=="glamour" OR $_POST['mag']=="cosmopolitan" OR $_POST['mag']=="menshealth" OR $_POST['mag']=="afisha" OR $_POST['mag']=="esquire" OR $_POST['mag']=="ptuch")) $error[]="Обложка не выбрана";
if(!isset($_SESSION['captcha_keystring']) or $_SESSION['captcha_keystring']!=$_POST['keystring']) $error[]="Код с картинки повторён неверно";
unset($_SESSION['captcha_keystring']);
//загружаем функции
include("../mod/func_hero.php");
include("../func_sub.php");
//если всё хорошо...
if ($error=="")
{
if($im=@ImageCreateFromjpeg($_FILES['fupload']['tmp_name'])) 
{
$meta=GetImageSize($_FILES['fupload']['tmp_name']);
if (($meta[0]*$meta[1])> 2000000)
{
$error[]="Размер изображения больше двух мегапикселей, уменьшите его пожалуйста, и загрузите снова";
imagedestroy($im);
}
else
{
//создаём обложку, и делаем вычисления для вписывания загруженной фотографии
$with=500;
$heith=680;
$main_img=ImageCreateTrueColor($with,$heith);
$kof1=$with/$heith;
$kof2=$meta[0]/$meta[1];
if ($kof1>=$kof2) $umenshit_v_raz=$meta[0]/$with;
if ($kof1<$kof2) $umenshit_v_raz=$meta[1]/$heith;
$nov_wirina=($meta[0]/$umenshit_v_raz)+2;
$nov_hisota=($meta[1]/$umenshit_v_raz)+2;
$koord_x= ($nov_wirina-$with)/(-2);
$koord_y= ($nov_hisota-$heith)/(-2);
//вписываем загруженную картинку в прямоугольник обложки
ImageCopyResampled($main_img,$im,$koord_x,$koord_y,0,0,$nov_wirina,$nov_hisota,$meta[0],$meta[1]); 
imagedestroy($im);


//цвета шрифтов
$color[black]=imagecolorallocate ($main_img, 0, 0, 0);
$color[white]=imagecolorallocate ($main_img, 255, 255, 255);
$color[pink]=imagecolorallocate ($main_img, 255, 0, 255);
$color[green]=imagecolorallocate ($main_img, 0, 255, 51);
//пути к шрифтам
//$fontfile="arialbd.ttf"; 
$fontfile="/guer.ttf"; 
$fontfile_arial="/arial.ttf"; 
$fontfile_crash="/crash.ttf"; 
////для начала дата
$mesyac=date('m');
$mesyac=$mesyac+1;
$mesyac=$mesyac-1;
$god=date('Y');
$q[1]="ЯНВАРЬ"; 
$q[2]="ФЕВРАЛЬ"; 
$q[3]="МАРТ"; 
$q[4]="АПРЕЛЬ"; 
$q[5]="МАЙ";
$q[6]="ИЮНЬ"; 
$q[7]="ИЮЛЬ"; 
$q[8]="ИЮЛЬ"; 
$q[9]="СЕНТЯБРЬ"; 
$q[10]="ОКТЯБРЬ"; 
$q[11]="НОЯБРЬ";
$q[12]="ДЕКАБРЬ";
$data="$q[$mesyac] $god";
//наносим изображение
if ($_POST['mag']=="playboy") imagettftextalign($main_img, 12, 0, 490, 122, $color[white], $fontfile_arial, strcod($data),"R");
if ($_POST['mag']=="glamour") imagettftextalign($main_img, 12, 0, 490, 130, $color[pink], $fontfile_arial, strcod($data),"R");
if ($_POST['mag']=="cosmopolitan") imagettftextalign($main_img, 12, 0, 495, 120, $color[black], $fontfile_arial, strcod($data),"R");
if ($_POST['mag']=="menshealth") imagettftextalign($main_img, 12, 0, 490, 20, $color[black], $fontfile_arial, strcod($data),"R");
if ($_POST['mag']=="ptuch") imagettftextalign($main_img, 12, 0, 490, 135, $color[white], $fontfile_arial, strcod($data),"R");
if ($_POST['mag']=="esquire") imagettftextalign($main_img, 26, 0, 455, 120, $color[black], $fontfile_arial, $mesyac,"R");



////делаем надписи
@imagettftext($main_img, 33, 0 ,20,290, $color[$color1], $fontfile, strcod($_POST['zag1']));
@imagettftext($main_img, 33, 0 ,20,330, $color[$color2], $fontfile, strcod($_POST['zag2']));
@imagettftext($main_img, 14, 0 ,20,360, $color[$color3], $fontfile_arial, strcod($_POST['zag3']));
@imagettftext($main_img, 26, 0 ,20,400, $color[$color4], $fontfile_crash, strcod($_POST['zag4']));
@imagettftext($main_img, 12, 0 ,20,426, $color[$color5], $fontfile_arial, strcod($_POST['zag5']));
@imagettftext($main_img, 15, 0 ,20,450, $color[$color6], $fontfile_arial, strcod($_POST['zag6']));
////копируем сверху обложку
$png=ImageCreateFrompng("obl_".$_POST['mag'].".png");
ImageCopy($main_img,$png,0,0,0,0,$with,$heith);
imagedestroy($png);
// генерируем пикчу, загружаем на имаджсхак, и редиректируем

$language="ru";
include ("../mod/postimage.php");
exit;



////отгенерировали
}
} 
else $error[]="Загруженный файл не является изображением JPG";
}

if ($error!="")
{
for ($i=0; $i<count($error);$i++)	{    $datavar[error].="<li>".$error[$i]."</li>";	}
@session_start();


top($pagecontent);
$template=load_templ("/oblojka/form.htm");
print_templ($template,$datavar,$m);
bottom($pagecontent,1);
}

?>

