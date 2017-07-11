# Nothing Private
This project is a proof of concept that any website can identify and track you, even if you are using **Private Browsing** or **Incognito Mode** in your web browser. Many people think that they can hide their identity if they are using private browsing or incognito. This project will prove them they are wrong.

![Meme](http://privatebrowsingmyths.com/images/im-a-flower-dog.jpg)


## How to use
* Just visit http://www.nothingprivate.ml and enter your name
* Click **See the magic** button
* Now you can see a confirmation page
* Visit the same website in your Private browsing / Incognito mode
* See the Magic :star:

#### Dont scroll down and ruin the fun... Just follow the above steps.. :smile:
<br/><br/><br/>

## Hey! How?
Hope you are surprised! :smile:. Yes the website can remember your name even if you had visited it via the **Private browsing** or **Incognito Mode**. Yes nothing is private in this world anymore! This is what the big companies are doing with your identity. You think that going into private mode will wipe out all the traces. **Absolutely Not!** Actually, using the private browsing or incognito mode will just help you to clear your browsing history. Your internet service providers, search engines and your favourite websites **can still track you**. They can know you. They know what you like and dislike. They use your data to earn money. The video below explains everything:

[![Not free](https://img.youtube.com/vi/5pFX2P7JLwA/0.jpg)](https://www.youtube.com/watch?v=5pFX2P7JLwA)

Yes nothing is free...

#### References
* http://privatebrowsingmyths.com/
* https://panopticlick.eff.org/
* https://amiunique.org/
* http://www.pcworld.com/article/192648/browser_fingerprints.html
* https://en.wikipedia.org/wiki/Device_fingerprint
* https://nakedsecurity.sophos.com/2014/12/01/browser-fingerprints-the-invisible-cookies-you-cant-delete/

### Some tech stuff
Nothing private uses the browser fingerprinting feature of Client.js to obtain the fingerprint of your web browser. When you submit the form, it will save the fingerprint along with your name in a SQLite database using PHP as backend. Next time you visit the website, your browser fingerprint is matched with the column in the database and it will return your name.

Visit [db_server](https://github.com/gautamkrishnar/nothing-private/tree/master/db_server) for the server files.

#### Technologies used
* Client.js Browser fingerprinting
* PHP
* SQLite Database
* JSON
* HTML & CSS

## Contributing
Feel free to modify the code and open any pull requests.

###### Todo
- [ ] Add more links
- [ ] Fix any typos

## Contributors
Special thanks to these rockstars:
* [Sidhin S Thomas](https://github.com/ParadoxZero)
* [Meshpi](https://github.com/meshpi)
* [Timothée Boucher](https://github.com/Timothee)
* [Noah](https://github.com/naltun)
* [Khushal Sharma](https://github.com/logan1x)
* [Pooja Bhaumik](https://github.com/PoojaB26)
* [Daniel Davis](https://github.com/tagawa)

## Thanks
* [33giga.com.br](https://33giga.com.br/)  for the [blog post](https://33giga.com.br/site-prova-que-janela-anonima-nao-e-sigilosa-veja-como-navegar-sem-deixar-vestigios-na-rede/)

## Having trouble
If you are having trouble using this project, please open a [New issue](https://github.com/gautamkrishnar/nothing-private/issues/new)
## Spread the word
Liked the project? Just give it a star :star: and spread the word.
