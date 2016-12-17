

function savefingerprint(check)
{
    var param = "";
    var client = new ClientJS(); // Create A New Client Object
    var fingerprint = client.getFingerprint(); // Calculate Device/Browser Fingerprint
    
    if (check==0)
        {
            param = encodeURI("?finger="+fingerprint+"&check=1");   
        }
    else{
            var name = document.getElementById("name").value;
            
            if (name == undefined||name =="")
            {
                name="NoNameEntered :D";
            }
            param = encodeURI("?finger="+fingerprint+"&name="+name);
            
    }
   
    var xhr = new XMLHttpRequest();
    var url="http://belikebill.azurewebsites.net/areyousafe/safedb.php";
    
    

    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var arr = JSON.parse(this.responseText);
            if(arr['status']==0)
                {
                    rendernewtabpage(arr['name']);
                }
            else if(arr['status']==1)
                {
                    rendersubmit(name);
                }
            else if(arr['status']==3)
                {
                    rendermain();
                }
        }
    };
    xhr.open("GET", url+param);
    xhr.send();
}

function rendernewtabpage(name){
    document.getElementById("maindiv").innerHTML='<h3>Welcome back to the website <b>'+name+'</b>. If i can remember your name even though you were using the <b>Private browsing</b> / <b>Icongnito mode</b>, I can surely track you.<br/><br/>. This is what big companies are doing. They are spying on you more when you are using the <b>Private browsing</b> or <b>Icognito mode</b>.<br/><br/><a href="https://github.com/gautamkrishnar/nothing-private" target="_blank">Read more</a><br/><br/>If you liked this project, dont forget to give it a Star:<br/></h3>';
    document.getElementById("user").innerHTML="You are <em>"+name+"!</em>"
}
function rendersubmit(name)
{
    document.getElementById("maindiv").innerHTML='<h3><b>Thank you! '+name+'</b> Lets see the magic... <br/><br/>Now visit <b>www.nothingprivate.ml</b> normaly to see the magic...</h3>';
    
}
function rendermain()
{
    document.getElementById("maindiv").innerHTML='<div id="maindiv"><h3>Do you think that your browser\'s <b>Private browsing mode</b> or <b>Icognito mode</b> will make you anonymous?<br/><br/> Sorry to disappoint you, <b>You are wrong!.</b> Everyone can track you. Lets check it out yourself. Just type your name below.</h3><input type="text" name="name" id="name" class="txt" placeholder="Your Name" /><br/><br/><input type="button" class="btn" onclick="savefingerprint(1)" value="See the magic !" /><br/></div>';
}
