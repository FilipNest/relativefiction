var config = {};

process.argv.forEach(function (val, index, array) {

  if (val.indexOf("=") !== -1) {
    val = val.split("=");
    config[val[0]] = val[1];
  }

});

require("./server.js")(config);
