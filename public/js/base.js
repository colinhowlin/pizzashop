/*
    This file holds code for displaying order menu as well
    as the order side panel and order review div.
    Also handles posting the order to the database and
    viewing existing orders.
 */

//generate pizza menu entries
pizzaTypes.forEach(function(pizzaType){
    $("#pizzaTableBody").append(
        "<tr><div class='ui-field-contain'>" +
        "<td>" + pizzaType.name + "</td>" +
        "<td>" + pizzaType.description + "</td>" +
        "<td><select name=select-" + pizzaType.pre + " id=select-" + pizzaType.pre + " data-mini='true'>" +
        "<option value='8'>8 inch</option>" +
        "<option value='10'>10 inch</option>" +
        "<option value='12'>12 inch</option></select>" +
        "<td id=pizzaPrice" + pizzaType.pre + " class='center-text'>€5</td>" +
        "</td><td><button id=addpizza" + pizzaType.pre + ">Add</button></td></div></tr>"
    );
    //create click handlers for Add buttons
    $("#addpizza" + pizzaType.pre).click(function(){
        var pizzaSize = $("#select-" + pizzaType.pre).val();
        var product_code = pizzaType.pre + pizzaSize;
        pizzas.forEach(function(pizza){
            if (product_code === pizza.product_code){
                pizza.qty++;
            }
        });
    });
    //create change handler to update price when different size pizza selected
    $("#select-" + pizzaType.pre).change(function(){
        var pizzaSize = $("#select-" + pizzaType.pre).val();
        if (pizzaSize == 8){
            $("#pizzaPrice" + pizzaType.pre).html("€5");
        } else if (pizzaSize == 10){
            $("#pizzaPrice" + pizzaType.pre).html("€10");
        } else if (pizzaSize == 12){
            $("#pizzaPrice" + pizzaType.pre).html("€15");
        }
    });
});

//function to build out the rest of the menu
function buildMenuBody(productArray, divId){
    productArray.forEach(function(product){
        $("#" + divId).append(
            "<tr><td>" + product.name + "</td>" +
            "<td>" + product.description + "</td>" +
            "<td class='center-text'>€" + product.price + "</td>" +
            "<td><button id=add" + product.product_code + ">Add</button></td></tr>"
        );
        //create click handlers for Add buttons
        $("#add" + product.product_code).click(function(){
            product.qty++;
            //$("#" + product.product_code + "qty").html(product.qty);
        });
    });
}
buildMenuBody(toppings, "toppingsTableBody");
buildMenuBody(sides, "sidesTableBody");
buildMenuBody(drinks, "drinksTableBody");

var delivery_address;
var comments;

//click handler for place order button
$( "#placeOrderButton" ).click(function() {
    //Grab the contents of the Delivery address and comments text boxes
    delivery_address = $("#delivery_address").val();
    comments = $("#comments").val();

    //build the customer order for customer to review
    var custOrder = "<h2>Your Order Details: </h2><table class='menu-table'><th>Item</th><th>Qty</th><th>Cost</th>";
    var orderCost = 0;

    //loop through products array and call addToOrder function on it
    pizzas.forEach(addToOrder);
    toppings.forEach(addToOrder);
    sides.forEach(addToOrder);
    drinks.forEach(addToOrder);

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

    $(" #orderDetailsDiv ").html(custOrder);
});

//click handler for confirmOrder button
$( "#confirmOrderButton" ).click(function() {
    //create empty array to hold ordered products
    var orderedItemsArray = [];

    function addToOrderArray(productsArray){
        productsArray.forEach(function(product){
            if(product.qty > 0){
                orderedItemsArray.push(product);
            }
        });
    }

    addToOrderArray(pizzas);
    addToOrderArray(toppings);
    addToOrderArray(sides);
    addToOrderArray(drinks);

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
            //$("#orderOptionsDiv").empty();

            //create dispatch and order buttons
            $("#orderOptionsDiv").empty().show().append(
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