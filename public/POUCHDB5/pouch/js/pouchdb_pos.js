
var can_close_cash_register = false;
var can_view_cash_register = false;
var can_expense_add = false;


pos_layout = true;
whitelist = ['127.0.0.1', '::1'];


go_back_url = "!#";
transaction_sub_type = "";
view_suspended_sell_url = "!#";
pos_redirect_url = "!#";


var html = '';

function isMobile() {
    return true;
}

serverfaker_init_app().then(function (contents) {
    alert(222222222222222);
   console.log('contents',contents);
});


