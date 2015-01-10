![alt Contenta][logo]

# contenta
## 'Things Contained'

>	content 2 |ˈkänˌtent| _noun_

>	ORIGIN late Middle English: from medieval Latin contentum (plural **contenta** ‘_things contained_’)

This project began for several reasons.

* As a Java (backend) and iOS developer I was a little frustrated with the complexity required to build a full Java backend to support a prototype iOS application.  There _must_ be a simple way to spin up some basic database driven services with basic pages and RESTful API
* Yes there are _tons_ of tools that would do what I wanted, but ..
** I need a tool I could deploy with an external face
** I need a self contained database (sqlite?) because my IT department can't support their own asses let alone a series of Oracle or MySQL or MariaDB instances
** It's fun to learn new tools
** Since it's just for me, I'm going to reinvent the wheel :)  .. again.

So this is a learning project and I have always found a _practical_ learning project teaches the most.  In other words it needs to actually solve a need for me.  Well it just so happens I have a need.  [Mylar](https://github.com/evilhero/mylar) is an interesting project, but _python_?  Based on [Headphones](https://github.com/rembo10/headphones) but .. Mylar keeps crashing on my Synology diskstation. So why not rewrite Mylar with my own requirements .. but not _python_.

Fun .. right?  So pick a language.  Well it needs to be deployed on my Synology .. but most things can.  I could go with Node.js, or PHP.  Not Java, I do enough of that at work and this is for _learning_.  I even toyed with [Swift](https://developer.apple.com/swift/) but then I would need a Mac (MacMini?  Old Laptop?) to deploy.  After testing with Node.js and PHP I decided to go with PHP.  I know it's a little old, but it has the advantage of deploying as part of a simple Apache site.

So now, what are the requirements?  I told you this would be fun.

1. Multi-user support
1. upload comics and have them automatically organized and indexed using [ComicVine][comicvine]
1. track what I've read
1. content rating?  My twins are only 7, but they might enjoy the library soon.
1. integrate with SabNZBd and automatically download content
1. load NZB RSS feeds
1. search Newznab based sites for matching content
1. target CBR/CBZ as a content type, but leave room for adding PDF/ePub or other formats
1. hmm, actually lets unpack the CBR and store them as CBZ .. I dislike RAR.  It's personal.
1. provide a simple RESTful api for scripting
1. although I don't desire to write an iOS comic book reader, maybe an iOS _Admin_ application ...

Thats enough to start ..

[logo]: https://raw.githubusercontent.com/vitolibrarius/contenta/master/public/img/Logo_sm.png "Contenta"
[comicvine]: http://www.comicvine.com
