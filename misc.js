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

      request({
        uri: tagParams[0],
        timeout: 1000
      }, function (error, response, body) {

        if (error) {

          fail(error.message);

        } else {

          if (tagParams[1] && typeof tagParams[1] === "string" && tagParams[1].toLowerCase() === "json" && typeof tagParams[2] === "string") {

            try {

              var feed = JSON.parse(body);

              var selector = tagParams[2].split(".");

              var currentPosition = feed;

              selector.forEach(function (value) {

                if (currentPosition[value]) {

                  currentPosition = currentPosition[value];

                } else {

                  fail("No such value in feed " + value)

                }

              })

              pass(currentPosition);

            } catch (e) {

              fail(e.message);

            }

          } else {

            pass(body);

          }


        }

      })

    } catch (e) {

      fail(e.message);

    }

  })

}, {
  category: "general"
})
