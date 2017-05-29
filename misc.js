rf.tag("longitude", function (tagParams, output) {

  return output.longitude;

}, {
  category: "general"
});

rf.tag("country", function (tagParams, output) {

  return output.country;

}, {
  category: "general"
});

var request = require("request");

rf.alter(function (output, callback) {

  output.urls = {};
  var urlPromises = [];

  output.tags.forEach(function (tag) {

    if (tag.params[0] === "url") {

      var promise = new Promise(function (pass, fail) {

        try {

          request({
            uri: tag.params[1],
            timeout: 7000
          }, function (error, response, body) {

            if (error) {

              output.errors.push(error.message);

              fail();

            } else {

              output.urls[tag.params[1]] = body;

              pass();

            }

          });

        } catch (e) {

          output.errors.push(e.message);

          fail();

        }

      });

      urlPromises.push(promise);

    }

  });

  if (urlPromises.length) {

    Promise.all(urlPromises).then(function (pass) {

      callback(output);

    }, function (fail) {

      if (fail) {

        output.errors.push(fail);

      }

      callback(output);

    });

  } else {

    callback(output);

  }

});

rf.tag("url", function (tagParams, context) {

  if (context.urls && context.urls[tagParams[0]]) {

    return context.urls[tagParams[0]];

  }

}, {
  category: "general"
});

// JSON helper

Handlebars.registerHelper('json', function (value, options) {

  if (!options.fn) {

    throw new Error("json needs to be used as a block helper");

  }

  return options.fn(JSON.parse(value));

});


var XML = require('pixl-xml');

Handlebars.registerHelper('xml', function (value, options) {

  if (!options.fn) {

    throw new Error("xml needs to be used as a block helper");

  }

  try {

    var doc = XML.parse(value);

  } catch (e) {

    throw new Error(e.message);

  }

  return options.fn(doc);

});
