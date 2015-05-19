$("#localise").click(function () {
    
  var selector = "#input";

  data = {};

  //Get text

  data.text = JSON.stringify($(selector).val());
  
  //Get date/time (strip out milliseconds for PHP)
  
   data.time = (new Date().getTime()/1000).toFixed();

  //Get location
  navigator.geolocation.getCurrentPosition(function (location) {

    data.location = location.coords;

    $.ajax({
      type: "POST",
      url: "story.php",
      data: data,
      success: function (result) {
        
        console.log(result);
  
        $("#output").html($.parseHTML(result));

      },
    });

  })
});