<?php
$db_table='psynews';

//цепляемся к базе
$db=mysql_connect($db_adr, $db_login, $db_pass);
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("set character_set_client='utf8'");
mysql_query("set character_set_results='utf8'");
mysql_query("set collation_connection='utf8_general_ci'");
mysql_select_db($db_name,$db);
//зацепились

if ($_POST[id]=="") $action="new"; else $id = obrabotka_texta($_POST['id']);



$tagss=" ". obrabotka_texta($_POST['tagss']) . " ";
$url=str_replace("http://","", obrabotka_texta($_POST['url']));
$maintag=obrabotka_texta($_POST['maintag']);

$newid=obrabotka_texta($_POST['newid']);
$fotoadd=obrabotka_texta($_POST['fotoadd']);


$text = obrabotka_texta($_POST['text']);
$smalltext = obrabotka_texta($_POST['smalltext']);
$logo = obrabotka_texta($_POST['logo']);
$small_logo=obrabotka_texta($_POST['small_logo']);
$data=obrabotka_texta($_POST['data']);
$istochnik=obrabotka_texta($_POST['istochnik']);
$author_id=obrabotka_texta($_POST['author_id']);
$image=obrabotka_texta($_POST['image']);
$image_alt=obrabotka_texta($_POST['image_alt']);
$keyword=obrabotka_texta($_POST['keyword']);
$image_istochnik=obrabotka_texta($_POST['image_istochnik']);
$gallery=obrabotka_texta($_POST['gallery']);
$f18=obrabotka_texta($_POST['f18']);
$f19=obrabotka_texta($_POST['f19']);
$photourl=obrabotka_texta($_POST['photourl']);








if ($id!="" and $fotoadd!=1) //изменяем новость
{
$result=mysql_query("UPDATE $db_table SET data='$data', tags='$tagss', logo='$logo', text='$text', smalltext='$smalltext', istochnik='$istochnik', url='$url', author_id='$author_id', image_alt='$image_alt', keyword='$keyword', ozon_id='$ozon_id', ozon_name='$ozon_name', image_istochnik='$image_istochnik', gallery='$gallery', maintag='$maintag', f19='$f19' WHERE id='$id'",$db);
$a=mysql_error();
print $a;
}


if($action=="new") //добавляем новость
{
$unixtime=time();
$result=mysql_query("INSERT INTO $db_table (data, tags, logo, text, smalltext, istochnik, url, author_id, image, image_alt, keyword, ozon_id, ozon_name, image_istochnik, unixtime, gallery, maintag, f19) VALUES ('$data','$tagss','$logo','$text','$smalltext','$istochnik','$url','$author_id','$image','$image_alt','$keyword','$ozon_id','$ozon_name','$image_istochnik','$unixtime','$gallery','$maintag','$f19')",$db);
$id=mysql_insert_id();
mysql_query("UPDATE image SET modid='$id' WHERE modid='$_POST[modid]'");
$a=mysql_error();
print $a;
}



if ($fotoadd==1)
{
print $_FILES['fupload']['tmp_name'];
if (isset($_FILES['fupload']['tmp_name']) and $test_img=@ImageCreateFromjpeg($_FILES['fupload']['tmp_name']) and $photourl=="") //добавление фото через форму
{
imagedestroy($test_img);
$final_path="../img/".$m."/full/".$id.".jpg";
$final_path2="../img/".$m."/small/".$id.".jpg";
move_uploaded_file( $_FILES['fupload']['tmp_name'], $final_path ) or die ("Unable To Copy");
create_tb($gd,$final_path,$final_path2,70,70);
$final_path2="../img/".$m."/mid/".$id.".jpg";
create_tb($gd,$final_path,$final_path2,200,200);
$image=1;
} 

if($photourl!="" and $test_img=@ImageCreateFromjpeg($photourl)) //Добавление фото через урл
{
imagedestroy($test_img);
$final_path="../img/".$m."/full/".$id.".jpg";
$final_path2="../img/".$m."/small/".$id.".jpg";
copy( $photourl, $final_path ) or die ("Unable To Copy");
create_tb($gd,$final_path,$final_path2,70,70);
$final_path2="../img/".$m."/mid/".$id.".jpg";
create_tb($gd,$final_path,$final_path2,200,200);
$image=1;
}  

if($photourl=="" and  $_FILES['fupload']['tmp_name']=="") //стирание фотографий если они есть
{
$image=0;
if(file_exists("../img/".$m."/small/".$id.".jpg")) unlink ("../img/".$m."/small/".$id.".jpg");
if(file_exists("../img/".$m."/mid/".$id.".jpg")) unlink ("../img/".$m."/mid/".$id.".jpg");
if(file_exists("../img/".$m."/full/".$id.".jpg")) unlink ("../img/".$m."/full/".$id.".jpg");
}

$result=mysql_query("UPDATE $db_table SET image='$image' WHERE id='$id'",$db);
}

//
top($pagecontent);
$template=load_templ("templ/".$m."/complete.htm");
$data_var[id]=$id;
$data_var[modid]=$id;
$data_var[modl]=$m;
print_templ($template,$data_var,$m);
bottom($pagecontent);
//
//Header("Location:index.php?m=".$m);

mysql_close($db);

?>