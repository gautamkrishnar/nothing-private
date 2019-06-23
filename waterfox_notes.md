# Notes on Waterfox
If you have same experience as presented below, then this is for you:
![Waterfox](https://imgur.com/penUoCK.png)

## Hey, I have Waterfox 56.2.11+! How can you still track me?
Because Waterfox still allows fingerprinting by default, however you can turn it off with a little dwelling in your browser configuration.

### Step 1.
In browser address bar type `about:config` and press enter, then press "I accept the risk"
![about:config](https://imgur.com/gMAMKE5.png)

### Step 2.
In configuration page search bar search for `fingerprinting` and look for option `privacy.resistFingerprinting`
![privacy.resistFingerprinting](https://imgur.com/UAyl8dR.png)

### Step 3.
Right-click on it and from menu choose `Toggle` option


![Toggle](https://imgur.com/SMXLYt3.png)

`privacy.resistFingerprinting` should change it's status from `default` to `modified` and value to `true`
![Toggled_on](https://imgur.com/dFaFygg.png)

### Final step
Restart your browser then visit page again. This time you should be safe.

![Fingerprinting_resisted](https://imgur.com/NBBom84.png)