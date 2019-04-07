//view current orders
$.getJSON("/order/view", function(data){
    var totalOrdersCost = 0;
    $.each(data, function(key, val){
        totalOrdersCost += val.total_cost;
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
        "<td>Total Cost</td>" +
        "<td></td>" +
        "<td></td>" +
        "<td>€" + totalOrdersCost + "</td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>"
    );
});