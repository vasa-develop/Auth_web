<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/plain; charset=UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
<script type="text/javascript" src="./qrcodejs/jquery.min.js"></script>
<script type="text/javascript" src="./qrcodejs/qrcode.js"></script>
<style type="text/css">

.panel{

	
margin-right: 3px;
}

.button {
    background-color: #4CAF50;
    border: none;
    color: white;
	margin-right: 30%;   
	margin-left: 30%;
    text-decoration: none;
    display: block;
    font-size: 16px;
    cursor: pointer;
	width:30%;
    height:40px;
	margin-top: 5px;
	 
}
input[type=text]{
		width:100%;
		margin-top:5px;
		
	}


.chat_wrapper {
	width: 70%;
	height:472px;
	margin-right: auto;
	margin-left: auto;
	background: #3B5998;
	border: 1px solid #999999;
	padding: 10px;
	font: 14px 'lucida grande',tahoma,verdana,arial,sans-serif;
}
.chat_wrapper .message_box {
	background: #F7F7F7;
	height:350px;
		overflow: auto;
	padding: 10px 10px 20px 10px;
	border: 1px solid #999999;
}
.chat_wrapper  input{
	//padding: 2px 2px 2px 5px;
}
.system_msg{color: #BDBDBD;font-style: italic;}
.user_name{font-weight:bold;}
.user_message{color: #88B6E0;}

@media only screen and (max-width: 720px) {
    /* For mobile phones: */
    .chat_wrapper {
        width: 95%;
	height: 40%;
	}
    

	.button{ width:100%;
	margin-right:auto;   
	margin-left:auto;
	height:40px;}
	
	
	
	
	
				
}

</style>
</head>
<body>	
<?php  
$colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
$user_colour = array_rand($colours);
?>


<script src="jquery-3.1.1.js"></script>
<input id="text" type="text" value="soket index" style="width:80%" /><br />
<center><div id="qrcode" style="width:500px; height:500px; margin-top:15px;"></div></center>

<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
	width : 500,
	height : 500
});

function makeQrCode (cert) {		
	/*var elText = document.getElementById("text");
	
	if (!elText.value) {
		alert("Input a text");
		elText.focus();
		return;
	}*/
	
	qrcode.makeCode(cert);
}

function hextobin(socket_index){
	var hex = socket_index, // ASCII HEX: 37="7", 57="W", 71="q"
    bytes = [],
    str;

for(var i=0; i< hex.length-1; i+=2){
    bytes.push(parseInt(hex.substr(i, 2), 16));
}

str = String.fromCharCode.apply(String, bytes);

return(str);
}

function getCertificate(str){
	var res = str.split("|");
	return (res);
}

//makeQrCode("gf");

/*$("#text").
	on("blur", function () {
		makeQrCode();
	}).
	on("keydown", function (e) {
		if (e.keyCode == 13) {
			makeQrCode();
		}
	});*/
</script>




<script language="javascript" type="text/javascript">  
$(document).ready(function(){
	//create a new WebSocket object.
	var wsUri = "ws://162.144.124.122:2000/~lokasotech/stage_v2/app_panel/admin/admin_panel/authprotocol/server.php"; 	
	websocket = new WebSocket(wsUri); 
	
	websocket.onopen = function(ev) { // connection is open 
		$('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
	}

	$('#send-btn').click(function(){ //use clicks message send button	
		var mymessage = $('#message').val(); //get message text
		var myname = $('#name').val(); //get user name
		
		if(myname == ""){ //empty name?
			alert("Enter your Name please!");
			return;
		}
		if(mymessage == ""){ //emtpy message?
			alert("Enter Some message Please!");
			return;
		}
		document.getElementById("name").style.visibility = "hidden";
		
		var objDiv = document.getElementById("message_box");
		objDiv.scrollTop = objDiv.scrollHeight;
		//prepare json data
		var msg = {
		message: mymessage,
		name: myname,
		color : '<?php echo $colours[$user_colour]; ?>'
		};
		//convert and send data to server
		websocket.send(JSON.stringify(msg));
	});
	
	//#### Message received from server?
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type; //message type
		var umsg = msg.message; //message text
		var uname = msg.name; //user name
		var ucolor = msg.color; //color
		var socket_index = msg.index;

		if(type == 'usermsg') 
		{	
			var ip = msg.ip;
			var index = msg.index;
			$('#message_box').append("<div><span class=\"user_name\" style=\"color:#"+"007AFF"+"\">"+ip+"</span> : <span class=\"user_message\">"+index+"</span></div>");
			
		}
		if(type == 'system')
		{	
			makeQrCode((socket_index));
			//$info =  getCertificate(hextobin(socket_index));

	

			$('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
		}
		if(type == 'qrcode') {
			$('#message_box').append("<img src=\""+umsg+"\"/>");
		}
		
		$('#message').val(''); //reset text
		
		var objDiv = document.getElementById("message_box");
		objDiv.scrollTop = objDiv.scrollHeight;
	};
	
	websocket.onerror	= function(ev){$('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
	websocket.onclose 	= function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");}; 
});




</script>
<div class="chat_wrapper">
<div class="message_box" id="message_box"></div>
<div class="panel">
<input type="text" name="name" id="name" placeholder="Your Name" maxlength="15" />

<input type="text" name="message" id="message" placeholder="Message" maxlength="80" 
onkeydown = "if (event.keyCode == 13)document.getElementById('send-btn').click()"  />





</div>

<button id="send-btn" class=button>Send</button>

</div>

</body>
<?php
function getURL($info){
openssl_public_decrypt($info[1], $decrypted, $info[2]["key"]);
echo $decrypted;
}
?>
</html>
