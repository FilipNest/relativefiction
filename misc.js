rf.tag("latitude", function (tagParams, output) {

  return output.latitude;

}, {
  category: "general"
});

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

rf.tag("url", function (tagParams, output) {
  
  return new Promise(function (pass, fail) {

    if (typeof tagParams[0] !== "string") {

      fail("Must pass in a url");

    }

    try {

      request(tagParams[0], function (error, response, body) {
        
        if (error) {
          
          fail(error.message);

        } else {

          pass(body);

        }

      })

    } catch (e) {
      
      fail(e.message);

    }

  })

}, {
  category: "general"
})
