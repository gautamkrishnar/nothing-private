# Nothing Private [![Build Status](https://travis-ci.org/gautamkrishnar/nothing-private.svg?branch=master)](https://travis-ci.org/gautamkrishnar/nothing-private) [![Build Status](https://gautamkrishnar.visualstudio.com/Nothing%20Private%20Backend/_apis/build/status/Nothing%20Private%20backend%20deployment)](https://gautamkrishnar.visualstudio.com/Nothing%20Private%20Backend/_build/latest?definitionId=6) [![All Contributors](https://img.shields.io/badge/all_contributors-33-orange.svg?style=flat-square)](#contributors) [![ProductHunt](https://img.shields.io/badge/View_on-Product_Hunt-orange.svg?style=flat-square)](https://www.producthunt.com/posts/nothing-private)

This project is a proof of concept that any website can identify and track you, even if you are using **private browsing** or **incognito mode** in your web browser. Many people think that they can hide their identity if they are using private browsing or incognito mode. This project will prove that they are wrong.

![Meme](https://i.imgur.com/Zdhatbj.jpg)

## How to use the website

* Visit <http://www.nothingprivate.ml> and enter your name
* Click the **"See the magic"** button
* Visit the same website in Private browsing / Incognito mode
* See the magic :star:

### Don't scroll down and ruin the fun... Just follow the steps above... :smile:

<br/><br/><br/><br/><br/><br/>

## Hey! How?

Hope you are surprised! :smile: Yes, the website can remember your name even if you had visited it via **private browsing** or **incognito mode**. Yes, nothing is private in this world anymore! This is what the big companies are doing with your identity. You think that going into private mode will wipe out all the traces? **Absolutely not!** In reality, using private browsing or incognito mode will just help you to clear your browsing history. Your internet service provider, search engines, and your favorite websites **can still track you**. They know your likes and dislikes. They use your data to earn money. The video below explains everything:

[![Not free](https://img.youtube.com/vi/5pFX2P7JLwA/0.jpg)](https://www.youtube.com/watch?v=5pFX2P7JLwA)

Yes, nothing is free...

### References

* <https://privatebrowsingmyths.com/>
* <https://panopticlick.eff.org/>
* <https://amiunique.org/>
* <https://www.pcworld.com/article/192648/browser_fingerprints.html>
* <https://en.wikipedia.org/wiki/Device_fingerprint>
* <https://nakedsecurity.sophos.com/2014/12/01/browser-fingerprints-the-invisible-cookies-you-cant-delete/>
* <https://spreadprivacy.com/browser-fingerprinting/>

### Some tech stuff

Nothing Private uses the browser fingerprinting feature of [Client.js](https://github.com/jackspirou/clientjs) to obtain the fingerprint of your web browser. When you submit the form, this fingerprint is saved, along with your name in an MySQL database using PHP as a backend. The next time you visit the website your browser fingerprint is matched with the column in the database and your name is returned.

Visit [db_server](https://github.com/gautamkrishnar/nothing-private/tree/master/db_server) for the server files. (See [historical SQLite version](https://github.com/gautamkrishnar/nothing-private/tree/2abc011c39e500279169f70118048d6592860cce/db_server_sqllite) of the backend code).

#### Technologies used

* [Client.js Browser fingerprinting](https://github.com/jackspirou/clientjs)
* [PHP](https://secure.php.net/)
* [MySQL Database](https://www.mysql.com/)
* [JSON](https://www.json.org/)
* [HTML](https://developer.mozilla.org/es/docs/Web/HTML) & [CSS](https://developer.mozilla.org/es/docs/Web/CSS)

## Contributing

Feel free to modify the code and open any pull requests.

### Todo

* [ ] Add more links
* [ ] Fix any typos

## Contributors
Special thanks to these rockstars:

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
| [<img src="https://avatars2.githubusercontent.com/u/3037552?v=4" width="100px;"/><br /><sub><b>Miles McCain</b></sub>](https://rmrm.io)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=milesmcc "Code") [📖](https://github.com/gautamkrishnar/nothing-private/commits?author=milesmcc "Documentation") | [<img src="https://avatars0.githubusercontent.com/u/8611427?v=4" width="100px;"/><br /><sub><b>eV</b></sub>](https://github.com/electron-volt)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=electron-volt "Code") [📖](https://github.com/gautamkrishnar/nothing-private/commits?author=electron-volt "Documentation") | [<img src="https://avatars2.githubusercontent.com/u/159328?v=4" width="100px;"/><br /><sub><b>Timothée Boucher</b></sub>](https://github.com/Timothee)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=Timothee "Code") [📖](https://github.com/gautamkrishnar/nothing-private/commits?author=Timothee "Documentation") | [<img src="https://avatars2.githubusercontent.com/u/2222702?v=4" width="100px;"/><br /><sub><b>Muhammad Ubaid Raza</b></sub>](https://mubaidr.github.io)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=mubaidr "Code") [📖](https://github.com/gautamkrishnar/nothing-private/commits?author=mubaidr "Documentation") | [<img src="https://avatars2.githubusercontent.com/u/1580541?v=4" width="100px;"/><br /><sub><b>Edipo Vinicius da Silva</b></sub>](https://github.com/edipox)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=edipox "Documentation") | [<img src="https://avatars3.githubusercontent.com/u/94173?v=4" width="100px;"/><br /><sub><b>Daniel Davis</b></sub>](http://twitter.com/ourmaninjapan)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=tagawa "Code") [📖](https://github.com/gautamkrishnar/nothing-private/commits?author=tagawa "Documentation") [🤔](#ideas-tagawa "Ideas, Planning, & Feedback") [📢](#talk-tagawa "Talks") |
| :---: | :---: | :---: | :---: | :---: | :---: |
| [<img src="https://avatars1.githubusercontent.com/u/38878456?v=4" width="100px;"/><br /><sub><b>Alleras the Sphinx </b></sub>](https://github.com/Anuradha-Iyer)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=Anuradha-Iyer "Documentation") | [<img src="https://avatars3.githubusercontent.com/u/19394896?v=4" width="100px;"/><br /><sub><b>Pooja Bhaumik</b></sub>](http://poojab26.github.io)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=PoojaB26 "Code") | [<img src="https://avatars3.githubusercontent.com/u/13940974?v=4" width="100px;"/><br /><sub><b>JOBIN PHILIP ABRAHAM</b></sub>](https://github.com/jophab)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=jophab "Documentation") | [<img src="https://avatars2.githubusercontent.com/u/14165258?v=4" width="100px;"/><br /><sub><b>Sidhin S Thomas</b></sub>](http://www.sidhin.in)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=ParadoxZero "Documentation") | [<img src="https://avatars2.githubusercontent.com/u/7507990?v=4" width="100px;"/><br /><sub><b>Noah</b></sub>](https://github.com/naltun)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=naltun "Documentation") [🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Analtun "Bug reports") | [<img src="https://avatars3.githubusercontent.com/u/31700998?v=4" width="100px;"/><br /><sub><b>jamesoreillyms</b></sub>](https://github.com/jamesoreillyms)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=jamesoreillyms "Documentation") |
| [<img src="https://avatars2.githubusercontent.com/u/7173984?v=4" width="100px;"/><br /><sub><b>Roger</b></sub>](https://github.com/rogersachan)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=rogersachan "Code") | [<img src="https://avatars1.githubusercontent.com/u/17156717?v=4" width="100px;"/><br /><sub><b>Fisayo Afolayan</b></sub>](https://www.fisayoafolayan.com)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=fisayoafolayan "Documentation") | [<img src="https://avatars2.githubusercontent.com/u/971925?v=4" width="100px;"/><br /><sub><b>Riddler</b></sub>](https://github.com/Waterloo)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=Waterloo "Code") [🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3AWaterloo "Bug reports") | [<img src="https://avatars1.githubusercontent.com/u/8358694?v=4" width="100px;"/><br /><sub><b>Nimit Bhargava</b></sub>](http://www.nimitbhargava.com)<br />[📖](https://github.com/gautamkrishnar/nothing-private/commits?author=nimitbhargava "Documentation") | [<img src="https://avatars1.githubusercontent.com/u/10465864?v=4" width="100px;"/><br /><sub><b>Bruno Massa</b></sub>](https://github.com/brunomassa)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=brunomassa "Code") [🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Abrunomassa "Bug reports") | [<img src="https://avatars0.githubusercontent.com/u/10944610?v=4" width="100px;"/><br /><sub><b>Khushal Sharma</b></sub>](https://logan1x.github.io/blag)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=Logan1x "Code") [🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3ALogan1x "Bug reports") |
| [<img src="https://avatars3.githubusercontent.com/u/7849552?v=4" width="100px;"/><br /><sub><b>CrisMen</b></sub>](https://github.com/CrisMen)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3ACrisMen "Bug reports") | [<img src="https://avatars2.githubusercontent.com/u/29022336?v=4" width="100px;"/><br /><sub><b>floppypanda</b></sub>](https://github.com/floppypanda)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Afloppypanda "Bug reports") | [<img src="https://avatars3.githubusercontent.com/u/3692175?v=4" width="100px;"/><br /><sub><b>logenkain</b></sub>](https://github.com/logenkain)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Alogenkain "Bug reports") | [<img src="https://avatars3.githubusercontent.com/u/25676806?v=4" width="100px;"/><br /><sub><b>Tilepaper</b></sub>](https://github.com/Tilepaper)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3ATilepaper "Bug reports") | [<img src="https://avatars2.githubusercontent.com/u/14135553?v=4" width="100px;"/><br /><sub><b>Karthik Varma</b></sub>](https://www.facebook.com/leovarmak)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Aleovarmak "Bug reports") | [<img src="https://avatars3.githubusercontent.com/u/3109072?v=4" width="100px;"/><br /><sub><b>Tobias Nießen</b></sub>](https://tnie.de)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Atniessen "Bug reports") |
| [<img src="https://avatars2.githubusercontent.com/u/5212261?v=4" width="100px;"/><br /><sub><b>Darpan</b></sub>](https://github.com/inboxdarpan)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Ainboxdarpan "Bug reports") | [<img src="https://avatars0.githubusercontent.com/u/11646902?v=4" width="100px;"/><br /><sub><b>Permik</b></sub>](http://permik.xyz)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3APermik "Bug reports") | [<img src="https://avatars2.githubusercontent.com/u/26408649?v=4" width="100px;"/><br /><sub><b>sleepyeinstein</b></sub>](https://github.com/sleepyeinstein)<br />[🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3Asleepyeinstein "Bug reports") | [<img src="https://avatars1.githubusercontent.com/u/35927327?v=4" width="100px;"/><br /><sub><b>Jan Stefański</b></sub>](https://github.com/JanStefanski)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=JanStefanski "Code") [🐛](https://github.com/gautamkrishnar/nothing-private/issues?q=author%3AJanStefanski "Bug reports") | [<img src="https://avatars3.githubusercontent.com/u/26848967?v=4" width="100px;"/><br /><sub><b>Matthew Leisy</b></sub>](https://github.com/mleisy)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=mleisy "Code") | [<img src="https://avatars1.githubusercontent.com/u/18668152?v=4" width="100px;"/><br /><sub><b>Zach Capshaw</b></sub>](https://www.linkedin.com/in/zcapshaw)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=zcapshaw "Code") |
| [<img src="https://avatars3.githubusercontent.com/u/3903383?v=4" width="100px;"/><br /><sub><b>Matthew</b></sub>](https://github.com/nbmatt26)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=nbmatt26 "Code") | [<img src="https://avatars3.githubusercontent.com/u/25396301?v=4" width="100px;"/><br /><sub><b>Ricky Singh</b></sub>](https://github.com/RickyRajinder)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=RickyRajinder "Code") | [<img src="https://avatars3.githubusercontent.com/u/9423417?v=4" width="100px;"/><br /><sub><b>Armando Lüscher</b></sub>](https://noplanman.ch)<br />[💻](https://github.com/gautamkrishnar/nothing-private/commits?author=noplanman "Code") |
<!-- ALL-CONTRIBUTORS-LIST:END -->

## Thanks

* Thanks to the **100K**:star: users.

* Thanks to [IssueHunt](https://issuehunt.io/) for sponsoring this project: <br/>
<a href='https://issuehunt.io/repos/76684607'><img src="https://raw.githubusercontent.com/BoostIO/issuehunt-materials/master/v1/issuehunt-button-v1.svg?sanitize=true" height="50px"></a>

* Thanks to [DuckDuckGo](https://ddg.gg) for this [tweet](https://twitter.com/duckduckgo/status/884763902847971329).
<img src="https://user-images.githubusercontent.com/8397274/43674474-23811f90-97f2-11e8-857f-94618ebb4fc9.jpg" width="300">

* Thanks to [CloudFlare](https://www.cloudflare.com) for their support and [PRO Plan](https://blog.cloudflare.com/cloudflare-open-source-your-upgrade-is-on-the-house/) Sponsorship.
<img src="https://www.cloudflare.com/img/logo-web-badges/cf-logo-on-white-bg.svg" width="300">

* **00Webhost** for hosting the API:
<a href="https://www.000webhost.com/1120509.html" target="_blank">
<img src="https://user-images.githubusercontent.com/8397274/48307610-96944080-e576-11e8-86f3-fa62ee0e6f55.png" width="300">
</a>

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
