trnsit.com

Author: Joshua Ross <joshualross@gmail.com>
http://joshualross.us

tl;dr
I used lots of new stuff to build a single page app that predicts transit arrivals,
check it out at http://trnsit.com

Description
===========

Thanks for checking out my coding challenge.  What started as a submission for 
Uber has become something I'm invested in building out into an actual service.  
I've built a transit departure single page application that leverages data 
from the nextmuni.com api.  My main motivation was nextmuni.com, which 
frequently forces me to wait and wait and wait for a prediction.  I built this 
app with the intention of improving the responsiveness of predictions and also, 
although this won't be difficult, making it more beautiful.

Since this started as a coding challenge, I really tried to extend myself by 
choosing tools that I was unfamiliar with.  I, too, thrive on learning new 
tools and methods.  I haven't had many opportunities to do so in my current 
role as our stack is pretty well defined.  I took this as an opportunity to not 
only build my first single page app, but get up to speed on new frameworks, 
technologies, and language features.  

Here is a short list of tools/libraries/features I used for the very first time:
 - composer (http://getcomposer.org)
 - silex (http://silex.sensiolabs.org‎)
 - twig (http://twig.sensiolabs.org/)
 - monolog (http://github.com/Seldaek/monolog)
 - requirejs (http://requirejs.org)
 - bootstrap (http://getbootstrap.com)
 - font-awesome (http://fortawesome.github.io/Font-Awesome/)
 - PHP 5.4, (specifically traits)
 - PSR-0 (https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
 - Single Page Applications in any frontend framework
 - Redis (specifically sorted sets and hashes)
 

Although I have used Backbone for lazy loading tabular data in the past, I've 
never built a single page application with it.  My front end skills, especially 
html/css, since the majority of my work from my current role is mid/backend.
So this challenge was fun, probably too much fun.

Interestingly, I had a lot of revelations about how Uber must do driver and 
user matching.  I didn't spend too much time developing algorithmic solutions 
to determine transit stops near my users due to time constraints.  But I figure
that Uber must use nearest neighbor or closest point algorithms.  These are 
great if you have a data store that allows you to calculate haversine distance 
on the fly.  Redis seemed like a good fit for the small amount of data I was
going to be storing.  So I came up with a much simpler approach that calculates 
a range of latitude and longitude minimums and maximums.  Then I calculated the 
haversine distance from my user's geolocation to each stop that was within the 
range.  This enabled me to sort the stops in order of distance.  The number of 
muni stops, 11222, is small enough that this approach was performant.  

There is a whole list of things I would like to add, including these items:
 - prediction caching (60s expiry)
 - expand range (show more stops)
 - map user location
 - map stop location

And probably longer term, less useful items
 - stop preferences for users
 - view current transit vehicles
 - plot route on map
 - predict from user input coordinate

Links
=====
 - Site: http://trnsit.com
 - Github: https://github.com/joshualross/trnsit (it's private, ask me, I'll add you)


Testing
=======
There are a number of unit tests that can be run using the following command
 $ cd test;php phpunit.phar .


Requirements:
*******************************************************************************

Web Coding Challenge
====================

Here is a sample project that will give us some insight into your current level
of experience.

This is a simple project, but please organize, design, test and document your
code as if it were going into production. Please then send us:

* A link to the hosted application
* A link to the hosted repository (e.g. Github/Bitbucket)
* Link to any other example of code you're particularly proud of  (ideally in your project's README)
* Link to your resume or public profile (ideally in your project's README)

Functional spec
---------------

Prototype **one of** the following projects.

Spend as much time as you like on this project. However, out of respect for your
time, if you need to scope it to 3-4 hours, feel free to mock/fake any feature
that you don't have time to develop.

The UX/UI is totally up to you. Feel free to explain any assumptions you have to
make. If you like, get creative and add additional features a user might find
useful!

While visual design is not part of the challenge, a polished interface is
preferred.

### Departure times

Create a web app that gives real-time departure time for public transportation
(use freely available public API). The app should geolocalize the user.

Here are some examples of freely available data:

* [511](http://511.org/developer-resources_transit-api.asp) (San Francisco)
* [Nextbus](http://www.nextbus.com/xmlFeedDocs/NextBusXMLFeed.pdf) (San
  Francisco)

### SF Movies

Create a web app that shows on a map where movies have been filmed in San
Francisco. The user should be able to filter the view using 
autocompletion search.

The data is available on [DataSF](http://www.datasf.org/): [Film
Locations](https://data.sfgov.org/Arts-Culture-and-Recreation-/Film-Locations-in-San-Francisco/yitu-d5am).

### Bicycle Parking

Create a web app providing directions to the nearest bicycle parking.

The data is available on [DataSF](http://www.datasf.org/): [Bicycle
Parking](https://data.sfgov.org/Transportation/Bicycle-Parking-Public-/w969-5mn4) 

### Food trucks

Create a web app that tells the user what types of food trucks
might be found near a specific location. The main interface should be
a map.

The data is available on [DataSF](http://www.datasf.org/): [Food
Trucks](https://data.sfgov.org/Permitting/Mobile-Food-Facility-Permit/rqzj-sfat) 

Technical spec
--------------

Split your architecture between a back-end and a web front-end, for instance
providing a JSON in/out RESTful API. Feel free to use any other technologies
provided that the general client/service architecture is respected.

### Back-end

We believe there is no one-size-fits-all technology. Good engineering is also
about using the right tool for the right job. A big part of what we do is
learning new tools and technologies. Therefore, feel free to mention in your
`README` how much experience you have with the technical stack you choose, we
will take note of that when reviewing your challenge. Feel free to mention the
reason behind your technical choices too.

Here are some technologies we are more familiar with:

* **Python** (most of our back-end uses this language)
* JavaScript
* Ruby
* PHP
* Go
* C++
* Haskell
* Java

You are also free to use any web framework, although we would prefer that you 
use a microframework (e.g. [Flask](http://flask.pocoo.org/)).

### Front-end

The front-end should ideally be a "one page app" with a single `index.html`
linking to external JS/CSS/etc. You may take this opportunity to demonstrate
your CSS3 or HTML5 knowledge.

We recommend using [Backbone.js](http://documentcloud.github.com/backbone/)
for front-end MVC.

Host it!
--------

When you.re done, host it somewhere and provide us with the server's URL, as
well as a Github/Bitbucket URL for the repo hosting your code.
