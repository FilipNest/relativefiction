Relative Fiction
================

Localised stories.

Plugins
=======

```

rf.process({
  longitude: "55",
  latitude: "1000",
  "text": `hello world the time is {time}`,
  time: Date.now()
}).then(function (output) {

  console.log(output);

}, function (error) {

  console.error(error);

})

rf.alter(function (output, next) {

  output.moment = moment(output.time);

  next();

});

// Sync method

rf.tag(function (tagParms, output) {

  if (tagParms[0] === "2time") {

    return output.moment.format("h:m")

  }

},2)

// Async method

rf.tag(function (tagParms, output, callback) {

  if (tagParms[0] === "2time") {

    callback(output.moment.format("H:m"));

  }

},1)





```

{{place "placetype" "placeid" "info"}}

"placetype" == FourSquare placetype
"placeid" == Numerical ID
"info" == Street, distance, city

{{dayofweek "offset"}}

dayofweek
hours12
hoursampm
hours24
minutes
monthofyear
dayofmonth
dayofmonthsuffix
year
longitude
latitude
country
weather
sunsethour
hourstosunset
sunrisehour
hourstosunrise
