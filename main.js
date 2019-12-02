/*jshint sub:true*/
"use strict";

const API_ROOT = "https://nothing-private-api.gautamkrishnar.com";
let Fingerprint = ''; // global variable to save the fingerprint

/**
 * Function that calculates fingerprint
 */
function calculateFingerprint() {
    let client = new ClientJS(); // Create A New Client Object
    return client.getFingerprint(); // Calculate Device/Browser Fingerprint
}

/**
 * function that calculates the fingerprint when the page finishes loading. It will save fingerprint to
 * the global variable
 */
function pageLoader() {
    // noinspection JSUnresolvedVariable
    if (window.requestIdleCallback) {
        // Browsers with request idle callback
        window.requestIdleCallback(function () {
            Fingerprint = calculateFingerprint();
            saveFingerPrintAPICall(true);
        });
    } else {
        // Fallback to native setTimeout for old browsers
        setTimeout(function () {
            Fingerprint = calculateFingerprint();
            saveFingerPrintAPICall(true);
        }, 500);
    }
}

/**
 * Function to do the API Call to the backend server to check fingerprint. Also handles rendering of messages.
 * @param check - true: Initial api call
 */
function saveFingerPrintAPICall(check) {
    let param = "";
    let name = "";
    // Constructing GET Params
    if (check) {
        // Checks whether the fingerprint already exists in DB.
        param = encodeURI("?finger=" + Fingerprint + "&check=1");
    } else {
        // Saves the name with fingerprint to DB.
        name = document.getElementById("name").value;

        if (!name.trim()) {
            name = "NoNameEntered :D";
        }
        param = encodeURI("?finger=" + Fingerprint + "&name=" + name);
    }

    // Constructing request
    const xhr = new XMLHttpRequest();
    const url = API_ROOT + "/safedb.php";

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const arr = JSON.parse(this.responseText);
            if (arr['status'] === 0) {
                // Fingerprint already exists.
                renderNewTabPage(arr['name']);
            } else if (arr['status'] === 1) {
                // Fingerprint saved successfully
                renderSubmit(name);
            } else if (arr['status'] === 3) {
                // New user. No fingerprint found in the DB.
                renderMain();
            }
        }
    };
    xhr.open("GET", url + param);
    xhr.onerror = function () {
        errorHandler(); // Error handler
    };
    xhr.send();
}

/**
 * Function to remove the saved fingerprint from the database.
 */
function forgetMe() {
    const param = encodeURI("?finger=" + Fingerprint);

    // Constructing request
    const xhr = new XMLHttpRequest();
    const url = API_ROOT + "/forgetme.php";

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const responsetext = JSON.parse(this.responseText);
            if (responsetext['state'] === 1) {
                document.getElementById("user").innerHTML = 'Are you <em>Anonymous?</em>';
                renderMain();
            }
        }
    };
    xhr.open("GET", url + param);
    xhr.onerror = function () {
        errorHandler(); // Error handler
    };
    xhr.send();
}

/**
 * Renders the page when the fingerprint is found in the db with the users name.
 * @param name saved name of the user in the DB
 */
function renderNewTabPage(name) {
    document.getElementById("maindiv").innerHTML = '<h3>Welcome back to the website <b>' + htmlEncode(name) + '</b>. If I can remember your name even though you are using <b>private browsing</b> / <b>incognito mode</b>, I can surely track you.<br/><br/> This is what big companies are doing. They are spying on you even when you are using <b>private browsing</b> or <b>incognito mode</b>.<br/><br/><a href="https://github.com/gautamkrishnar/nothing-private" target="_blank">Read more</a><br/><br/><input type="button" class="btn" onclick="forgetMe()" value="Forget Me!" /><br><br>If you liked this project, don\'t forget to give it a Star:<br/></h3>';
    document.getElementById("user").innerHTML = 'You are <em>' + htmlEncode(name) + '!</em>';
}

/**
 * Function to encode HYML value to prevent XSS Attack.
 * @param value Value to encode
 * @return {string} encoded value
 */
function htmlEncode(value) {
    // Code to prevent xss attack
    const temp_div = document.createElement("div");
    temp_div.textContent = value;
    return temp_div.innerHTML;
}

/**
 * Renders the page after submitting the fingerprint to the API
 * @param name
 */
function renderSubmit(name) {
    document.getElementById("maindiv").innerHTML = '<h3><b>Thank you, ' + htmlEncode(name) + '! </b> Let\'s see the magic... <br/><br/>Now open a <b>private browsing window</b> or <b>incognito window</b> on your browser and visit <b>www.nothingprivate.ml</b> to see the magic...</h3><br><br><input type="button" class="btn" onclick="forgetMe()" value="Forget Me!" /><br><br>';

}

/**
 * Renders the page on initial loading of the page (No fingerprint is found)
 */
function renderMain() {
    document.getElementById("maindiv").innerHTML = '<div id="maindiv"><h3>Do you think that switching to your browser\'s <b>private browsing mode</b> or <b>incognito mode</b> will make you anonymous?<br/><br/> Sorry to disappoint you, <b>but you are wrong!.</b> Everyone can track you. You can check it out for yourself. Just type your name below.</h3><input type="text" name="name" id="name" class="txt" placeholder="Your Name" autofocus/><br/><br/><input type="button" class="btn" onclick="this.disabled=true; saveFingerPrintAPICall(false)" value="See the magic!" /><br/></div>';
}

/**
 * Redo the API Call on clicking the retry button
 */
function reload() {
    document.getElementById("maindiv").innerHTML = '<div id="maindiv"><h3>Loading... please wait...</h3></div>';
    saveFingerPrintAPICall(false);
}

/**
 * Error handling code
 */
function errorHandler() {
    document.getElementById("maindiv").innerHTML = '<h3><b>An API error occurred! Please try again.</b> <br/><br/> Nothing Private is using free hosting (00Webhost) to host the APIs.<br/><br/> It doesn\'t guarantee 100% uptime. Sorry for the inconvenience caused. Please visit <a href="https://status.nothingprivate.ml/">status.nothingprivate.ml</a> for the checking the API Status. </h3><br><br><input type="button" class="btn" onclick="reload(0)" value="Retry" /><br><br>';
}


// Calls loading function manually
pageLoader();