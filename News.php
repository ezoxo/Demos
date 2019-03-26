<?php
$ap=10;
if( $tag=="news" OR  $tag=="psychology" OR  $tag=="business" OR  $tag=="education" OR  $tag=="family" OR  $tag=="transpersonal") $adon1="/".$tag."/";
 
 





####
$db_table="news";
$db=mysql_connect($db_adr, $db_login, $db_pass);
mysql_query("SET CHARACTER SET 'utf8'");
mysql_select_db($db_name,$db);
$mysql_error=mysql_error();
print $mysql_error;



if($id=="")
{


if ($tag=="") 
{
$mysql_query_result=mysql_query("SELECT id FROM $db_table ORDER BY id DESC",$db); 
$count_mysql=mysql_num_rows($mysql_query_result);
if ($count_mysql < 1 and $tag=="") {top($pagecontent);include ("templ/empty.htm"); bottom($pagecontent); mysql_close($db); exit;} // 404 вывод заглушки
top($pagecontent); //вывод заголовка
}


if ($tag!="")
{
$mysql_query_result=mysql_query("SELECT id FROM $db_table WHERE tags LIKE '% $tag %'",$db);
$count_mysql=mysql_num_rows($mysql_query_result);
$tag_templ_file="templ/".$m."/tag.htm";
$tag_template=load_templ($tag_templ_file);
$tag_mysql_query_result=mysql_query("SELECT * FROM psytag WHERE id='$tag'",$db);

if($tag_data_var=mysql_fetch_row($tag_mysql_query_result))
{
$pagecontent[content]=$tag_data_var[3];
if ($tag_data_var[11]!="") $pagecontent[content]=$pagecontent[content].", ".$tag_data_var[11];
top($pagecontent);
print_templ($tag_template,$tag_data_var,$m);
}
else
{
$tag_formated=ucfirst($tag); // в верхний регистр
$tag_formated=str_replace("_"," ",$tag_formated); // заменяем _ на пробел
$tag_data_var[3]=$tag_formated;
$tag_data_var[2]=$tag_formated;
$tag_data_var[9]=0;
$pagecontent[content]=$tag_formated;
top($pagecontent);
print_templ($tag_template,$tag_data_var,$m);
}
}


///////это вывод новостей или заглушки
  if ($count_mysql < 1) 
  {
  include ("templ/empty.htm"); 
  bottom($pagecontent); 
  mysql_close($db); 
  exit;
  } // 404
  else
  {
  $kol=get_page($h,$ap);
  $templ_file="templ/".$m."/short.htm"; 
  $template=load_templ($templ_file);
  $a=$kol-1;
  if ($tag=="") $mysql_query_result=mysql_query("SELECT * FROM $db_table ORDER BY id DESC LIMIT ".$a.",".$ap,$db); else $mysql_query_result=mysql_query("SELECT * FROM $db_table WHERE tags like '% $tag %' ORDER BY id DESC LIMIT ".$a.",".$ap,$db);

  while ($data_var=mysql_fetch_row($mysql_query_result))
     {
     print_templ($template,$data_var,$m);
     $done=1;
     }
  paginator($h,$count_mysql,$ap,6,$adon1,$adon2); mysql_close($db); bottom($pagecontent); exit;
  }
////////



}






////стартуем вывод одиночной новости

if($id!="") 
{

$templ_file="templ/".$m."/full.htm";
$template=load_templ($templ_file);



$mysql_query_result=mysql_query("SELECT * FROM $db_table WHERE id=".$id,$db);

if ($data_var=mysql_fetch_row($mysql_query_result))
{
mysql_free_result($mysql_query_result);
$pagecontent[content]= strip_tags($data_var[3]);
$pagecontent[description]= strip_tags($data_var[5]);

$data_var[2]=chop($data_var[2]);
$data_var[2]=trim($data_var[2]);
$tag_array=explode(" ",$data_var[2]);
for($xy=0;$xy<=count($tag_array)-1;$xy++)
{
$tagname=$tag_array[$xy];
$tag_mysql_query_result=mysql_query("SELECT * FROM psytag WHERE id='$tagname'",$db);

   if ($tag_smalllogo=mysql_fetch_row($tag_mysql_query_result)) 
   {
   $tag_smalllogo=$tag_smalllogo[2];
   
   
   
 if( $tag_array[$xy]=="news" OR  $tag_array[$xy]=="psychology" OR  $tag_array[$xy]=="business" OR  $tag_array[$xy]=="education" OR  $tag_array[$xy]=="family" OR  $tag_array[$xy]=="transpersonal") 
{   
$data_var[tag].='<a href="/'.$tag_array[$xy].'/" class="tag">'.$tag_smalllogo.'</a>';
}
else  
{
$data_var[tag].='<a href="/tag/'.$tag_array[$xy].'/" class="tag">'.$tag_smalllogo.'</a>';
}
   
   }
   else
   {
$tag_formated=str_replace("_"," ",$tag_array[$xy]); // заменяем _ на пробел
$data_var[tag].='<a href="/tag/'.$tag_array[$xy].'/" class="tag">'.$tag_formated.'</a>';

 }

   if($tag_array[$xy+1]!="" and  $data_var[tag]!="") $data_var[tag].=", ";
}

if ($data_var[11]!="") $pagecontent[content]=$pagecontent[content].", ".$data_var[11];
top($pagecontent);

///socialnetwork
if ($data_var[9]==1) $data_var[sn_imageurl]=htmlspecialchars("/img/news/mid/".$data_var[0].".jpg");
$data_var[sn_url]=htmlspecialchars("/post/".$data_var[0]."/");
$data_var[sn_title]=htmlspecialchars($data_var[3]);
$data_var[sn_description]=htmlspecialchars($data_var[5]);




$data_var[sn_url_enc]=urlencode(convvv($data_var[sn_url]));
$data_var[sn_title_enc]=urlencode(convvv("psyservice.ru: ".$data_var[sn_title]));


$data_var[sn_description_enc]=urlencode(convvv($data_var[sn_description]));
if ($data_var[9]==1) $data_var[sn_description_enc]=urlencode('<img src="'.$data_var[sn_imageurl].'" align="left">').$data_var[sn_description_enc];

$twitter_title=$data_var[3];

$twitter_no_url=140-strlen($data_var[sn_url])-1;
if (strlen($twitter_title)>$twitter_no_url) 
$data_var[twitter_status]=substr($twitter_title, 0, $twitter_no_url-3)."...";
else 
$data_var[twitter_status]=$twitter_title;
$data_var[twitter_status]=$data_var[twitter_status]." ".$data_var[sn_url];
$data_var[twitter_status]=urlencode(convvv($data_var[twitter_status]));

///socialnetwork!!


$data_var[redir]="http://".$data_var[7];
$data_var[redir]=urlencode($data_var[redir]);
$data_var[russiandate]=UnixtimeToRussianDate($data_var[15]);

print_templ($template,$data_var,$m);


///////похожие новости
$templ_file="templ/".$m."/short.htm";
$template=load_templ($templ_file);
if ($tag_array[0]=="") $mysql_query_result=mysql_query("SELECT * FROM $db_table WHERE id<>'$id' ORDER BY id DESC LIMIT 0,2",$db); else $mysql_query_result=mysql_query("SELECT * FROM $db_table WHERE id<>'$id' AND tags like '% $tag_array[0] %' ORDER BY id DESC LIMIT 0,2",$db); /// сортировка по типу записи, внизу выводятся похожие по теме новости
      while ($data_var=mysql_fetch_row($mysql_query_result))
      {
      print_templ($template,$data_var,$m);
      }



bottom($pagecontent);
exit;

} else {top($pagecontent);include ("templ/empty.htm"); mysql_close($db); bottom($pagecontent); exit;} // 404
}


?>