# Local stories

Local Stories is a tool for localising text for its reader. It was created for making relative fiction that changes depending on where a reader is, what the weather is like and what time of day or year it is. It uses geolocation, the Foursquare venues API, the OpenWeatherMap API and a lot of PHP time and date functions.

Visit http://relativefiction.com to find out more about the project, its syntax and how it works along with interactive examples. You can also use this site to upload stories using the software so you don't even need to download this.

If you do want to download it and use it on your own project, here's what you need to do.

* Download the files.
* Register for Open Weather Map and Foursquare API accounts. Put these details in a renamed secrets.php file (renamed from the default one in the repository).
* Make a POST call with geolocation data, time and date and the text you wish to translate (variables syntax is on the http://relativefiction.com homepage).  Here's a quick example using jQuery to read an article element on a web page, process it and replace it.

```javascript

$("document").ready(function () {
    
  var selector = "article";

  data = {};

  //Get text

  data.text = JSON.stringify($(selector).html());
      
  //Get date/time (strip out milliseconds for PHP)

x = new Date()
data.time = (x.getTime() - x.getTimezoneOffset()*60*1000)/1000;
  
  data.time = parseInt(data.time);
  
  //Get location
  navigator.geolocation.getCurrentPosition(function (location) {

    data.location = location.coords;

    $.ajax({
      type: "POST",
      url: "/story.php",
      data: data,
      success: function (result) {
  
        $("article").html($.parseHTML(result));
        $("article").fadeIn();

      },
    });

  })
});

```

Don't hesitate to get in touch if you need help. Please contribute pull requests and issues if you have any. I've released the code under the Apache 2 licence at the moment but this can probably change if you suggest good reasons for it to. 
