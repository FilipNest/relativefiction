Relative Fiction
================

A tool for creating dynamic stories relative to a reader's place, time, location, weather (and more).

https://relativefiction.com

## Story syntax

Dynamic parts of stories are written in tags surrounded by a pair of single curly brackets. Some of these take additional parameters which you can seperate within the brackets with a space.

Stories look something like:

```

I love {dayofweek}s. It was around {hours12}{ampm} on a {dayofweek} when I first saw its outlines. I was sitting in {park} and looking out at the sky. It was a {weather} day much like this one. Wings. Clearly a pair of wings. Floating somewhere towards {supermarket street} as if going about their everyday business. This month is even more special. It happened in {monthofyear}. So I walk over to {park} and look out. But there's never anything anymore...

```


Time
----

* hours24
* hours12
* minutes
* seconds
* year
* ampm
* dayofweek
* dayofmonth
* dayofmonthsuffix
* monthofyear

The date can be offset by adding an offset parameter for example `{dayofweek + 1 day}`

General
-------

* longitude
* latitude

Foursquare
----------

* categoryname - Get a list of the names you can use at https://developer.foursquare.com/categorytree

Instead of a venue you can get additional information about it.

* `{park distance}` gives you the distance in meters from the park.
* `{park street}` gives you the street the park is on.

If dealing with multiple venues of the same type, pass in a number to distinguish them and get different venues. `{park 1} was nearer. {park 2} was {park 2 distance} meters away on {park 2 street}`. Venues are ordered by distance.

Weather
-------

* weather
* temperature
* humidity
* windspeed
* sunsethour24
* sunsethour12
* hourstosunset
* sunrisehour24
* sunrisehour12
* hourstosunrise
