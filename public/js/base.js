var delivery_address;
var comments;

//generate the menu from Products array
products.forEach(function(product){
    $("#menuTableBody").append("<tr><td>" + product.name + "</td>"
        + "<td>" + product.description + "</td>"
        + "<td>€" + product.price + "</td>"
        + "<td><button class='centered' id=rem" + product.product_code + ">-</button>" + "</td>"
        + "<td class='center-text' id=" + product.product_code + "qty>" + product.qty + "</td>"
        + "<td><button class='centered' id=add" + product.product_code + ">+</button></td></tr>");
});

//This loops through the products array and creates click handlers
//for each product to remove from order
products.forEach(function(product) {
    $("#rem" + product.product_code).click(function(){
        if (product.qty > 0) {
            product.qty -= 1;
            $("#" + product.product_code + "qty").html(product.qty);
        }
    });
});

//This loops through the products array and creates click handlers
//for each product to add to order
products.forEach(function(product) {
    $("#add" + product.product_code).click(function(){
        product.qty += 1;
        $("#" + product.product_code + "qty").html(product.qty);
    });
});

//initially hide some content
$("#orderDetailsDiv").hide();
$("#backToOrderButton").hide();
$("#confirmOrderButton").hide();
$("#orderDetailsTable").hide();

//click handler for backToOrder button
$( "#backToOrderButton").click(function() {
    $("#orderDetailsDiv").hide();
    $("#backToOrderButton").hide();
    $("#confirmOrderButton").hide();
    $("#menuList").show();
    $("#placeOrderButton").show();
    $("#commentsArea").show();
});

//click handler for place order button
$( "#placeOrderButton" ).click(function() {
    //Hide some content
    $("#menuList").hide();
    $("#placeOrderButton").hide();

    //Grab the contents of the Delivery address and comments text boxes
    delivery_address = $("#delivery_address").val();
    comments = $("#comments").val();
    //then hid the comments area
    $("#commentsArea").hide();

    //build the customer order for customer to review
    var custOrder = "<h2>Your Order Details: </h2><table class='menu-table'><th>Item</th><th>Qty</th><th>Cost</th>";
    var orderCost = 0;

    //loop through products array and call addToOrder function on it
    products.forEach(addToOrder);

    //This function checks if the item has been ordered and adds it the the order review.
    function addToOrder(product){
        if(product.qty > 0) {
            custOrder += "<tr><td>" + product.name + "</td><td>" +
                product.qty + "</td><td>" +
                "€" + (product.qty * product.price) +
                "</td></tr>";
            orderCost += product.price * product.qty;
        }
    }

    custOrder += "<td>Total Cost</td><td></td><td>€" + orderCost + "</td>";
    custOrder += "</table>";
    custOrder += "<br>Delivery Address: " + delivery_address + "<br/>";
    custOrder += "<br>Additional Comments: " + comments + "<br/>"

    $(" #orderDetailsDiv ").html(custOrder).show();
    $("#backToOrderButton").show();
    $("#confirmOrderButton").show();
});

//click handler for confirmOrder button
$( "#confirmOrderButton" ).click(function() {
    //create empty array to hold ordered products
    var orderedItemsArray = [];

    //If product has been ordered, add to the array
    products.forEach(function(item){
        if(item.qty > 0){
            orderedItemsArray.push(item);
        }
    });

    //Convert the array into a JSON
    var orderedItemsArrayJson = JSON.stringify(orderedItemsArray);

    console.log("JSON Array: " + orderedItemsArrayJson);
    console.log("Array: " + orderedItemsArray);
    console.log("Comments: " + comments);
    console.log("Delivery Address: " + delivery_address);

    //Post the order to the "/order" OrderController
    $.post("/order", { orderedItems:orderedItemsArrayJson, delivery_address:delivery_address, comments:comments })
        .done(function(data){
            window.location.replace("/order/confirmation")
    });
});

//view current orders
$.getJSON("/order/view", function(data){
    $.each(data, function(key, val){
        //Build the table to show orders
        $("#orderTableBody").append(
            "<tr><td><button id=orderDetailsButton" + val.id + ">" + val.id + "</button></td>"
            + "<td>" + val.delivery_address + "</td>"
            + "<td>" + val.status + "</td>"
            + "<td>" + val.comments + "</td>"
            + "<td>€" + val.total_cost + "</td>"
            + "<td>" + val.timestamp + "</td>"
        );
        //Click handler for orderDetailsButton
        $("#orderDetailsButton" + val.id).click(function(){
            //When clicked, empty the current contents of the table body and show the div
            $("#orderDetailsTable").show();
            $("#orderDetailsTableBody").empty();
            $("#orderOptionsDiv").empty();

            //create dispatch and order buttons
            $("#orderOptionsDiv").show().append(
                "<button id=markDispatched" + val.id + " class='centered'>Mark Order " + val.id + " as Dispatched</button><br/>"
                + "<button id=markComplete" + val.id + " class='centered'>Mark Order " + val.id + " as Complete</button><br/>"
            );
            $("#markDispatched" + val.id).click(function(){
                $.post("/order/dispatch/" + val.id);
                window.location.reload(true);
                alert("Order " + val.id + " dispatched!");
            });
            $("#markComplete" + val.id).click(function(){
                $.post("/order/complete/" + val.id);
                window.location.reload(true);
                alert("Order " + val.id + " delivered!");
            });

            //grab JSON of orderDetails from controller
            $.getJSON("/order/view/" + val.id, function(data){
                $("#orderIdHeader").html("Order Details for Order Number " + val.id);
                $.each(data, function(key, val){
                    $("#orderDetailsTableBody").append(
                        "<tr><td>" + val.name + "</td>"
                        + "<td>" + val.description + "</td>"
                        + "<td>" + val.quantity + "</td>"
                        + "<td>" + val.price + "</td>"
                        + "<td>€" + (val.quantity * val.price) + "</td>"
                    )
                })
            })
        });
    })
});