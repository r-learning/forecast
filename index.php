<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> 
  <!-- font awesome css-->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
  <title>3 days forecast of Japan cities</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>

<script type="text/javascript">

//Google Geocoding api is used for getting complete location name: town, city, prefecture
//However OpenWeatherApi contains city and country name, it does not return location name as precise as Google Geocoding api
// Reference Japan Postal Code: https://www.japanpostalcode.net/tokyo/toshima-ku/page9.html
$(document).ready(function() {
 
});
$(function() {
 $('#zip').inputmask();

var placeID;
var lat, lng;
    // parsing Google Geocoding API response
  function geocodeResponseAddress(responseJSON) 
    { 
      var parsedLocalities = [];
      if(responseJSON.results.length) {
        for(var i = 0; i < responseJSON.results.length; i++)
        {
          var result = responseJSON.results[i];      
          var locality = {};
            placeID=result.place_id;
            lat=result.geometry.location.lat;
            lng=result.geometry.location.lng;
              
           for(var j=0; j<result.address_components.length; j++)
           {
            var types = result.address_components[j].types;
            for(var k = 0; k < types.length; k++) 
            {
              if(types[k] == 'locality') {
                locality.city = result.address_components[j].long_name;
               } else if(types[k] == 'administrative_area_level_1') {
                locality.state = result.address_components[j].short_name;
               } else if (types[k]=='sublocality') {
                locality.sublocal = result.address_components[j].long_name; 
               } 
            }
           }
          parsedLocalities.push(locality);
        }
      } else {
        alert('Error: No Address found for this Postal Code! Please check the Postal code and try again!');
      }
      return parsedLocalities;
    }
 
       
 $("#btnSubmit").click(function() {
   $('#locationName').val('');
   $('#response').html('');
     var zip = $('#zip').val();
     var completeLocationName='';
    // google API key
     var api_key = 'AIzaSyCIU5li4S62GnsKcQ3ty3-QoYFgD8HYOlo';
     if(zip.length){
      //make a request to the google geocode api with the zipcode
        $.get('https://maps.googleapis.com/maps/api/geocode/json?address='+zip+'&key='+api_key).then(function(response)
        { 
          var addressResponse = geocodeResponseAddress(response);
          completeLocationName=addressResponse[0].state + ', '+ addressResponse[0].city  + ', ' + addressResponse[0].sublocal;
          $('#locationName').val(completeLocationName);
          $('#place').val(addressResponse[0].placeID);
        });
     }
  // post to response.php to get weather for the location  
  var data_post={zipval: zip, loc: completeLocationName, place: placeID}; // data to post to response.php
    $.ajax({
         url: 'response.php', // action to be perform
         type: 'POST',       //type of posting the data
         data: data_post,    // posting values
         dataType: 'html',
         success: function (e, data) {
            $('#response').html(e);
         } 
    });    
 });
});

</script>
<div class="container">
 <div class="page__wrapper shadow-lg">
        <div class="row no-gutters">    
    <?php 
     
     $zipcode=(isset($_POST['zip'])) ? $_POST['zip']: '';
     $loc=(isset($_POST['loc'])) ? $_POST['loc']: '';
   
     ?>
      <div class="col-12 mx-auto mb-6">      
      <form class="form-inline" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="myForm">
         <div class="row">
          <div class="form-group mx-sm-3 mb-2 mt-4 col-sm-4">
            <label for="zip" class="sr-only">Postal code</label>
            <input type="text" class="form-control" id="zip" name="zip" placeholder="Type your Postal code" data-inputmask="'mask': '999-9999'" value="<?php echo $zipcode;?>">
          
          </div>
           <div class="form-group mb-2 mt-4 col-sm-4">
            <button type="button" class="btn btn-primary mb-2" name="btnSubmit" id="btnSubmit">Submit</button>
           </div>
         </div>
        <div class="form-group col mt-2 mb-2 px-3">
           <input type="text" id="locationName" name="locationName" value="<?php echo $loc;?>" style="border:0; font-size: 1.8rem;width:100%;">
        </div>
       </form>
       <div class="row mb-2 px-3" id="response"></div>
      
     </div>       
   </div>
 </div>
</div>
 
</body>
</html