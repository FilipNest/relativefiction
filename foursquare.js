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
          intent: "browse",
          ll: output.latitude + "," + output.longitude,
          radius: 800
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

        if (output.foursquare[category]) {

          var venues = output.foursquare[category];
                    
          return venues[0].name;

        } else {

          return "the " + category;

        }

      })

    })

  }

});
