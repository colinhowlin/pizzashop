/*
    Holds details on product classes and creates
    arrays of products for use in building the menu.
 */

class product{
    constructor(name, description, price, product_code, qty, prefix){
        this.name = name;
        this.description = description;
        this.price = price;
        this.product_code = product_code;
        this.qty = qty;
        this.prefix = prefix;
    }
}

class pizzaType {
    constructor(pre, name, description){
        this.pre = pre;
        this.name = name;
        this.description = description;
    }
}

const pizzaTypes = [
    hawaiian = new pizzaType("haw", "Hawaiian", "Ham & Pineapple"),
    newyorker = new pizzaType("ny", "New Yorker", "Classic New York Style"),
    house = new pizzaType("house", "House Special", "Our special recipe sauce"),
    custom = new pizzaType("cust", "Custom Pizza", "Choose your own toppings")
];

const pizzas = [
    haw8 = new product("Hawaiian Small", "Ham and Pineapple 8\"", 5, "haw8", 0, "haw"),
    haw10 = new product("Hawaiian Medium", "Ham and Pineapple 10\"", 10, "haw10", 0, "haw"),
    haw12 = new product("Hawaiian Large", "Ham and Pineapple 12\"", 15, "haw12", 0, "haw"),
    ny8 = new product("New Yorker Small", "Classic New York Style 8\"", 5, "ny8", 0, "ny"),
    ny10 = new product("New Yorker Medium", "Classic New York Style 10\"", 10, "ny10", 0, "ny"),
    ny12 = new product("New Yorker Large", "Classic New York Style 12\"", 15, "ny12", 0, "ny"),
    house8 = new product("House Special Small", "Our special recipe pizza 8\"", 5, "house8", 0, "house"),
    house10 = new product("House Special Medium", "Our special recipe pizza 10\"", 10, "house10", 0, "house"),
    house12 = new product("House Special Large", "Our special recipe pizza 12\"", 15, "house12", 0, "house"),
    cust8 = new product("Custom Small", "Choose your own toppings! 8\"", 5, "cust8", 0, "cust"),
    cust10 = new product("Custom Medium", "Choose your own toppings! 10\"", 10, "cust10", 0, "cust"),
    cust12 = new product("Custom Large", "Choose your own toppings! 12\"", 15, "cust12", 0, "cust"),
];

const toppings = [
    topham = new product("Extra Topping - Ham", "Choice of topping", 1, "topham", 0, "top"),
    topchi = new product("Extra Topping - Chicken", "Choice of topping", 1, "topchi", 0, "top"),
    toppepi = new product("Extra Topping - Pepperoni", "Choice of topping", 1, "toppepi", 0, "top"),
    topswe = new product("Extra Topping - Sweetcorn", "Choice of topping", 1, "topswe", 0, "top"),
    toptom = new product("Extra Topping - Tomato", "Choice of topping", 1, "toptom", 0, "top"),
    toppep = new product("Extra Topping - Peppers", "Choice of topping", 1, "toppep", 0, "top"),
];

const sides = [
    wedge = new product("Potato Wedges", "Spicy Potato Wedges", 5, "wedge", 0, "side"),
    gbread = new product("Garlic Bread", "6\" Garlic Bread", 5, "gbread", 0, "side"),
];

const drinks = [
    coke = new product("Coke 330ml", "330ml Coke", 1, "coke", 0, "drink"),
    fanta = new product("Fanta 330ml", "330ml Fanta", 1, "fanta", 0, "drink"),
    sprite = new product("Sprite 330ml", "330ml Sprite", 1, "sprite", 0, "drink")
];