<style type="text/css">
.time {font-size:1em; font-weight:600;}

</style>
<?php

 if (isset($_POST['zipval']))
 { 

  $zipcode=trim($_POST['zipval']);
  $locationName=$_POST['loc'];
  $url = "http://api.openweathermap.org/data/2.5/forecast/daily?zip=".$zipcode.",JP&units=metric&cnt=3&lang=en&appid=c0c4a4b4047b97ebc5948ac9c48c0559";  
  $json=file_get_contents($url);
  $data=json_decode($json,true);
  $longt=$data['city']['coord']['lon'];
  $latt=$data['city']['coord']['lat'];
 
echo "<div class='row px-4 mb-1'><h4 class='text-primary'>3 day forecast</h4></div>
       <div class='row px-2'>";
foreach ( $data['list'] as $day => $value ) {
   
  $desc = $value['weather'][0]['description'];
  $max_temp = number_format($value['temp']['max']);
  $min_temp = number_format($value['temp']['min']);
  $pressure = $value['pressure'];
  $humidity = $value['humidity'];
  $icon=$value['weather'][0]['icon'];
  $currentTime=$value['dt'];
  
?>      <div class="col-3 mx-auto text-center p-2 m-4" style="border:solid 1px #ddd;color: #4B515D; border-radius: 35px;">  
          <div><img src="http://openweathermap.org/img/w/<?php echo $icon; ?>.png" class="weather-icon" width="120"/></div>
          <div class="time"><?php echo date("jS F, Y l",$currentTime); ?></div>
          <div class="mt-2 mb-2 px-4" style="font-weight:bold;font-size:1.2em;"><?php echo ucwords($desc); ?></div>
          <div><span>Max: <?php echo $max_temp; ?> °C </span>&nbsp;<span class="min-temperature">Min: <?php echo $min_temp; ?> °C</span></div>
        </div>
<?php 
 }
   echo "</div>";
   echo "<div class='col-7 mt-4 mb-1 mr-2 px-4 text-center'>";  
   //MAP show
   echo "<div class='mb-1 bg-info p-2'><h4>Map</h4></div>";   
   echo "<iframe id=\"map_frame\" "
       . "width=\"100%\" height=\"500px\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" ".
         "src=\"https://www.google.com/maps/embed/v1/place?key=AIzaSyCIU5li4S62GnsKcQ3ty3-QoYFgD8HYOlo&q=".$latt.",".$longt."\"></iframe>"; 
   echo "</div>";              
 // place detail
    echo "<div class='col-5 px-2 mt-4 mb-1 ml-5'>";  
    echo "<div class='mb-1 bg-info p-2'><h4>Top Restaurants nearby</h4></div>
          <div><ul class='list-group list-group-light'>";  
   
  $a_url="https://maps.googleapis.com/maps/api/place/nearbysearch/json?key=AIzaSyCIU5li4S62GnsKcQ3ty3-QoYFgD8HYOlo&location=".$latt.",".$longt."&radius=5000&type=restaurant&fields=name,rating,opening_hours,user_ratings_total";
  $jsonplace=file_get_contents($a_url);
  $data_place=json_decode($jsonplace,true);
 
 $i=1;
  foreach ( $data_place['results'] as $res => $valuer ) {
  if ($valuer!='NULL')
  {
  $name = $valuer['name'];
  $rating = $valuer['rating'];
  $rating_total=$valuer['user_ratings_total'];
   if ($i<=10 && $rating>=3.6)
   {
  ?>
   <li class="list-group-item d-flex justify-content-between align-items-start border-0">
    <div class="ms-2 me-auto">
      <div class="fw-bold"><?php echo $name; ?></div>
         Rating <?php echo $rating; ?>
    </div>
  </li>
<?php 
   }
  if ($rating>=3.6) $i++;
  }
}
 echo "</ul></div></div>";
}
?>
 