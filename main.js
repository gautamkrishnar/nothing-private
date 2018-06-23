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
    var url = window.location.protocol + "//belikebill.azurewebsites.net/areyousafe/safedb.php";

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
    xhr.open("GET", url + param);
    xhr.send();
    iziToast.show({
        id: 'show',
        title: 'Hi, We need your help!',
        icon: 'icon-drafts',
        class: 'custom1',
        message: 'Support <b>Nothing Private</b> by voting for a new logo on GitHub',
        position: 'bottomCenter',
        image: 'private.jpg',
        balloon: false,
        close: false,
        progressBar: false,
        timeout: 20 * 1000,
        buttons: [
            ['<button>Vote</button>', function (instance, toast) {

                //instance.hide({ transitionOut: 'fadeOutUp' }, toast);
                window.open('https://github.com/gautamkrishnar/nothing-private/issues/45','_self');


            }],
            ['<button>Not Now!</button>', function (instance, toast) {

                instance.hide({ transitionOut: 'fadeOutUp' }, toast);


            }]
        ]
    });
}

function forgetme() {
    var param = "";
    var client = new ClientJS(); // Create A New Client Object
    var fingerprint = client.getFingerprint(); // Calculate Device/Browser Fingerprint

    param = encodeURI("?finger=" + fingerprint);

    var xhr = new XMLHttpRequest();
    var url = window.location.protocol + "//belikebill.azurewebsites.net/areyousafe/forgetme.php";

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var responsetext = JSON.parse(this.responseText);
            if (responsetext['state'] === 1) {
                document.getElementById("user").innerHTML = 'Are you <em>Anonymous?</em>';
                rendermain();
            }
        }
    };
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
