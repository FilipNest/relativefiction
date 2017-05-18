// Get Foursquare API key and secret from config

var querystring = require("querystring");

var params = {
  v: "20170517",
  client_id: rf.config.foursquareKey,
  client_secret: rf.config.foursquareSecret
}

var request = require('request');

// Get list of Foursquare category types

request('https://api.foursquare.com/v2/venues/categories?' + querystring.stringify(params), function (error, response, body) {

  body = JSON.parse(body);

  if (body.meta.code === 200) {

    var categories = body.response.categories;

    var categoryList = {};

    var checkCategories = function (category) {

      categoryList[category.name.toLowerCase()] = category.id;

      category.categories.forEach(function (innerCategory) {

        checkCategories(innerCategory);

      })

    }

    Object.keys(categories).forEach(function (id) {

      checkCategories(categories[id]);

    });

    // Loop over each and create tag functions for it

    rf.alter(function (output, next) {

      var textVenues = {};

      output.tags.forEach(function (tag, index) {

        if (categoryList[tag.params[0].toLowerCase()]) {

          textVenues[tag.params[0].toLowerCase()] = categoryList[tag.params[0].toLowerCase()];

        }

      })

      if (Object.keys(textVenues).length) {

        // Make request to get venues

        var newParams = {
          limit: 50,
          ll: output.latitude + "," + output.longitude
        };

        // Get category ids

        var categoryIDs = [];

        Object.keys(textVenues).forEach(function (venue) {

          categoryIDs.push(textVenues[venue]);

        })

        newParams.categoryId = categoryIDs.join(",");

        request('https://api.foursquare.com/v2/venues/search?' + querystring.stringify(Object.assign(params, newParams)), function (error, response, body) {

          body = JSON.parse(body);

          if (body.meta.code === 200) {

            // Got list of venues, now sort into category groups

            output.foursquare = {};

            body.response.venues.forEach(function (venue) {

              venue.categories.forEach(function (category) {

                if (!output.foursquare[category.name.toLowerCase()]) {

                  output.foursquare[category.name.toLowerCase()] = [];

                }

                output.foursquare[category.name.toLowerCase()].push(venue);

              })

            })

            next(output);

          } else {

            output.errors.push(body.meta.errorDetail);

          }

        })

      } else {

        next();

      }

    })

    // Actual replacement function

    Object.keys(categoryList).forEach(function (category) {

      rf.tag(category, function (tagParams, output) {

        var number;

        if (!isNaN(parseInt(tagParams[1]))) {

          number = parseInt(tagParams[1]) - 1;

          // Shift back params

          tagParams[1] = tagParams[2];

        }

        if (!number || number < 0) {

          number = 0;

        }

        var venue;

        var param = tagParams[1];

        if (output.foursquare[category] && output.foursquare[category][number]) {

          venue = output.foursquare[category][number];

        }

        switch (param) {
          case "street":

            if (venue && venue.location.address) {

              // Strip out numbers thanks to http://stackoverflow.com/questions/1012883/stripping-street-numbers-from-street-addresses by Pesto

              return venue.location.address.replace(/^((\d[a-zA-Z])|[^a-zA-Z])*/, '');

            } else {

              return "a street";

            }

          case "distance":

            if (venue && venue.location.distance) {

              return venue.location.distance

            } else {

              return "some distance";

            }

          default:
            if (venue && venue.name) {

              return venue.name

            } else {

              return "the " + category;

            }
        }


      }, {
        category: "foursquare"
      })

    })

  }

});
