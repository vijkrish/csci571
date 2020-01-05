/* Import modules */
const express = require('express')
const bodyParser = require('body-parser')
const querystring = require('querystring')
const https = require('https')
const cors = require('cors')

const app = express()
const serverPort = 8081 
const geocode_api_key = "AIzaSyBXZSHzGzFoJmKfPOT1rF2PxnAKMOFiR4Q"
const darksky_api_key = "30db0189551257ca9ab189bdccf1b1b4"

// Google search API
const cx = '014449360106520511784:neqlfbf50bm'
const google_search_api_key = 'AIzaSyBXZSHzGzFoJmKfPOT1rF2PxnAKMOFiR4Q';

app.use(cors())

function getGoogleImageUrl(state) {
    var url =  "https://www.googleapis.com/customsearch/v1?q=Seal%20Of%20" + state + "&cx="
      + cx + "&num=1&searchType=image&key=" + google_search_api_key;
    console.log(url)
    return url
}

function getWeatherInfo(lat, lon, state, res) {
    var url = 'https://api.darksky.net/forecast/' + darksky_api_key + '/' +
        lat + ',' + lon;

    https.get(url, (resp)=> {
        let data = '';

        resp.on('data', (chunk) => {
            data += chunk
        })

        resp.on('end', ()=> {
            data = JSON.parse(data)
			data['lat'] = lat
			data['lon'] = lon

			https.get(getGoogleImageUrl(state), (imgResp)=> {
				let img_data = '';
				imgResp.on('data', (chunk)=> {
					img_data += chunk
				})

				imgResp.on('end', ()=> {
					img_data = JSON.parse(img_data)
					data['stateImageUrl'] = img_data['items'][0]['link']
					console.log(data)
					res.send(data)
					res.end()
				})
			})
        })

    }).on("error", (err) => {
        console.log(err)
    })
}

app.get('/getDailyInfo/:lat/:lon/:time', (req, res)=>{
	var lat = req.params.lat
    var lon = req.params.lon
	var time = req.params.time
	
	var url = 'https://api.darksky.net/forecast/' + darksky_api_key + '/' + lat + ',' +
		lon + ',' + time	

    https.get(url, (resp)=> {
        let data = '';

        resp.on('data', (chunk) => {
            data += chunk
        })

        resp.on('end', ()=> {
            data = JSON.parse(data)
            res.send(data)
            res.end()
        })

    }).on("error", (err) => {
        console.log(err)
    })

})

app.get('/getWeatherInfo/:lat/:lon/:state', (req, res)=>{
	var lat = req.params.lat
	var lon = req.params.lon
	var state = req.params.state
	
	getWeatherInfo(lat, lon, state, res)
	
})

app.get('/getLoc/:street/:city/:state/:stateName', (req, res)=>{
	console.log('getLoc')
	var street = req.params.street.replace(/ /g,"+");
	var city = req.params.city.replace(/ /g,"+");
	var state = req.params.state.replace(/ /g,"+");
	var stateName = req.params.stateName;

	var url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + 
				street + ',' + city + ',' + state + '&key=' + geocode_api_key;				

	https.get(url, (resp)=> {
		let data = '';
		
		resp.on('data', (chunk) => {
        	data += chunk
	    })

		resp.on('end', ()=> {
			data = JSON.parse(data)
			console.log(data)
			getWeatherInfo(data.results[0].geometry.location.lat, data.results[0].geometry.location.lng, stateName, res)
		})

	}).on("error", (err) => {
		console.log(err)
	})	

})

app.get('/autocomplete/:input/:sessiontoken', (req, res) => {
	console.log('Autocomplete')
	var input = req.params.input.replace(/ /g, "+")
	var sessiontoken = req.params.sessiontoken	

	var url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=" + input +
		"&types=(cities)&key=" + geocode_api_key + "&sessiontoken=" + sessiontoken

	https.get(url, (resp)=> {
        let data = '';

        resp.on('data', (chunk) => {
            data += chunk
        })

        resp.on('end', ()=> {
            data = JSON.parse(data)
			res.send(data)
			res.end()
        })

    }).on("error", (err) => {
        console.log(err)
    })

})

app.get('/empty', function(req, res) {
	data = {
		predictions: []
	}
	res.send(data)
	res.end()
})

var server = app.listen(serverPort)
