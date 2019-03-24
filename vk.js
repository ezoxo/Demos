//Расширение для хрома и фаерфокса для симметричного шифрования переписки методом AES



// func sending aes message from secure form to main vk form
function sendAES(chatId) 
{ 
//alert (1); 
var nonCrypted = document.getElementById('secureForm'+chatId).value;
var crypted =  CryptoJS.AES.encrypt(nonCrypted, window.secureKeysArray[chatId]);


//alert( nonCrypted + ' - aes - ' + crypted); //test of crypting

document.getElementById('im_editable'+chatId).innerHTML='AESSTART'+crypted;
//nonCrypted.value=''; //clear secureform
}



function chatByIdDecrypt(chatId)
{
var logNode = document.getElementById("im_log"+chatId);
var messageTexts=logNode.getElementsByClassName("im_msg_text");

for (var i = 0; i < messageTexts.length; ++i) {
var item = messageTexts[i];


//decrypt HTML! if massage contain 'aesstart' and secret key is entered
if (item.innerHTML.indexOf('AESSTART')!=-1 && window.secureKeysArray[chatId]!=null) {
item.innerHTML = item.innerHTML.replace('AESSTART ','');
item.innerHTML = item.innerHTML.replace('AESSTART','');


//alert(item.innerHTML);
//alert(window.secureKeysArray[chatId]);

var decrypted = CryptoJS.AES.decrypt(item.innerHTML, window.secureKeysArray[chatId]);
decryptedHTML = decrypted.toString(CryptoJS.enc.Utf8);

var designKey="<img align=\"right\" src=\"data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8\/9hAAAA5ElEQVR42s3TPWvCUBTG8cTJSXwp\r\nfgURKoqfQBy7ugquFYpFUAd3cRBBJ3XxI7j5QvfuIpS2oIMijpbSQXSQ\/g9EOGQwBpcc+A259\/Ak\r\n9x5iGneW6ckAH85uA2JoIosA9pihhp1TQAQfiOIda8SRxidSOF0LKKGLCtpqr4NXNKwX2EuO+SYB\r\nPTwjjB\/VkMTc4QqGOiCIX7WZwAJLbG3rcuwvlPURJGSgGuXT68hhpNbHeEIRfQl4sC4rhIm6RJnI\r\nCo84qoACXpDH92WM0tRCBn78YYoqNk5jtD9LwMG4sbz5L7iqf0eHLAe+b17mAAAAAElFTkSuQmCC\">";


var postHTML = '<br><font style="font-size:9px;">Encoded and Decoded by VkCrypt</font>';


if (decryptedHTML==null || decryptedHTML=='') decryptedHTML = '<b>ERROR</b> (this message can be encoded by another Secret Key)'; 
item.innerHTML = designKey + decryptedHTML + '';




item.style.backgroundColor="#DEFFDE";
item.style.padding="3px";
}



}


}


function chatDecrypt()
{
}





function vkPageDeCrypt()
{

//если открыт чат
if (document.getElementById('im_tabs'))
{



var chats = document.getElementById('im_tabs').childNodes;
for(i=0; i<chats.length; i++) {
if (chats[i].className=="im_tab_selected") 
{
var chatId=chats[i].id;
chatId=chatId.replace('im_tab','');
//alert ('selected chat id: ' + chatId); //подсказка для определения ID чата
}

}

if (document.getElementById('secureForms')) {} else {

var secureFormsElem = document.createElement("div");
secureFormsElem.setAttribute("id", "secureForms");
secureFormsElem.setAttribute("style", "display: none;");


document.getElementById("im_texts").appendChild(secureFormsElem);

//document.getElementById('im_texts').innerHTML=document.getElementById('im_texts').innerHTML+'<div id="secureForms" style="display: none;"></div>';

}




var secureKeyHtmlButton='<div id="sucureButtonOn"><a href="#" onClick="window.secureKeysArray['+ chatId + ']=prompt(\'ENTER SECRET KEY FOR ID '+ chatId + ' HERE\',\'\'); return false;">ENTER SECRET KEY</a></div>';


//алерт для определения секретного ключа
//if (window.secureKeysArray[chatId]!=null) {alert('secret key for'+ chatId + ' is ' + window.secureKeysArray[chatId]);}





//если в окне не отрисована кнопка и окно не секретно
if (document.getElementById('im_peer_holders').innerHTML.indexOf('ENTER')==-1 && window.secureKeysArray[chatId]==null){
document.getElementById('im_peer_holders').innerHTML=document.getElementById('im_peer_holders').innerHTML + secureKeyHtmlButton;
document.getElementById('im_texts').style.opacity='1';


}



var secureForms = document.getElementById('secureForms').childNodes;

//если чат секретный
if (window.secureKeysArray[chatId]!=null){


//decode log by ID
chatByIdDecrypt(chatId);


document.getElementById('im_texts').style.position='relative';
document.getElementById('secureForms').style.display='block';


//создаём секретную форму, если её нет
if (document.getElementById('secureForm'+chatId)) {} else document.getElementById('secureForms').innerHTML=document.getElementById('secureForms').innerHTML+'<textarea id="secureForm'+chatId+'" style="height: 40px; width: 348px; padding: 3px 5px 5px 3px; display: block; font: normal normal 400 11px/16px Tahoma; border: 1px solid #C0CAD5; z-index: 140;  position: absolute; top:0; resize: none; " ></textarea>'


for(i=0; i<secureForms.length; i++) {
if (secureForms[i].id!='secureForm'+chatId) 
{
secureForms[i].style.display='none'; 
document.getElementById('im_send').onclick=function() {IM.send();};

}
else 
{
secureForms[i].style.display='block';
document.getElementById('im_send').onclick=function() {sendAES(chatId); IM.send(); document.getElementById('secureForm'+chatId).value='';};

}
}


//если была отрисована кнопка введите ключ - удалить её
if (document.getElementById('sucureButtonOn')) {element=document.getElementById("sucureButtonOn"); element.parentNode.removeChild(element);}

//если кнопка введите ключ не была отрисована
if (document.getElementById('secureOn')) {} 
else 
{

var hiddenKey = '';
for(i=0; i<window.secureKeysArray[chatId].length; i++) 
{
hiddenKey = hiddenKey + '*';
if (i==9) hiddenKey=hiddenKey+'...';
if (i==9) break;
}


document.getElementById('im_peer_holders').innerHTML=document.getElementById('im_peer_holders').innerHTML + '<div id="secureOn">SECURE KEY ON (' + hiddenKey + ')</div>';
}


}
else
{

for(i=0; i<secureForms.length; i++) {
secureForms[i].style.display='none';
}

}






}

//запускаем функцию снова
setTimeout(function() {
vkPageDeCrypt();
}, 500);

}
vkPageDeCrypt();