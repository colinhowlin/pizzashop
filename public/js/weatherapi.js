var url = "http://api.openweathermap.org/data/2.5/forecast?id=2961690&APPID=c200fa916b000779e1dfce1ebbe67b4e&mode=json";

 $.getJSON(url, function(data){
    //console.log(data['timestamp']);
    $.each(data['list'], function(key, val){
        var weatherData = (val.weather);
        var timestamp = timeConverter(val.dt);
        $.each(weatherData, function(key, val){
            var description= val.description;
            var icon = val.icon;
            var icon_url = "http://openweathermap.org/img/w/" + icon;
            console.log(icon_url);
            $("#weatherTableBody").append(
                "<tr><td>" + timestamp + "</td>" +
                "<td>" + description + "</td>" +
                "<td><img alt='icon' src=" + icon_url + ".png></td></tr>"
            );
        })
    });
});

//following function got from https://stackoverflow.com/questions/847185/convert-a-unix-timestamp-to-time-in-javascript
//used to convert UNIX epoch time to a usable date format
function timeConverter(UNIX_timestamp){
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    var min = a.getMinutes();
    var sec = a.getSeconds();
    var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + '0:0' + sec ;
    return time;
}

//Using table.js jquery plugin to transpose table
