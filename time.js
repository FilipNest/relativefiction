var moment = require("moment");

// Create moment object for later parsing

rf.alter(function (output, next) {

  output.moment = moment(output.time);

  next();

});

rf.tag(function (tagParms, output) {

  var time = output.moment;

  if (tagParms[1] === "+") {

    time = time.add(tagParms[2], tagParms[3]);

  } else if (tagParms[1] === "-") {

    time = time.subtract(tagParms[2], tagParms[3]);

  }

  switch (tagParms[0]) {
    case "hours24":
      return time.format("H");
    case "hours12":
      return time.format("h");
    case "minutes":
      return time.format("m");
    case "year":
      return time.format("Y");
    case "seconds":
      return time.format("s");
    case "ampm":
      return time.format("a");
  }

}, 2)
