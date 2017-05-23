// Get Foursquare API key and secret from config

// Register globals so you don't have to wrap them in strings

rf.registerGlobals(["street", "distance", "city"]);

var querystring = require("querystring");

var params = {
  v: "20170517",
  client_id: rf.config.foursquareKey,
  client_secret: rf.config.foursquareSecret
};

var safeString = function (input) {

  return input.toLowerCase().split(" ").join("-");

};

var request = require('request');

// Get list of Foursquare category types

request('https://api.foursquare.com/v2/venues/categories?' + querystring.stringify(params), function (error, response, body) {

  body = JSON.parse(body);

  if (body.meta.code === 200) {

    var categories = body.response.categories;

    var categoryList = {};

    var checkCategories = function (category) {

      // Skip country and city as they're used globally

      if (safeString(category.name) === "city" || safeString(category.name) === "country") {

        return false;

      }

      categoryList[safeString(category.name)] = category.id;

      category.categories.forEach(function (innerCategory) {

        checkCategories(innerCategory);

      });

    };

    Object.keys(categories).forEach(function (id) {

      checkCategories(categories[id]);

    });

    // Loop over each and create tag functions for it

    rf.alter(function (output, next) {

      var textVenues = {};

      output.tags.forEach(function (tag, index) {

        var safeTag = safeString(tag.params[0]);

        if (categoryList[safeTag]) {

          textVenues[safeTag] = categoryList[safeTag];

        }

      });

      if (Object.keys(textVenues).length) {


        // Get category ids

        var categoryIDs = [];

        Object.keys(textVenues).forEach(function (venue) {

          categoryIDs.push(textVenues[venue]);

        });

        var venuePromises = [];

        categoryIDs.forEach(function (id) {

          // Make request to get venues

          var newParams = {
            limit: 50,
            ll: output.latitude + "," + output.longitude,
            intent: "checkin"
          };

          newParams.categoryId = id;

          var categoryPromise = new Promise(function (pass) {

            request('https://api.foursquare.com/v2/venues/search?' + querystring.stringify(Object.assign(params, newParams)), function (error, response, body) {

              body = JSON.parse(body);

              if (body.meta.code === 200) {

                // Got list of venues, now sort into category groups

                if (!output.foursquare) {
                  output.foursquare = {};
                }

                body.response.venues.forEach(function (venue) {

                  venue.categories.forEach(function (category) {

                    if (!output.foursquare[safeString(category.name)]) {

                      output.foursquare[safeString(category.name)] = [];

                    }

                    output.foursquare[safeString(category.name)].push(venue);

                  });

                });

                // sort by distance

                Object.keys(output.foursquare).forEach(function (category) {

                  output.foursquare[category] = output.foursquare[category].sort(function (a, b) {

                    if (a.location.distance < b.location.distance) {

                      return -1;

                    } else if (a.location > b.location.distance) {

                      return 1;

                    } else {

                      return 0;

                    }

                  });

                });

                pass(output);

              } else {

                output.errors.push(body.meta.errorDetail);

                pass(output);

              }

            });

          });

          venuePromises.push(categoryPromise);

        });

        Promise.all(venuePromises).then(function () {

          next(output);

        });

      } else {

        next(output);

      }

    });

    // Actual replacement function

    Object.keys(categoryList).forEach(function (category) {

      rf.tag(category, function (tagParams, output) {

        var number;

        if (!isNaN(parseInt(tagParams[0]))) {

          number = parseInt(tagParams[0]) - 1;

          // Shift back params

          tagParams[0] = tagParams[1];

        }

        if (!number || number < 0) {

          number = 0;

        }

        var venue;

        var param = tagParams[0];

        if (output.foursquare[category] && output.foursquare[category][number]) {

          venue = output.foursquare[category][number];

        }

        switch (param) {
          case "street":

            if (venue && venue.location.address) {

              // Strip out numbers thanks to http://stackoverflow.com/questions/1012883/stripping-street-numbers-from-street-addresses by Pesto

              return venue.location.address.replace(/^((\d[a-zA-Z])|[^a-zA-Z])*/, '');

            } else {

              return "some street";

            }

            break;
          case "distance":

            if (venue && venue.location.distance) {

              return venue.location.distance;

            } else {

              return "some distance";

            }

            break;
          case "city":

            if (venue && venue.location.city) {

              return venue.location.city;

            } else {

              return "some city";

            }

            break;
          default:
            if (venue && venue.name) {

              return venue.name.replace("The", "");

            } else {

              return "the " + category;

            }
        }


      }, {
        category: "foursquare"
      });

    });

  }

});
