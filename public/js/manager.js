/*
    Code relating to manager tasks lives here.
 */

$("#reportHeading").hide();

//view current orders
$.getJSON("/order/view", function(data){
    var totalOrdersCost = 0;
    var totalOrders = 0;
    $.each(data, function(key, val){
        totalOrdersCost += val.total_cost;
        totalOrders++;
        //Build the table to show orders
        $("#orderTableBody").append(
            "<tr><td>" + val.id + "</td>"
            + "<td>" + val.delivery_address + "</td>"
            + "<td>" + val.status + "</td>"
            + "<td>" + val.comments + "</td>"
            + "<td>€" + val.total_cost + "</td>"
            + "<td>" + val.timestamp + "</td>"
            + "<td><button style='width:100%;' id=cancelOrder" + val.id + ">Cancel Order " + val.id + "</button></td></tr>"
        );

        //click handler for cancel buttons
        $("#cancelOrder" + val.id).click(function(){
            $.post("/manager/cancel/" + val.id);
            window.location.reload(true);
            alert("Order " + val.id + " deleted!");
        });

    });
    $("#orderTableBody").append(
        "<tr>" +
        "<td></td>" +
        "<td>Total Sales</td>" +
        "<td></td>" +
        "<td></td>" +
        "<td>€" + totalOrdersCost + "</td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>"
    );
    $("#salesSummary").append(
        "<p>Total Orders: " + totalOrders + "</p>" +
        "<p>Total Sales: €" + totalOrdersCost + "</p><br>"
    );
});


var date;
var dateurl;
$("#datepicker").change(function(){
    date = $("#datepicker").val();
    dateurl = "/manager/report/daily/" + date;
    //$("#target").prop("href", newurl);
});

$("#salesDatePickerButton").click(function(){
    if (date == null){
        $("#reportHeading").show().html("<h3 class='center-text'>Please select a date</h3>");
    }else {
        $("#reportHeading").show().html("<h3 class='center-text'>Sales for " + date);
    }

    $.getJSON(dateurl, function(data){
        $("#salesResultTableBody").empty().append(
            "<tr><td>" + data['total_orders'] + "</td>" +
            "<td>€" + data['total_sales'] + "</td></tr>"
        );
    });
});

$("#monthPicker").on('change', function(){
    var url = "/manager/report/monthly/" + $("#monthPicker").val();
    $.getJSON(url, function(data){
        $("#monthlyTableBody").empty().append(
            "<tr><td>" + data['total_orders'] + "</td>" +
            "<td>€" + data['total_sales'] + "</td>" +
            "<td>€" + data['total_sales'] / data['total_orders'] + "</td></tr>"
        );
    });
});

$.getJSON("/manager/report/monthlysummary", function(data){
    $("#monthlySummaryTableBody").append("<tr>");
    $.each(data['monthly_sales'], function(key, val) {
        $("#monthlySummaryTableBody").append(
            "<td>€" + val + "</td>"
        );
    });
    $("#monthlySummaryTableBody").append("</tr>");
});

//build weekly select div
for (var i = 1; i < 53; i++) {
    $("#weekPicker").append(
        "<option value=" + i + ">Week" + i + "</option>"
    );
}

//on change handler for weekPicker select
$("#weekPicker").on('change', function(){
    var url = "/manager/report/weekly/" + $("#weekPicker").val();
    $.getJSON(url, function(data){
        $("#weeklyTableBody").empty().append(
            "<tr><td>" + data['total_orders'] + "</td>" +
            "<td>€" + data['total_sales'] + "</td>" +
            "<td>€" + data['total_sales'] / data['total_orders'] + "</td></tr>"
        );
    });
});