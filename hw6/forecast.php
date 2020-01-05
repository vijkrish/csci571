<html>
    <head> 
        <title> Weather Search </title>
        <style>
            #searchBox {
                width: 50%;
                height: 260px;
                background-color: rgb(66, 182, 66);
                margin: auto;
                margin-top: 50px;
                color: white;
                border-radius: 10px;
            }
            #searchBox h2 {
                font-size: 40px;
                font-weight: 400;
                font-style: italic;
                text-align: center;
            }
            #searchForm {
                margin-left: 40px;
            }
            #searchForm input, #searchForm select {
                margin-top: 5px;
                width: 140px;
            }
            #searchForm select {
                margin-left: -10px;
                width: 200px;
            }
            #separator hr {
                background-color: white;
                width: 5px;
                height: 120px;
                margin-top: -80px;
                margin-left: 420px;
            }
            table {
                width: 100%;
                margin-top: -30px;
                color: white;
                font-size: 20px;
                font-weight: 800;
            }
            #weatherTable, #weatherTable th, #weatherTable td  {
                background-color: rgb(127, 188, 212);
                width: 1000px;
                margin: auto;
                margin-top: 20px;
                border: 1px solid blue;
                border-collapse: collapse;
                text-align: center;
            }
            #weatherTable {
                width: 1000px;
            }
            #weatherTable th, #weatherTable td {
                height: 50px;
            }
            #typeInput {
                width: 60%;
            }
            #locationInput {
                margin-top: 5px; 
                vertical-align: top;
                padding-left: 80px;
            }
            #buttons {
                margin-left: 300px;
                bottom: 0;
            }
            #errorLog {
                width: 30%;
                /* border: 1px solid black; */
                background-color: rgb(212, 211, 211);
                /* height: 20px; */
                margin: auto;
                margin-top: 20px;
                text-align: center;
            }
            #weatherCard, #dailyWeatherCard {
                width: 500px;
                height: 300px;
                background-color: #5EC4F4;
                color: white;
                margin: auto;
                border-radius: 10px;
                padding-left: 20px;
                padding-top: 20px;
                padding-right: 20px;
            }
            
            #dailyWeatherCard {
                height: 500px;
                background-color: #A8D0D8;
            }
            #cityCard, #summaryCard {
                font-size: 35px;
                font-weight: 800;
            }
            .imageLogo {
                height: 25px;
                width: 25px;
            }
            #valueCard {
                text-align: center;
                margin-top: 30px;
                margin-left: -20px;
                width: 540px;
            }
            #timezoneCard {
                font-size: 15px;
                font-weight: bold;
            }
            #temperatureCard {
                font-size: 90px;
                font-weight: bold;
            }
            #degreeImg {
                height: 15px;
                width: 15px;
                vertical-align: top;
                padding-top:10px;
                padding-left: -10px;
            }
            #degree {
                vertical-align: top;
                margin-left: -20px;
            }
            .statusLogo {
                height: 45px;
                width: 45px;
            }
            .summaryLink:hover {
                cursor: pointer;
            }
            #dailyIcon {
                height: 250px;
                width: 250px;
            }
            #dailyDetailsTable {
                margin-top: 20px;
            }
            #flipText {
                content: "\25bc";
                border: 1px solid black;
            }
            #errorBox {
                border: 1px solid red;
                
            }
            #showChart img {
                height: 20px;
                width: 30px;
            }
            #showChart img:hover {
                cursor: pointer;
            }
            #chart {
                width: 50%;
                margin: auto;
            }
            .errorInput {
                border: 1px solid red;
            }
        </style>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script>
            var hasExpanded = false;
            function isEmptyString(testString) {
                return (!testString || /^\s*$/.test(testString));
            }

            function validateInput() {
                var currentLocation = document.getElementById('currentLocation');
                var weatherResult = document.getElementById('weatherResult');
                var errorLogButton = document.getElementById('errorLog');
                var dailyCardResult = document.getElementById('dailyCardResult');

                if (dailyCardResult)
                    dailyCardResult.innerHTML = " ";
                if (weatherResult)
                    weatherResult.innerHTML = " ";

                if (currentLocation.checked === true) {
                    currentLocation.value = "true";
                    
                    errorLogButton.style = "";
                    errorLogButton.innerHTML = "";
                    return true;    
                }

                currentLocation.value = "false";
                var streetVal = document.getElementById('street').value;
                var cityVal = document.getElementById('city').value;
                var stateVal = document.getElementById('state').value;

                
                if (streetVal === undefined || cityVal === undefined || stateVal === "state" || stateVal === "empty" ||
                        isEmptyString(streetVal) || isEmptyString(cityVal)) {
                    
                    
                    if (streetVal === undefined || isEmptyString(streetVal)) {
                        document.getElementById('street').classList.add('errorInput');
                    }
                    if (cityVal === undefined || isEmptyString(cityVal)) {
                        document.getElementById('city').classList.add('errorInput');
                    }
                    if (stateVal === undefined || stateVal === "state" || stateVal === "empty") {
                        document.getElementById('state').classList.add('errorInput');
                    }

                    errorLogButton.style = "height: 20px; border: 1px solid black;";
                    errorLogButton.innerHTML = "Please check the input address";
                    return false;
                }
                
                errorLogButton.style = "";
                errorLogButton.innerHTML = "";
                return true;
            }

            var currLocationJson;
            function handleClick() {
                var currLocationEnabled = document.getElementById('currentLocation').checked
                if (currLocationEnabled === false) {
                    document.getElementById('street').disabled = false;
                    document.getElementById('city').disabled = false;
                    document.getElementById('state').disabled = false;
                    
                    document.getElementById('street').value = "";
                    document.getElementById('city').value = "";
                    document.getElementById('state').value = "state";  
                } else {
                    document.getElementById('street').disabled = true;
                    document.getElementById('city').disabled = true;
                    document.getElementById('state').disabled = true;

                    if (currLocationJson === undefined) {
                        getCurrentLocation();
                        document.getElementById('latitude').value = currLocationJson.lat;
                        document.getElementById('longitude').value = currLocationJson.lon;
                        document.getElementById('lcity').value = currLocationJson.city;
                    } 
                }
            }

            function clearSelection() {
                document.getElementById('street').value = "";
                document.getElementById('city').value = "";
                document.getElementById('state').value = "state"; 
                document.getElementById('currentLocation').value = "false";
                document.getElementById('currentLocation').checked = false;
                
                document.getElementById('street').disabled = false;
                document.getElementById('city').disabled = false;
                document.getElementById('state').disabled = false;          
               
                if (document.getElementById('weatherResult'))
                    document.getElementById('weatherResult').innerHTML = "";
                if (document.getElementById('dailyCardResult'))
                    document.getElementById('dailyCardResult').innerHTML = "";
                
                var errorLogButton = document.getElementById('errorLog');
                if (errorLogButton) {
                    errorLogButton.style = "";
                    errorLogButton.innerHTML = "";
                }

            }

            function displayDailyWeather(timestamp, latitude, longitude) {
                document.getElementById('weatherSummaryForm').innerHTML +=
                    '<input type="hidden" name="timestamp" value="' + timestamp + '">' +
                    '<input type="hidden" name="latitude" value="' + latitude + '">' +
                    '<input type="hidden" name="longitude" value="' + longitude + '">';
                
                document.getElementById('weatherSummaryForm').submit();
            }

            function drawChart(weatherJson) {  
                var hourlyList = weatherJson.hourly.data
                var index = 0

                var data = new google.visualization.DataTable();
                data.addColumn('number', 'Time');
                data.addColumn('number', 'T'); 
                var arr = []
                hourlyList.forEach((val)=> {
                    arr.push([index, val.temperature])
                    index++
                })
                var options = {
                    curveType: 'function',
                    vAxis: {
                        title: 'Temperature',
                        textPosition: 'none'
                    },
                    hAxis: {
                        title: 'Time'
                    },
                    series: {
                        0: {color: '#66d9ff'}
                    }
                };

                var chart = new google.visualization.LineChart(document.getElementById('chart'));

                data.addRows(arr)
                chart.draw(data, options);
            }

            function expandChart(weatherJson) {
                var expandButton = document.getElementById('showChart')
                if (hasExpanded === false) {
                    google.charts.load('current', {packages: ['corechart', 'line']});
                    google.charts.setOnLoadCallback( ()=> {
                        drawChart(weatherJson);
                    })
                    expandButton.innerHTML = "<img src = 'http://csci571.com/hw/hw6/images/arrow_up.png'>";
                    hasExpanded = true
                } else {
                    expandButton.innerHTML = "<img src = 'http://csci571.com/hw/hw6/images/arrow_down.png'>";
                    document.getElementById('chart').innerHTML = "";
                    document.getElementById('chart').value = "";
                    hasExpanded = false
                }
                
            }

            function getCurrentLocation() {
                var url="http://ip-api.com/json";
                var req = new XMLHttpRequest();
                req.overrideMimeType("application/json");
                req.open('GET', url, false);
                req.send();
    
                currLocationJson = JSON.parse(req.responseText);
            }

        </script>
    </head>
    <body>
        <form name="searchForm" method="POST" action="forecast.php" onsubmit="return validateInput()">
            <div id="searchBox"> 
                <h2> Weather Search </h2>
                <table>
                    <tr>
                        <td id="typeInput">
                            <div id="searchForm">
                                <label for="street"><b>Street </b></label>
                                <?php
                                    $disabled_val = "";
                                    $checked_val = "";
                                    if (isset($_POST["currentLocation"]) && ($_POST["currentLocation"] == "true")) {
                                        $disabled_val = "disabled='disabled'";
                                        $checked_val = "checked='checked'";
                                    } 
                                ?>
                                <input type="text" name="street" id="street" value="<?php echo isset($_POST["street"]) ? $_POST["street"] : "" ?>" <?php echo $disabled_val; ?> >
                                <br>
                                <label for="city"><b>City </b></label> &nbsp;&nbsp;
                                <input type="text" name="city" id="city" value="<?php echo isset($_POST["city"]) ? $_POST["city"] : "" ?>"  <?php echo $disabled_val; ?>>
                                <br>
                                <?php 
                                    $optionSelected = isset($_POST["state"]) ? $_POST["state"] : "state";
                                ?>
                                <label for="state"><b>State </b></label> &nbsp;
                                <select name="state" id="state" value="<?php echo isset($_POST["state"]) ? $_POST["state"] : "state" ?>"  <?php echo $disabled_val; ?>>
                                    <option value="state" <?php ($optionSelected == "state") ? "selected='selected'" : "" ?> > State </option>
                                    <option value="empty" disabled="disabled"> ---------------------------- </option>
                                    <option value="AL" <?php echo ($optionSelected == "AL") ? "selected='selected'" : "" ?>>Alabama</option>
                                    <option value="AK" <?php echo ($optionSelected == "AK") ? "selected='selected'" : "" ?>>Alaska</option>
                                    <option value="AZ" <?php echo ($optionSelected == "AZ") ? "selected='selected'" : "" ?>>Arizona</option>
                                    <option value="AR" <?php echo ($optionSelected == "AR") ? "selected='selected'" : "" ?>>Arkansas</option>
                                    <option value="CA" <?php echo ($optionSelected == "CA") ? "selected='selected'" : "" ?> >California</option>
                                    <option value="CO" <?php echo ($optionSelected == "CO") ? "selected='selected'" : "" ?> >Colorado</option>
                                    <option value="CT" <?php echo ($optionSelected == "CT") ? "selected='selected'" : "" ?> >Connecticut</option>
                                    <option value="DE" <?php echo ($optionSelected == "DE") ? "selected='selected'" : "" ?> >Delaware</option>
                                    <option value="DC" <?php echo ($optionSelected == "DC") ? "selected='selected'" : "" ?> >District Of Columbia</option>
                                    <option value="FL" <?php echo ($optionSelected == "FL") ? "selected='selected'" : "" ?> >Florida</option>
                                    <option value="GA" <?php echo ($optionSelected == "GA") ? "selected='selected'" : "" ?> >Georgia</option>
                                    <option value="HI" <?php echo ($optionSelected == "HI") ? "selected='selected'" : "" ?> >Hawaii</option>
                                    <option value="ID" <?php echo ($optionSelected == "ID") ? "selected='selected'" : "" ?> >Idaho</option>
                                    <option value="IL" <?php echo ($optionSelected == "IL") ? "selected='selected'" : "" ?> >Illinois</option>
                                    <option value="IN" <?php echo ($optionSelected == "IN") ? "selected='selected'" : "" ?> >Indiana</option>
                                    <option value="IA" <?php echo ($optionSelected == "IA") ? "selected='selected'" : "" ?> >Iowa</option>
                                    <option value="KS" <?php echo ($optionSelected == "KS") ? "selected='selected'" : "" ?> >Kansas</option>
                                    <option value="KY" <?php echo ($optionSelected == "KY") ? "selected='selected'" : "" ?> >Kentucky</option>
                                    <option value="LA" <?php echo ($optionSelected == "LA") ? "selected='selected'" : "" ?> >Louisiana</option>
                                    <option value="ME" <?php echo ($optionSelected == "ME") ? "selected='selected'" : "" ?> >Maine</option>
                                    <option value="MD" <?php echo ($optionSelected == "MD") ? "selected='selected'" : "" ?> >Maryland</option>
                                    <option value="MA" <?php echo ($optionSelected == "MA") ? "selected='selected'" : "" ?> >Massachusetts</option>
                                    <option value="MI" <?php echo ($optionSelected == "MI") ? "selected='selected'" : "" ?> >Michigan</option>
                                    <option value="MN" <?php echo ($optionSelected == "MN") ? "selected='selected'" : "" ?> >Minnesota</option>
                                    <option value="MS" <?php echo ($optionSelected == "MS") ? "selected='selected'" : "" ?> >Mississippi</option>
                                    <option value="MO" <?php echo ($optionSelected == "MO") ? "selected='selected'" : "" ?> >Missouri</option>
                                    <option value="MT" <?php echo ($optionSelected == "MT") ? "selected='selected'" : "" ?> >Montana</option>
                                    <option value="NE" <?php echo ($optionSelected == "NE") ? "selected='selected'" : "" ?> >Nebraska</option>
                                    <option value="NV" <?php echo ($optionSelected == "NV") ? "selected='selected'" : "" ?> >Nevada</option>
                                    <option value="NH" <?php echo ($optionSelected == "NH") ? "selected='selected'" : "" ?> >New Hampshire</option>
                                    <option value="NJ" <?php echo ($optionSelected == "NJ") ? "selected='selected'" : "" ?> >New Jersey</option>
                                    <option value="NM" <?php echo ($optionSelected == "NM") ? "selected='selected'" : "" ?> >New Mexico</option>
                                    <option value="NY" <?php echo ($optionSelected == "NY") ? "selected='selected'" : "" ?> >New York</option>
                                    <option value="NC" <?php echo ($optionSelected == "NC") ? "selected='selected'" : "" ?> >North Carolina</option>
                                    <option value="ND" <?php echo ($optionSelected == "ND") ? "selected='selected'" : "" ?> >North Dakota</option>
                                    <option value="OH" <?php echo ($optionSelected == "OH") ? "selected='selected'" : "" ?> >Ohio</option>
                                    <option value="OK" <?php echo ($optionSelected == "OK") ? "selected='selected'" : "" ?> >Oklahoma</option>
                                    <option value="OR" <?php echo ($optionSelected == "OR") ? "selected='selected'" : "" ?> >Oregon</option>
                                    <option value="PA" <?php echo ($optionSelected == "PA") ? "selected='selected'" : "" ?> >Pennsylvania</option>
                                    <option value="RI" <?php echo ($optionSelected == "RI") ? "selected='selected'" : "" ?> >Rhode Island</option>
                                    <option value="SC" <?php echo ($optionSelected == "SC") ? "selected='selected'" : "" ?> >South Carolina</option>
                                    <option value="SD" <?php echo ($optionSelected == "SD") ? "selected='selected'" : "" ?> >South Dakota</option>
                                    <option value="TN" <?php echo ($optionSelected == "TN") ? "selected='selected'" : "" ?> >Tennessee</option>
                                    <option value="TX" <?php echo ($optionSelected == "TX") ? "selected='selected'" : "" ?> >Texas</option>
                                    <option value="UT" <?php echo ($optionSelected == "UT") ? "selected='selected'" : "" ?> >Utah</option>
                                    <option value="VT" <?php echo ($optionSelected == "VT") ? "selected='selected'" : "" ?> >Vermont</option>
                                    <option value="VA" <?php echo ($optionSelected == "VA") ? "selected='selected'" : "" ?> >Virginia</option>
                                    <option value="WA" <?php echo ($optionSelected == "WA") ? "selected='selected'" : "" ?> >Washington</option>
                                    <option value="WV" <?php echo ($optionSelected == "WV") ? "selected='selected'" : "" ?> >West Virginia</option>
                                    <option value="WI" <?php echo ($optionSelected == "WI") ? "selected='selected'" : "" ?> >Wisconsin</option>
                                    <option value="WY" <?php echo ($optionSelected == "WY") ? "selected='selected'" : "" ?> >Wyoming</option>
                                </select>
                                <input type="hidden" name="latitude" id="latitude" value="<?php echo isset($_POST["latitude"]) ? $_POST["latitude"] : "" ?>">
                                <input type="hidden" name="longitude" id="longitude" value="<?php echo isset($_POST["longitude"]) ? $_POST["longitude"] : "" ?>">
                                <input type="hidden" name="lcity" id="lcity" value="<?php echo isset($_POST["lcity"]) ? $_POST["lcity"] : "" ?>">
                            </div>
                            <div id="separator">
                                <hr width="3" size="100">
                            </div>
                        </td>
                        <td id="locationInput">
                            <div>
                                <input type="checkbox" id="currentLocation" name="currentLocation"  value="<?php echo isset($_POST["currentLocation"]) ? $_POST["currentLocation"] : "false" ?>" onclick="handleClick()" <?php echo $checked_val; ?>)>Current Location
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="buttons">
                    <input type="submit" value="search" name="search" id="searchSubmit">
                    <input type="button" value="clear" onclick="clearSelection()">
                </div>
            </div>
            <div id="errorLog"></div>
        </form>
        <?php
            function getCurrLocationDetails() {
                $url = "http://ip-api.com/json";
                $output = file_get_contents($url);
                return json_decode($output);
            }
            function getLocationDetails() {
                $street = str_replace(' ','+',$_POST["street"]);
                $city = str_replace(' ','+',$_POST["city"]);
                $state =  str_replace(' ','+',$_POST["state"]);
                $api_key = "AIzaSyBXZSHzGzFoJmKfPOT1rF2PxnAKMOFiR4Q";
                $url = "https://maps.googleapis.com/maps/api/geocode/xml?address=".$street.",".$city.",".$state."&key=".$api_key;
                $output = file_get_contents($url);
                
                $googleXml = simplexml_load_string($output);
                return $googleXml;
            }

            function getDarkSkyData($latitude, $longitude) {
                $api_key = "30db0189551257ca9ab189bdccf1b1b4";
                $url = "https://api.forecast.io/forecast/".$api_key."/".$latitude.",".$longitude."?exclude=minutely,hourly,alerts,flags";
                $output = file_get_contents($url);
                return $output;
            }

            function getDailyDarkSkyData($latitude, $longitude, $timestamp) {
                $api_key = "30db0189551257ca9ab189bdccf1b1b4";
                $url = "https://api.forecast.io/forecast/".$api_key."/".$latitude.",".$longitude.",".$timestamp."?exclude=minutely";
                $output = file_get_contents($url);
                return $output;               
            }            

            function displayWeatherCard($weatherJson, $city) {
                
                $timezone = $weatherJson->{'timezone'};
                $temperature = $weatherJson->{'currently'}->{'temperature'};
                $summary = $weatherJson->{'currently'}->{'summary'};

                $humdity = $weatherJson->{'currently'}->{'humidity'};
                $pressure = $weatherJson->{'currently'}->{'pressure'};
                $windSpeed = $weatherJson->{'currently'}->{'windSpeed'};
                $visibility = $weatherJson->{'currently'}->{'visibility'};
                $cloudCover = $weatherJson->{'currently'}->{'cloudCover'};
                $ozone = $weatherJson->{'currently'}->{'ozone'};

                echo '
                    <div id = "weatherCard"> 
                        <div id="cityCard">'.
                        $city. '</div>'.
                        '<div id="timezoneCard">'.
                        $timezone. '</div>'.
                        '<div id="temperatureCard">'.
                        $temperature. '<span id="degree"> <img id="degreeImg" src="https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png"> </span>'. 
                        '<span style="font-size: 45px; margin-left: -15px;">F</span> </div>'.
                        '<div id="summaryCard">'. 
                        $summary. '</div> <div>'.
                        '<table id="valueCard"> <tr>'. 
                        '<td title="Humidity"> <img  class="imageLogo" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-16-512.png"> </img> </td>'.
                        '<td title="Pressure"> <img class="imageLogo" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-25-512.png"> </img> </td>'.
                        '<td title="Wind Speed"> <img class="imageLogo" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png"> </img> </td>'.
                        '<td title="Wind Speed"> <img class="imageLogo" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-30-512.png"> </img> </td>'.
                        '<td title="Cloud Cover"> <img class="imageLogo" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png"> </img> </td>'.
                        '<td title="Ozone"> <img class="imageLogo" src="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-24-512.png"> </img> </td>'.
                        '</tr> <tr>'.
                        '<td title="Humidity">'.$humdity.'</td>'.
                        '<td title="Pressure">'.$pressure.'</td>'.
                        '<td title="Wind Speed">'.$windSpeed.'</td>'.
                        '<td title="Wind Speed">'.$visibility.'</td>'.
                        '<td title="Cloud Cover">'.$cloudCover.'</td>'.
                        '<td title="Ozone">'.$ozone.'</td>'.
                        '</tr></table> </div>'.
                    '</div>';
            }

            function getStatusImage($weatherStatus) {
                if ($weatherStatus == "clear-day" || $weatherStatus == "clear-night") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png";
                } else if ($weatherStatus == "rain") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-04-512.png";
                } else if ($weatherStatus == "snow") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-19-512.png";
                } else if ($weatherStatus == "sleet") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-07-512.png";
                } else if ($weatherStatus == "wind") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png";
                } else if ($weatherStatus == "fog") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png";
                } else if ($weatherStatus == "cloudy") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-01-512.png";
                } else if ($weatherStatus == "partly-cloudy-day" || $weatherStatus == "partly-cloudy-night") {
                    return "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png";
                }
                return " ";
            }

            function displayWeatherTable($weatherJson, $latitude, $longitude) {
                $dailyList = $weatherJson->{'daily'}->{'data'};

                echo '<form id="weatherSummaryForm" name="weatherSummaryForm" method="POST" action="forecast.php">';
                echo '<table id="weatherTable">
                    <tr>
                        <th style="width:1000px;"> Date </th>
                        <th style="width: 50px;"> Status </th>
                        <th> Summary </th>
                        <th> TemperatureHigh </th>
                        <th> TemperatureLow </th>
                        <th> Wind Speed </th>
                    </tr>';
                
                foreach ($dailyList as $dailyObj) {
                    
                    echo '<tr>'. 
                    '<td style="width:600px;">'.date('Y-m-d', $dailyObj->{'time'}).'</td>'.
                    '<td style="width: 50px;"><img class="statusLogo" src="'.getStatusImage($dailyObj->{'icon'}).'"></td>'.
                    '<td class="summaryLink" style="width: 3000px;" onclick="displayDailyWeather('. $dailyObj->{'time'} .','.$latitude.','.$longitude.')">'.$dailyObj->{'summary'}.'</td>'.
                    '<td>'.$dailyObj->{'temperatureHigh'}.'</td>'.
                    '<td>'.$dailyObj->{'temperatureLow'}.'</td>'.
                    '<td>'.$dailyObj->{'windSpeed'}.'</td></tr>';
                }
                $street = isset($_POST["street"]) ? $_POST["street"] : "";
                $city = isset($_POST["city"]) ? $_POST["city"] : "";
                $state = isset($_POST["state"]) ? $_POST["state"] : "state";
                $currentLocation = isset($_POST["currentLocation"]) ? $_POST["currentLocation"] : "false";
                echo '<input type="hidden" name="street" value="' . $street . '">';
                echo '<input type="hidden" name="city" value="' . $city . '">';
                echo '<input type="hidden" name="state" value="' . $state . '">';
                echo '<input type="hidden" name="currentLocation" value="' . $currentLocation . '">';
                echo '</table>';
                echo '</form>';
            }

            function getDailyImage($weatherStatus) {
                if ($weatherStatus == "clear-day" || $weatherStatus == "clear-night") {
                    return "https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png";
                } else if ($weatherStatus == "rain") {
                    return "https://cdn3.iconfinder.com/data/icons/weather-344/142/rain-512.png";
                } else if ($weatherStatus == "snow") {
                    return "https://cdn3.iconfinder.com/data/icons/weather-344/142/snow-512.png";
                } else if ($weatherStatus == "sleet") {
                    return "https://cdn3.iconfinder.com/data/icons/weather-344/142/lightning-512.png";
                } else if ($weatherStatus == "wind") {
                    return "https://cdn4.iconfinder.com/data/icons/the-weather-is-nice-today/64/weather_10-512.png";
                } else if ($weatherStatus == "fog") {
                    return "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloudy-512.png";
                } else if ($weatherStatus == "cloudy") {
                    return "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloud-512.png";
                } else if ($weatherStatus == "partly-cloudy-day" || $weatherStatus == "partly-cloudy-night") {
                    return "https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png";
                }
                return " ";              
            }

            function getPrecipitationString($precipIntensity) {
                if (!isset($precipIntensity->{'precipIntensity'})) {
                    return "N/A";
                }

                $precipIntensity = $precipIntensity->{'precipIntensity'};
                if ($precipIntensity <= 0.001) {
                    return "None";
                } elseif ($precipIntensity <= 0.015) {
                    return "Very Light";
                } elseif ($precipIntensity <= 0.05) {
                    return "Light";
                } elseif ($precipIntensity <= 0.1) {
                    return "Moderate";
                } else {
                    return "Heavy";
                }
            }

            function getTimestampString($timestamp, $timezone) {
                $timeZoneObj = new DateTimeZone($timezone);
                $dateTime = new DateTime("now", $timeZoneObj);
                $dateTime->setTimestamp($timestamp);
                $result = '<span style="font-size: 30px; font-weight: bold;">'. $dateTime->format('g') . '</span> '. $dateTime->format('A');

                return $result;
            }

            function displayDailyWeatherCard($weatherJson) {
                $currently = $weatherJson->{'currently'};
                
                echo '<div id="dailyWeatherCard">'.
                '<table id="dailySummaryTable">'.
                    '<tr>'.
                        '<td style="width: 80%;" valign="bottom">'.
                            '<table>'.
                                '<tr><td style="font-size: 30px; font-weight: 900;">'. $currently->{'summary'} . '</td></tr>'.
                                '<tr><td style="font-size: 100px;">'. round($currently->{'temperature'}) . '<span id="degree"> <img id="degreeImg" src="https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png"> </span> <span style="font-size: 80px; margin-left: -20px;"> F </span> </td></tr>'.
                            '</table>'.
                        '</td>'.
                        '<td>'.
                            '<img id="dailyIcon" src="'. getDailyImage($currently->{'icon'}).'" align="right"></img>'.
                        '</td>'.
                    '</tr>'.
                '</table>'.
                '<table id="dailyDetailsTable">'.
                    '<tr> <td align="right" style="width:65%;"> Precipitation: </td> <td style="font-size: 30px; font-weight: bold;">'.getPrecipitationString($currently). '</td></tr>'.
                    '<tr> <td align="right"> Chance of Rain: </td> <td> <span style="font-size: 30px; font-weight: bold;">'.($currently->{'precipProbability'} * 100). '</span> %</td></tr>'.
                    '<tr> <td align="right"> Wind Speed: </td> <td> <span style="font-size: 30px; font-weight: bold;">'. $currently->{'windSpeed'}. '</span> mph</td></tr>'.
                    '<tr> <td align="right"> Humidity: </td> <td> <span style="font-size: 30px; font-weight: bold;">'. ($currently->{'humidity'} * 100). '</span> %</td></tr>'.
                    '<tr> <td align="right"> Visibility: </td> <td> <span style="font-size: 30px; font-weight: bold;">'. $currently->{'visibility'}. '</span> mi</td></tr>'.
                    '<tr> <td align="right"> Sunrise / Sunset: </td> <td>'. getTimestampString($weatherJson->{'daily'}->{'data'}[0]->{'sunriseTime'}, $weatherJson->{'timezone'}). ' / '. getTimestampString($weatherJson->{'daily'}->{'data'}[0]->{'sunsetTime'}, $weatherJson->{'timezone'}) . '</td></tr>'.
                '</table>'.
                '</div>';
            }

            if (isset($_POST["timestamp"])) {
                $latitude = $_POST["latitude"];
                $longitude = $_POST["longitude"];
                $timestamp = $_POST["timestamp"];

                echo "<div id='dailyCardResult'>";
                echo "<h1 style='text-align: center;'> Daily Weather Detail </h1>"; 
                $weatherJson = json_decode(getDailyDarkSkyData($latitude, $longitude, $timestamp));
                displayDailyWeatherCard($weatherJson);
                
                $weatherJsonString = json_encode($weatherJson);
                echo "<h1 style='text-align: center;'> Daily's Hourly Weather </h1>";
                echo "<h1 id='showChart' style='text-align: center; font-size: 50px;' onclick='expandChart(".$weatherJsonString.")'><img src = 'http://csci571.com/hw/hw6/images/arrow_down.png'></h1>";

                echo "<div id='chart'> </div>
                </div>";
            } elseif (isset($_POST["search"])) {
                if (isset($_POST["currentLocation"])) {
                    $latitude = $_POST["latitude"];
                    $longitude = $_POST["longitude"];
                    $city = $_POST["lcity"];
                } else {
                    $xmlObj = getLocationDetails();
                    if ($xmlObj->status == "ZERO_RESULTS") {
                        return;
                    }
                    $googleXml = $xmlObj->result[0]->geometry->location;
                    $latitude = $googleXml->lat;
                    $longitude = $googleXml->lng;
                    $city = $_POST["city"];
                }

                echo "<div id='weatherResult'>";
                $weatherJson = json_decode(getDarkSkyData($latitude, $longitude));
                displayWeatherCard($weatherJson, $city);
                displayWeatherTable($weatherJson, $latitude, $longitude);
                echo "</div>";
            }
        ?>
    </body>

</html>