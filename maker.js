angular.element(document).ready(function () {
  angular.bootstrap(document, ['app']);
});

var variableHelper = function ($scope, $http) {

  $scope.$watchGroup(['foursquare', 'placeid', 'placeextra', 'date', 'misc', 'type'], function (newValues, oldValues, scope) {

    switch ($scope.type) {

      case "foursquare":

        var place = $scope.foursquare;
        var id = $scope.placeid;

        if (place && id && !$scope.placeextra) {

          $scope.output = "[" + place + "|" + id + "]";

        }

        if (place && id && $scope.placeextra) {

          if ($scope.placeextra !== "name") {

            $scope.output = "[" + place + "|" + id + "|" + $scope.placeextra + "]";

          } else {

            $scope.output = "[" + place + "|" + id + "]";

          }

        }

        break;

      case "date":

        if ($scope.date) {

          $scope.output = "[" + $scope.date + "]";

        } else {

          $scope.output = "";

        }

        break;

      case "misc":

        if ($scope.misc) {

          $scope.output = "[" + $scope.misc + "]";

        } else {

          $scope.output = "";

        }

        break;

    };

  });

};

var makerForm = function ($scope, $http) {

  $scope.edit = function (id) {

    var data = {};

    data.id = id;
    data.title = $("#story-name").val();
    data.author = $("#author-name").val();
    data.story = JSON.stringify($.trim($("#story").val()));
    data.email = JSON.stringify($.trim($("#author-email").val()));
    data.editkey = $("#editkey").val();

    $.post("/stories/edit/edit.php", data, function (data) {

      if (data) {

        window.location.href = "../" + id;

      }

    }).fail(function (e) {

      alert(e.responseText);

    });

  }

  $scope.upload = function () {

    //Prevent multiple upload

    $scope.uploadAllow = false;

    var data = {};

    data.title = $("#story-name").val();
    data.author = $("#author-name").val();
    data.story = JSON.stringify($.trim($("#story").val()));
    data.email = JSON.stringify($.trim($("#author-email").val()));

    $.post("/upload.php", data, function (data) {

      data = JSON.parse(data);

      if (data._id) {

        //Add saved story data

        $("#makerForm").html("<section id='saved'><div class='heading'><h1>" + data.title + "</h2><h2>by " + data.author + "</h3></div><h4>Has been successfully saved.</h4><p><a href='/stories/" + data._id + "'>The story can be viewed here.</a></p><p>Please take note of edit key <b>" + data.editkey + "</b> for when if you need to make any changes.</p><p>Thank you for supporting the project.</p></section>");

      };

    })

  };

  $scope.preview = function () {

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

    var title = $("#story-name").val();
    var author = $("#author-name").val();

    var data = {};

    data.text = JSON.stringify($.trim($("#story").val()));

    //Get date/time (strip out milliseconds for PHP)

    x = new Date()

    data.time = (x.getTime() - x.getTimezoneOffset() * 60 * 1000) / 1000;

    data.time = parseInt(data.time);

    //Get location
    navigator.geolocation.getCurrentPosition(function (location) {

      data.location = location.coords;

      $.ajax({
        type: "POST",
        url: "/api/story.php",
        data: data,
        success: function (result) {

          $.unblockUI(500);

          //Allow upload

          $scope.uploadAllow = true;
          $scope.$apply();

          //Clear preview

          $("#preview").html("");

          //Add title

          $("#preview").append("<div class='heading'>");

          $("#preview").append("<h1>" + title + "</h1>");

          if (author) {

            $("#preview").append("<h2>by " + author + "</h2>");

          }

          $("#preview").append("</div>");

          var output = $.parseHTML(result);

          $("#preview").append("<article></article>");

          $("#preview article").html($.parseHTML(result));

          $("#preview-button").text("Update preview");

        }
      });

    });

  };

};

var app = angular.module("app", []);

app.controller("variableHelper", ["$scope", variableHelper])
app.controller("makerForm", ["$scope", "$http", makerForm])
