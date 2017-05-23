var request = require("request");
var querystring = require("querystring");

var fs = require("fs");

var countries = JSON.parse(fs.readFileSync(__dirname + "/countries.js", "utf8"));

rf.alter(function (output, next) {

  var params = {
    APPID: rf.config.openWeatherKey,
    lat: output.latitude,
    lon: output.longitude,
    units: "metric"
  };

  var url = "http://api.openweathermap.org/data/2.5/weather?" + querystring.stringify(params);

  request(url, function (error, response, body) {

    body = JSON.parse(body);

    var country = body.sys.country;

    // Get country data from JSON

    countries.forEach(function (item) {

      if (item["ISO3166-1-Alpha-2"] === country || item["ISO3166-1-Alpha-3"] === country) {

        output.country = item.name;

      }

    });

    output.weather = body;

    next(output);

  });

});

var moment = require("moment");

rf.tag("weather", function (tagParams, context) {

  var values = [
    {
      "stormy": [200, 201, 202, 210, 211, 212, 221, 230, 231, 232, 960, 961, 962, 900, 901, 902, 781, 731]
    },
    {
      "rainy": [300, 301, 302, 310, 311, 312, 313, 314, 321, 500, 501, 502, 503, 504, 511, 520, 521, 522, 531]
    },
    {
      "snowy": [600, 601, 602, 611, 612, 615, 616, 620, 621, 622, 906]
    },
    {
      "hailing": [906]
    },
    {
      "clear": [800, 801]
    },
    {
      "cloudy": [802, 803, 804]
    },
    {
      "calm": [951, 952, 953]
    },
    {
      "windy": [954, 955, 956, 957, 958, 959, 960, 905]
    },
    {
      "scorching": [904]
    },
    {
      "freezing": [906, 903]
    },
    {
      "misty": [701, 711, 741]
    },
    {
      "hazy": [721]
    }
  ];

  var mainWeather = context.weather.weather[0].id;
  var weatherType;

  values.forEach(function (type) {

    var currentType = type[Object.keys(type)[0]];

    if (currentType.indexOf(mainWeather) !== -1) {

      weatherType = Object.keys(type)[0];

    }
  });

  if (!weatherType) {

    weatherType = "unusual";

  }

  return weatherType;

}, {
  category: "weather"
});

rf.tag("temperature", function (tagParams, context) {

  var temp = context.weather.main.temp;

  return Math.floor(temp);

}, {
  category: "weather"
});

rf.tag("humidity", function (tagParams, context) {

  var humidity = context.weather.main.humidity;

  return Math.floor(humidity);

}, {
  category: "weather"
});

rf.tag("windspeed", function (tagParams, context) {

  var windspeed = context.weather.wind.speed;

  return Math.floor(windspeed);

}, {
  category: "weather"
});

rf.tag("hourstosunrise", function (tagParams, context) {

  var sunrise = moment.unix(context.weather.sys.sunrise);

  var currentTime = moment(context.time);

  var diff = sunrise.diff(currentTime, "hours");

  if (diff < 0) {

    diff = 24 + diff;

  }

  return diff;

}, {
  category: "weather"
});

rf.tag("hourstosunset", function (tagParams, context) {

  var sunset = moment.unix(context.weather.sys.sunset);

  var currentTime = moment(context.time);

  var diff = sunset.diff(currentTime, "hours");

  if (diff < 0) {

    diff = 24 + diff;

  }

  return diff;

}, {
  category: "weather"
});

rf.tag("sunrisehour24", function (tagParams, context) {

  var sunrise = moment.unix(context.weather.sys.sunrise);

  return sunrise.format("H");

}, {
  category: "weather"
});


rf.tag("sunsethour24", function (tagParams, context) {

  var sunset = moment.unix(context.weather.sys.sunset);

  return sunset.format("H");

}, {
  category: "weather"
});

rf.tag("sunrisehour12", function (tagParams, context) {

  var sunrise = moment.unix(context.weather.sys.sunrise);

  return sunrise.format("h");

}, {
  category: "weather"
});


rf.tag("sunsethour12", function (tagParams, context) {

  var sunset = moment.unix(context.weather.sys.sunset);

  return sunset.format("h");

}, {
  category: "weather"
});
