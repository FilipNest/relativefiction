<!doctype HTML>
<?php

ob_start();

?>
  <html>

  <head>
    <title>%TITLE%</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="A tool by Filip Hnízdo for creating dynamic stories relative to a reader's place, time, location, weather and perhaps more.">
    <link rel="shortcut icon" href="/favicon.ico" />
    <script src="//use.typekit.net/vct1gvu.js"></script>
    <script>
      try {
        Typekit.load();
      } catch (e) {}

    </script>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="/jquery.blockUI.js"></script>

    <link rel="stylesheet" href="/main.css">
  </head>

  <body>
    <nav>
      <ul>
        <li><a class="home" href="/">Local Stories</a></li>
        <li><a href="/stories/">Read</a></li>
        <li><a href="/maker.php">Write</a></li>
        <li class="github">
          <a class="github-button" href="https://github.com/FilipNest/localstories/issues" data-style="mega" aria-label="Issue FilipNest/localstories on GitHub">Bugs/Requests</a>
          <script async defer id="github-bjs" src="https://buttons.github.io/buttons.js"></script>
        </li>
        <li><a href='http://twitter.com/filipnest'>by FilipNest</a></li>
      </ul>
    </nav>
    <section id="main">
