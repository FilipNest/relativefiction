// a little request lib returning bluebird-based promises
var request = require('request');
var wdk = require("wikidata-sdk");

var util = require("util");

function getProperty(title, property, callback) {

  request(wdk.getWikidataIdsFromWikipediaTitles(title), function (error, response, body) {

    var body = JSON.parse(body);

    var entity = body.entities[Object.keys(body.entities)[0]];

    // Get properties

    var statements = wdk.simplify.claims(entity.claims);

    var properties = Object.keys(statements);

    var result = {};

    request(wdk.getEntities(properties), function (error, response, body) {

      var propertyEntities = JSON.parse(body).entities;
      var deepSearch = {};

      Object.keys(propertyEntities).forEach(function (id) {

        var propertyEntity = propertyEntities[id].labels;

        var key = propertyEntity.en.value;

        var value = statements[id][0];

        // Check if statement id starts with Q

        if (value[0] === "Q") {

          deepSearch[value] = key;

        } else {

          result[key] = value;

        }

      })

      request(wdk.getEntities(Object.keys(deepSearch)), function (error, response, deepBody) {

        var deepPropertyEntities = JSON.parse(deepBody).entities;

        Object.keys(deepPropertyEntities).forEach(function (deepPropertyEntityID) {

          result[deepSearch[deepPropertyEntityID]] = deepPropertyEntities[deepPropertyEntityID].labels.en.value;

        })

        callback(result[property]);

      })

    });

  });


}

rf.tag(function (tagParams, output, callack) {

  if (tagParams[0] === "wiki") {

    getProperty(tagParams[1], tagParams[2], function (output) {

      console.log(output);
      return output;

    })
    
  }

})
