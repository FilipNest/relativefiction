<?php include "header.php"; 

$title = "Local Stories - Home";

?>

  <script src="intro.js"></script>

  <link rel="stylesheet" href="/intro.css" />

  <h1>Local Stories</h1>

<h2>A tool by for creating dynamic stories relative to a reader's place, time, location, weather and perhaps more*</h2>

  <small>*This first launch is focused on places (using the Foursquare API), times/dates, and weather (using the OpenWeatherMap API) but adding related music (via the Last.fm API), names of a reader's friends (via Facebook) and more could certainly be possible in the future. Any suggestions would be very welcome. Tap the request button in the top right to suggest ideas.</small>

  <h3>An example</h3>

  <p>Throughout this page you can tap the <b>Localise</b> buttons to see what a translation of the text would look like where you are. Then hit <b>Reset</b> to go back to the raw text.</p>

  <div class="codeblock">
    <div class="inner">

      I love [dayofweek]s. It was around [hours12][hoursampm] on a [dayofweek] when I first saw its outlines. I was sitting in [park|1] and looking out at the sky. It was a [weather] day much like this one. Wings. Clearly a pair of wings. Floating somewhere towards [supermarket|1|street] as if going about their everyday business. This month is even more special. It happened in [monthofyear]. So I walk over to [park|1] and look out. But there's never anything anymore...

    </div>

  </div>

  <h3>How it works</h3>

  <p>A writer writes a story and at certain points, replaces static words like days of the week and place names with special placeholders or <em>variables</em>.</p>
 
  <p>Alongside variables, writers can put in logic to show text if some statements are true and some other text if they are not.</p>
  <p>Say you are writing a piece of science fiction and you get frustrated by the thought of the year you chose arriving too quickly, thereby making your choice of date laughabily ancient.</p>
  <p>You hope they'll have jetpack factories in 2035 but don't know for sure, so you write a conditional statement that sets the story 30 years in the future, unless there's already a jetpack factory around the corner from where the reader is.</p>
  <div class="codeblock">
    <div class="inner">
      It was {if +[jetpack factory|1|distance]<5000|[year]|[year|+30]}AD. I returned the jetpack I'd borrowed. I never got on with those things.
    </div>
  </div>
  <small>You'll probably find conditionals are most useful for things like the weather, time of day, country or the distance to the nearest beach.</small>

        <p>Once a reader starts reading a story and agrees to share their location (longitude and latitude picked up through their web browser), all relevant data is collected and swapped out to make a story that is unique to that reader but still keeps the original essense of the story.</p>

        <p>Data for places is pulled in from <a href="http://foursquare.com">Foursquare</a>, a wonderful place-based social network and directory. Data for weather and sunset and sunrise times is brought in from the kind people who run <a href="http://openweathermap.org">OpenWeatherMap</a>.</p>

        <p><small>Side note: As mentioned, the tool can be used for more than just fiction, it should work on HTML so you might even be able to use it in your application to show pictures, video or other data relevant to a viewer/reader.</small></p>

        <h3>Types of variable</h3>
        <h4>Places</h4>
        <p>To put a place in your story, a zoo or japanese restaurant for example, first look up the name of the category of place on <a href="https://developer.foursquare.com/categorytree">this list of place categories from Foursquare</a> to make sure it's available. The list used to match place categories in text is updated live from this Foursquare list so if squirrel cafes (or jetpack factories) suddenly become a huge thing they might be a useable variable in the future. Parent categories contain everything below them so you can use them to get less specific variables.</p>
        <p>Next, enclose the name of the category in square brackets followed by a | (pipe) character and a unique number for that place. The number is there in case you want to re-use the same place in multiple parts of the story or have multiple places of the same category.</p>

        <div class="codeblock">
          <div class="inner">
            We went out for a few drinks at [nightclub|1] and somehow ended up naked and shivering in the waters of the [river|1]
          </div>
        </div>
        <small>The locations will get swapped out by the location of that category nearest to the reader. The word "The" is stripped out if a place starts with it.</small>
        </p>
        <h4>Other information about places</h4>
        <p>You might want to use other information about a place, like the street it's on or how far away it is from the reader.</p>
        <p>If you've got how places work this bit should be easy as it's just another pipe character after the ID.</p>
        <div class="codeblock">
          <div class="inner">
            He lived on [park|1|street]. I never liked that part of town after what happened to Emily.
          </div>
        </div>
        <small>Putting the word "street" after the pipe will get translated to the street the venue is on. Parts of street names containing numbers are stripped out (11,33b...). If there's no street set it defaults to "The Street". Might be useful for providing a general street for things to happen in.</small>

        <div class="codeblock">
          <div class="inner">
            She arranged to meet me at [zoo|1]. For some reason she never wanted to see me more than [zoo|1|distance] meters from an animal park.
          </div>
        </div>
        <small>Distance in meters from a reader's location to a place. Useful for conditionals like if you want to find out how far someone is from the sea.</small>

        <div class="codeblock">
          <div class="inner">
            I love coming back to the smell of [park|1|city]. It reminds me of that night in [chinese restaurant|1] all those years ago.
          </div>
        </div>
        <small>The city or town the venue is in. Defaults to "The City" if the venue doesn't have one set.</small>

        <h4>Other variables</h4>
        <p>It's not just places you can swap into text, here are some other variables you can use.</p>

        <div class="codeblock">
          <div class="inner">
            It was [dayofweek]. She always came round on [dayofweek]s. Yesterday would have been better. [dayofweek|-1]s were always better than [dayofweek]s.
          </div>
        </div>
        <small>The day of the week. Monday, Tuesday... etc. Add a pipe character followed by a positive or negative number to add or substract days.</small>
        <div class="codeblock">
          <div class="inner">
            [hours12][hoursampm]. If only I had set my alarm to [hours12|-2]:[minutes|+30][hoursampm|-1]. It may have saved my life. The dial read [hours24]:[minutes] and it was over.
          </div>
        </div>
        <small>Various ways of getting times of day. <b>hoursampm</b> returns am if it's before noon and pm if after. It can be offset by adding positive or negative hours after a pipe character. <b>hours24</b> and <b>hours12</b> return the current hour in either 24 hour time or 12 hour time. They can also be offset by positive or negative hours. <b>minutes</b> returns the current time in minutes. Add positive or negative minutes after a pipe to add or subtract minutes from the time.</small>
        <div class="codeblock">
          <div class="inner">
            It was [monthofyear]. Five years after they caught the fish. I still have the newspaper cuttings from [monthofyear] [dayofmonth][dayofmonthsuffix], [year|-5].
          </div>
        </div>
        <small>Get the current month or current year. You can add a pipe followed by a positive/negative count of months/years to set it forward or back in time. <b>dayofmonth</b> returns the day of the month in number form (you can offset this by days after a pipe character). <b>dayofmonthsuffix</b> gives you the English language suffix for that day "th","st","rd" or "nd". It also works with day offsets</small>
        <div class="codeblock">
          <div class="inner">
            [longitude],[latitude] said the screen. This was the right place. It's what it said on the paper. But my instinct told me [country] wasn't even the right part of the world.
          </div>
        </div>
        <small>Longitude and latitude for those who need it. Also a country name from <a href="https://gist.github.com/vxnick/380904">this handy PHP array mapping country codes to names.</a></small>

        <div class="codeblock">
          <div class="inner">

            It was a [weather] day like this one when my ears stopped working.
          </div>
        </div>
        <small>The reader's current weather. This can return one of the following values: stormy, rainy, snowy, clear, cloudy, calm, windy, scorching, freezing. You can re-write them using conditionals.</small>

        <div class="codeblock">

          <div class="inner">
            It could only happen exactly at sunset. [sunsethour] hundred hours. That was [hourstosunset] hours away.
          </div>

        </div>
        <small>Hour (in 24 hour time) of the next sunset. You can also use <b>hourstosunset</b> to get how much time until the next sunset. Both useful in conditionals.</small>
        <div class="codeblock">

          <div class="inner">

            It could only happen exactly at sunrise. [sunrisehour] hundred hours. That was [hourstosunrise] hours away.
          </div>

        </div>
        <small>Same as sunset but for sunrise. See above.</small>

        <p>More variables will come in the future but that should hopefully give you a nice start. Please feel free to suggest some if you feel anything is missing.</p>

        <h3>Conditionals</h3>

        <p>Conditionals are the most complex part of the system but hopefully not so complex that you don't try them out as they're probably the best part of it.</p>
        <p>They're enclosed in curly brackets with the word <em>if</em> followed by a space after the opening bracket.</p>
        <p>After this space and before the second bracket, you can put in statements that should either be true or false. First decide if a statement should be true or false and put in a + or a - sign. Then put in the name of the value you wish to compare to something (this can be static such as the number 5 or dynamic such as [sunsethour]). Then put in an operator from either == (equals (two == signs)), > (greater than) or
          < (less than).</p>
            <p>Then put the name of the value you want to use this operator to compare the first value to. This can be static text or a dynamic variable.</p>
            <p>Put in as many of these rules as you want seperated by commas.</p>
            <p>Then put in a pipe character | followed by what you want to write if this set of rules passes.</p>
            <p>Then put in another pipe character | followed by what to write if the rule does not pass.</p>
            <p>Finally, close the curly bracket.</p>
            <p>Here's an example that checks if the reader has missed Wednesday's sunset.</p>
            <div class="codeblock">
              <div class="inner">
                {if +[sunsethour]>[hours24],+[dayofweek]==Wednesday|You can still catch sunset on Wednesday|You missed Wednesday's sunset}
              </div>
            </div>

            <br />

            <?php include "footer.php"; ?>
