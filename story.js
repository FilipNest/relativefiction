var localise = function (text) {

  //Analyse text for variables

  var variables = [];

  //Find and isolate unique variables

  if (!text.match(/\[(.*?)\]/g)) {

    return false;

  }

  text.match(/\[(.*?)\]/g).forEach(function (element, index) {

    if (!variables[element]) {

      variables.push(element);

    };

  });

  //Get location

  navigator.geolocation.getCurrentPosition(function (position) {

    lookup(position.coords.latitude + "," + position.coords.longitude, variables);

  });

  //Foursquare category lookup

  var foursquarelookup = function (latlng, category, callback) {

    category = category.toLowerCase();

    if (!venues[category]) {

      return false;

    }

    var data = {

      client_id: foursquare.id,
      client_secret: foursquare.secret,
      ll: latlng,
      v: "20150516",
      categoryId: venues[category]

    };

    $.ajax({
      url: "https://api.foursquare.com/v2/venues/search",
      data: data,
      success: function (results) {

        var results = results.response.venues;

        callback(results);

      },
    });

  };


  //Once location received, loop over variables and replace

  var lookup = function (location, variables) {

    //Get day

    var d = new Date();
    var n = d.getDay();

    var day = "";

    switch (n) {

    case 0:
      day = "Sunday";
      break;
    case 1:
      day = "Monday";
      break;
    case 2:
      day = "Tuesday";
      break;
    case 3:
      day = "Wednesday";
      break;
    case 4:
      day = "Thursday";
      break;
    case 5:
      day = "Friday";
      break;
    case 6:
      day = "Saturday";
      break;


    }

    variables.forEach(function (element, index) {

      if (element === "[day]") {

        $("body").html($("body").html().replace("[day]", day));

      }

      if (element === "[weather]") {

        weather(location, function (result) {

          $("body").html($("body").html().replace("[weather]", result.weather[0].main.toLowerCase()));

          return true;

        });


      };

      //Store for later replacing

      var variable = element;

      //Strip brackets

      variable = variable.replace("[", "").replace("]", "");

      var category = variable.split("|")[0];
      var id = variable.split("|")[1] - 1;

      foursquarelookup(location, category, function (result) {

        //Get nearest result

        function distance(a, b) {
          if (a.location.distance < b.location.distance)
            return -1;
          if (a.location.distance > b.location.distance)
            return 1;
          return 0;
        }

        result.sort(distance);

        $("body").html($("body").html().replace("[" + variable + "]", result[id].name));

      });

    });


  };

};

var venues = {};

var getvenues = function (callback) {

  //Traverse list of venue types

  var traverse = function (object) {

    venues[object["name"].toLowerCase()] = object["id"]

    if (object.categories && object.categories.length > 0) {

      object.categories.forEach(function (element, index) {

        traverse(element);

      });

    }

  };

  var data = {

    client_id: foursquare.id,
    client_secret: foursquare.secret,
    v: "20150516"
  };

  $.ajax({
    url: "https://api.foursquare.com/v2/venues/categories",
    data: data,
    success: function (results) {

      var categories = results.response.categories;

      categories.forEach(function (element, index) {

        traverse(element);

      });

      callback();

    }

  });
}

//Get Foursquare venues

getvenues(function () {

  localise($("body").html());

});

//Get weather data

gotweather = false;

var weather = function (latlng, callback) {

  if (!gotweather) {

    data = {
      lat: latlng.split(",")[0],
      lon: latlng.split(",")[1],
    }

    $.ajax({
      url: "http://api.openweathermap.org/data/2.5/weather",
      data: data,
      success: function (results) {

        callback(results);

      },
    });

    gotweather = true;

  }

};