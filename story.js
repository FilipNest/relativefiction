$("#localise").click(function () {
    
  var selector = "#input";

  data = {};

  //Get text

  data.text = JSON.stringify($(selector).val());
  
  if($(selector).val().length < 1){
    
    data.text = JSON.stringify($(selector).attr("placeholder"));
    
  }
  
  //Get date/time (strip out milliseconds for PHP)

x = new Date()
data.time = (x.getTime() - x.getTimezoneOffset()*60*1000)/1000;
  
  data.time = parseInt(data.time);
  
  //Get location
  navigator.geolocation.getCurrentPosition(function (location) {

    data.location = location.coords;

    $.ajax({
      type: "POST",
      url: "story.php",
      data: data,
      success: function (result) {
  
        $("#output").html($.parseHTML(result));

      },
    });

  })
});