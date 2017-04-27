// Require main RF code

global.rf = require("./relativefiction");

require("./time");

rf.process({
  longitude: "55",
  latitude: "1000",
  "text": `hello world the time is {hours12 - 1 hour}:{minutes - 20 minute}{ampm} {year + 3000 years}`,
  time: Date.now()
}).then(function (output) {

  console.log(output);

}, function (error) {

  console.error(error);

})

// Setup server

const express = require('express');
const bodyParser = require('body-parser');

var server = express();
var port = 3000;

// parse application/x-www-form-urlencoded
server.use(bodyParser.urlencoded({
  extended: false
}))

// parse application/json
server.use(bodyParser.json())

// serve static files
server.use(express.static('static'));

// Start server

server.listen(port);

server.post("/", (req, res) => {

  rf(req.body).then((output) => {

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
