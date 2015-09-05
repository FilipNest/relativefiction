<?php include "header.php"; 

$title = "Local Stories - Home";

?>

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>

  <script src="maker.js"></script>

  <link rel="stylesheet" href="maker.css" />

  <h1>Write a story...</h1>

  <form ng-controller="makerForm">

    <label for="title">Title of story:</label>
    <textarea rows="4" id="story-name" name="title"></textarea>

    <label for="author">Author:</label>
    <input id="author-name" name="author" />

    <div id="variables" ng-controller="variableHelper" ng-cloak>
      <h3>Variable helper</h3>
      <p>Use this to create variables you can push in.</p>
      <p><small>Please note that it doesn't do conditionals as although the syntax is hopefully simple enough for humans to write, a form to create them on the fly is a bit of a nightmare. <a href="/">Read the documentation for help</a>.</small>
      </p>

      <form>

        <label for="type">Type of variable</label>

        <select ng-model="type" name="type">
          <option value="" selected="selected">Pick a value</option>
          <option value='date'>Date/Time</option>
          <option value='foursquare'>Place</option>
          <option value='misc'>Weather/Location</option>
        </select>
        <br />
        
        <div ng-show="type == 'foursquare'">
          <label for="placelist">Place type</label>
          <select ng-model="foursquare" name="placelist">
            <option value="" selected="selected">Pick a place category</option>

            <?php

$venues = json_decode(file_get_contents("api/venuecategories.json"), TRUE);

ksort($venues);

   foreach($venues as $key => $value) {
           print "<option>".$key."</option>";
       }

          ?>
          </select>
          <br />
          <label for="place-id">Place id</label>
          <select ng-model="placeid" name="place-id">
            <option value="" selected="selected">Pick an id</option>
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
          </select>
          <br />
          <label for="place-extra">Place information</label>
          <select ng-model="placeextra" name="place-id">
            <option value="" selected="selected">Name</option>
            <option value="distance">Distance</option>
            <option value="street">Street</option>
          </select>
          <br />
        </div>

        <div ng-show="type == 'date'">

          <label for="date-time">Date/time</label>

          <select ng-model="date" name="date-time">
            <option value="" selected="selected">Pick an variable</option>
            <option value='dayofweek'>Day of week</option>
            <option value='monthofyear'>Month of year</option>
            <option value='dayofmonth'>Day of month</option>
            <option value='dayofmonthsuffix'>Day of month suffix</option>
            <option value="year">Year</option>
            <option value="sunsethour">Sunset hour</option>
            <option value="hourstosunset">Hours to sunset</option>
            <option value="sunrisehour">Sunrise hour</option>
            <option value="hourstosunrise">Hours to sunrise</option>

          </select>
          <br />

        </div>

        <div ng-show="type == 'misc'">
          <label for="weather-location">Weather/location</label>

          <select ng-model="misc" name="weather-location">
            <option value="" selected="selected">Pick an variable</option>
            <option value='weather'>Weather</option>
            <option value='longitude'>Longitude</option>
            <option value='latitude'>Latitude</option>
            <option value='country'>Country</option>

          </select>
        </div>

      </form>
      <span ng-if="output" class="variable">{{output}}</span>

      <span class="help" ng-show="type == 'date' && date">Note: You can add a pipe after the date variable (before the closing bracket with a +1 or -1 (other numbers work!) to offset most values. Previous years, months, days etc. Putting them all in a form would be silly.) </span>

      <span class="help" ng-show="type == 'foursquare'">Note: Foursquare provides a LOT of locations. Not all of them will actually provide results anywhere near a reader's location. Stick to the more obvious ones if you're getting errors or not finding results when previewing.</span>

      <br />

    </div>

    <label for="story">Your story:</label>
    <textarea id="story" name="story"></textarea>

    <button id="preview-button" onclick="window.preview()">Preview your story</button>
    <br />

  </form>

  <div id="preview"></div>

  <br />

  <?php include "footer.php"; ?>
