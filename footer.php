</section>

<script>
  (function(i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function() {
      (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
      m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
  })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

  ga('create', 'UA-63609457-1', 'auto');
  ga('send', 'pageview');

</script>

</body>

</html>

<?php

$buffer=ob_get_contents(); 
ob_end_clean(); 
$buffer=str_replace("%TITLE%",$title,$buffer); 
echo $buffer;


?>
