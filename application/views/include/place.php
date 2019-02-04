  <html>
       <head>
       <style type="text/css">
               body {
                       font-family: sans-serif;
                       font-size: 14px;
               }
       </style>
       <title>Google Maps JavaScript API v3 Example: Places Autocomplete</title>
       <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDqld41CE4TjcM4bC2MXegnrL3aItT4uwo&amp;sensor=false&amp;libraries=places" type="text/javascript"></script>
        <script type="text/javascript">
               function initialize() {
                       var input = document.getElementById('searchTextField');
                       var autocomplete = new google.maps.places.Autocomplete(input);
               }
               google.maps.event.addDomListener(window, 'load', initialize);
       </script>
       </head>
           <body>
               <div>
                       <input id="searchTextField" type="text" size="50" placeholder="Enter a location" autocomplete="on">
               </div>
           </body>
       </html>