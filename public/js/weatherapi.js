//API Url to get 5-day forecast for Rosslare area.
//API Key = c200fa916b000779e1dfce1ebbe67b4e
var url = "http://api.openweathermap.org/data/2.5/forecast?id=2961690&APPID=c200fa916b000779e1dfce1ebbe67b4e&mode=json";

//class to hold values about the weather
class weather {
    constructor(timestamp, dateHeading, description, icon_url, temp, rain, wind){
        this.timestamp = timestamp;
        this.dateHeading = dateHeading;
        this.description = description;
        this.icon_url = icon_url;
        this.temp = temp;
        this.rain = rain;
        this.wind = wind;
    }
}

//array to hold weather objects
var weatherDatas = [];

//retrieve the weather data from OpenWeatherMap
$.getJSON(url, function(data) {
    var count = 0;

    //loop through datasets['list'] array
    $.each(data['list'], function (key, val) {
        var timestamp = val.dt_txt;
        var month = timestamp.substring(5,7);
        var day = timestamp.substring(8,10);
        var dateHeading = day + "/" + month;

        //create new weather object, set timestamp and add to array
        weatherDatas[count] = new weather(val.dt_txt, dateHeading, null, null, null, null, null);

        //loop through current dataset's 'main' array and pull required info
        $.each(val.main, function (key, val) {
            weatherDatas[count].temp = val.temp;
        });

        //Get rainfall - only a value if it has rained so check for null
        if (val.rain != null) {
            weatherDatas[count].rain = val.rain['3h'];
        } else {
            weatherDatas[count].rain = 0;
        }

        //Temperature returned in Kelvin.
        //convert to Celsius and round to 2 places
        weatherDatas[count].temp = Math.round((val.main['temp'] - 273.15) * 100) / 100;

        //Get windspeed
        weatherDatas[count].wind = val.wind['speed'];

        //loop through current dataset's 'weather' array and pull out description and weather icon
        $.each(val.weather, function (key, val) {
            //Description of the weather
            weatherDatas[count].description = val.description;
            //weather graphic
            var icon = val.icon;
            icon_url = "http://openweathermap.org/img/w/" + icon + ".png";
            weatherDatas[count].icon_url = icon_url;
        });
        count++;
    });
    //console.log(weatherDatas);

    //array to hold values for forecase summary
    var fiveDayForecast = [];

    //populate above array with weatherData if timestamp == 15:00:00
    weatherDatas.forEach(function(weatherData){
        var timestamp = weatherData['timestamp'];
        var month = timestamp.substring(5,7);
        var day = timestamp.substring(8,10);
        var dateHeading = day + "/" + month;

        if (timestamp.includes("12:00:00") || timestamp.includes("00:00:00")){
            fiveDayForecast.push(weatherData);
        }

        //console.log(weatherData['icon_url']);

        $("#weatherTableBody").append(
            "<tr><td>" + weatherData['timestamp'] + "</td>" +
            "<td>" + weatherData['temp'] + "</td>" +
            "<td>" + weatherData['wind'] + "</td>" +
            "<td>" + weatherData['rain'] + "</td>" +
            "<td>" + weatherData['description'] + "</td>" +
            "<td><img alt=icon src=" + weatherData['icon_url'] + "></td></tr>"
        );
    });
    //console.log(fiveDayForecast);

    fiveDayForecast.forEach(function(data){
        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var dayOfWeek = days[new Date(data['timestamp']).getDay()];
        var time = data['timestamp'].substring(11,);
        if(time == "12:00:00"){
            var ampm = "AM";
        } else {
            var ampm = "PM";
        }

        $("#forecastTable").append(
            "<td>" + dayOfWeek + " " + ampm +
            "<hr><br>" + data['dateHeading'] +
            "<hr><br><img alt='icon' src=" + data['icon_url'] + ">" +
            "<hr><br>" + data[['temp']] + "&#176;C" +
            "<hr><br>" + data['description'] + "</td>"
        );
    })
});
