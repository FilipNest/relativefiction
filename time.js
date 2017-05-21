var moment = require("moment");

rf.registerGlobals(["add", "minus", "days", "years", "months", "minutes", "seconds", "hours", "second", "minute", "hour", "day", "month", "year"]);

var alter = function (time, operator, first, second) {
    
  if (operator !== "add" && operator !== "minus") {

    return time;

  }
  
  if (operator === "add") {

    return time.add(first, second);

  } else if (operator === "minus") {

    return time.subtract(first, second);
  }

}

rf.tag("minutes", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("mm");

}, {
  category: "time"
})

rf.tag("hour24", function (tagParams, session) {

  var time = moment(session.time);

  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("H");

}, {
  category: "time"
})

rf.tag("hour12", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("h");

}, {
  category: "time"
})

rf.tag("hours12", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("m");

}, {
  category: "time"
})

rf.tag("year", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("Y");

}, {
  category: "time"
})

rf.tag("seconds", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("s");

}, {
  category: "time"
})

rf.tag("ampm", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("a");

}, {
  category: "time"
})

rf.tag("dayofweek", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("dddd");

}, {
  category: "time"
})

rf.tag("dayofmonth", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("D");

}, {
  category: "time"
})

rf.tag("dayofmonthsuffix", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("Do");

}, {
  category: "time"
})

rf.tag("monthofyear", function (tagParams, session) {

  var time = moment(session.time);
  time = alter(time, tagParams[0], tagParams[1], tagParams[2]);

  return time.format("MMMM");

}, {
  category: "time"
})
