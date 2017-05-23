// Require main RF code

module.exports = function (config = {}) {

  global.rf = require("./core");

  rf.config = config;

  require("./time");
  require("./misc");
  require("./foursquare");
  require("./weather");

  require("./helpers")(global.Handlebars);

  // Setup server

  const express = require('express');
  const bodyParser = require('body-parser');

  var server = express();
  var port = rf.config.port || 80;

  rf.server = server;

  // Set static directory if one set

  if (rf.config.static) {

    server.use(express.static(rf.config.static));

  }

  // parse application/x-www-form-urlencoded
  server.use(bodyParser.urlencoded({
    extended: false
  }));

  // parse application/json
  server.use(bodyParser.json());

  // serve static files
  server.use(express.static('static'));

  rf.server = server;

  // Start server

  server.listen(port);

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

    });

  });

  return rf;

};
