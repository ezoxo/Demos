<?php

//pagination function 
//example call: 
//paginator(1,200,10,15,"/blog/page", "/");
 



function paginator($actualPageNumber, $totalCountOfAllRecords, $maxRecordsOnOnePage, $maxLinksInLine, $linkPrefix, $linkPostfix)
{

//Эти слова можно писать на русском языке $pageText='Страница: '; $nextText='Далее';
$pageText="Page: ";
$nextText = "Next ";





//если в totalCountOfAllRecords передан массив записей, который надо подсчитать
if (is_array($totalCountOfAllRecords)) $totalCountOfAllRecords=count($totalCountOfAllRecords);


//проверяем нужен ли список страниц, если количество записей всего, превышает лимит записей на одной странице, то выводим
if ($totalCountOfAllRecords > $maxRecordsOnOnePage) 
{
print "<p align=\"center\" class=\"pages\"> " . $pageText;
$maxRecordsOnOnePageage=floor($totalCountOfAllRecords/$maxRecordsOnOnePage);
if ($totalCountOfAllRecords!=$maxRecordsOnOnePage*$maxRecordsOnOnePageage) $maxRecordsOnOnePageage++;
$forw=0;
$backw=0;
for($i=1;$i<=$maxLinksInLine;$i++)
{
if(($forw+$actualPageNumber)<$maxRecordsOnOnePageage)$forw=$forw+1;;
if(($actualPageNumber-$backw)>1)$backw=$backw+1;
if(($forw+$backw)>=$maxLinksInLine) break;
}
for ( $id=($actualPageNumber-$backw); $id<=($forw+$actualPageNumber); $id++) 
{
if(($id!=1)and($id==($actualPageNumber-$backw))) print '<a href="' . $linkPrefix . '">1</a> | .. | ';
if($id!=$actualPageNumber) 
{
if($id!=1) print '<a href="'.$linkPrefix.$id.$linkPostfix.'">'.$id.'</a>';
if($id==1) print '<a href="'.$linkPrefix.'">'.$id.'</a>';
}
else print '<b class="currentpage">'.$id.'</b>';
if (($id!=$maxRecordsOnOnePageage))print " | ";
}
if($id<($maxRecordsOnOnePageage)+1) print '.. | <a href="'.$linkPrefix.$maxRecordsOnOnePageage.$linkPostfix.'">'.$maxRecordsOnOnePageage.'</a>';
if ($actualPageNumber!=$maxRecordsOnOnePageage)
{
$actualPageNumber++;
print ' | <a href="' . $linkPrefix.$actualPageNumber.$linkPostfix . '">' . $nextText . '</a></p>';
}
}

}

//example 
paginator(1,200,10,15,"/blog/page", "/");


?>