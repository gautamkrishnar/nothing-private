/*jshint sub:true*/
"use strict";

function savefingerprint(check) {

    var param = "";
    var client = new ClientJS(); // Create A New Client Object
    var fingerprint = client.getFingerprint(); // Calculate Device/Browser Fingerprint

    if (check === 0) {
        param = encodeURI("?finger=" + fingerprint + "&check=1");
    } else {
        var name = document.getElementById("name").value;

        if (!name.trim()) {
            name = "NoNameEntered :D";
        }
        param = encodeURI("?finger=" + fingerprint + "&name=" + name);
    }

    var xhr = new XMLHttpRequest();
    var url = window.location.protocol + "//nothingprivate.000webhostapp.com/safedb.php";

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var arr = JSON.parse(this.responseText);
            if (arr['status'] === 0) {
                rendernewtabpage(arr['name']);
            } else if (arr['status'] === 1) {
                rendersubmit(name);
            } else if (arr['status'] === 3) {
                rendermain();
            }
        }
    };
    xhr.onerror = errorHandler();
    xhr.open("GET", url + param);
    xhr.send();
}

function forgetme() {
    var client = new ClientJS(); // Create A New Client Object
    var fingerprint = client.getFingerprint(); // Calculate Device/Browser Fingerprint

    var param = encodeURI("?finger=" + fingerprint);

    var xhr = new XMLHttpRequest();
    var url = window.location.protocol + "//nothingprivate.000webhostapp.com/forgetme.php";

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var responsetext = JSON.parse(this.responseText);
            if (responsetext['state'] === 1) {
                document.getElementById("user").innerHTML = 'Are you <em>Anonymous?</em>';
                rendermain();
            }
        }
    };
    xhr.onerror = errorHandler();
    xhr.open("GET", url + param);
    xhr.send();
}

function rendernewtabpage(name) {
    document.getElementById("maindiv").innerHTML = '<h3>Welcome back to the website <b>' + htmlEncode(name) + '</b>. If I can remember your name even though you are using <b>private browsing</b> / <b>incognito mode</b>, I can surely track you.<br/><br/> This is what big companies are doing. They are spying on you even when you are using <b>private browsing</b> or <b>incognito mode</b>.<br/><br/><a href="https://github.com/gautamkrishnar/nothing-private" target="_blank">Read more</a><br/><br/><input type="button" class="btn" onclick="forgetme()" value="Forget Me!" /><br><br>If you liked this project, don\'t forget to give it a Star:<br/></h3>';
    document.getElementById("user").innerHTML = 'You are <em>' + htmlEncode(name) + '!</em>';
}

function htmlEncode(value){
    // Code to prevent xss attack
    var temp_div = document.createElement("div");
    temp_div.textContent = value;
    return temp_div.innerHTML;
}

function rendersubmit(name) {
    document.getElementById("maindiv").innerHTML = '<h3><b>Thank you, ' + htmlEncode(name) + '! </b> Let\'s see the magic... <br/><br/>Now open a <b>private browsing window</b> or <b>incognito window</b> on your browser and visit <b>www.nothingprivate.ml</b> to see the magic...</h3><br><br><input type="button" class="btn" onclick="forgetme()" value="Forget Me!" /><br><br>';

}

function rendermain() {
    document.getElementById("maindiv").innerHTML = '<div id="maindiv"><h3>Do you think that switching to your browser\'s <b>private browsing mode</b> or <b>incognito mode</b> will make you anonymous?<br/><br/> Sorry to disappoint you, <b>but you are wrong!.</b> Everyone can track you. You can check it out for yourself. Just type your name below.</h3><input type="text" name="name" id="name" class="txt" placeholder="Your Name" autofocus/><br/><br/><input type="button" class="btn" onclick="savefingerprint(1)" value="See the magic!" /><br/></div>';
}

function reload() {
    document.getElementById("maindiv").innerHTML = '<div id="maindiv"><h3>Loading... please wait...</h3></div>';
    savefingerprint(0);
}

function errorHandler() {
    document.getElementById("maindiv").innerHTML = '<h3><b>An API error occurred! Please try again.</b> <br/><br/> Nothing Private is using free hosting (00Webhost) to host the APIs.<br/><br/> It doesn\'t guarantee 100% uptime. Sorry for the inconvenience caused.  </h3><br><br><input type="button" class="btn" onclick="reload(0)" value="Retry" /><br><br>';
}
