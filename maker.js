angular.element(document).ready(function () {
  angular.bootstrap(document, ['app']);
});

var variableHelper = function ($scope, $http, $sce, $rootScope) {

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

var makerForm = function ($scope, $http, $sce, $rootScope) {

  window.preview = function () {

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

          //Clear preview

          $("#preview").html("");

          //Add title

          $("#preview").append("<div id='heading'>");
          
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

app.controller("variableHelper", ["$scope", "$attrs", "$http", "$sce", "$rootScope", variableHelper])
app.controller("makerForm", ["$scope", "$attrs", "$http", "$sce", "$rootScope", makerForm])
