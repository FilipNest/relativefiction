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

rf.tag("minutes", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("m");

}, {
  category: "time"
})

rf.tag("hour24", function (tagParams, session) {

  var time = moment(session.time);

  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("H");

}, {
  category: "time"
})

rf.tag("hour12", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("h");

}, {
  category: "time"
})

rf.tag("hours12", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("m");

}, {
  category: "time"
})

rf.tag("year", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("Y");

}, {
  category: "time"
})

rf.tag("seconds", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("s");

}, {
  category: "time"
})

rf.tag("ampm", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("a");

}, {
  category: "time"
})

rf.tag("dayofweek", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("dddd");

}, {
  category: "time"
})

rf.tag("dayofmonth", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("D");

}, {
  category: "time"
})

rf.tag("dayofmonthsuffix", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("Do");

}, {
  category: "time"
})

rf.tag("monthofyear", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[1], tagParams[2], tagParams[3]);

  return time.format("MMMM");

}, {
  category: "time"
})
