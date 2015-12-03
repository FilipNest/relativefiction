$("document").ready(function () {

  var selector = "article";

  data = {};

  //Get text

  data.text = JSON.stringify($(selector).html());

  //Get date/time (strip out milliseconds for PHP)

  x = new Date()
  data.time = (x.getTime() - x.getTimezoneOffset() * 60 * 1000) / 1000;

  data.time = parseInt(data.time);

  //Get location
  navigator.geolocation.getCurrentPosition(function (location) {

    data.location = location.coords;

    $.blockUI({
      css: {
        border: 'none',
        padding: '15px',
        backgroundColor: '#000',
        'border-radius': '10px',
        opacity: .5,
        color: '#fff',
      },
      message: "Localising, please wait..."
    });

    $.ajax({
      type: "POST",
      url: "../api/story.php",
      data: data,
      success: function (result) {

        $.unblockUI(500);

        $("#main").show();
        $("article").html($.parseHTML(result));
        $("article").fadeIn();
        $(".edit-link").css("display", "inline-block");

      },
    });

  })
});
