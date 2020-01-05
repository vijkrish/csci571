import { Component, OnInit, NgModule, ViewChild} from '@angular/core';
import { FormGroup, FormControl, ReactiveFormsModule, FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { AppComponent }  from '../../app.component';
import { HttpClient } from '@angular/common/http';
import { WeeklyTabComponent } from '../weekly-tab/weekly-tab.component';
import { ChartsModule } from 'ng2-charts';
import * as CanvasJS from '../../../assets/canvasjs.min.js'

import {NgbModal, NgbModalOptions} from '@ng-bootstrap/ng-bootstrap';
import {startWith, switchMap, debounceTime} from 'rxjs/operators'
import { BaseChartDirective } from 'ng2-charts';

@Component({
  selector: 'app-weather-form',
  templateUrl: './weather-form.component.html',
  styleUrls: ['./weather-form.component.css']
})


@NgModule({
  imports: [
      BrowserModule,
      FormsModule,
      ReactiveFormsModule,
      ChartsModule
  ],
  declarations: [
      AppComponent,
      WeatherFormComponent,
      WeeklyTabComponent
  ],
  bootstrap: [AppComponent]
})


export class WeatherFormComponent implements OnInit {
  @ViewChild("baseChart", {static: true}) chart: BaseChartDirective
  weatherForm: FormGroup;
  readonly ip_api_url = 'http://ip-api.com/json'
  currentLocation: boolean;
  ipJson: any;
  google_search_api_key='AIzaSyBXZSHzGzFoJmKfPOT1rF2PxnAKMOFiR4Q';

  currentTabSelected = true;
  hourlyTabSelected = false;
  weeklyTabSelected = false;

  /* Modal Creation */
  shouldDisplayModal = false;
  modalOptions:NgbModalOptions;
  closeResult: string;
  @ViewChild('mymodal', {static: false}) mymodal

  /* Daily stats */
  daily_date = ""
  daily_city = ""
  daily_temperature = 0
  daily_summary = ""
  daily_precipitation = 0
  daily_chance_of_rain = 0
  daily_wind_speed = 0
  daily_humidity = 0
  daily_visibility = 0

  lat = 0
  lon = 0

  notFavCity = true
  favCity = false

  cities: string[] = ['Champs-Élysées', 'Lombard Street', 'Abbey Road', 'Fifth Avenue'];

  ngAfterViewInit() {
  }

  // Weather Card Details
  city = '';
  state = '';
  stateName = '';
  timezone = '';
  stateImageUrl = '';
  temperature = 0;
  weatherSummary = '';
  dailyInfo = {}

  shouldDisplayProgressBar = false;
  hourlyDataReady = false;

  currently = {};
  hourly = {}
  daily = {}
  
  units = {
    'temperature': 'Fahrenheit',
    'pressure': 'Millibars',
    'humidity': '% Humidity',
    'ozone': 'Dobson Units',
    'visibility': 'Miles (Maximum 10)',
    'windSpeed': 'Miles per Hour'
  }

  // Google search API
  cx = '014449360106520511784:neqlfbf50bm'

  // Twitter
  tweet = 'http://twitter.com/intent/tweet?text='

  // Favorites
  favIndex = 0

  options = []


  displayFn(subject) {
    return subject ? subject : ""
  }

  stateMap = {
    "AL": "Alabama",
    "AK": "Alaska",
    "AS": "American Samoa",
    "AZ": "Arizona",
    "AR": "Arkansas",
    "CA": "California",
    "CO": "Colorado",
    "CT": "Connecticut",
    "DE": "Delaware",
    "DC": "District Of Columbia",
    "FM": "Federated States Of Micronesia",
    "FL": "Florida",
    "GA": "Georgia",
    "GU": "Guam",
    "HI": "Hawaii",
    "ID": "Idaho",
    "IL": "Illinois",
    "IN": "Indiana",
    "IA": "Iowa",
    "KS": "Kansas",
    "KY": "Kentucky",
    "LA": "Louisiana",
    "ME": "Maine",
    "MH": "Marshall Islands",
    "MD": "Maryland",
    "MA": "Massachusetts",
    "MI": "Michigan",
    "MN": "Minnesota",
    "MS": "Mississippi",
    "MO": "Missouri",
    "MT": "Montana",
    "NE": "Nebraska",
    "NV": "Nevada",
    "NH": "New Hampshire",
    "NJ": "New Jersey",
    "NM": "New Mexico",
    "NY": "New York",
    "NC": "North Carolina",
    "ND": "North Dakota",
    "MP": "Northern Mariana Islands",
    "OH": "Ohio",
    "OK": "Oklahoma",
    "OR": "Oregon",
    "PW": "Palau",
    "PA": "Pennsylvania",
    "PR": "Puerto Rico",
    "RI": "Rhode Island",
    "SC": "South Carolina",
    "SD": "South Dakota",
    "TN": "Tennessee",
    "TX": "Texas",
    "UT": "Utah",
    "VT": "Vermont",
    "VI": "Virgin Islands",
    "VA": "Virginia",
    "WA": "Washington",
    "WV": "West Virginia",
    "WI": "Wisconsin",
    "WY": "Wyoming"
  }
  public barChartOptions = {
    
  };
  public barChartLabels = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12',
  '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
  public barChartType = 'bar';
  public barChartLegend = true;
  public barChartData = [
    {
      backgroundColor: 'rgb(119, 199, 250)',
      hoverBackgroundColor: 'rgb(63, 127, 163)',
      data: []
    }
  ];

  dailyChart = null
  weatherFavorites = undefined

  public updateChart() {
    this.chart.datasets = this.barChartData
    this.chart.labels = this.barChartLabels
    this.chart.options = this.barChartOptions
    this.chart.legend = this.barChartLegend
  }

  @ViewChild('baseChart', {static: true}) private chartComponent: any;

  imageSource = ''

  redrawChart(field) {
    this.hourlyDataReady = false;
    this.barChartData[0]['data'] = []
    this.barChartOptions = {}

    setTimeout(function(field){
      var maxVal = 0
      var minVal = 99999
      for (var i = 0; i < 24; i++) {
        if (field === 'humidity') {
          this.hourly['data'][i][field] *= 100
        } else {
          this.hourly['data'][i][field] = Math.round(this.hourly['data'][i][field])
        }
        maxVal = Math.max(maxVal, this.hourly['data'][i][field])
        minVal = Math.min(minVal, this.hourly['data'][i][field])
        this.barChartData[0]['data'].push(this.hourly['data'][i][field])
      }
      var stepSize = 5
      if (field === 'visibility' || field === 'windSpeed') {
        maxVal += 1
        minVal = 0
        stepSize = 1
      }  else {
        minVal = (Math.floor(minVal/5)*5 - 5) < 0 ? 0 : (Math.floor(minVal/5)*5 - 5)
        maxVal = Math.round(maxVal/5)*5 + 5
      }

      this.barChartOptions = {
        scaleShowVerticalLines: false,
        responsive: true,
        scales: {
          yAxes: [{
            scaleLabel: {
              display: true,
              labelString: this.units[field]
            }, ticks: {
              max: maxVal,
              min: minVal,
              stepSize: stepSize
            }
          }],
          xAxes: [{
            scaleLabel: {
              display: true,
              labelString: 'Time difference from current hour'
            }
          }]
        } 
      }
    
      this.barChartData[0]['label'] = field
      
      this.hourlyDataReady = true
    }.bind(this), 100, field)
  }

  public fillHourlyData(field) {
    console.log(field, this.units[field])

    this.redrawChart(field)
  }

  constructor(private http: HttpClient, private modalService: NgbModal) {
    this.modalOptions = {
      backdrop:'static',
      backdropClass:'customBackdrop'
    }
  }

  open(content) {
    this.modalService.open(content, this.modalOptions).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed `;
    });
  }

  delay(ms: number) {
    return new Promise( resolve => setTimeout(resolve, ms) );
  }

  myControl = new FormControl('')
  filteredOptions: any

  autocompleteApi = 'http://vijaysai-hw8.us-east-2.elasticbeanstalk.com/autocomplete/'
  sessiontoken = '12345'

  getApi(value) {
    if (value) {
      return this.autocompleteApi + value.toLowerCase() + "/" + this.sessiontoken
    }
    return 'http://vijaysai-hw8.us-east-2.elasticbeanstalk.com/empty'
  }

  ngOnInit() {
    this.weatherForm = new FormGroup({
      street: new FormControl(''),
      city: new FormControl(''),
      state: new FormControl(''),
      selectHourlyOption: new FormControl('temperature'),
    })

    this.myControl.valueChanges
    .pipe(
      debounceTime(200),
      switchMap(value => this.http.get(this.getApi(value))
      )
    )
    .subscribe(data => {
      if (data['predictions'] == undefined) {
        this.filteredOptions = [];
      } else {
        this.filteredOptions = data['predictions'];
      }
      console.log(this.filteredOptions);
    });

    this.barChartOptions = {
      scaleShowVerticalLines: false,
      responsive: true,
      scales: {
        yAxes: [{
          scaleLabel: {
            display: true,
            labelString: 'Fahrenheit'
          }
        }],
        xAxes: [{
          scaleLabel: {
            display: true,
            labelString: 'Time difference from current hour'
          }
        }]
      } 
    }
  }


  deleteAllRows() {
    console.log('Deleting all rows')
    var table =  document.getElementById('favTable') as HTMLTableElement
    for (var id = table.childNodes.length - 1; id > 0; id--) {
      table.deleteRow(id)
    }
  }


  deleteRow(city) {
    console.log('About to delete all rows in the table \n ------------- \n')
    this.deleteAllRows()
    
    console.log('About to delete an entry \n ------------ \n',this.weatherFavorites)
    localStorage.removeItem(city)
    
    console.log('After splicing \n ---------------- \n', this.weatherFavorites)
    this.addAllRows()
  }

  showLocation(city) {
    console.log(city)
    var locationObj = JSON.parse(localStorage.getItem(city))
    if (locationObj === null)
      return
    
    
    var lat = locationObj['lat']
    var lon = locationObj['lon']
    var state = locationObj['state']
    console.log(locationObj, locationObj['lat'], lat)

    var api_url = "http://vijaysai-hw8.us-east-2.elasticbeanstalk.com/getWeatherInfo/" + lat + "/" + lon + "/" + state;

    console.log(api_url, locationObj)
    this.callApi(api_url).subscribe((resData) => {
       this.handleWeatherData(resData)
    }) 

    this.toggle_shouldDisplayProgressBar()
  }

  addRow(weatherInfo, insertIndex) {
    insertIndex++
    var table =  document.getElementById('favTable') as HTMLTableElement
    var row = table.insertRow(insertIndex)

    row.style.backgroundColor = '#6593AD'
    if (insertIndex % 2) {
      row.style.backgroundColor = '#9CD1F1'
    } 
    row.style.height = '50px';

    var cityToolKey = weatherInfo['city'].replace(/\s+/g, '-')
    var tooltipTag = "[ngbToolTip]"

    row.insertCell(0).innerHTML = insertIndex.toString()
    row.insertCell(1).innerHTML = "<img class='smallStateSeal' src = '" + 
      weatherInfo['stateImageUrl'] + "' style='height: 30px; width: auto;'>"
    row.insertCell(2).innerHTML = '<data-toggle="tooltip" title="'+ weatherInfo['city'] + '"><a href="#" id="cityBtn '+ insertIndex +'">' + weatherInfo['city'] + "</a>"
    row.insertCell(3).innerHTML = weatherInfo['state']
    row.insertCell(4).innerHTML = '<button id="deleteBtn '+ insertIndex +'" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g style="border-radius: 5px;"><path d="M0 0h24v24H0z" fill="white"/></g><g style="fill: orange"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></g></svg> </button>'
    
    document.getElementById("deleteBtn " + insertIndex).addEventListener('click', 
      this.deleteRow.bind(this, weatherInfo['city'], weatherInfo['state']))

    document.getElementById("cityBtn " + insertIndex).addEventListener('click', 
      this.showLocation.bind(this, weatherInfo['city']))

    console.log(row)
  }

  addFavorites() {
    localStorage.setItem(this.city, JSON.stringify({
      stateImageUrl: this.stateImageUrl,
      city: this.city,
      state: this.state,
      lat: this.lat,
      lon: this.lon
    }))

    this.enableFavOption()

    console.log(localStorage)
  }

  handleFavorites() {
    if (localStorage.getItem(this.city) === null) {
      this.addFavorites()
    } else {
      localStorage.removeItem(this.city)
    }
  }

  addAllRows() {
    var index = 0
    console.log(localStorage)

    for (var i = 0; i < localStorage.length; i++) {
      var key = localStorage.key(i)
      console.log(key, 'Adding all rows')
      console.log(localStorage.getItem(key))
      this.addRow(JSON.parse(localStorage.getItem(key)), index++)
    }
  }

  hasOpenedFav = false

  selectFavs() {
    this.dataBg = false

    this.enableFavSelected()

    setTimeout(function() {  
      this.deleteAllRows()
      this.addAllRows()
    }.bind(this), 1000)


    this.hasOpenedFav = true
  }

  disableInputs() {
    this.weatherForm.controls.street.disable();
    this.myControl.disable();
    this.weatherForm.controls.state.disable();
  }
  enableInputs() {
    this.weatherForm.controls.street.enable();
    this.myControl.enable();
    this.weatherForm.controls.state.enable();
  }

  toggleCurrentLocation() {
    this.currentLocation = this.currentLocation != true;
    if (this.currentLocation === true) {
      this.disableInputs()
    } else {
      this.enableInputs()
    }
  }

  enableCurrentResults() {
    this.enableDataSelected()
    this.selectTab('current')
  }

  turnOffProgressBar(obj) {
    obj.shouldDisplayProgressBar = false;
    obj.enableCurrentResults()
  }

  toggle_shouldDisplayProgressBar() {
    this.shouldDisplayProgressBar = true;
    setTimeout(this.turnOffProgressBar, 1000, this)
  }

  disableResultsAndFavs() {
    this.dataSelected = false;
    this.favSelected = false;
    this.dataBg = true;
  }

  clearInput() {
    /* Mark fields as untouched - i.e remove the error log displayed  */
    this.weatherForm.controls.street.markAsUntouched()
    this.myControl.markAsUntouched()
    this.weatherForm.controls.state.markAsUntouched()

    /* Reset the input fields */
    this.weatherForm.controls.street.setValue('');
    this.myControl.setValue('');
    this.weatherForm.controls.state.setValue('');
    this.enableInputs()
    this.disableResultsAndFavs()
  }

  constructTweetUrl(city, temperature, summary) {
    city = city.split(' ').join('+');
    summary = summary.split(' ').join('+')
    this.tweet += "The+current+temperature+at+" + this.city + "+is+" + this.temperature +
      ".+The+weather+conditions+are+" + this.weatherSummary
    this.tweet += "&hashtags=CSCI571WeatherSearch" 
    console.log(this.tweet)
  }

  public getIpApi() {
    return this.http.get(this.ip_api_url)
  }

  isCityFav(city) {
    if (localStorage.getItem(city) === null) {
      return false
    }
    return true
  }


  handleWeatherData(data) {
    this.timezone = data['timezone']
    this.temperature = Math.round(data['currently']['temperature'])
    this.weatherSummary = data['currently']['summary']
    this.currently = data['currently']
    this.hourly = data['hourly'];
    this.daily = data['daily']

    this.lat = data['lat']
    this.lon = data['lon']
    this.stateImageUrl = data['stateImageUrl']

    console.log(this.lat, this.lon, this)

    this.constructTweetUrl(this.city, this.temperature, this.weatherSummary)

    if (this.isCityFav(this.city)) {
      this.enableFavOption()
    } else {
      this.disableFavOption()
    }
  }

  public getIpJson() {
     this.getIpApi().subscribe((data)=> {
       this.state = data['region']
       this.stateName = data['regionName']
       this.city = data['city']

       var api_url = "http://vijaysai-hw8.us-east-2.elasticbeanstalk.com/getWeatherInfo/" + data['lat'] + "/" + data["lon"] + "/" + data['region'];
       this.callApi(api_url).subscribe((resData) => {
          console.log(resData)
          this.handleWeatherData(resData)
       })
     })
  }

  public getDateString(utc_time) {
    var dateObj = new Date(utc_time)
    return  dateObj.getMonth() + "/" + dateObj.getDate() + "/" + dateObj.getFullYear()
  }

  public callApi(api_url) {
    return this.http.get(api_url)
  }

  getUrl(street, city, state, stateName) {
    state = state.split(' ').join('+');
    city = city.split(' ').join('+');
    street = street.split(' ').join('+');
    stateName = stateName.split(' ').join('+');

    var backendApi = "http://vijaysai-hw8.us-east-2.elasticbeanstalk.com/getLoc/" + street + "/" + city + "/" + state + "/" + stateName + "/";
    return backendApi 
  }

  getDailyUrl(time) {
    return "http://vijaysai-hw8.us-east-2.elasticbeanstalk.com/getDailyInfo/" + this.lat + "/" + this.lon + "/" + time
  }

  dataSelected = false;
  favSelected = false;
  dataBg = true;

  disableFavOption() {
    this.favCity = false
    this.notFavCity = true
  }

  enableFavOption() {
    this.favCity = true
    this.notFavCity = false
  }

  enableDataSelected() {
    this.dataSelected = true 
    this.favSelected = false
    this.dataBg = true
  }

  enableFavSelected() {
    this.dataSelected = false
    this.favSelected = true
  }

  selectResults() {
    if (!this.city)
      return

    this.enableDataSelected()

    this.currentTabSelected = true;
    this.weeklyTabSelected = false;
    this.hourlyTabSelected = false;

    if (this.isCityFav(this.city)) {
      this.enableFavOption()
    } else {
      this.disableFavOption()
    }
  }

  public getLocation() {
    var url = ""

    this.disableResultsAndFavs()
    this.toggle_shouldDisplayProgressBar()

    if (this.currentLocation == true) {
      this.getIpJson()
    } else {
      this.state = this.weatherForm.controls.state.value
      this.stateName = this.stateMap[this.state]
      url = this.getUrl(this.weatherForm.controls.street.value, this.myControl.value, this.weatherForm.controls.state.value, this.stateName)
      this.callApi(url).subscribe((data) => {
        this.handleWeatherData(data)
      })
    }


  }

  onSubmit() {
    this.getIpJson();
  }

  displayModal() {
    console.log(this.currently)
  }

  getImageSource(icon) {
    if (icon === "clear-day" || icon === "clear-night") {
      return "https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png"
    } else if (icon === "rain") {
      return "https://cdn3.iconfinder.com/data/icons/weather-344/142/rain-512.png"
    } else if (icon === "snow") {
      return "https://cdn3.iconfinder.com/data/icons/weather-344/142/snow-512.png"
    } else if (icon === "sleet") {
      return "https://cdn3.iconfinder.com/data/icons/weather-344/142/lightning-512.png"
    } else if (icon === "wind") {
      return "https://cdn4.iconfinder.com/data/icons/the-weather-is-nice-today/64/weather_10-512.png"
    } else if (icon === "fog") {
      return "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloudy-512.png"
    } else if (icon === "cloudy") {
      return "https://cdn3.iconfinder.com/data/icons/weather-344/142/cloud-512.png"
    } else if (icon === "partly-cloudy-day" || icon === "partly-cloudy-night") {
      return "https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png"
    }
    
    return ""
  }


  public fillDailyData(dailyData, obj) {
    console.log('About to load chart')
    var dailyDataPoints = []

    var chartData = {
      showInLegend: true,
      legendText: "Day wise weather temperature",
      type: "rangeBar",
      toolTipContent: "<b>{label}</b>: {y[0]} to {y[1]}",
      dataPoints: dailyDataPoints,
      indexLabel: "{y[#index]}",
      color: 'rgb(119, 199, 250)',
      click: function(e) {
        console.log(e)
        var api_url = "http://vijaysai-hw8.us-east-2.elasticbeanstalk.com/getDailyInfo/" + obj['lat'] + "/" + obj["lon"] + "/" + dailyData[e.dataPointIndex]['time'];
        this.callApi(api_url).subscribe((resData) => {
          obj.dailyInfo = resData
          obj.dailyInfo['date'] = e['dataPoint']['label']
          obj.imageSource = obj.getImageSource(resData['currently']['icon'])
          document.getElementById('modalBtn').click()
        })  
      }.bind(obj)
    }
    
    var minVal = 1000
    var maxVal = -1000

    let chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      exportEnabled: true,
      title: {
        text: "Weekly Weather"
      },
      dataPointWidth: 15,
      axisX: {
        gridColor: "white",
        title: 'Days'
      },
      axisY: {
        gridColor: "white",
        interval: 5,
        title: 'Temperature in Fahrenheit',
        minimum: 45,
        maximum: 90
      },
      legend: {
        verticalAlign: "top"
      },
      data: [chartData]
    })


    for (var i = 0; i < 8; i++) {
      dailyData[i]['temperatureLow'] = Math.round(dailyData[i]['temperatureLow'])
      dailyData[i]['temperatureHigh'] = Math.round(dailyData[i]['temperatureHigh'])

      minVal = Math.min(minVal, dailyData[i]['temperatureLow'])
      maxVal = Math.max(maxVal, dailyData[i]['temperatureHigh'])

      var dataRange = [dailyData[i]['temperatureLow'], dailyData[i]['temperatureHigh']]
      var dateObj = new Date(0)
      dateObj.setUTCSeconds(dailyData[i]['time'])

      var dateString = dateObj.getDate() + "/" + dateObj.getMonth() + "/" + dateObj.getFullYear()
      
      dailyDataPoints.push({
        label: dateString,
        y: dataRange,
        x: i
      })
    }

    minVal = Math.floor(minVal/5)*5 - 5
    maxVal = Math.round(maxVal/5)*5 + 5

    chart.options.axisY.minimum = minVal
    chart.options.axisY.maximum = maxVal

    chart.render()
  }

  selectTab(tabName) {
    if (tabName === "current") {
      this.currentTabSelected = true;
      this.weeklyTabSelected =false;
      this.hourlyTabSelected = false;
    } else if (tabName === "hourly") {
      this.currentTabSelected = false;
      this.weeklyTabSelected =false;
      this.hourlyTabSelected = true;
      this.fillHourlyData('temperature')
    } else if (tabName === "weekly") {
      this.currentTabSelected = false;
      this.weeklyTabSelected =true;
      this.hourlyTabSelected = false;
      setTimeout(this.fillDailyData, 500, this.daily['data'], this)
    }
  }

  chooseHourlyData() {
    this.fillHourlyData(this.weatherForm.controls.selectHourlyOption.value)
  }
}

