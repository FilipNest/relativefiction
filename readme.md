Relative Fiction
================

Localised stories.

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

The date can be offset by adding an offset parameter for example `{dayofweek + 1 day}`

General
-------

* longitude
* latitude

Foursquare
----------

* categoryname

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
