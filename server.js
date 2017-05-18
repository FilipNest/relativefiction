// Require main RF code

module.exports = function (config = {}) {

  global.rf = require("./core");

  rf.config = config;

  require("./time");
  require("./misc");
  require("./foursquare");
  require("./weather");

  // Setup server

  const express = require('express');
  const bodyParser = require('body-parser');

  var server = express();
  var port = rf.config.port || 80;

  // parse application/x-www-form-urlencoded
  server.use(bodyParser.urlencoded({
    extended: false
  }))

  // parse application/json
  server.use(bodyParser.json())

  // serve static files
  server.use(express.static('static'));
  
  rf.server = server;

  // Start server

  server.listen(port);

  // Get info on tags
  
  server.get("/taghelp", function (req, res) {

    res.json(rf.tagsInfo());

  })

  server.post("/", (req, res) => {

    rf.process(req.body).then((output) => {

      // Returns Object {result: String, errors:[String], longitude:String, latitude:String, original:String, time: Date}

      res.status(200);
      res.json(output);

    }, (output) => {

      // Called if any fatal errors. Returns output Object (see above) but deletes result if set but changes status to 400 Bad Request.

      delete output.result;

      res.status(400);
      res.json(output);

    })

  })

  // Handle 404s

  server.use((req, res) => {

    res.status(404);
    res.send("404");

  })

  return rf;

}
