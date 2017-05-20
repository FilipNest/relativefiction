var tags = {};
var alterHooks = [];
var globals = {};

var Handlebars = require("handlebars");

require("./helpers")(Handlebars);

module.exports = {

  process: function ({
    longitude,
    latitude,
    text,
    time = Date.now()
  }) {

    return new Promise((pass, fail) => {

      // Output object

      var output = {
        latitude: latitude,
        longitude: longitude,
        text: text,
        errors: [],
        result: ""
      };

      // Fatal errors first

      if (!latitude) {

        output.errors.push("Missing 'latitude' parameter")

      }

      if (!longitude) {

        output.errors.push("Missing 'longitude' parameter")

      }

      if (!latitude) {

        output.errors.push("Missing 'text' parameter")

      }

      latitude = parseFloat(latitude);
      longitude = parseFloat(longitude);

      if (isNaN(latitude) || isNaN(longitude)) {

        output.errors.push("Longitude and latitude need to be numbers")

      }

      if (typeof text !== "string") {

        output.errors.push("Text parameter needs to be text");

      }

      if (output.errors.length) {

        // Fatal error, stop

        delete output.result;

        fail(output);

      } else {

        // Get words between curlies (from StackOverflow user CMS )

        function getWordsBetweenCurlies(str) {
          var results = [],
            re = /{{([^}}]+)}}/g,
            text;

          while (text = re.exec(str)) {
            results.push(text[1]);
          }
          return results;
        }

        var tags = getWordsBetweenCurlies(text);

        output.tags = [];

        tags.forEach(function (tag) {

          // Split tags into space seperated parameters

          var splitTags = tag.match(/(?:[^\s"]+|"[^"]*")+/g);

          splitTags.forEach(function (splitTag, index) {

            splitTags[index] = splitTag.split('"').join("");

          })

          output.tags.push({
            tag: "{{" + tag + "}}",
            params: splitTags
          });

        })

        var sortByWeight = function (a, b) {

          if (a.weight < b.weight) {

            return -1;

          } else if (a.weight > b.weight) {

            return 1;

          } else {

            return 0;

          }

        };

        // Function for promisechains

        var promiseChain = function (tasks, parameters, success, fail) {

          tasks.reduce(function (cur, next) {

            return cur.then(next);

          }, Promise.resolve(parameters)).then(success, fail);

        };

        // Sort alter hooks by weight and run promises in order

        var preprocessors = [];

        alterHooks.sort(sortByWeight).forEach(function (alterHook) {

          var promise = function () {

            return new Promise(function (resolve) {

              alterHook.processor(output, function (processed) {

                resolve(processed);

              });

            })

          }

          preprocessors.push(promise);

        });

        promiseChain(preprocessors, output, function (readyOutput) {

          output = readyOutput;

          output.result = output.text;

          var source = output.result;
          var template = Handlebars.compile(source);

          var parameters = Object.assign(output, globals);

          parameters.context = output;

          try {

            output.result = (template(parameters));

          } catch (e) {

            output.errors.push(e.message)

          }

          pass({
            result: output.result,
            errors: output.errors,
            text: output.text
          });

        })


      };

    })

  },

  // The alter function gets the whole output object and alters it. Useful for setting up things for the tag processors. Weight field is used to order processors.

  alter: function (preprocessor, weight) {

    if (typeof preprocessor !== "function") {

      throw "alter takes a function"

    } else {

      alterHooks.push({
        weight: weight,
        processor: preprocessor
      });

    }

  },

  // Alter function gets a tag and changes its contents.

  tag: function (tagName, processor, options = {}) {

    if (typeof processor !== "function") {

      throw "tag takes a function"

    } else {

      if (!tags[options.category]) {

        tags[options.category] = [];

      }

      tags[options.category].push(tagName)

      // Register helper

      Handlebars.registerHelper(tagName, function () {

        return processor(arguments, this.context);

      });

    }

  },
  registerGlobals: function (globalArray) {

    globalArray.forEach(function (global) {

      globals[global] = global;

    })

  }

}
