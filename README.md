# Nothing Private [![Build Status](https://travis-ci.org/gautamkrishnar/nothing-private.svg?branch=master)](https://travis-ci.org/gautamkrishnar/nothing-private)

This project demosntrated the ability of a  website to identify and track users, even if they are using **private browsing** or **incognito mode** in their web browsers. Many people think that they can hide their identity if they use private browsing or incognito mode. This project will prove that they are wrong.

![Meme](https://i.imgur.com/Zdhatbj.jpg)

## How to use the website

* Visit <http://www.nothingprivate.ml> and enter your name
* Click the **"See the magic"** button
* Visit the same website in Private browsing / Incognito mode
* See the magic :star:

### Don't scroll down and ruin the fun... Just follow the steps above... :smile:

<br/><br/><br/><br/><br/><br/>

## Hey! How?

Hope you are surprised! :smile: Yes, the website can remember your name even if you had visited it via **private browsing** or **incognito mode**. Yes, nothing is private in this world anymore! This is what the big companies are doing with your identity. You think that going into private mode will wipe out all the traces? **Absolutely not!** In reality, using private browsing or incognito mode will just help you to clear your browsing history. Your internet service provider, search engines, and your favorite websites **can still track you**. They know what you like and dislike. They use your data to earn money. The video below explains everything:

[![Not free](https://img.youtube.com/vi/5pFX2P7JLwA/0.jpg)](https://www.youtube.com/watch?v=5pFX2P7JLwA)

Yes, nothing is free...

### References

* <https://privatebrowsingmyths.com/>
* <https://panopticlick.eff.org/>
* <https://amiunique.org/>
* <https://www.pcworld.com/article/192648/browser_fingerprints.html>
* <https://en.wikipedia.org/wiki/Device_fingerprint>
* <https://nakedsecurity.sophos.com/2014/12/01/browser-fingerprints-the-invisible-cookies-you-cant-delete/>

### Some tech stuff

Nothing Private uses the browser fingerprinting feature of [Client.js](https://github.com/jackspirou/clientjs) to obtain the fingerprint of your web browser. When you submit the form, this fingerprint is saved, along with your name, in an SQLite database using PHP as a backend. The next time you visit the website, your browser fingerprint is matched with the column in the database and your name is returned.

Visit [db_server](https://github.com/gautamkrishnar/nothing-private/tree/master/db_server) for the server files.

#### Technology used

* [Client.js Browser fingerprinting](https://github.com/jackspirou/clientjs)
* [PHP](https://secure.php.net/)
* [SQLite Database](https://www.sqlite.org/)
* [JSON](https://www.json.org/)
* [HTML](https://developer.mozilla.org/es/docs/Web/HTML) & [CSS](https://developer.mozilla.org/es/docs/Web/CSS)

## Contributing

Feel free to modify the code and open any pull requests.

### Todo

* [ ] Add more links
* [ ] Fix any typos

## Contributors

Special thanks to these rock stars:

* [Sidhin S Thomas](https://github.com/ParadoxZero)
* [Meshpi](https://github.com/meshpi)
* [Timothée Boucher](https://github.com/Timothee)
* [Noah](https://github.com/naltun)
* [Khushal Sharma](https://github.com/logan1x)
* [Pooja Bhaumik](https://github.com/PoojaB26)
* [Daniel Davis](https://github.com/tagawa)
* [eV](https://github.com/electron-volt)
* [Edipo Vinicius da Silva](https://github.com/edipox)
* [Bruno Massa](https://github.com/brunomassa)
* [Nimit Bhargava](https://github.com/nimitbhargava)
* [Riddler](https://github.com/Waterloo)
* [Muhammad Ubaid Raza](https://github.com/mubaidr)
* [Fisayo Afolayan](https://github.com/fisayoafolayan)
* [Miles McCain](https://github.com/milesmcc)
* [Jobin Philip Abraham](https://github.com/jophab)

## Thanks

* Thanks to the **100K**:star: users.
* Thanks to [DuckDuckGo](https://ddg.gg) for this [tweet](https://twitter.com/duckduckgo/status/884763902847971329).
* [33giga.com.br](https://33giga.com.br/)  for the [blog post](https://33giga.com.br/site-prova-que-janela-anonima-nao-e-sigilosa-veja-como-navegar-sem-deixar-vestigios-na-rede/).
* Thanks to everyone who [tweeted](https://www.google.co.in/search?q=intext%3Anothingprivate.ml+site%3Atwitter.com) about this.
* Thanks to TechCycle for this [demo video](https://www.youtube.com/watch?v=R_Dbu0BSjus).
* Thanks Joe for [https://pnut.io/](https://pnut.io/@joe/133641).
* Thanks to the @Mozilla community for [discussing](https://plus.google.com/+la%C3%A9rciohenriquedasilva/posts/UAZPhC7qrfi) privacy issues. Some users even reported that nothing private is even working correctly with the latest version of Firefox Focus. They created an [issue](https://github.com/mozilla-mobile/focus-android/issues/900) for it.
* [https://softwarelivre.org/](https://softwarelivre.org/piratas/blog/site-prova-que-janela-anonima-nao-e-sigilosa).

## Having trouble?

If you are having trouble using this project, please open a [new issue](https://github.com/gautamkrishnar/nothing-private/issues/new) and describe your problem.

## Spread the word!

Liked the project? Just give it a star :star: and spread the word!
