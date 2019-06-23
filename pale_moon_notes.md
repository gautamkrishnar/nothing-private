# Notes on Pale Moon
If you have same experience as presented below, then this is for you:
![Pale_Moon](https://imgur.com/0T9m30W.png)

## Hey, I have Pale Moon 28.5.2+! How can you still track me?
Because Pale Moon still allows fingerprinting by default, however you can turn it off with a little dwelling in your browser configuration.

### Step 1.
In browser address bar type `about:config` and press enter, then press "I promise to be careful"
![about:config](https://imgur.com/RBqRqdx.png)

### Step 2.
In configuration page search bar search for `poisondata` and look for option `canvas.poisondata`
![canvas.poisondata](https://imgur.com/MCmvzWh.png)

### Step 3.
Right-click on it and from menu choose `Toggle` option


![Toggle](https://imgur.com/SMXLYt3.png)

`canvas.poisondata` should change it's status from `default` to `modified` and value to `true`
![Toggled_on](https://imgur.com/Ewj42Jd.png)

### Final step
Restart your browser then visit page again. This time you should be safe.

![poisondata_resisted](https://imgur.com/bx9UlpT.png)