
var itemOnPage=12;
var totalUser;
var totalpages;
var friendsArray;
var userFirstName1;
var userLastName1;
var userSex1;
var selectedUser2;
var ajaxUrl;
var userId1;
var userId2;
var hideId;
var showId;







window.onload = (function() {


VK.init(function() {

//получаем свой профиль
VK.api("getProfiles", {uids:window.viewer_id, fields:"photo_medium, bdate, sex"}, function(data) { 
document.getElementById('selectBoxUser1Name').innerHTML = data.response[0].first_name + ' ' + data.response[0].last_name ;
document.getElementById('selectBoxUser1Image').src = data.response[0].photo_medium;
window.userFirstName1=data.response[0].first_name;
window.userLastName1=data.response[0].last_name;
window.userSex1=data.response[0].sex;
window.userId1=data.response[0].uid;


if(data.response[0].bdate!=null)
{
var bdatax=data.response[0].bdate.split('.');
var day1=Math.ceil(bdatax[0]);
var month1=Math.ceil(bdatax[1]);
document.getElementById("userDay1").options[day1].selected=true;
document.getElementById("userMonth1").options[month1].selected=true;
document.getElementById("userDay1").disabled=true;
document.getElementById("userMonth1").disabled=true;
}

});







//запрос на список друзей
VK.api("friends.get", {fields:"uid", fields:"photo_medium, bdate, sex"}, function(data) {
// считаем количество друзей
var totalUser = data.response.length;
window.totalUser=totalUser;
window.totalpages=Math.ceil(window.totalUser/window.itemOnPage);



// сортируем друзей по алфавиту
var friends_data = data.response.sort(sFirstName);
window.friendsArray=friends_data; 

  
    
// записываем отсортированный список друзей в переменную, вытаскивая из массива
	
showPageUsers(friends_data, 1, window.itemOnPage);
//pagerGeneration(1, window.totalpages)
	
});
 
	
	
});
});
 
// функции сортировки
function sFirstName(a,b) {
    if (a.first_name > b.first_name)
        return 1;
    else if  (a.first_name < b.first_name)
        return -1;
    else
        return 0;
}




//функция вывода страницы с друзьями
function showPageUsers(friendsArray, pageNumber, itemOnPage)
{
var totalUserTemp = friendsArray.length;
var frListHtml = '';

var startFrom=((itemOnPage*pageNumber)-itemOnPage);
var endFrom=(itemOnPage*pageNumber)-1;
if (endFrom>totalUserTemp)endFrom=totalUserTemp-1;

for(var i=startFrom; i<=endFrom; i++){
	
		
		
frListHtml += 
'<div class="userblock"><div class="username">'
+ friendsArray[i].first_name 
+ ' ' 
+ friendsArray[i].last_name 
+ ' ' + '</div>'
+ '<a href="#" onclick="selectUser('
+ i 
+ '); return false;">'
+ '<img class="ovalImg" width="100" height="100" src="'+ friendsArray[i].photo_medium+'"/>'
+ '<br>'
+ 'Узнать</a>'
+ '</div>'
;
}
// выводим друзей отсортированных по имени
document.getElementById('friends_list').innerHTML = frListHtml;
pagerGeneration(pageNumber, window.totalpages);
}

function pagerGeneration(currentPage, totalPages)
{

var html='';
if (totalPages!=1)
{
if (currentPage==1) 
html+= '<span class="pagergray">&lt;</span>' ;
else 
html+= '<a href="#" onclick="showPageUsers(window.friendsArray, '
+ (currentPage-1)
+', '
+ window.itemOnPage
+' );"  class="pager">&lt;</a>' ;


html+= ' '+currentPage + ' ';
html+= ' / ';
html+= totalPages;
html+= '  ';
//

if (currentPage==totalPages) 
html+= '<span class="pagergray">&gt;</span>' ;
else 
html+= '<a href="#" onclick="showPageUsers(window.friendsArray, '
+ (currentPage+1)
+', '
+ window.itemOnPage
+' );"  class="pager">&gt;</a>' ;
}




document.getElementById('pager').innerHTML = html;

}




function selectUser(i)
{
document.getElementById('popBox').style.opacity=0;
showById('popBox');
document.getElementById('popBox').style.display="block";
document.getElementById('selectBox').style.display = "block";
document.getElementById('resultBox').style.display = "none";
document.getElementById('horoCloseButton').style.display = "block";

window.selectedUser2=i;
window.userId2=window.friendsArray[i].uid;

document.getElementById('selectBoxUser2Name').innerHTML = window.friendsArray[i].first_name + ' ' + window.friendsArray[i].last_name;
document.getElementById('selectBoxUser2Image').src = window.friendsArray[i].photo_medium;

if(window.friendsArray[i].bdate!=null)
{
var bdatax=window.friendsArray[i].bdate.split('.');
var day2=Math.ceil(bdatax[0]);
var month2=Math.ceil(bdatax[1]);


document.getElementById("userDay2").options[day2].selected=true;
document.getElementById("userMonth2").options[month2].selected=true;
document.getElementById("userDay2").disabled=true;
document.getElementById("userMonth2").disabled=true;
} else 
{
alert ('Пользователь скрыл дату рождения, введите её самостоятельно.');
document.getElementById("userDay2").disabled=false;
document.getElementById("userMonth2").disabled=false;
document.getElementById("userDay2").options[0].selected=true;
document.getElementById("userMonth2").options[0].selected=true;
}





}


//создаём AJAX GET запрос
function ajaxUrlCreate()
{
var i=window.selectedUser2;
window.ajaxUrl='ajax.php?userfirstname1=' 
+ encodeURIComponent(window.userFirstName1)  
+ '&userlastname1=' 
+ window.userLastName1 
+ '&userfirstname2=' 
+ encodeURIComponent(window.friendsArray[i].first_name) 
+ '&userlastname2=' 
+ window.friendsArray[i].last_name 
+ '&userid2=' 
+ window.friendsArray[i].uid 
+'&day1=' 
+ document.getElementById("userDay1").selectedIndex 
+'&day2=' 
+ document.getElementById("userDay2").selectedIndex  
+ '&month1=' 
+ document.getElementById("userMonth1").selectedIndex 
+'&month2=' 
+ document.getElementById("userMonth2").selectedIndex 
+'&sex1=' 
+ window.userSex1;
+'&sex2=' 
+ window.friendsArray[i].sex  ;
}

function sendHoroData()
{


if (document.getElementById("userMonth2").selectedIndex==0 || document.getElementById("userMonth1").selectedIndex==0 || document.getElementById("userDay2").selectedIndex==0 || document.getElementById("userDay1").selectedIndex==0)
{
if (document.getElementById("userMonth2").selectedIndex==0 || document.getElementById("userDay2").selectedIndex==0) alert ("Дата рождения второго пользователя не выбрана!"); 
if (document.getElementById("userMonth1").selectedIndex==0 || document.getElementById("userDay1").selectedIndex==0) alert ("Дата рождения первого пользователя не выбрана!"); 
}  
else
{
document.getElementById("button").style.display='none';
ajaxUrlCreate();
requestAJAX(window.ajaxUrl);
}



}

function clearForm()
{
}
function closePopup()
{
hideById('popBox');



setTimeout('document.getElementById(\'horoCloseButton\').style.display = \'none\'',150);
setTimeout('document.getElementById(\'resultBox\').style.display = \'none\'',150);
setTimeout('document.getElementById(\'popBox\').style.display = \'none\'',150);
setTimeout('document.getElementById(\'resultBox\').innerHTML=\'\'',150);
setTimeout('document.getElementById(\'button\').style.display=\'block\'',150);




setTimeout('document.getElementById(\'selectBoxUser2Name\').innerHTML = \'\'',150);
setTimeout('document.getElementById(\'selectBoxUser2Image\').src = \'\'',150);
setTimeout('document.getElementById(\'userDay2\').disabled=true',150);
setTimeout('document.getElementById(\'userMonth2\').disabled=true',150);
setTimeout('document.getElementById(\'userMonth2\').selectedIndex=0',150);
setTimeout('document.getElementById(\'userDay2\').selectedIndex=0',150);
//возвращаем видимость
setTimeout('document.getElementById(\'popBox\').style.opacity=1',250);

selectedUser2=false;
ajaxUrl=false;
userId2=false;
//document.getElementById('selectBox').style.display = "none";


}

function ajaxParsing(text)
{
var message;
var replay=text.split('%%%');
var i=window.selectedUser2;


document.getElementById('resultBox').innerHTML = replay[0].replace(/\n/g, '<br />');
document.getElementById('resultBox').style.display = "block";
document.getElementById('selectBox').style.display = "none";

if (document.getElementById("postToWall1").checked==true){
		 message=replay[1]+"\n"+ replay[0];
		 
		VK.api('wall.post',{owner_id:window.userId1, message:message, attachments:"photo131511912_299749110,http://vk.com/app3472072"},function(data) { 
		if (data.response) {  } else {
		alert('Ошибка! ' + data.error.error_code + ' ' + data.error.error_msg);
		}
		});

}

if (document.getElementById("postToWall2").checked==true){
		 message=replay[2]+"\n"+ replay[0];
//message=111;
		// VK.api('wall.post',{owner_id:window.userId2, message:message, attachments:"photo131511912_299749110"},function(data) { 
		// if (data.response) {} else {
		// alert('Ошибка! ' + data.error.error_code + ' ' + data.error.error_msg);	
		// }
		// });

}




}
