var moment = require("moment");

rf.tag(function (tagParms, output) {

  var time = moment(output.time);

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
    case "dayofweek":
      return time.format("dddd");
    case "dayofmonth":
      return time.format("D")
    case "dayofmonthsuffix":
      return time.format("Do");
  }

}, 2)
