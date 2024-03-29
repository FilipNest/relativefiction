Relative Fiction
================

A tool for creating dynamic stories relative to a reader's place, time, location, weather. Via the Foursquare API and OpenWeatherMap API, custom XML/RSS/JSON feeds and more.

Write stories that feel like they're taking place where the reader is, science fiction with dates and information that doesn't age and lots more.

Play around and read some stories at: https://relativefiction.com

## This document

- [Story syntax](#story-syntax)
- [Tags](#tags)
	- [Time](#time)
		- [Offsetting dates](#offsetting-dates)
	- [Foursquare](#foursquare)
		- [Defaults](#defaults)
	- [Weather](#weather)
    - [Feeds](#external-feeds)
	- [Misc](#misc)
	- [Conditionals](#conditionals)
- [Basic API](#basic-api)
	- [Parse a story](#parse-a-story)
- [Customising and running it yourself](#customising-and-running-it-yourself)
	- [Running it yourself](#running-it-yourself)
	- [Defining new tags](#defining-new-tags)
  	- [Altering the context](#altering-the-context)

## Story syntax

Dynamic parts of stories are written in tags surrounded by a pair of single curly brackets. Some of these take additional parameters which you can seperate within the brackets with a space.

Stories look something like:

>I love {{dayofweek}}s. It was around {{hour12}}{{ampm}} on a {{dayofweek}} when I first saw its outlines. I was sitting in {{park}} and looking out at the sky. It was a {{weather}} day much like this one. Wings. Clearly a pair of wings. Floating somewhere towards {{supermarket street}} as if going about their everyday business. This month is even more special. It happened in {{monthofyear}}. So I walk over to {{park}} and look out. But there's never anything anymore...

Which localises into something like:

>I love Thursdays. It was around 9pm on a Thursday when I first saw its outlines. I was sitting in Russell Square and looking out at the sky. It was a rainy day much like this one. Wings. Clearly a pair of wings. Floating somewhere towards St. John St as if going about their everyday business. This month is even more special. It happened in May. So I walk over to Russell Square and look out. But there's never anything anymore...

## Tags

### Time

* hour24 - current hour in 24 hour time.
* hour12 - current hour in 12 hour time.
* minutes - number of minutes past the current hour
* seconds - number of seconds past the current minute
* year - current year
* ampm - am or pm (useful for 12 hour time)
* dayofweek - current day (Monday to Sunday)
* dayofmonth - Day of the month (1 to 31)
* dayofmonthsuffix - st or th depending on the day of month
* monthofyear - Current month (January to December)

#### Offsetting dates

The date can be offset by adding an offset parameter for example `{{dayofweek add 1 day}}` or `{{year minus 50 years}}`

### Foursquare

Pulls in information about local venues. To see which you can use, go to https://location.foursquare.com/places/docs/categories to get relevant category names.

If you want to show information about a venue rather than the venue itself you can use `{{park distance}}` to get the distance in meters and `{{park street}}` to get the street the park is on. The street is taken from the Foursquare address and takes the house number part out. `{{park city}}` would get the city the park is in.

If you have multiple locations in your story of the same type, use `{{park 1}}` etc with numbers for each unique location.

The basic formula for these is to put in the category name, such as `{{park}}`. If the venue category has spaces in it, pass it in with hyphens. Like `{{"chinese-restaurant" distance}}`.

### Defaults

If no suitable venues are found for a valid Foursquare category, the tag is replaced with something like `{the park}`, `{{some distance}}`, `{{some street}}` or `{{some city}}`.

### Weather

Information about local weather.

* weather - one of: stormy, rainy, snowy, hailing, clear, cloudy, calm, windy, scorching, freezing, misty, hazy
* temperature - in celcius
* humidity - in %
* windspeed - in m/s
* sunsethour24 - hour of sunset in 24 hour time
* sunsethour12 - hour of sunset in 12 hour time
* sunrisehour24 - hour of sunrise in 24 hour time
* sunrisehour12 - hour of sunrise in 12 hour time
* hourstosunset - hours to next sunset
* hourstosunrise - hours to next sunrise 

### External feeds

Relative Fiction supports XML feeds (RSS extra) and JSON feeds for you to customise your story. This is super powerful.

#### Getting the contents of a website

Both XML and JSON depend on parsing a url. So first you'll need to use the `url` tag.

`{{url "https://relativefiction.com"}}` - Would print the contents of the Relative Fiction homepage. This is a bit useless on its own but you can use it in contains queries (see conditionals below) like:

`{{#contains (url "https://en.wikipedia.org/wiki/Main_Page") "goat"}}The wikipedia front page had goats on it{{/contains}}`

#### Parsing JSON or XML

The `xml` and `json` tags are block tags. Anything possible in Handlebars is possible within the block so you can do conditionals and more based on anything in the fetched data. The following example gets the 1st top news story from the UK edition of BBC news.

`{{#xml (url "http://feeds.bbci.co.uk/news/rss.xml?edition=uk")}}{{channel.item.[0].title}}{{/xml}}`

### Misc

* longitude - current longitude
* latitude - current latitude
* country - the country the reader is in

### Conditionals

Alongside variables, writers can put in logic to show text if some statements are true and some other text if they are not. Master this bit and you can write stories that morph dramatically with the reader's circumstances.

Relative Fiction is powered by the Handlebars library (http://handlebarsjs.com)\*. Instead of explaining how Handlebars here, the Handlebars documentation itself is best. Understand blocks and subexpressions and you'll have all you need.

<small>* the previous version had a probably impressive but hugely unnecessary template language of its own. It's now switched to a library.</small>

Below are some of the extra Handlebars helpers that have been added to make your stories easier to write.

These are:

* equals
* not
* less
* more
* and
* or
* contains

Here's an example of how you use them. 

```
{{#more (hour24) 12}}

It was a {{weather}} {{dayofweek}} afternoon. Georgina was tired.

{{else}}

It was a {{weather}} {{dayofweek}} morning. Georgina was hungry.

{{/more}}

```

Note that expressions within blocks (like `(hours24)` should be wrapped in round brackets and don't need their usual curly brackets (see subexpressions in the Handlebars docs).

## Basic API

### Parse a story

Send the following parameters to https://relativefiction.com as an HTTP POST.

* longitude (number)
* latitude (number)
* text (your text, complete with tags)

You'll get back a JSON object with the following parameters:

* result - your parsed story
* errors - any errors (an array)
* original - the text you sent

## Customising and running it yourself

### Running it yourself

If you wish to roll your own version of this, require the `server.js` file in a node.js script (run `npm install` to get dependencies) and pass in the following options:

* port - where you want the server to run
* foursquareKey - your Foursquare API key
* foursquareSecret - your Foursquare API secret
* openWeatherKey - Open Weather Map API key 

Here's an example:

```javaScript

var config = {
  port: 3000,
  foursquareKey: "1234",
  foursquareSecret: "abcd",
  openWeatherKey: "!?#@"
};

var rf = require("./server.js")(config);

```

### Defining new tags

The `rf` object then allows you to define new tags with the `rf.tag` function.

This takes a tag name (the first parameter of the tag, a function and an options object for weight and category).

The function gets two parameters:

* the tag parameters in an array
* the current context for the whole story including the user's latitude, longitude and time, all the tags in the story and more.

Here's an example:

```javascript

rf.tag("longitude", function (tagParams, context) {

  return context.longitude;

}, {
  category: "general"
});


```

### Altering the context

If you want to add to the context or do a bulk action before any of the tags are parsed (used in both the Foursquare and OpenWeatherMap tags to load everything at once rather than making several calls) you can use the `rf.alter` function.

This takes a function with two parameters, the current context (as above) and a callback function you call when done, passing in a new, altered context object.
