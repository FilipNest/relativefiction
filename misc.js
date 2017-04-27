rf.tag(function (tagParams, output) {

  if (tagParams[0] === "longitude") {

    return output.longitude;

  }

  if (tagParams[0] === "latitude") {

    return output.latitude;

  }

})
