# Notes on Firefox Quantum
If you have same experience as presented below, then this is for you:
![Firefox_quantum](https://i.imgur.com/ZxBIoTx.png)

## Hey, I have Firefox 57.0.4+! How can you still track me?
Because Firefox Quantum still allows fingerprinting by default, however you can turn it off with a little dwelling in your browser configuration.

### Step 1.
In browser address bar, type `about:config` and press enter, then press "I accept the risk"
![about:config](https://i.imgur.com/J5VtOsH.png)

### Step 2.
In configuration page search bar, search for `fingerprinting` and look for option `privacy.resistFingerprinting`
![privacy.resistFingerprinting](https://i.imgur.com/2ZhL0VB.png)

### Step 3.
Right-click on it and from menu choose `Toggle` option


![Toggle](https://i.imgur.com/DUnt2Zd.png)

`privacy.resistFingerprinting` should change its status from `default` to `modified` and value to `true`
![Toggled_on](https://i.imgur.com/jQ9TghN.png)

### Final step
Restart your browser, then visit page again. This time firefox should ask you if you want to let page extract data from canvas.
![Request](https://i.imgur.com/BgCInpu.png)

If you choose to disallow this, then you should be safe.
![Fingerprinting_resisted](https://i.imgur.com/LSnYsJS.png)