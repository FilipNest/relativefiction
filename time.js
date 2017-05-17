var moment = require("moment");

var alter = function (time, operator, first, second) {
  
  if (operator !== "-" && operator !== "+") {

    return time;

  }

  if (operator === "+") {

    return time.add(first, second);

  } else if (operator === "-") {

    return time.subtract(first, second);
  }

}

rf.tag("hours24", function (tagParams, session) {

  var time = moment(session.time);

  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("H");

})

rf.tag("hours12", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("h");

})

rf.tag("hours12", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("m");

})

rf.tag("year", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("Y");

})

rf.tag("seconds", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("s");

})

rf.tag("ampm", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("a");

})

rf.tag("dayofweek", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("dddd");

})

rf.tag("dayofmonth", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("D");

})

rf.tag("dayofmonthsuffix", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("Do");

})
