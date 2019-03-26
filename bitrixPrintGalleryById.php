<?
//вывод превью изображений из медиаколлекции битрикс со ссылкой на оригинал 
function bitrixPrintGalleryById($collectionId)
{



// получим элементы коллекции с идентификатором $collectionId
$arItems = CMedialibItem::GetList(array('arCollections' => array("0" => $collectionId)));


//Заполним массив путей к картинкам
$arImgPath= array();
$arPreviewImgPath= array();


foreach ($arItems as $arItem){
 $imgPath= $arItem['PATH'];
 $imgPreviewPath= $arItem['THUMB_PATH'];
 
 $arImgPath[]= $imgPath;
 $arPreviewImgPath[]= $imgPreviewPath;
};

		
		

//print_r($arImgPath);
for ($iii=0; $iii<count($arImgPath);$iii++)
{
print '<div class="imageBoxHorizontal"><div class="imageBoxVert"></div>
<a target="_blank" class="fancybox" href="' . $arImgPath[$iii] .  '">
<img class="imgBoxContainerImg" src="' . $arPreviewImgPath[$iii] . '" > 
</a>
</div>';
	
}
}
?>