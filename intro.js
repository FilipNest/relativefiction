//Add preview boxes to codeblocks

$(document).ready(function () {

  $(".codeblock").append("<button class='localise'>Localise</button>");

  //Convert code tags

  $.each($(".codeblock"), function (index, inside) {

    var content = $(inside).html();

    var codeTags = content.match(/[^[\]]+(?=])/g);

    $.each(codeTags, function (index, tagcontent) {

      var tag = "[" + tagcontent + "]";

      $(inside).html($(inside)
        .html()
        .split(tag)
        .join("<span class='tag'>" + tag + "</span>"))

    });

    var content = $(inside).html();

    var ifTags = content.match(/{(.*?)}/g);

    if (!ifTags) {

      ifTags = [];

    }

    $.each(ifTags, function (index, tagcontent) {

      $(inside).html($(inside)
        .html()
        .split(tagcontent)
        .join("<span class='if'>" + tagcontent + "</span>"))

    });

  });

  $("body").on("click", ".localise", function (e) {

    var button = event.target;

    var block = $(event.target).parent().find(".inner");

    var data = {};

    var rawtext = $.trim($(block).html());

    data.text = JSON.stringify($.trim($(block).text()));

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
        url: "/api/story.php",
        data: data,
        success: function (result) {
      
          $.unblockUI(500);

          // Put in result

          $(block).attr("data-raw", rawtext);
          $(block).html($.parseHTML(result));
          $(block).html($.trim($(block).html()))
          $(button).attr("class", "reset");
          $(button).html("Reset");

        },
      });

    })

  });

  $("body").on("click", ".reset", function (e) {

    var button = event.target;

    var block = $(event.target).parent().find(".inner");

    $(block).html($(block).attr("data-raw"));

    $(button).attr("class", "localise");
    $(button).html("Localise");

  });

});
