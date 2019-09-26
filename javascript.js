
//Code source https://www.developphp.com/video/JavaScript/Custom-Alert-Box-Programming-Tutorial
function CustomAlert(){
    this.render = function(dialog){
        var winW = window.innerWidth;
        var winH = window.innerHeight;
        var dialogoverlay = document.getElementById('dialogoverlay');
        var dialogbox = document.getElementById('dialogbox');
        dialogoverlay.style.display = "block";
        dialogoverlay.style.height = winH+"px";
        dialogbox.style.left = (winW/2) - (550 * .5)+"px";
        dialogbox.style.top = "100px";
        dialogbox.style.display = "block";
        document.getElementById('dialogboxhead').innerHTML = "<h1>Ooops!</h1>";
        document.getElementById('dialogboxbody').innerHTML = dialog;
        document.getElementById('dialogboxfoot').innerHTML = "<button id='alert_button' onclick='Alert.ok()'><h2>OK</h2></button>";
    }
	this.ok = function(){
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
	}
}
var Alert = new CustomAlert();


//Error message if search is empty
function validateForm() {
    var x = document.forms["searchform"]["searchbox"].value;
    if (x == "")
	{
        Alert.render('<h2>Please search for something.</h2>');
        return false;
    }
}