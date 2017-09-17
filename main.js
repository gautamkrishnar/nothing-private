/*jshint sub:true*/

function savefingerprint(check)
{
	"use strict";
    var param = "";
    var client = new ClientJS(); // Create A New Client Object
    var fingerprint = client.getFingerprint(); // Calculate Device/Browser Fingerprint
    
    if (check===0)
        {
            param = encodeURI("?finger="+fingerprint+"&check=1");   
        }
    else{
            var name = document.getElementById("name").value;
            
            if (name === undefined||name ==="")
            {
                name="NoNameEntered :D";
            }
            param = encodeURI("?finger="+fingerprint+"&name="+name);
            
    }
   
    var xhr = new XMLHttpRequest();
    var url= window.location.protocol + "//belikebill.azurewebsites.net/areyousafe/safedb.php";
    
    

    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            var arr = JSON.parse(this.responseText);
            if(arr['status']===0)
                {
                    rendernewtabpage(arr['name']);
                }
            else if(arr['status']===1)
                {
                    rendersubmit(name);
                }
            else if(arr['status']===3)
                {
                    rendermain();
                }
        }
    };
    xhr.open("GET", url+param);
    xhr.send();
}

function forgetme(){
	
	"use strict";
    var param = "";
    var client = new ClientJS(); // Create A New Client Object
    var fingerprint = client.getFingerprint(); // Calculate Device/Browser Fingerprint
    
    param = encodeURI("?finger="+fingerprint);
            
   
    var xhr = new XMLHttpRequest();
    var url= window.location.protocol +"//belikebill.azurewebsites.net/areyousafe/forgetme.php";
    
    

    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            var responsetext=JSON.parse(this.responseText);
			if(responsetext['state']===1){
                document.getElementById("user").innerHTML = 'Are you <em>Anonymous?</em>';
				rendermain();
			}
        }
    };
    xhr.open("GET", url+param);
    xhr.send();
}

function rendernewtabpage(name){
	'use strict';
    document.getElementById("maindiv").innerHTML='<h3>Welcome back to the website <b>'+name+'</b>. If I can remember your name even though you are using the <b>Private browsing</b> / <b>Incognito mode</b>, I can surely track you.<br/><br/> This is what big companies are doing. They are spying on you even  when you are using the <b>Private browsing</b> or <b>Incognito mode</b>.<br/><br/><a href="https://github.com/gautamkrishnar/nothing-private" target="_blank">Read more</a><br/><br/><input type="button" class="btn" onclick="forgetme()" value="Forget Me!" /><br><br>If you liked this project, don\'t forget to give it a Star:<br/></h3>';
    document.getElementById("user").innerHTML='You are <em>'+name+'!</em>';
}
function rendersubmit(name)
{
	'use strict';
    document.getElementById("maindiv").innerHTML='<h3><b>Thank you! '+name+'</b> Let\'s see the magic... <br/><br/>Now open a <b>Private browsing</b> or <b>Incognito window</b> on your browser and visit <b>www.nothingprivate.ml</b> to see the magic...</h3><br><br><input type="button" class="btn" onclick="forgetme()" value="Forget Me!" /><br><br>';
    
}
function rendermain()
{
	'use strict';
    document.getElementById("maindiv").innerHTML='<div id="maindiv"><h3>Do you think that switching to your browser\'s <b>Private browsing mode</b> or <b>Incognito mode</b> will make you anonymous?<br/><br/> Sorry to disappoint you, <b>you are wrong!.</b> Everyone can track you. You can check it out for yourself. Just type your name below.</h3><input type="text" name="name" id="name" class="txt" placeholder="Your Name" /><br/><br/><input type="button" class="btn" onclick="savefingerprint(1)" value="See the magic!" /><br/></div>';
}
