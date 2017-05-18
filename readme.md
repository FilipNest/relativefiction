Relative Fiction
================

A tool for creating dynamic stories relative to a reader's place, time, location, weather (and more).

https://relativefiction.com

## Story syntax

Dynamic parts of stories are written in tags surrounded by a pair of single curly brackets. Some of these take additional parameters which you can seperate within the brackets with a space.

Stories look something like:


>I love {dayofweek}s. It was around {hours12}{ampm} on a {dayofweek} when I first saw its outlines. 
I was sitting in {park} and looking out at the sky. It was a {weather} day much like this one. 
Wings. Clearly a pair of wings. Floating somewhere towards {supermarket street} as if going about their everyday business. This month is even more special. It happened in {monthofyear}. So I walk over to {park} and look out. But there's never anything anymore...

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

The date can be offset by adding an offset parameter for example `{dayofweek + 1 day}` or `{year - 50 years}`

### Foursquare

Pulls in information about local venues. To see which you can use, go to https://developer.foursquare.com/categorytree to get relevant category names.

The basic formula for these is to put in the category name, such as `{park}`

If you have multiple locations in your story of the same type, use `{park 1}` etc with numbers for each unique location.

If you want to show information about a venue rather than the venue itself you can use `{park distance}` to get the distance in meters and `{park street}` to get the street the park is on. The street is taken from the Foursquare address and takes the house number part out.

### Defaults

If no suitable venues are found for a valid Foursquare category, the tag is replaced with something like `{the park}`, `{some distance}` and `{a street}`.

### Weather

Information about local weather.

* weather - one of: stormy, rainy, snowy, hailing, clear, cloudy, calm, windy, scorching, freezing, misty, hazy
* temperature in celcius
* humidity - in %
* windspeed - in m/s
* sunsethour24 - hour of sunset in 24 hour time
* sunsethour12 - hour of sunset in 12 hour time
* sunrisehour24 - hour of sunrise in 24 hour time
* sunrisehour12 - hour of sunrise in 12 hour time
* hourstosunset - hours to next sunset
* hourstosunrise - hours to next sunrise 

### Misc

* longitude - current longitude
* latitude - current latitude

## API

All return JSON.

### Get a categorised list of available tags

https://relativefiction.com/taghelp

### Parse a story

Send the following parameters to https://relativefiction.com as an HTTP POST.

* longitude (number)
* latitude (number)
* text (your text, complete with tags)

You'll get back an object with the following parameters:

* result - your parsed story
* errors - any errors (an array)
* original - the text you sent

## Extending with new tags

If you wish to roll your own version of this, require the `server.js` file in a node.js script (run `npm install` to get dependencies) and pass in the following options:

* port - where you want the server to run
* foursquareKey - your Foursquare API key
* foursquareSecret - your Foursquare API secret
* openWeatherKey - Open Weather Map API key 

Here's an example:

```JavaScript

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

```

rf.tag("longitude", function (tagParams, context) {

  return context.longitude;

}, {
  category: "general"
});


```

You can either return a value directly, or if you want to do some async work, you can return a JavaScript Promise. The system should detect you've returned a promise and wait for it to finish.

### Altering the context

If you want to add to the context or do a bulk action before any of the tags are parsed (used in both the Foursquare and OpenWeatherMap tags to load everything at once rather than making several calls) you can use the `rf.alter` function.

This takes a function with two parameters, the current context (as above) and a callback function you call when done, passing in a new, altered context object.
