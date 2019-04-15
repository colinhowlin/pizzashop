/*
   File for code related to updating the current order side panel
 */

//function to update the order panel
function updateOrderPanel(productArray){
    productArray.forEach(function(product){
        if(product.qty > 0){
            $("#currentOrderPanelTbody").append(
                "<tr><td>" + product.name + "</td>" +
                "<td id=curQty" + product.product_code + ">" + product.qty + "</td>" +
                "<td id=curCost" + product.product_code + ">€" + product.qty * product.price +"</td>" +
                "<td><button id=remove" + product.product_code + ">Delete</button></td>" +
                "<td><button id=addToOrder" + product.product_code + ">Add</button></td></tr>"
            );
        }
        //click handler for delete button
        $("#remove" + product.product_code).click(function(){
            if (product.qty > 0) {
                product.qty -= 1;
                $("#curQty" + product.product_code).html(product.qty);
                $("#curCost" + product.product_code).html("€" + product.qty * product.price);
            }
        });
        //click handler for add button
        $("#addToOrder" + product.product_code).click(function(){
            product.qty++;
            $("#curQty" + product.product_code).html(product.qty);
            $("#curCost" + product.product_code).html("€" + product.qty * product.price);

        });
    });
}

//click handler for view current order button.
//simply calls update OrderPanel function on each product array
$("#viewCurrentOrder").click(function(){
    $("#currentOrderPanelTbody").empty();
    updateOrderPanel(pizzas);
    updateOrderPanel(toppings);
    updateOrderPanel(sides);
    updateOrderPanel(drinks);

    //$("#totalCostTd").append()
});