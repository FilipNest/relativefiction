// a little request lib returning bluebird-based promises
var request = require('request');
var wdk = require("wikidata-sdk");

var util = require("util");

// Levenshtein distance for properties on Stackoverflow by David and overlord1234

function editDistance(s1, s2) {
  s1 = s1.toLowerCase();
  s2 = s2.toLowerCase();

  var costs = new Array();
  for (var i = 0; i <= s1.length; i++) {
    var lastValue = i;
    for (var j = 0; j <= s2.length; j++) {
      if (i == 0)
        costs[j] = j;
      else {
        if (j > 0) {
          var newValue = costs[j - 1];
          if (s1.charAt(i - 1) != s2.charAt(j - 1))
            newValue = Math.min(Math.min(newValue, lastValue),
              costs[j]) + 1;
          costs[j - 1] = lastValue;
          lastValue = newValue;
        }
      }
    }
    if (i > 0)
      costs[s2.length] = lastValue;
  }
  return costs[s2.length];
}

function similarity(s1, s2) {
  var longer = s1;
  var shorter = s2;
  if (s1.length < s2.length) {
    longer = s2;
    shorter = s1;
  }
  var longerLength = longer.length;
  if (longerLength == 0) {
    return 1.0;
  }
  return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
}


function getProperty(title, property, callback, optional = " ") {

  var query = `SELECT ?id WHERE {
  ?item ?label "${title}"@en .
  ?item schema:description ?itemdesc.
  SERVICE wikibase:label { bd:serviceParam wikibase:language "en"}
  OPTIONAL {
  FILTER(CONTAINS(LCASE(?itemdesc), "${optional}"@en))
  }
}
LIMIT 1`

  request(wdk.sparqlQuery(query), function (error, response, body) {

    var body = JSON.parse(body);
    
    if(!body.results.bindings.length){
      
      return callback(false);
      
    }
    
    console.log(body.results.bindings);
    
    var entity = body.entities[Object.keys(body.entities)[0]];

    // Get properties

    var statements = wdk.simplify.claims(entity.claims);

    var properties = Object.keys(statements);

    var result = {};

    if (!properties.length) {

      return callback(false);

    }

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

        // Sort keys by similarity to searched for property

        var best = Object.keys(result).sort(function (a, b) {

          if (similarity(a, property) > similarity(b, property)) {

            return -1

          } else if (similarity(a, property) < similarity(b, property)) {

            return 1

          } else {

            return 0;

          }

        });

        // Get similarity of top. Discard if less than 0.7.

        if (similarity(best[0], property) >= 0.5) {

          callback(result[best[0]]);

        } else {

          callback(false);

        }

      })

    });

  });


}

rf.tag(function (tagParams, output, callack) {

  if (tagParams[0] === "wiki") {

    return new Promise(function (resolve, reject) {

      getProperty(tagParams[1], tagParams[2], function (output) {

        resolve(output);

      })

    })
  }

})
