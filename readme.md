## Tweekly.fm

This is the tweekly.fm application.

---
#### Quick Start

Copy the `.env.sample` to `.env` and complete with your values. You're 
going to need API keys for at least Last.fm and Twitter.


You'll need a beanstalk queue daemon running, MariaDB or MySQL and a 
webserver running pointing everthing at `public/index.php` going 
forward. The live servers run on PHP 7.1, MariaDB 10.1 and 
beanstalk 1.10.

THe development environment includes a `docker-compose.yml` with 
everything you need to get up and running.  

Further instructions will be added soon and I'm happy to accept PRs
for the main site to implement new functionality.


#### A Personal Note

A lot of work, effort and time went in to this application over the 
years which has been used by over half a million people. In the time 
that the service has been running, well over 30 million status updates
have been posted around the web.

Open sourcing this app and giving away the codebase for free, was a 
tough challenge internally to take. I hope that in the future it will
benefit people to have an idea on how to approach technical challenges 
and how others have approached situations.

If you would like to give a donation as a thank you - you're welcome to
either sponsor via [Patreon](https://www.patreon.com/user?u=5066287). If you 
 via would like to donate via Paypal or another method then please get in 
 touch with me at [scott@dor.ky](mailto:scott@dor.ky) to discuss.
 
 #### License & Commercial Use
 
 If you would like to take this codebase and use it in a commercial 
 fashion or to make profit then you will be required to make a reasonable 
 donation and obtain written permission. 
 
 For non-profit or free use, you may use the codebase under an 
 Apache 2.0 license. 