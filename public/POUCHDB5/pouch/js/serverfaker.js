
/*******************CONVERSTION URI TO OBJECT************************** */
var getParamsAsObject = function (query) {
    const isNumeric = (string) => /^[+-]?\d+(\.\d+)?$/.test(string)

    query = query.substring(query.indexOf('?') + 1);

    var re = /([^&=]+)=?([^&]*)/g;
    var decodeRE = /\+/g;

    var decode = function (str) {
        return decodeURIComponent(str.replace(decodeRE, " "));
    };

    var params = {}, e;
    while (e = re.exec(query)) {
        var k = decode(e[1]), v = decode(e[2]);
        if (k.substring(k.length - 2) === '[]') {
            k = k.substring(0, k.length - 2);
            (params[k] || (params[k] = [])).push(v);
        }
        else params[k] = v;
    }

    var assign = function (obj, keyPath, value) {
        var lastKeyIndex = keyPath.length - 1;
        for (var i = 0; i < lastKeyIndex; ++i) {
            var key = keyPath[i];
            if (!(key in obj))
                obj[key] = {}
            obj = obj[key];
        }

        if(keyPath[lastKeyIndex]=="unit_price_inc_tax" || keyPath[lastKeyIndex]=="amount"){
            value=value.replace(__currency_thousand_separator,'');
            value=value.replace(__currency_decimal_separator,'.');
        }

        obj[keyPath[lastKeyIndex]] = value;

        
    }

    for (var prop in params) {
        var structure = prop.split('[');
        if (structure.length > 1) {
            var levels = [];
            structure.forEach(function (item, i) {
                var key = item.replace(/[?[\]\\ ]/g, '');
                levels.push(key);
            });
            assign(params, levels, params[prop]);
            delete (params[prop]);
        }
    }
    return params;
};
/******************************************************************* */

/****************** PROCESSUS DE DECONNEXTION **********************/
function singout(params) {
    //je verifie u'il ny a pas des ventes à synchroniser 

    //si il n'ya pas on passe à la deconnection
    alert(11111111111);
    localStorage.setItem('APP','');
    window.location="index.html";
    
}
/*********************************************************************** */


function serverfaker_init_app() {
    return new Promise(function (resolve, reject) {
        alert("=====>"+doc_id);
        alert('++++++doc>'+doc_obj);
        db.get(doc_id).then(function (doc) {
            //console.log("docdocdocdocdocdoc",doc); return;
            document.title = "H360 POS LITE - " + doc.business.name;

            DATA = doc;
            console.log("j====>", doc);
            doc_contacts = doc.contacts;
            console.log("doc_contacts====>", doc_contacts);


            //je recupere le location id passé en argument
            location_id = 0;
            businesslocations = doc.businesslocations;

            /******************SITE COMMERCIAUX SELECT BOX******************* */
            var data_html = `<div style="width: 28%;margin-bottom: 5px;">
            <select class="form-control input-sm" id="select_location_id" required="" autofocus=""
                                        name="select_location_id">`;
            if (Object.keys(businesslocations).length > 0) {
                //data_string=`<select class="form-control input-sm" id="select_location_id" required="" autofocus="" name="select_location_id">`;

                businesslocations.forEach(function (location, index) {
                    //alert(location.name+"===>");
                    data_html += `<option value="` + location.id + `" ` + ((index == 0) ? `selected="selected"` : '')
                        + `  data-receipt_printer_type="` + location.attributes["data-receipt_printer_type"] + `"
									data-default_payment_accounts="`+ location.attributes["data-default_payment_accounts"].replace(/"/g, "&quot;")
                        + `" data-default_sale_invoice_scheme_id="` + location.attributes["data-default_sale_invoice_scheme_id"]
                        + `" data-default_invoice_scheme_id="` + location.attributes["data-default_invoice_scheme_id"]
                        + `"data-default_invoice_layout_id="` + location.attributes["data-default_invoice_layout_id"]
                        + `" second_currency="` + location.attributes.second_currency + `"
									second_currency_rate="`+ location.attributes.second_currency_rate
                        + `" second_currency_code="` + location.attributes.second_currency_code + `"
									second_currency_symbol="`+ location.attributes.second_currency_symbol + `">`
                        + location.name + ` (` + location.location_id + `)</option>`;
                });
                data_html+=`</select></div>`;

            } else {
                data_html = doc.businesslocations[0].name;
            }

            $('.replace_select_location_id').replaceWith(data_html);
            $('input#location_id').val(doc.businesslocations[0].id);
            //je modifie le location_id par defaut en mettant le premier location

            
            
            /*
            document.getElementById('select_location_id').innerHTML = data_string;
            //$('#select_location_id').html(data_string).create(); //enlevé car avec cettte facon les evenement attaché au dom ne sont plus declenchés
            /*****************************************************************/

            /******************WALKIN CUSTOMMER******************* *
            document.getElementById("default_customer_id").innerHTML = doc.walk_in_customer.id;
            document.getElementById("default_customer_name").innerHTML = doc.walk_in_customer.name;
            document.getElementById("default_customer_balance").innerHTML = doc.walk_in_customer.balance;
            document.getElementById("default_customer_address").innerHTML = doc.walk_in_customer.address_line_1;
            /******************************************************** */
        

            data_string = '';
            businesslocations.forEach(function (location) {

                //alert($('#location_show_name1').html());


                if (location.id == location_id) {

                    //document.getElementById('location_show_name1')= location.name;
                    console.log(location.products);
                    Object.values(location.products).forEach(function (product) {
                        console.log(product);



                    });
                }
            });

            // console.log(formattedList);
            dbInited=1; //db initié  , signalé que le db est initié
            //alert("dbInited-->"+dbInited);
            resolve(data_string);
        }).catch(function (err) {
            dbInited=2; //db initié avec erreur
            console.log(err);
        });
    });
}

/********************************************* */
function serverfaker_get_product_suggestion(location_id) {
    return new Promise(function (resolve, reject) {


        db.get(doc_id).then(function (doc) {
            var data_string = '';
            console.log("serverfaker_get_product_suggestion====>", doc);

            //je recupere le location id passé en argument
            businesslocations = doc.businesslocations;
            businesslocations.forEach(function (row) {
                if (row.id == location_id) {
                    console.log(row.products);
                    Object.values(row.products).forEach(function (product) {
                        console.log("product=====>",product);
                        data_string += "<div class=\"col-md-3 col-xs-4 product_list no-print\" >"
                            + "<div class=\"product_box\" data-variation_id=\"" + product.variation_id + "\" title=\"" + product.product_actual_name + "(" + product.sub_sku + ")\">"

                            + "<div class=\"image-container\" style=\"background-image: url(http://h360.test/img/default.png);background-repeat: no-repeat; background-position: center;background-size: contain;\"></div>"

                            + "<div class=\"text_div\">"
                            + "<small class=\"text text-muted\">" + product.product_actual_name + "</small>"

                            + "<small class=\"text-muted\">(" + product.sub_sku + ")</small>"
                            + "</div>"

                            + "</div>"
                            + "</div>";
                    });
                }
            });
            // console.log(formattedList);
            resolve(data_string);
        }).catch(function (err) {
            console.log(err);
        });
    });
}
/*********************************************** */
/*******************GET CONTACTS************************** */
function serverfaker_search_contacts(q) {
    return new Promise(function (resolve, reject) {
        db.get(doc_id).then(function (doc) {
            var data_string = '';
            resolve(doc.contacts);
        }).catch(function (err) {
            console.log(err);
        });
    });
}
/*********************************************** */

/*******************GET CONTACTS************************** */
function serverfaker_put_sell(form) {
    return new Promise(function (resolve, reject) {
        db.get(doc_id).then(function (doc) {
            var product_row_count = $(form).find('.product_row').length;
            var payment_row_count = $(form).find('.payment_row_index').length;
            //alert(product_row_count);

            const queryString = $(form).serialize();
            const urlParams = new URLSearchParams(queryString);

            payload = getParamsAsObject(queryString);
            console.log('payload',payload);
            console.log("params object", payload);

            //prompt('a',queryString);

            //alert(urlParams.get('contact_id'));
            //alert(urlParams.get('products[1][product_type]')+'<--');
            /*
            products%5B1%5D%5Bquantity%5D
            products%5B1%5D%5Bproduct_unit_id%5D
            products%5B1%5D%5Bbase_unit_multiplier%5D
            final_total
            payment%5B0%5D%5Bamount%5D
            */
            //alert('tout');
            var nw_sell = { "_id": 1, "id": 1, "data": queryString, 'payload': payload, 'date': __current_datetime() }
            var location_id = urlParams.get('location_id');
            var receipt_details = {};
            doc.businesslocations.forEach(function (location) {
                if (location.id == location_id) {
                    var contact = null; //si le contact est null je force le walk-in customer
                    Object.values(doc.contacts).forEach(function (_contact) {
                        (_contact.id == doc.walk_in_customer.id) ? contact = _contact : '';
                    });

                    Object.values(doc.contacts).forEach(function (_contact) {

                        if (_contact.id == urlParams.get('contact_id')) {
                            contact = _contact;
                        }
                    });


                    sell_lines = [];
                    Object.values(location.products).forEach(function (product) {
                        for (let i = 1; i < product_row_count + 1; i++) {
                            if (product.variation_id == urlParams.get('products[' + i + '][variation_id]')) {
                                product.qty_available = product.qty_available - urlParams.get('products[' + i + '][quantity]');

                                sell_line = [];
                                sell_line.id = 0;
                                sell_line.transaction_id = 0;
                                sell_line.product_id = product.product_id;
                                sell_line.variation_id = product.variation_id;
                                sell_line.quantity = urlParams.get('products[' + i + '][quantity]');
                                sell_line.mfg_waste_percent = "0.0000";
                                sell_line.mfg_ingredient_group_id = null;
                                sell_line.secondary_unit_quantity = "0.0000";
                                sell_line.quantity_returned = "0.0000";
                                sell_line.unit_price_before_discount = urlParams.get('products[' + i + '][unit_price]');
                                sell_line.unit_price = urlParams.get('products[' + i + '][unit_price]');
                                sell_line.unit_price_before_customupdate = urlParams.get('products[' + i + '][unit_price_before_customupdate]');
                                sell_line.line_discount_type = urlParams.get('products[' + i + '][line_discount_type]');
                                sell_line.line_discount_amount = urlParams.get('products[' + i + '][line_discount_amount]');
                                sell_line.unit_price_inc_tax = urlParams.get('products[' + i + '][unit_price_inc_tax]');
                                sell_line.item_tax = urlParams.get('products[' + i + '][item_tax]');
                                sell_line.tax_id = urlParams.get('products[' + i + '][tax_id]');
                                sell_line.discount_id = urlParams.get('products[' + i + '][discount_id]');
                                sell_line.lot_no_line_id = urlParams.get('products[' + i + '][lot_no_line_id]');
                                sell_line.sell_line_note = urlParams.get('products[' + i + '][sell_line_note]');
                                sell_line.woocommerce_line_items_id = urlParams.get('products[' + i + '][woocommerce_line_items_id]');
                                sell_line.so_line_id = urlParams.get('products[' + i + '][so_line_id]');

                                sell_line.sub_sku = product.sub_sku;

                                sell_line.total_payed = urlParams.get('payment[' + "0" + '][amount]');


                                sell_line.so_quantity_invoiced = urlParams.get('products[' + i + '][so_quantity_invoiced]');
                                sell_line.res_service_staff_id = urlParams.get('products[' + i + '][res_service_staff_id]');
                                sell_line.res_line_order_status = urlParams.get('products[' + i + '][res_line_order_status]');
                                sell_line.parent_sell_line_id = urlParams.get('products[' + i + '][parent_sell_line_id]');
                                sell_line.children_type = urlParams.get('products[' + i + '][children_type]');
                                sell_line.sub_unit_id = urlParams.get('products[' + i + '][sub_unit_id]');
                                /*** a checque */
                                sell_line.second_currency = 2;
                                sell_line.second_currency_rate = "1.000000000000";
                                /****************/
                                sell_line.created_at = "2024-08-31 17:02:58";
                                sell_line.updated_at = "2024-08-31 17:02:58";
                                //alert(product.qty_available+'trouvé'+i);

                                //utile car on va l'utiliser dans la fonction appelée
                                sell_line.product = product;
                                sell_line.sub_units = product.sub_units;

                                //on met l'unité 
                                /*
                                Object.values(product.sub_units).forEach(function (sub_unit) {
                                    if(product.unit==sub_unit)
                                });
                            */

                                sell_lines.push(sell_line);
                            }
                        }

                    });

                    console.log('my contact', contact);
                    var transaction = DATA_TRANSACTION
                    transaction.sales_person = doc.user;
                    
                    transaction.id= 35469;
                    transaction.business_id= 1;
			        transaction.location_id= 1;
			        transaction.journal_entry_id= null;
			        transaction.res_table_id= null;
			        transaction.res_waiter_id= null;
			        transaction.res_order_status= null;
			        transaction.type= "sell";
			        transaction.sub_type= null;
			        transaction.status= "final";
			        transaction.sub_status= null;
			        transaction.is_quotation= 0;
			        transaction.payment_status= "paid";
			        transaction.adjustment_type= null;
			        transaction.contact_id= 1;
			        transaction.customer_group_id= null;
                    transaction.invoice_no= "0034";
                    transaction.ref_no= "";
                    transaction.source= null;
                    transaction.subscription_no= null;
                    transaction.subscription_repeat_on= null;
                    transaction.transaction_date= "2024-08-30 13=24=56";
                    transaction.total_before_tax= "2.0";
                    transaction.tax_id= null;
                    transaction.tax_amount= "0.0000";
                    transaction.discount_type= "percentage";
                    transaction.discount_amount= "0.0000";
                    transaction.rp_redeemed= 0;
                    transaction.rp_redeemed_amount= "0.0000";
                    transaction.shipping_details= null;
                    transaction.shipping_address= null;
                    transaction.delivery_date= null;
                    transaction.shipping_status= null;
                    transaction.delivered_to= null;
                    transaction.delivery_person= null;
			transaction.shipping_charges= "0.0000";
			transaction.shipping_custom_field_1= null;
			transaction.shipping_custom_field_2= null;
			transaction.shipping_custom_field_3= null;
			transaction.shipping_custom_field_4= null;
			transaction.shipping_custom_field_5= null;
			transaction.additional_notes= null;
			transaction.staff_note= null;
			transaction.is_export= 0;
			transaction.export_custom_fields_info= null;
			transaction.round_off_amount= "0.0000";
			transaction.additional_expense_key_1= null;
			transaction.additional_expense_value_1= "0.0000";
			transaction.additional_expense_key_2= null;
			transaction.additional_expense_value_2= "0.0000";
			transaction.additional_expense_key_3= null
			transaction.additional_expense_valueù_3= "0.0000";
			transaction.additional_expense_key_4= null;
			transaction.additional_expense_value_4= "0.0000";
			transaction.final_total=payload.final_total;
			transaction.expense_category_id= null;
			transaction.expense_sub_category_id= null;
			transaction.expense_for= null;
			transaction.commission_agent= null;
			transaction.document= null;
			transaction.is_direct_sale= 0;
			transaction.is_suspend= 0;
			transaction.exchange_rate= "1.000";
			transaction.total_amount_recovered= null;
			transaction.transfer_parent_id= null;
			transaction.return_parent_id= null;
			transaction.opening_stock_product_id= null;
			transaction.created_by= 1;
			transaction.repair_completed_on= null;
			transaction.repair_warranty_id= null;
			transaction.repair_brand_id= null;
			transaction.repair_status_id= null;
			transaction.repair_model_id= null;
			transaction.repair_job_sheet_id= null;
			transaction.repair_defects= null;
			transaction.repair_serial_no= null;
			transaction.repair_checklist= null;
			transaction.repair_security_pwd= null;
			transaction.repair_security_pattern= null;
			transaction.repair_due_date= null;
			transaction.repair_device_id= null;
			transaction.repair_updates_notif= 0;
			transaction.woocommerce_order_id= null;
			transaction.mfg_parent_production_purchase_id= null;
			transaction.mfg_wasted_units= null;
			transaction.mfg_production_cost= "0.0000";
			transaction.mfg_production_cost_type= "percentage";
			transaction.mfg_is_final= 0;
			transaction.essentials_duration= "0.00";
			transaction.essentials_duration_unit= null;
			transaction.essentials_amount_per_unit_duration= "0.0000";
			transaction.essentials_allowances= null;
			transaction.essentials_deductions= null;
			transaction.crm_is_order_request= 0;
			transaction.purchase_requisition_ids= null;
			transaction.prefer_payment_method= null;
			transaction.prefer_payment_account= null;
			transaction.sales_order_ids= null;
			transaction.purchase_order_ids= null;
			transaction.custom_field_1= null;
			transaction.custom_field_2= null;
			transaction.custom_field_3= null;
			transaction.custom_field_4= null;
			transaction.import_batch= null;
			transaction.import_time= null;
			transaction.types_of_service_id= null;
			transaction.packing_charge= "0.0000";
			transaction.packing_charge_type= null;
			transaction.service_custom_field_1= null;
			transaction.service_custom_field_2= null;
			transaction.service_custom_field_3= null;
			transaction.service_custom_field_4= null;
			transaction.service_custom_field_5= null;
			transaction.service_custom_field_6= null;
			transaction.is_created_from_api= 0;
			transaction.rp_earned= 0;
			transaction.order_addresses= null;
			transaction.is_recurring= 0;
			transaction.recur_interval= 1.0;
			transaction.recur_interval_type= "days";
			transaction.recur_repetitions= 0;
			transaction.recur_stopped_on= null;
			transaction.recur_parent_id= null;
			transaction.invoice_token= null;
			transaction.pay_term_number= null;
			transaction.pay_term_type= null;
			transaction.pjt_project_id= null;
			transaction.pjt_title= null;
			transaction.selling_price_group_id= 0;
			transaction.second_currency= 2;
			transaction.second_currency_rate= "1.000000000000";
			transaction.hms_booking_arrival_date_time= null;
			transaction.hms_booking_departure_date_time= null;
			transaction.hms_coupon_id= null;
			transaction.created_at= "2024-08-30 13=24=57";
			transaction.updated_at= "2024-08-30 13=25=01";


                    console.log('sell_linessell_linessell_lines',sell_lines);

                    receipt_details = getReceiptDetails(nw_sell, location, contact, sell_lines, transaction);


                }
            });


            doc.sells.to.push(nw_sell);
            //db.put(doc);
            html_content = get_html_content(receipt_details);
            //html_content=`<!-- business information here -->\n\n<div class=\"row\" style=\"color: #000000 !important;\">\n\t\t\t<!-- Logo -->\n\t\t\t\t\t<img style=\"max-height: 120px; width: auto;\" src=\"http:\/\/localhost:8001\/uploads\/invoice_logos\/1685190346_logo rs.png\" class=\"img img-responsive center-block\">\n\t\t\n\t\t<!-- Header text -->\n\t\t\n\t\t<!-- business information here -->\n\t\t<div class=\"col-xs-12 text-center\">\n\t\t\t<h2 class=\"text-center\">\n\t\t\t\t<!-- Shop & Location Name  -->\n\t\t\t\t\t\t\t\t\tHospitality 360\n\t\t\t\t\t\t\t<\/h2>\n\n\t\t\t<!-- Address -->\n\t\t\t<p>\n\t\t\t\t\t\t\t\t<small class=\"text-center\">\n\t\t\t\t\tKINSHASA, KINSHASA, KINSHASA, 001, RDC\n\t\t\t\t\t<\/small>\n\t\t\t\t\t\t\t\t\t\t<br\/><b>Mobile:<\/b> 243812558314\n\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t\t\t<\/p>\n\t\t\t<p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t<br>1USD=1.0000USD\n\t\t\t\t\t\t<\/p>\n\t\t\t<p>\n\t\t\t\t\t\t\t<b>COM: <\/b> T001\n\t\t\t\n\t\t\t\t\t\t<\/p>\n\n\t\t\t<!-- Title of receipt -->\n\t\t\t\t\t\t\t<h3 class=\"text-center\">\n\t\t\t\t\tInvoice\n\t\t\t\t<\/h3>\n\t\t\t\t\t<\/div>\n\t\t<div class=\"col-xs-12 text-center\">\n\t\t<!-- Invoice  number, Date  -->\n\t\t<p style=\"width: 100% !important\" class=\"word-wrap\">\n\t\t\t<span class=\"pull-left text-left word-wrap\">\n\t\t\t\t\t\t\t\t\t<b>Invoice No.<\/b>\n\t\t\t\t\t\t\t\t0033\n\n\t\t\t\t\n\t\t\t\t<!-- Table information-->\n\t\t        \n\t\t\t\t<!-- customer info -->\n\t\t\t\t\t\t\t\t\t<br\/>\n\t\t\t\t\t<b>Customer<\/b> <br> Walk-In Customer<br><b>Mobile<\/b>:  <br>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<\/span>\n\n\t\t\t<span class=\"pull-right text-left\">\n\t\t\t\t<b>Date<\/b> 30\/08\/2024 01:02\n\n\t\t\t\t\n\t\t\t\t\n\n\t\t        \n\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t        \n\t\t        \t\t        \n\t\t\t\t<!-- Waiter info -->\n\t\t\t\t\t\t        \n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\t\t\t\t\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\n\t\t\t\t\n\t\t\t<\/span>\n\t\t<\/p>\n\t<\/div>\n<\/div>\n\n<div class=\"row\" style=\"color: #000000 !important;\">\n\t<!-- \/.col --><\/div>\n\n<div class=\"row\" style=\"color: #000000 !important;\">\n\t<div class=\"col-xs-12\">\n\t\t<br\/>\n\t\t\t\t\t\t\t\t<table class=\"table table-responsive table-slim\">\n\t\t\t<thead>\n\t\t\t\t<tr>\n\t\t\t\t\t<th width=\"45%\">Product<\/th>\n\t\t\t\t\t<th class=\"text-right\" width=\"15%\">Quantity<\/th>\n\t\t\t\t\t<th class=\"text-right\" width=\"15%\">Unit Price<\/th>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<th class=\"text-right\" width=\"15%\">Subtotal<\/th>\n\t\t\t\t<\/tr>\n\t\t\t<\/thead>\n\t\t\t<tbody>\n\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t                            PRODUIT A   \n                            , 3455                                                            \n                             \n                             \n                            \n                                                         \n                                                    <\/td>\n\t\t\t\t\t\t<td class=\"text-right\">\n\t\t\t\t\t\t\t1.0000 Pc(s) \n\n\t\t\t\t\t\t\t\t\t\t\t\t\t<\/td>\n\t\t\t\t\t\t<td class=\"text-right\">6.2500<\/td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<td class=\"text-right\">6.2500<\/td>\n\t\t\t\t\t<\/tr>\n\t\t\t\t\t\t\t\t\t\t\t\t<\/tbody>\n\t\t<\/table>\n\t<\/div>\n<\/div>\n\n<div class=\"row\" style=\"color: #000000 !important;\">\n\t<div class=\"col-md-12\"><hr\/><\/div>\n\t<div class=\"col-xs-6\">\n\n\t\t<table class=\"table table-slim\">\n\n\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t<td>En esp\u00e8ces<\/td>\n\t\t\t\t\t\t<td class=\"text-right\" >6.2500 $<\/td>\n\t\t\t\t\t\t<td class=\"text-right\">30\/08\/2024<\/td>\n\t\t\t\t\t<\/tr>\n\t\t\t\t\t\t\t\n\t\t\t<!-- Total Paid-->\n\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t<th>\n\t\t\t\t\t\tTotal Paid\n\t\t\t\t\t<\/th>\n\t\t\t\t\t<td class=\"text-right\">\n\t\t\t\t\t\t6.2500 $\n\t\t\t\t\t<\/td>\n\t\t\t\t<\/tr>\n\t\t\t\n\t\t\t<!-- Total Due-->\n\t\t\t\n\t\t\t\t\t<\/table>\n\t<\/div>\n\n\t<div class=\"col-xs-6\">\n        <div class=\"table-responsive\">\n          \t<table class=\"table table-slim\">\n\t\t\t\t<tbody>\n\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t<th style=\"width:70%\">\n\t\t\t\t\t\t\tSubtotal:\n\t\t\t\t\t\t<\/th>\n\t\t\t\t\t\t<td class=\"text-right\">\n\t\t\t\t\t\t\t6.2500 $\n\t\t\t\t\t\t<\/td>\n\t\t\t\t\t<\/tr>\n\t\t\t\t\t\t\t\t\t\t<!-- Shipping Charges -->\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<!-- Discount -->\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<!-- Tax -->\n\t\t\t\t\t\n\t\t\t\t\t\n\t\t\t\t\t<!-- Total -->\n\t\t\t\t\t<tr>\n\t\t\t\t\t\t<th>\n\t\t\t\t\t\t\tTotal:\n\t\t\t\t\t\t<\/th>\n\t\t\t\t\t\t<td class=\"text-right\">\n\t\t\t\t\t\t\t6.2500 $\n\t\t\t\t\t\t\t\t\t\t\t\t\t<\/td>\n\t\t\t\t\t<\/tr>\n\t\t\t\t<\/tbody>\n        \t<\/table>\n        <\/div>\n    <\/div>\n\n    <div class=\"border-bottom col-md-12\">\n\t    \t<\/div>\n\n\t    \n<\/div>\n<div class=\"row\" style=\"color: #000000 !important;\">\n\t\t<\/div>`,




            console.log('receipt_details', receipt_details);

            var res = {
                "success": 1,
                "msg": "Vente ajout\u00e9e avec succ\u00e8s",
                "receipt": {
                    "is_enabled": true,
                    "print_type": "browser",
                    "html_content": html_content,
                    "printer_config": [],
                    "data": [],
                    "print_title": "0033"
                }
            };

            resolve(res);
        })
            /*
            .catch(function (err) {
                console.log(err);
            })
            */
            ;
    });
}
/*********************************************** */

/***** la gestion de produit pour le mettre dans le panier */
function serverfaker_get_product_row(variation_id, location_id, row_count = 0, customer_id = 0,
    quantity = 1, is_direct_sell = false
    , price_group = 0, purchase_line_id = null, weighing_scale_barcode = null,
    is_sales_order = false, is_draft = false, disable_qty_alert = false, so_line = null
) {
    return new Promise(function (resolve, reject) {

        db.get(doc_id).then(function (doc) {
            var data_string = '';
            businesslocations = doc.businesslocations;




            businesslocations.forEach(function (row) {
                if (row.id == location_id) {
                    row_count = parseInt(row_count) + 1;
                    quantity = 1;
                    //$weighing_barcode = request()->get('weighing_scale_barcode', null); la gestion de balance pas encore prise en charge
                    is_direct_sell = is_direct_sell;

                    pos_settings = doc.business.default_pos_settings;
                    if (!doc.business.default_pos_settings) {
                        pos_settings = doc.business.default_pos_settings;
                    }





                    //pos_settings=JSON.parse(pos_settings);
                    //console.log("jj",pos_settings);

                    //Check for weighing scale barcode
                    //weighing_barcode = request()->get('weighing_scale_barcode');

                    check_qty = (!pos_settings.allow_overselling) ? false : true;
                    //alert(check_qty);

                    is_sales_order = is_sales_order;
                    is_draft = is_draft;

                    if (is_sales_order || (!so_line) || is_draft) {
                        $check_qty = false;
                    }


                    pos_settings.allow_overselling = disable_qty_alert;

                    /****doit pprovenir du serveur , ici mis juste ppour les testes */
                    edit_price = true;
                    /****************************************************************** */






                    Object.values(row.products).forEach(function (product) {
                        if (product.variation_id == variation_id) {
                            user_role = doc.user.roles[0].name;
                            can_edit_product_price_from_sale_screen = false;
                            can_edit_product_discount_from_pos_screen = false

                            Object.values(doc.user.roles[0].permissions).forEach(function (permission, permission_key) {
                                //console.log(permission_key,permission);
                                if (permission.name == 'edit_product_price_from_sale_screen') {
                                    can_edit_product_price_from_sale_screen = true;
                                }
                                if (permission.name == 'edit_product_discount_from_pos_screen') {
                                    can_edit_product_discount_from_pos_screen = true;
                                }

                            });


                            hide_tax = (doc.business.enable_inline_tax == 1) ? '' : 'hide';
                            tax_id = product.tax_id;
                            item_tax = (product.item_tax) ? product.item_tax : 0;
                            unit_price_inc_tax = product.sell_price_inc_tax;

                            if (hide_tax == 'hide') {
                                tax_id = null;
                                unit_price_inc_tax = product.default_sell_price;
                            }
                            if ((so_line)) {
                                tax_id = so_line.tax_id;
                                item_tax = so_line.item_tax;
                                unit_price_inc_tax = so_line.unit_price_inc_tax;
                            }

                            discount = null; //a gerer
                            discount_type = (product.line_discount_type) ? product.line_discount_type : 'fixed';
                            discount_amount = (product.line_discount_amount) ? product.line_discount_amount : 0;
                            if (discount) {
                                discount_type = discount.discount_type;
                                discount_amount = discount.discount_amount;
                            }

                            if ((so_line)) {
                                discount_type = so_line.line_discount_type;
                                discount_amount = so_line.line_discount_amount;
                            }

                            sell_line_note = '';
                            if ((product.sell_line_note)) {
                                sell_line_note = product.sell_line_note;
                            }
                            if ((so_line)) {
                                sell_line_note = so_line.sell_line_note;
                            }



                            /**************************************************************************/
                            allow_decimal = true;
                            if (product.unit_allow_decimal != 1) {
                                allow_decimal = false;
                            }
                            /*******************************************************************************/



                            /***************************************************************************/
                            max_quantity = product.qty_available;
                            //$formatted_max_quantity = $product -> formatted_qty_available;
                            formatted_max_quantity = max_quantity;
                            /*
                            if (!empty($action) && $action == 'edit') {
                                if (!empty($so_line)) {
                                    $qty_available = $so_line -> quantity - $so_line -> so_quantity_invoiced + $product -> quantity_ordered;
                                    $max_quantity = $qty_available;
                                    $formatted_max_quantity = number_format($qty_available, session('business.quantity_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
                                }
                            } else {
                                if (!empty($so_line) && $so_line -> qty_available <= $max_quantity) {
                                    $max_quantity = $so_line -> qty_available;
                                    $formatted_max_quantity = $so_line -> formatted_qty_available;
                                }
                            }
                            */
                            max_qty_rule = max_quantity;
                            max_qty_msg = "Seulement " + max_qty_rule + " " + product.unit + " disponible";
                            /*********************************************************************************/




                            data_string +=
                                `<tr class="product_row" data-row_index="` + row_count + `">
                                    <td>
                                        <div title="Modifier le produit Prix unitaire et taxe">
                                            <span class="text-link text-info cursor-pointer" data-toggle="modal"
                                                data-target="#row_edit_product_price_modal_`+ row_count + `">
                                                `+ product.product_actual_name + `<br />` + product.sub_sku + `
                                                &nbsp;<i class="fa fa-info-circle"></i>
                                            </span>
                                        </div>
                                        <input type="hidden" class="enable_sr_no" value="`+ product.enable_sr_no + `">
                                        <input type="hidden" class="product_type" name="products[`+ row_count + `][product_type]" value="` + product.product_type + `">

                                        
                                        <div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_`+ row_count + `" tabindex="-1"
                                            role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">`+ product.product_actual_name + ` - ` + product.sub_sku + `</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="form-group col-xs-12 `+ ((can_edit_product_price_from_sale_screen) ? '' : 'hide') + ` ">
                                                                <label>Prix unitaire</label>
                                                                <input type="text" name="products[`+ row_count + `][unit_price]"
                                                                    class="form-control pos_unit_price input_number mousetrap" value="`+ ((product.unit_price_before_discount) ? product.unit_price_before_discount : product.default_sell_price) + `">
                                                            </div>`
                                +
                                ((can_edit_product_price_from_sale_screen) ? '' : `
                                                            <div class="form-group col-xs-12">
                                                                <strong>Prix unitaire:`+ ((product.unit_price_before_discount) ? product.unit_price_before_discount : product.default_sell_price) + `</strong>
                                                            </div>
                                                            `)
                                +
                                `
                                                            <div class="form-group col-xs-12 col-sm-6 `+ ((can_edit_product_discount_from_pos_screen) ? '' : 'hide') + `">
                                                                <label>Type de remise</label>
                                                                <select class="form-control row_discount_type" name="products[`+ row_count + `][line_discount_type]">
                                                                    <option value="fixed" selected="selected">Fixé</option>
                                                                    <option value="percentage">Pourcentage</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-xs-12 col-sm-6 `+ ((can_edit_product_discount_from_pos_screen) ? '' : 'hide') + `">
                                                                <label>Montant de remise</label>
                                                                <input class="form-control input_number row_discount_amount"
                                                                    name="products[`+ row_count + `][line_discount_amount]" type="text" value="0.0000">
                                                            </div>
                                                            `
                                /*
                                @if(!empty($discount))
<div class="form-group col-xs-12">
<p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
</div>
@endif
                                */
                                +
                                `
                                                            <div class="form-group col-xs-12 `+ hide_tax + ` a_travailler hide">
                                                                <label>Impôt</label>

                                                                <input class="item_tax" name="products[`+ row_count + `][item_tax]" type="hidden" value="0.0000">

                                                                <select class="form-control tax_id" name="products[1][tax_id]">
                                                                    <option selected="selected" value="">Select</option>
                                                                    <option value="" selected="selected">Aucun</option>
                                                                    <option value="2" data-rate="20">FRAIS DE RETRAIT 1$ - 5$</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-xs-12">
                                                                <label>La description</label>
                                                                <textarea class="form-control" name="products[`+ row_count + `][sell_line_note]" rows="3">` + sell_line_note + `</textarea>
                                                                <p class="help-block">Ajouter le produit IMEI, le numéro de série ou d'autres
                                                                    informations ici.</p>
                                                            </div>
                                                            `+
                                `
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Description modal end -->
                                        <div class="modifiers_html">
                                        </div>


                                    </td>

                                    <td>
                                        `+ ((product.transaction_sell_lines_id) ? `<input type="hidden" name="products[` + row_count + `][transaction_sell_lines_id]" class="form-control" value="` + product.transaction_sell_lines_id + `">` : '') + `

                                        <input type="hidden" name="products[`+ row_count + `][product_id]" class="form-control product_id" value="` + product.product_id + `">

                                        <input type="hidden" value="`+ product.variation_id + `" name="products[` + row_count + `][variation_id]" class="row_variation_id">

                                        <input type="hidden" value="1" name="products[`+ row_count + `][enable_stock]">


                                        <div class="input-group input-number">
                                            <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-down"><i
                                                        class="fa fa-minus text-danger"></i></button></span>
                                            <input type="text" data-min="1" class="form-control pos_quantity input_number mousetrap input_quantity"
                                                value="`+ quantity + `" name="products[` + row_count + `][quantity]" data-allow-overselling="` + ((!pos_settings.allow_overselling) ? 'false' : 'true')
                                + `" ` + ((allow_decimal) ? `data-decimal=1` :
                                    `
                                                data-decimal=0
                                                data-rule-abs_digit="true" 
                                                data-msg-abs_digit="Valeur décimale non autorisée" data-rule-required="true"
                                                `) + `
                                                
                                                data-rule-required="true" 
                                                data-msg-required="Ce champ est requis" 
                                                ` + ((!(product.enable_stock && !pos_settings.allow_overselling && !is_sales_order)) ? `` :
                                    `
                                                data-rule-max-value="`+ max_qty_rule + `" data-qty_available="` + product.qty_available + `"
                                                data-msg-max-value="`+ max_qty_msg + `"
                                                data-msg_max_default="`+ max_qty_msg + `"
                                                `) + `
                                                >
                                            <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-up"><i
                                                        class="fa fa-plus text-success"></i></button></span>
                                        </div>

                                        <input type="hidden" name="products[`+ row_count + `][product_unit_id]" value="` + product.unit_id + `">`;

                            if (Object.keys(product.sub_units).length > 0) {

                                data_string += `
                                            <br>
                                            <select name="products[`+ row_count + `][sub_unit_id]" class="form-control input-sm sub_unit">`;

                                Object.values(product.sub_units).forEach(function (sub_unit, key_sub_unit) {
                                    data_string += `<option value="` + key_sub_unit + `" data-multiplier="` + sub_unit.multiplier + `" 
                                                data-unit_name="`+ sub_unit.name + `" 
                                                data-allow_decimal="`+ sub_unit.allow_decimal + `" 
                                                ` + ((product.sub_unit_id && product.sub_unit_id == key_sub_unit) ? `selected` : ``) +
                                        `>
                                                    `+ sub_unit.name + `
                                                </option>`;
                                });

                                data_string += `
                                            </select>
                                            `;
                            } else {
                                data_string += product.unit;
                            }

                            if (product.second_unit) {
                                data_string += `<br>
                                            <span style="white-space: nowrap;">
                                            Quantité en `+ product.second_unit + ` *:</span><br>
                                            <input type="text" 
                                            name="products[`+ row_count + `][secondary_unit_quantity]" 
                                            value="`+ product.secondary_unit_quantity + `"
                                            class="form-control input-sm input_number"
                                            required>
                                            `;
                            }
                            multiplier = 1;
                            data_string += `<input type="hidden" class="base_unit_multiplier" name="products[` + row_count + `][base_unit_multiplier]" value="` + multiplier + `">

                                        <input type="hidden" class="hidden_base_unit_sell_price" value="`+ (product.default_sell_price / multiplier) + `">
                                    </td>`;
                            /************* gestion des produits combo
                            {{-- Hidden fields for combo products --}}
                            @if($product->product_type == 'combo'&& !empty($product->combo_products))

                                @foreach($product->combo_products as $k => $combo_product)

                                    @if(isset($action) && $action == 'edit')
                                        @php
                                            $combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;

                                            $qty_total = $combo_product['quantity'];
                                        @endphp
                                    @else
                                        @php
                                            $qty_total = $combo_product['qty_required'];
                                        @endphp
                                    @endif

                                    <input type="hidden" 
                                        name="products[{{$row_count}}][combo][{{$k}}][product_id]"
                                        value="{{$combo_product['product_id']}}">

                                        <input type="hidden" 
                                        name="products[{{$row_count}}][combo][{{$k}}][variation_id]"
                                        value="{{$combo_product['variation_id']}}">

                                        <input type="hidden"
                                        class="combo_product_qty" 
                                        name="products[{{$row_count}}][combo][{{$k}}][quantity]"
                                        data-unit_quantity="{{$combo_product['qty_required']}}"
                                        value="{{$qty_total}}">

                                        @if(isset($action) && $action == 'edit')
                                            <input type="hidden" 
                                                name="products[{{$row_count}}][combo][{{$k}}][transaction_sell_lines_id]"
                                                value="{{$combo_product['id']}}">
                                        @endif

                                @endforeach
                            @endif
                            **************************************/


                            data_string += `

                                    <td class="`+ hide_tax + `">
                                        <input type="text" name="products[`+ row_count + `][unit_price_inc_tax]"
                                            class="form-control pos_unit_price_inc_tax input_number" value="`+ unit_price_inc_tax + `"
                                            `+ ((!can_edit_product_price_from_sale_screen) ? 'readonly' : ((pos_settings.enable_msp)) ? `data-rule-min-value="` + unit_price_inc_tax + `" data-msg-min-value="Prix minimum de vente est ` + unit_price_inc_tax + `"` : '') + `
                                            >
                                    </td>`;
                            /*
                            @if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
                                <td>
                                    {!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
                                </td>

                            @endif
                            */
                            subtotal_type = ((pos_settings.is_pos_subtotal_editable) ? 'text' : 'hidden');
                            data_string += `
                                    <td class="text-center">
                                        <input type="`+ subtotal_type + `" class="form-control pos_line_total ` + ((pos_settings.is_pos_subtotal_editable) ? 'input_number' : '') + `" value="` + (1 * unit_price_inc_tax) + `">
                                        <span class="display_currency pos_line_total_text `+ ((pos_settings.is_pos_subtotal_editable) ? 'hide' : '') + `" data-currency_symbol="true">` + (1 * unit_price_inc_tax) + `</span>
                                    </td>
                                    <td class="text-center v-center">
                                        <i class="fa fa-times text-danger pos_remove_row cursor-pointer" aria-hidden="true"></i>
                                    </td>
                                </tr>`;
                            resolve({
                                "success": true,
                                "enable_sr_no": 0,
                                "html_content": data_string
                            });
                            return;


                            var product_name = product.product_actual_name + '<br/>' + product.sub_sku;
                            product_name = (!product.brand) ? product_name : product_name + ' ' + product.brand;

                            alert(product_name);

                            var html_content = '';
                            data_string += '<tr class="product_row" data-row_index="' + row_count + '" ' + ((!so_line) ? '' : 'data-so_id="' + so_line.transaction_id + '"') + '>';
                            //alert(`<td>`+((!so_line)?'':'<input type="hidden" name="'+product.so_line_id+'" value="'+so_line.id+'">      "'+so_line.transaction_id+'"'));
                            console.log('text==>', data_string);



                            data_string += `<td>` + ((!so_line) ? '' : '<input type="hidden" name="' + product.so_line_id + '" value="' + so_line.id + '">"' + so_line.transaction_id + '"')
                                + (((edit_price || edit_discount) && !is_direct_sell) ? '<div title="@lang(lang_v1.pos_edit_product_price_help)"><span class="text-link text-info cursor-pointer" data-toggle="modal" data-target="#row_edit_product_price_modal_' + row_count + '">' + product_name + '<i class="fa fa-info-circle"></i></span></div>' : product_name)
                                + '<input type="hidden" class="enable_sr_no" value="' + product.enable_sr_no + '">'
                                + '<input type="hidden" class="product_type" name="products[' + row_count + '][product_type]" value="' + product.product_type + '">';


                            hide_tax = (doc.business.enable_inline_tax == 1) ? '' : 'hide';
                            tax_id = product.tax_id;
                            item_tax = (product.item_tax) ? product.item_tax : 0;
                            unit_price_inc_tax = product.sell_price_inc_tax;

                            if (hide_tax == 'hide') {
                                tax_id = null;
                                unit_price_inc_tax = product.default_sell_price;
                            }
                            if ((so_line)) {
                                tax_id = so_line.tax_id;
                                item_tax = so_line.item_tax;
                                unit_price_inc_tax = so_line.unit_price_inc_tax;
                            }

                            discount = null; //a gerer
                            discount_type = (product.line_discount_type) ? product.line_discount_type : 'fixed';
                            discount_amount = (product.line_discount_amount) ? product.line_discount_amount : 0;
                            if (discount) {
                                discount_type = discount.discount_type;
                                discount_amount = discount.discount_amount;
                            }

                            if ((so_line)) {
                                discount_type = so_line.line_discount_type;
                                discount_amount = so_line.line_discount_amount;
                            }

                            sell_line_note = '';
                            if ((product.sell_line_note)) {
                                sell_line_note = product.sell_line_note;
                            }
                            if ((so_line)) {
                                sell_line_note = so_line.sell_line_note;
                            }


                            if ((discount)) {
                                data_string += '<input type="hidden" name="products[' + row_count + '][discount_id]" id="sell_price_tax" value="' + discount.id + '">';

                            }

                            /*
                            @php
                                $warranty_id = !empty($action) && $action == 'edit' && !empty($product->warranties->first())  ? $product->warranties->first()->id : $product->warranty_id;
                    
                                if($discount_type == 'fixed') {
                                    $discount_amount = $discount_amount * $multiplier;
                                }
                            @endphp

                            @if(empty($is_direct_sell))
                                <div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_{{$row_count}}" tabindex="-1" role="dialog">
                                    @include('sale_pos.partials.row_edit_product_price_modal')
                                </div>
                            @endif
                            <!-- Description modal end -->
                            @if(in_array('modifiers' , $enabled_modules))
                                <div class="modifiers_html">
                                    @if(!empty($product->product_ms))
                                        @include('restaurant.product_modifier_set.modifier_for_product', array('edit_modifiers' => true, 'row_count' => $loop->index, 'product_ms' => $product->product_ms ) )
                                    @endif
                                </div>
                            @endif

                            @php
                                $max_quantity = $product->qty_available;
                                $formatted_max_quantity = $product->formatted_qty_available;
                    
                                if(!empty($action) && $action == 'edit') {
                                    if(!empty($so_line)) {
                                        $qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
                                        $max_quantity = $qty_available;
                                        $formatted_max_quantity = number_format($qty_available, session('business.quantity_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
                                    }
                                } else {
                                    if(!empty($so_line) && $so_line->qty_available <= $max_quantity) {
                                        $max_quantity = $so_line->qty_available;
                                        $formatted_max_quantity = $so_line->formatted_qty_available;
                                    }
                                }
                                
                    
                                $max_qty_rule = $max_quantity;
                                $max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
                            @endphp
                    
                            @if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
                                @php
                                    $lot_enabled = session()->get('business.enable_lot_number');
                                    $exp_enabled = session()->get('business.enable_product_expiry');
                                    $lot_no_line_id = '';
                                    if(!empty($product->lot_no_line_id)){
                                        $lot_no_line_id = $product->lot_no_line_id;
                                    }
                                @endphp
                                @if(!empty($product->lot_numbers) && empty($is_sales_order))
                                <select class="form-control lot_number input-sm" name="products[{{$row_count}}][lot_no_line_id]" @if(!empty($product->transaction_sell_lines_id)) disabled @endif>
                                    <option value="">@lang('lang_v1.lot_n_expiry')</option>
                                    @foreach($product->lot_numbers as $lot_number)
                                        @php
                                            $selected = "";
                                            if($lot_number->purchase_line_id == $lot_no_line_id){
                                                $selected = "selected";
                    
                                                $max_qty_rule = $lot_number->qty_available;
                                                $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
                                            }
                    
                                            $expiry_text = '';
                                            if($exp_enabled == 1 && !empty($lot_number->exp_date)){
                                                if( \Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date)) ){
                                                    $expiry_text = '(' . __('report.expired') . ')';
                                                }
                                            }
                    
                                            //preselected lot number if product searched by lot number
                                            if(!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
                                                $selected = "selected";
                    
                                                $max_qty_rule = $lot_number->qty_available;
                                                $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
                                            }
                                        @endphp
                                        <option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])" {{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}} @endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 && !empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif {{$expiry_text}}</option>
                                    @endforeach
                                </select>
                            @endif
                        @endif
                            */



                            data_string += (is_direct_sell) ? '<br><textarea class="form-control" name="products[' + row_count + '][sell_line_note]" rows="2">' + sell_line_note + '</textarea><p class="help-block"><small>@lang(lang_v1.sell_line_description_help)</small></p></td>' : '';

                            data_string += '<td>' + (product.transaction_sell_lines_id) ? '<input type="hidden" name="products[' + row_count + '][transaction_sell_lines_id]" class="form-control" value="' + product.transaction_sell_lines_id + '">' : '';

                            data_string += '<input type="hidden" name="products[' + row_count + '][product_id]" class="form-control product_id" value="' + product.product_id + '">';

                            data_string += '<input type="hidden" value="' + product.variation_id + '" name="products[' + row_count + '][variation_id]" class="row_variation_id">';
                            data_string += '<input type="hidden" value="' + product.enable_stock + '" name="products[' + row_count + '][enable_stock]">';

                            product.quantity_ordered = (!product.quantity_ordered) ? product.quantity_ordered = 1 : '';
                            allow_decimal = (product.unit_allow_decimal != 1) ? false : true;
                            //alert(data_string);

                            Object.values(product.sub_units).forEach(function (sub_unit, key_sub_unit) {
                                //alert(product.product_name);
                                //console.log('sub_unit',sub_unit);
                                if ((product.sub_unit_id) && product.sub_unit_id == key_sub_unit) {
                                    max_qty_rule = max_qty_rule / sub_unit.multiplier;
                                    unit_name = sub_unit.name;
                                    //max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
                                    max_qty_msg = "message de stock insuff";

                                    if (product.lot_no_line_id) {
                                        max_qty_msg = "message de stock insuff2";
                                    }
                                    if (sub_unit.allow_decimal) {
                                        allow_decimal = true;
                                    }
                                }


                            });

                            data_string += `
                        <div class="input-group input-number">
                            <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-down"><i class="fa fa-minus text-danger"></i></button></span>
                        <input type="text" data-min="1" class="form-control pos_quantity input_number mousetrap input_quantity" 
                            value="`+ format_quantity(quantity) + `" name="products[` + row_count + `][quantity]" data-allow-overselling="` + (!pos_settings.allow_overselling) ? 'false' : 'true' + `"`
                                + (allow_decimal) ? 'data-decimal=1 ' : 'data-decimal=0 data-rule-abs_digit="true" data-msg-abs_digit="@lang(lang_v1.decimal_value_not_allowed)" ' + ` 
                            
                            data-rule-required="true" 
                            data-msg-required="@lang('validation.custom-messages.this_field_is_required')" `+
                                    (product.enable_stock && pos_settings.allow_overselling && is_sales_order == false) ? 'data-rule-max-value="' + max_qty_rule + '" data-qty_available="' + product.qty_available + '" data-msg-max-value="' + max_qty_msg + '" data-msg_max_default="@lang(\'validation.custom-messages.quantity_not_available\', [\'qty\'=> $product->formatted_qty_available, \'unit\' => $product->unit  ])" >' : ''
                            + `
                        <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-up"><i class="fa fa-plus text-success"></i></button></span>
                        </div>
                        
                        <input type="hidden" name="products[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">
                        @if(count($sub_units) > 0)
                            <br>
                            <select name="products[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit">
                                @foreach($sub_units as $key => $value)
                                    <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
                                        {{$value['name']}}
                                    </option>
                                @endforeach
                           </select>
                        @else
                            {{$product->unit}}
                        @endif
                
                        @if(!empty($product->second_unit))
                            <br>
                            <span style="white-space: nowrap;">
                            @lang('lang_v1.quantity_in_second_unit', ['unit' => $product->second_unit])*:</span><br>
                            <input type="text" 
                            name="products[{{$row_count}}][secondary_unit_quantity]" 
                            value="{{@format_quantity($product->secondary_unit_quantity)}}"
                            class="form-control input-sm input_number"
                            required>
                        @endif
                
                        <input type="hidden" class="base_unit_multiplier" name="products[{{$row_count}}][base_unit_multiplier]" value="{{$multiplier}}">
                
                        <input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}">
                        
                        {{-- Hidden fields for combo products --}}
                        @if($product->product_type == 'combo'&& !empty($product->combo_products))
                
                            @foreach($product->combo_products as $k => $combo_product)
                
                                @if(isset($action) && $action == 'edit')
                                    @php
                                        $combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;
                
                                        $qty_total = $combo_product['quantity'];
                                    @endphp
                                @else
                                    @php
                                        $qty_total = $combo_product['qty_required'];
                                    @endphp
                                @endif
                
                                <input type="hidden" 
                                    name="products[{{$row_count}}][combo][{{$k}}][product_id]"
                                    value="{{$combo_product['product_id']}}">
                
                                    <input type="hidden" 
                                    name="products[{{$row_count}}][combo][{{$k}}][variation_id]"
                                    value="{{$combo_product['variation_id']}}">
                
                                    <input type="hidden"
                                    class="combo_product_qty" 
                                    name="products[{{$row_count}}][combo][{{$k}}][quantity]"
                                    data-unit_quantity="{{$combo_product['qty_required']}}"
                                    value="{{$qty_total}}">
                
                                    @if(isset($action) && $action == 'edit')
                                        <input type="hidden" 
                                            name="products[{{$row_count}}][combo][{{$k}}][transaction_sell_lines_id]"
                                            value="{{$combo_product['id']}}">
                                    @endif
                
                            @endforeach
                        @endif
                    </td>
                    @if(!empty($is_direct_sell))
                        @if(!empty($pos_settings['inline_service_staff']))
                            <td>
                                <div class="form-group">
                                    <div class="input-group">
                                        {!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
                                    </div>
                                </div>
                            </td>
                        @endif
                        @php
                            $pos_unit_price = !empty($product->unit_price_before_discount) ? $product->unit_price_before_discount : $product->default_sell_price;
                
                            if(!empty($so_line) && $action !== 'edit') {
                                $pos_unit_price = $so_line->unit_price_before_discount;
                            }
                        @endphp
                        <td class="@if(!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif">
                            <input type="text" name="products[{{$row_count}}][unit_price]" class="form-control pos_unit_price input_number mousetrap" value="{{@num_format($pos_unit_price)}}" @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$pos_unit_price}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($pos_unit_price)])}}" @endif> 
                
                            @if(!empty($last_sell_line))
                                <br>
                                <small class="text-muted">@lang('lang_v1.prev_unit_price'): @format_currency($last_sell_line->unit_price_before_discount)</small>
                            @endif
                        </td>
                        <td @if(!$edit_discount) class="hide" @endif>
                            {!! Form::text("products[$row_count][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount']); !!}<br>
                            {!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $discount_type , ['class' => 'form-control row_discount_type']); !!}
                            @if(!empty($discount))
                                <p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
                            @endif
                
                            @if(!empty($last_sell_line))
                                <br>
                                <small class="text-muted">
                                    @lang('lang_v1.prev_discount'): 
                                    @if($last_sell_line->line_discount_type == 'percentage')
                                        {{@num_format($last_sell_line->line_discount_amount)}}%
                                    @else
                                        @format_currency($last_sell_line->line_discount_amount)
                                    @endif
                                </small>
                            @endif
                        </td>
                        <td class="text-center {{$hide_tax}}">
                            {!! Form::hidden("products[$row_count][item_tax]", @num_format($item_tax), ['class' => 'item_tax']); !!}
                        
                            {!! Form::select("products[$row_count][tax_id]", $tax_dropdown['tax_rates'], $tax_id, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], $tax_dropdown['attributes']); !!}
                        </td>
                
                    @else
                        @if(!empty($pos_settings['inline_service_staff']))
                            <td>
                                <div class="form-group">
                                    <div class="input-group">
                                        {!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
                                    </div>
                                </div>
                            </td>
                        @endif
                    @endif
                    <td class="{{$hide_tax}}">
                        <input type="text" name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="{{@num_format($unit_price_inc_tax)}}" @if(!$edit_price) readonly @endif @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$unit_price_inc_tax}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)])}}" @endif>
                    </td>
                    @if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
                        <td>
                            {!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
                        </td>
                
                    @endif
                    <td class="text-center">
                        @php
                            $subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';
                
                        @endphp
                        <input type="{{$subtotal_type}}" class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="{{@num_format($product->quantity_ordered*$unit_price_inc_tax )}}">
                        <span class="display_currency pos_line_total_text @if(!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif" data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span>
                    </td>
                    <td class="text-center v-center">
                        <i class="fa fa-times text-danger pos_remove_row cursor-pointer" aria-hidden="true"></i>
                    </td>
                </tr>`;

                            var html_content = "<tr class=\"product_row\" data-row_index=\"1\" ><td><div title=\"Modifier le produit Prix unitaire et taxe\"><span class=\"text-link text-info cursor-pointer\" data-toggle=\"modal\" data-target=\"#row_edit_product_price_modal_1\">" + product.name + "<br\/>" + product.sub_sku + "<i class=\"fa fa-info-circle\"><\/i><\/span><\/div><input type=\"hidden\" class=\"enable_sr_no\" value=\"0\"><input type=\"hidden\" class=\"product_type\" name=\"products[1][product_type]\" value=\"single\"><div class=\"modal fade row_edit_product_price_model\" id=\"row_edit_product_price_modal_1\" tabindex=\"-1\" role=\"dialog\"><div class=\"modal-dialog\" role=\"document\"><div class=\"modal-content\"><div class=\"modal-header\"><button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;<\/span><\/button><h4 class=\"modal-title\" id=\"myModalLabel\">" + product.name + " - " + product.sub_sku + "<\/h4><\/div><div class=\"modal-body\"><div class=\"row\"><div class=\"form-group col-xs-12 \"><label>Prix unitaire<\/label><input type=\"text\" name=\"products[1][unit_price]\" class=\"form-control pos_unit_price input_number mousetrap\" value=\"0.0000\" ><\/div><div class=\"form-group col-xs-12 col-sm-6 \"><label>Type de remise<\/label><select class=\"form-control row_discount_type\" name=\"products[1][line_discount_type]\"><option value=\"fixed\" selected=\"selected\">Fix\u00e9<\/option><option value=\"percentage\">Pourcentage<\/option><\/select><\/div><div class=\"form-group col-xs-12 col-sm-6 \"><label>Montant de remise<\/label><input class=\"form-control input_number row_discount_amount\" name=\"products[1][line_discount_amount]\" type=\"text\" value=\"0.0000\"><\/div><div class=\"form-group col-xs-12 hide\"><label>Imp\u00f4t<\/label><input class=\"item_tax\" name=\"products[1][item_tax]\" type=\"hidden\" value=\"0.0000\"><select class=\"form-control tax_id\" name=\"products[1][tax_id]\"><option selected=\"selected\" value=\"\">Select<\/option><option value=\"\" selected=\"selected\">Aucun<\/option><option value=\"2\" data-rate=\"20\">FRAIS DE RETRAIT 1$ - 5$<\/option><\/select><\/div><div class=\"form-group col-xs-12\">      <label>La description<\/label>      <textarea class=\"form-control\" name=\"products[1][sell_line_note]\" rows=\"3\"><\/textarea>      <p class=\"help-block\">Ajouter le produit IMEI, le num\u00e9ro de s\u00e9rie ou d'autres informations ici.<\/p>      <\/div><\/div><\/div><div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fermer<\/button><\/div><\/div><\/div><\/div><!-- Description modal end --><div class=\"modifiers_html\"><\/div><\/td><td><input type=\"hidden\" name=\"products[1][product_id]\" class=\"form-control product_id\" value=\"3454\"><input type=\"hidden\" value=\"3516\" name=\"products[1][variation_id]\" class=\"row_variation_id\"><input type=\"hidden\" value=\"0\" name=\"products[1][enable_stock]\">                <div class=\"input-group input-number\"><span class=\"input-group-btn\"><button type=\"button\" class=\"btn btn-default btn-flat quantity-down\"><i class=\"fa fa-minus text-danger\"><\/i><\/button><\/span><input type=\"text\" data-min=\"1\" class=\"form-control pos_quantity input_number mousetrap input_quantity\" value=\"1.0000\" name=\"products[1][quantity]\" data-allow-overselling=\"false\"  data-decimal=0 data-rule-abs_digit=\"true\" data-msg-abs_digit=\"Valeur d\u00e9cimale non autoris\u00e9e\" data-rule-required=\"true\" data-msg-required=\"Ce champ est requis\"  ><span class=\"input-group-btn\"><button type=\"button\" class=\"btn btn-default btn-flat quantity-up\"><i class=\"fa fa-plus text-success\"><\/i><\/button><\/span><\/div><input type=\"hidden\" name=\"products[1][product_unit_id]\" value=\"1\"><br><select name=\"products[1][sub_unit_id]\" class=\"form-control input-sm sub_unit\">                                    <option value=\"1\" data-multiplier=\"1\" data-unit_name=\"Pieces\" data-allow_decimal=\"0\" >                        Pieces                    <\/option>                           <\/select><input type=\"hidden\" class=\"base_unit_multiplier\" name=\"products[1][base_unit_multiplier]\" value=\"1\"><input type=\"hidden\" class=\"hidden_base_unit_sell_price\" value=\"0\"><\/td><td class=\"hide\"><input type=\"text\" name=\"products[1][unit_price_inc_tax]\" class=\"form-control pos_unit_price_inc_tax input_number\" value=\"0.0000\"  ><\/td><td class=\"text-center\"><input type=\"hidden\" class=\"form-control pos_line_total \" value=\"0.0000\"><span class=\"display_currency pos_line_total_text \" data-currency_symbol=\"true\">0<\/span><\/td><td class=\"text-center v-center\"><i class=\"fa fa-times text-danger pos_remove_row cursor-pointer\" aria-hidden=\"true\"><\/i><\/td><\/tr>"

                            html_content = data_string;
                            resolve({
                                "success": true,
                                "enable_sr_no": 0,
                                "html_content": html_content
                            });
                            return;
                        }
                    });
                }
            });
            // console.log(formattedList);

        }).catch(function (err) {
            console.log(err);
        });
    });
}
/****************************************************** */


/***************FOR RECEIPT  ************************************/
function get_html_content(receipt_details) {
    return window["get_html_content_" + receipt_details.design](receipt_details);
}

function get_html_content_slim2(receipt_details) {
    
    var html = ``;
    html += `<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <!-- <link rel="stylesheet" href="style.css"> -->
            <title>Receipt-${receipt_details.invoice_no}</title>
        </head>`;

    html +=
        `<body style="border: 1px solid #ccc;padding:0px -10px 0 -10px; !important">
        <div class="ticket" >`;

        if(!receipt_details.letter_head)
        {
            html+=`${receipt_details.logo ?  '<div class="text-box centered"><!-- <img style="max-height: 100px; width: auto;" src="${receipt_details.logo}" alt="Logo"> --></div>' : '' }
            <div class="text-box">
                <p class="centered">
                    <!-- Header text -->
                    ${receipt_details.header_text ? '<span class="headings">' + receipt_details.header_text + '</span><br/>' : ''}

                    <!-- business information here -->
                    ${receipt_details.display_name ? '<span class="headings">' + receipt_details.display_name + '</span><br/>' : ''}

                    ${receipt_details.address ? receipt_details.address + '<br/>' : ''}

                    ${receipt_details.contact ? '<br/>' + receipt_details.contact : ''}

                    ${(receipt_details.contact && receipt_details.website) ? ', ' : ''}


                    ${receipt_details.website ? receipt_details.website : ''}
                    
                    ${receipt_details.location_custom_fields ? '<br/>' + receipt_details.location_custom_fields : ''}

                    ${receipt_details.sub_heading_line1 ?  receipt_details.sub_heading_line1 : ''}
                    ${receipt_details.sub_heading_line2 ? receipt_details.sub_heading_line2+'<br/>' : ''}
                    ${receipt_details.sub_heading_line3 ?  receipt_details.sub_heading_line3 +'<br/>' : ''}
                    ${receipt_details.sub_heading_line4 ? receipt_details.sub_heading_line4 +'<br/>' : ''}
                    ${receipt_details.sub_heading_line5 ? "<br>"+receipt_details.sub_heading_line5 +'<br/>' : ''}
                    ${receipt_details.tax_info1 ? '<br><b>'+receipt_details.tax_label1 +'</b> '+receipt_details.tax_info1 : ''}
                    ${receipt_details.tax_info2 ? '<br><b>'+receipt_details.tax_label2 +'</b> '+receipt_details.tax_info2 : ''}
                </p>
            </div>`;
    }
    else{
        html +=
        `<div class="text-box">
            <!-- <img style="width: 100%;margin-bottom: 10px;" src="${receipt_details.letter_head}"> --></img>
        </div>`;
    }


    html +=
        `<div class="border-top textbox-info">
            <p class="f-left"><strong>${receipt_details.invoice_no_prefix}</strong></p>
            <p class="f-right">
                ${receipt_details.invoice_no}
            </p>
        </div>
        <div class="textbox-info">
            <p class="f-left"><strong>${receipt_details.date_label}</strong></p>
            <p class="f-right">
                ${receipt_details.invoice_date}
            </p>
        </div>`;

    
    html +=
        `
        ${receipt_details.due_date_label? '<div class="textbox-info"><p class="f-left"><strong>'+
        receipt_details.due_date_label+'</strong></p><p class="f-right">'+(receipt_details.due_date ?? '')+'</p></div>':''}

        
        ${receipt_details.sales_person_label? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.sales_person_label+'</strong></p><p class="f-right">'+(receipt_details.sales_person)+'</p></div>':''}


        ${receipt_details.commission_agent_label? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.commission_agent_label+'</strong></p><p class="f-right">'+receipt_details.commission_agent+'</p></div>':''}

        
        ${receipt_details.brand_label || receipt_details.repair_brand ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.brand_label+'</strong></p><p class="f-right">'+receipt_details.repair_brand+'</p></div>':''}

        

        ${receipt_details.device_label || receipt_details.repair_device ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.device_label+'</strong></p><p class="f-right">'+ receipt_details.repair_device +'</p></div>':''}


        

        ${receipt_details.model_no_label || receipt_details.repair_model_no? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.model_no_label+'</strong></p><p class="f-right">'+receipt_details.repair_model_no +'</p></div>':''}

            

        ${receipt_details.serial_no_label || receipt_details.repair_serial_no? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.serial_no_label+'</strong></p><p class="f-right">'+receipt_details.repair_serial_no +'</p></div>':''}



        ${receipt_details.repair_status_label || receipt_details.repair_status? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.repair_status_label+'</strong></p><p class="f-right">'+receipt_details.repair_status +'</p></div>':''}

        

        ${receipt_details.repair_warranty_label || receipt_details.repair_warranty? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.repair_warranty_label+'</strong></p><p class="f-right">'+receipt_details.repair_warranty +'</p></div>':''}


        <!-- Waiter info -->
        ${receipt_details.service_staff_label || receipt_details.service_staff? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.service_staff_label+'</strong></p><p class="f-right">'+receipt_details.service_staff+'</p></div>':''}

        
        ${receipt_details.table_label || receipt_details.table? '<div class="textbox-info"><p class="f-left"><strong>'+
            (receipt_details.table_label ? receipt_details.table_label : '') +'</strong></p><p class="f-right">'+receipt_details.table +'</p></div>':''}


        
        ${receipt_details.sell_custom_field_1_value ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.sell_custom_field_1_label+'</strong></p><p class="f-right">'+ receipt_details.sell_custom_field_1_value +'</p></div>':''}
        ${receipt_details.sell_custom_field_2_value ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.sell_custom_field_2_label+'</strong></p><p class="f-right">'+ receipt_details.sell_custom_field_2_value +'</p></div>':''}
        ${receipt_details.sell_custom_field_3_value ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.sell_custom_field_3_label+'</strong></p><p class="f-right">'+ receipt_details.sell_custom_field_3_value +'</p></div>':''}
        ${receipt_details.sell_custom_field_4_value ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.sell_custom_field_4_label+'</strong></p><p class="f-right">'+ receipt_details.sell_custom_field_4_value +'</p></div>':''}


        <!-- customer info -->
        <div class="textbox-info">
            <p style="vertical-align: top;"><strong>
                    ${receipt_details.customer_label ?? ''}
                </strong></p>

            <p>
            ${receipt_details.customer_info ? '<div class="bw">'+receipt_details.customer_info+'</div>':''}
            </p>
        </div>
        
        ${receipt_details.client_id_label ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.client_id_label+'</strong></p><p class="f-right">'+ receipt_details.client_id +'</p></div>':''}

        ${receipt_details.customer_tax_label ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.customer_tax_label+'</strong></p><p class="f-right">'+ receipt_details.customer_tax_number +'</p></div>':''}

        
        ${receipt_details.customer_custom_fields ? '<div class="textbox-info"><p class="centered">'+ receipt_details.customer_custom_fields +'</p></div>':''}

        ${receipt_details.customer_rp_label ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.customer_rp_label+'</strong></p><p class="f-right">'+ receipt_details.customer_total_rp +'</p></div>':''}


        ${receipt_details.shipping_custom_field_1_label ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.shipping_custom_field_1_label+'</strong></p><p class="f-right">'+ (receipt_details.shipping_custom_field_1_value ?? '') +'</p></div>':''}
        ${receipt_details.shipping_custom_field_2_label ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.shipping_custom_field_2_label+'</strong></p><p class="f-right">'+ (receipt_details.shipping_custom_field_2_value ?? '') +'</p></div>':''}
        ${receipt_details.shipping_custom_field_3_label ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.shipping_custom_field_3_label+'</strong></p><p class="f-right">'+ (receipt_details.shipping_custom_field_3_value ?? '') +'</p></div>':''}
        ${receipt_details.shipping_custom_field_1_4abel ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.shipping_custom_field_5_label+'</strong></p><p class="f-right">'+ (receipt_details.shipping_custom_field_4_value ?? '') +'</p></div>':''}
        ${receipt_details.shipping_custom_field_5_label ? '<div class="textbox-info"><p class="f-left"><strong>'+
            receipt_details.shipping_custom_field_5_label+'</strong></p><p class="f-right">'+ (receipt_details.shipping_custom_field_5_value ?? '') +'</p></div>':''}
                            
        

        ${receipt_details.sale_orders_invoice_no ? '<div class="textbox-info"><p class="f-left"><strong>'+__('restaurant.order_no')+'</strong></p><p class="f-right">'+ (receipt_details.sale_orders_invoice_no ?? '') +'</p></div>':''}
        ${receipt_details.sale_orders_invoice_date ? '<div class="textbox-info"><p class="f-left"><strong>'+__('lang_v1.order_dates')+'</strong></p><p class="f-right">'+ (receipt_details.sale_orders_invoice_date ?? '') +'</p></div>':''}
        `;
    
    html += `<div class="bb-lg mt-15 mb-10"></div>
                <table style="padding-top: 5px !important" class="border-bottom width-100 table-f-12 mb-10">
                    <tbody>`;

    Object.values(receipt_details.lines).forEach(function (line, index) {
        //alert(receipt_details.hide_price);
        html+=`<tr class="bb-lg">
                        <td class="description">
                            <div style="display:flex; width: 100%;">
                                <p class="m-0 mt-5" style="white-space: nowrap;">#${index+1}.&nbsp;</p>
                                <p class="text-left m-0 mt-5 pull-left">${ line.name }
                                ${line.sub_sku ? ', ' + line.sub_sku : ''} 
                                ${line.brand ? ', ' + line.brand : ''} 
                                ${line.cat_code ? ', ' + line.cat_code : ''} 
                                ${line.product_custom_fields ? ', ' + line.product_custom_fields : ''} 

                                ${line.product_description ? '<br><span class="f-8">' + line.product_description + '</span>' : ''} 
                                ${line.sell_line_note ? '<br><span class="f-8">' + line.sell_line_note + '</span>' : ''} 

                                    
                                ${line.lot_number ? '<br> ' + line.lot_number_label + ': ' + line.lot_number : ''} 
                                ${line.product_expiry ? ', ' + line.product_expiry_label + ': ' + line.product_expiry : ''} 

                                ${line.variation ? ', ' + line.product_variation + ' ' + line.variation : ''} 

                                ${line.warranty_name ? ',<small>' + line.warranty_name + '</small>' : ''} 

                                ${line.warranty_exp_date ? '<small>- ' + line.warranty_exp_date + '</small>' : ''} 

                                ${line.warranty_description ? '<small> ' + (line.warranty_description ?? '') + '</small>' : ''} 

                                ${line.show_base_unit_details && line.quantity && line.base_unit_multiplier !== 1 ?
                                    '<br><small> 1 ' + line.units + " = " + line.base_unit_multiplier + " " + line.base_unit_name + " <br>" >
                                    +line.base_unit_price + " x " + line.orig_quantity + " = " + line.line_total + '____________</small>' : ''}
                                </p>
                            </div>
                            <div style="display:flex; width: 100%;">
                                <p class="text-left width-60 quantity m-0 bw" style="direction: ltr;">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    
                                    ${line.quantity} 
                                    ${!receipt_details.hide_price ? 
                                        'x '+ line.unit_price_exc_tax
                                        + (receipt_details.total_line_discount && receipt_details.total_line_discount !=0) ? 
                                        ' x '+ line.total_line_discount+ output.currency_symbol :'' : ''}
                                    
                                </p>
                                ${line.hide_price ? '<p class="text-right width-40 price m-0 bw">' + line.line_total  + '</p>' : ''} 
                            </div>
                        </td>
                    </tr>`;

                    if (line.modifiers)
                    {
                        Object.values(line.modifiers).forEach(function (modifier, modifier_index) {
                            html+=`<tr>
                                <td>
                                    <div style="display:flex;">
                                        <p style="width: 28px;" class="m-0">
                                        </p>
                                        <p class="text-left width-60 m-0" style="margin:0;">
                                        ${modifier.name} 
                                        ${modifier.sub_sku ? ', ' + modifier.sub_sku  : ''} 
                                        ${modifier.cat_code ? ', ' + modifier.cat_code  : ''} 
                                        ${modifier.sell_line_note ? ', (' + modifier.sell_line_note +')' : ''} 
                                            
                                        </p>
                                        <p class="text-right width-40 m-0">
                                            ${modifier.variation}
                                        </p>
                                    </div>
                                    <div style="display:flex;">
                                        <p style="width: 28px;"></p>
                                        <p class="text-left width-50 quantity">
                                            ${modifier.quantity}

                                            ${!receipt_details.hide_price ? 'x ' + modifier.unit_price_inc_tax  : ''}
                                            
                                        </p>
                                        <p class="text-right width-50 price">
                                            ${modifier.line_total}
                                        </p>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    }
    });
    
    html += `</tbody>
    </table>`;

    console.log('receipt_details',receipt_details);

    html += `
    ${receipt_details.total_quantity_label ? '<div class="flex-box"><p class="left text-left">' + receipt_details.total_quantity_label + 
        '</p><p class="width-50 text-right">'+ receipt_details.total_quantity +'</p></div>'  : ''} 
        
    ${receipt_details.total_items_label ? '<div class="flex-box"><p class="left text-left">' + receipt_details.total_items_label + 
    '</p><p class="width-50 text-right">'+ receipt_details.total_items +'</p></div>'  : ''}`;
    
    if(!receipt_details.hide_price){
        html+=`
            <div class="flex-box">
                <p class="left text-left">
                    <strong>${receipt_details.subtotal_label}</strong>
                </p>
                <p class="width-50 text-right">
                    <strong>${receipt_details.subtotal}</strong>
                </p>
            </div>

            <!-- Shipping Charges -->
            ${receipt_details.shipping_charges ? '<div class="flex-box"><p class="left text-left">' + receipt_details.shipping_charges_label + 
            '</p><p class="width-50 text-right">'+ receipt_details.shipping_charges +'</p></div>'  : ''} 
            ${receipt_details.packing_charge ? '<div class="flex-box"><p class="left text-left">' + receipt_details.packing_charge_label + 
            '</p><p class="width-50 text-right">'+ receipt_details.packing_charge +'</p></div>'  : ''} 

            <!-- Discount : pour le moment on enleve les remise -->
            ${receipt_details.discount ? '<div class="flex-box" style="display:none"><p class="left text-left">' + receipt_details.discount_label + 
            '</p><p class="width-50 text-right">(-) '+ receipt_details.discount +'</p></div>'  : ''} 
            ${receipt_details.total_line_discount ? '<div class="flex-box"  style="display:none"><p class="left text-left">' + receipt_details.line_discount_label + 
            '</p><p class="width-50 text-right">(-) '+ receipt_details.total_line_discount +'_______</p></div>'  : ''} 
            `;

            if(receipt_details.additional_expenses){
                Object.values(receipt_details.additional_expenses).forEach(function (val, key) {
                    html+=`<tr>
                                <td>
                                    ${key}:
                                </td>

                                <td class="text-right">
                                    (+) ${val}
                                </td>
                            </tr>`;});
            }

            html+=`
            ${receipt_details.reward_point_label ? '<div class="flex-box"><p class="left text-left">' + receipt_details.reward_point_label + 
            '</p><p class="width-50 text-right">(-) '+ receipt_details.reward_point_label +'</p></div>'  : ''} 
            ${receipt_details.tax ? '<div class="flex-box"><p class="left text-left">' + receipt_details.tax_label + 
            '</p><p class="width-50 text-right">(+) '+ __currency_trans_from_en(receipt_details.tax) +'</p></div>'  : ''} 
            

            ${(receipt_details.round_off_amount>0) ? '<div class="flex-box"><p class="left text-left">' + receipt_details.round_off_label + 
            '</p><p class="width-50 text-right">'+ __currency_trans_from_en(receipt_details.round_off) +'</p></div>'  : ''} 

            <div class="flex-box">
                <p class="width-50 text-left">
                    <strong>${receipt_details.total_label}</strong>
                </p>
                <p class="width-50 text-right">
                    <strong>${__currency_trans_from_en(receipt_details.total)}</strong>
                </p>
            </div>

            ${receipt_details.total_in_words ? '<p colspan="2" class="text-right mb-0"><small>('+ receipt_details.total_in_words +')</small></p>'  : ''} 
            `;

            if(receipt_details.payments)
            {
                Object.values(receipt_details.payments).forEach(function (payment, key) {
                    html+=`<div class="flex-box">
                            <p class="width-50 text-left">${payment.method} <br>(${payment.date}) </p>
                            <p class="width-50 text-right">${__currency_trans_from_en(payment.amount)}</p>
                        </div>`;
                });
            }
            html+=`
            ___________________
            <!-- Total Paid-->
            ${receipt_details.total_paid ? '<div class="flex-box"><p class="left text-left">' + receipt_details.total_paid_label + 
            '</p><p class="width-50 text-right">'+ __currency_trans_from_en(receipt_details.total_paid) +'</p></div>'  : ''} 


				<!-- Total Due-->
                ${receipt_details.total_due && receipt_details.total_due_label ? '<div class="flex-box"><p class="left text-left">' + receipt_details.total_due_label + 
            '</p><p class="width-50 text-right">'+ __currency_trans_from_en(receipt_details.total_due) +'</p></div>'  : ''} 
                
                ${receipt_details.all_due ? '<div class="flex-box"><p class="left text-left">' + receipt_details.all_bal_label + 
            '</p><p class="width-50 text-right">'+ __currency_trans_from_en(receipt_details.all_due) +'</p></div>'  : ''}`;

    }

    html+=`<div class="border-bottom width-100">&nbsp;</div>`;
    /* on hide le taxe poour le moment */
    if(!receipt_details.hide_price && receipt_details.tax_summary_label){
        if((receipt_details.taxes)){
            html+=`
            <!-- tax -->
            <table class="border-bottom width-100 table-f-12">
                <tr>
                    <th colspan="2" class="text-center">${receipt_details.tax_summary_label}</th>
                </tr>`;
                Object.values(receipt_details.taxes).forEach(function (value, key) {
                    html += `<tr><td class="left">${key}</td><td class="right">${value}</td></tr>`;
                });
            html+=`</table>`
        }
    }

    html+=
    `   ${receipt_details.additional_notes ? '<p class="centered" >' + receipt_details.additional_notes + '</p>'  : ''} 

        <!-- Barcode -->
        ${receipt_details.show_barcode ? '<p class="<br/><!--  <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, \'C128\', 2,30,array(39, 48, 54), true)}}"> -->'  : ''} 

        ${receipt_details.show_barcode ? '<!--  <img class="center-block mt-5" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, \'QRCODE\')}}"> -->'  : ''} 

        ${receipt_details.footer_text ? '<p class="centered">' + receipt_details.footer_text + 
            '</p>'  : ''}`;


    html += `</div>
        </body></html>`;



    html += `<style type="text/css">.f-8 {	font-size: 8px !important;}body {	color: #000000;}@media print {	* {    	font-size: 12px;    	font-family: 'Times New Roman';    	word-break: break-all;	}	.f-8 {		font-size: 8px !important;	}.headings{	font-size: 16px;	font-weight: 700;	text-transform: uppercase;}.sub-headings{	font-size: 15px;	font-weight: 700;}.border-top{    border-top: 1px solid #242424;}.border-bottom{	border-bottom: 1px solid #242424;}.border-bottom-dotted{	border-bottom: 1px dotted darkgray;}td.serial_number, th.serial_number{	width: 5%;    max-width: 5%;}td.description,th.description {    width: 35%;    max-width: 35%;}td.quantity,th.quantity {    width: 15%;    max-width: 15%;    word-break: break-all;}td.unit_price, th.unit_price{	width: 25%;    max-width: 25%;    word-break: break-all;}td.price,th.price {    width: 20%;    max-width: 20%;    word-break: break-all;}.centered {    text-align: center;    align-content: center;}.ticket {    width: 100%;    max-width: 100%;}img {    max-width: inherit;    width: auto;}    .hidden-print,    .hidden-print * {        display: none !important;    }}.table-info {	width: 100%;}.table-info tr:first-child td, .table-info tr:first-child th {	padding-top: 8px;}.table-info th {	text-align: left;}.table-info td {	text-align: right;}.logo {	float: left;	width:35%;	padding: 10px;}.text-with-image {	float: left;	width:65%;}.text-box {	width: 100%;	height: auto;}.m-0 {	margin:0;}.textbox-info {	clear: both;}.textbox-info p {	margin-bottom: 0px}.flex-box {	display: flex;	width: 100%;}.flex-box p {	width: 50%;	margin-bottom: 0px;	white-space: nowrap;}.table-f-12 th, .table-f-12 td {	font-size: 12px;	word-break: break-word;}.bw {	word-break: break-word;}.bb-lg {	border-bottom: 1px solid lightgray;}.text-right,.f-right{    margin-right: 25px !important;}</style>`;

    return html;
}

function get_html_content_classic(receipt_details) {
    var html = '';

    html += `<!-- business information here -->
                <div class="row" style="color: #000000 !important;">`;

    if (!receipt_details.letter_head) {
        html += `<!-- Logo -->
            ${(receipt_details.logo) ? '<img style="max-height: 120px; width: auto;" src="' + receipt_details.logo + '" class="img img-responsive center-block">' : ''}
            
            ${(receipt_details.header_text) ? '<!-- Header text --><div class="col-xs-12">' + receipt_details.header_text + '</div>' : ''}

                <!-- business information here -->
                <div class="col-xs-12 text-center">
                    <h2 class="text-center">
                        <!-- Shop & Location Name  -->
                        ${(receipt_details.display_name) ? receipt_details.display_name : ''}
                    </h2>

                    <!-- Address -->
                    <p>
                    ${(receipt_details.address) ? '<small class="text-center">' + receipt_details.address + '</small>' : ''}
                    
                    ${(receipt_details.contact) ? '<br/>' + receipt_details.contact : ''}

                    ${(receipt_details.contact && receipt_details.website) ? ',' : ''}

                    ${(receipt_details.website) ? receipt_details.website : ''}

                    ${(receipt_details.location_custom_fields) ? '<br>' + receipt_details.location_custom_fields : ''}
                    </p>
                    <p>
                    ${(receipt_details.sub_heading_line1) ? receipt_details.sub_heading_line1 : ''}
                    ${(receipt_details.sub_heading_line2) ? '<br>' + receipt_details.sub_heading_line2 : ''}
                    ${(receipt_details.sub_heading_line3) ? '<br>' + receipt_details.sub_heading_line3 : ''}
                    ${(receipt_details.sub_heading_line4) ? '<br>' + receipt_details.sub_heading_line4 : ''}
                    ${(receipt_details.sub_heading_line5) ? '<br>' + receipt_details.sub_heading_line5 : ''}
                    </p>
                    <p>
                    ${(receipt_details.tax_info1) ? '<b>' + receipt_details.tax_label1 + '</b> ' + receipt_details.tax_info1 : ''}
                    ${(receipt_details.tax_info2) ? '<b>' + receipt_details.tax_label2 + '</b> ' + receipt_details.tax_info2 : ''}
                    </p>
                    <!-- Title of receipt -->
                    ${(receipt_details.invoice_heading) ? '<h3 class="text-center">' + receipt_details.invoice_heading + '</h3> ' : ''}
                </div>`;
    } else {
        html += `<div class="col-xs-12 text-center">
                        <img style="width: 100%;margin-bottom: 10px;" src="`+ receipt_details.letter_head + `">
                    </div>`;
    }
    html += `<div class="col-xs-12 text-center">
                <!-- Invoice  number, Date  -->
                <p style="width: 100% !important" class="word-wrap">
                    <span class="pull-left text-left word-wrap">
                        ${(receipt_details.invoice_no_prefix) ? '<b>' + receipt_details.invoice_no_prefix + '</b>' : ''}
                        ${receipt_details.invoice_no} 

                        ${(receipt_details.types_of_service) ? '<br/><span class="pull-left text-left"><strong>'
            +
            receipt_details.types_of_service_label + ':</strong>'
            + receipt_details.types_of_service
            + ((receipt_details.types_of_service_custom_fields) ? '' : '')
            : ''}`;

    if (receipt_details.types_of_service) {
        html += `<br/>
                            <span class="pull-left text-left">
                                <strong>${receipt_details.types_of_service_label}:</strong>
                                ${receipt_details.types_of_service}
                                <!-- Waiter info -->`;

        if (receipt_details.types_of_service) {
            Object.values(receipt_details.types_of_service_custom_fields).forEach(function (value, key) {
                html += `<br><strong>${key}: </strong> ${value}`;
            });
        }
        html += `</span>`;
    }

    html += `<!-- Table information-->`;
    if (receipt_details.table_label || receipt_details.table) {
        html += `<br/>
                            <span class="pull-left text-left">
                                ${(receipt_details.table_label) ? '<b>' + receipt_details.table_label + '</b>' : ''}
                                ${receipt_details.table}
                                <!-- Waiter info -->
                            </span>`;
    }


    html += `
                        <!-- customer info -->
                        ${(receipt_details.customer_info) ? '<br/>' + '<b>' + receipt_details.customer_label + '</b> ' + '<br>' + receipt_details.customer_info + '<br>' : ''}
                        ${(receipt_details.client_id_label) ? '<br/>' + '<b>' + receipt_details.client_id_label + '</b> ' + receipt_details.client_id : ''}
                        ${(receipt_details.customer_tax_label) ? '<br/>' + '<b>' + receipt_details.customer_tax_label + '</b> ' + receipt_details.customer_tax_number : ''}
                        ${(receipt_details.customer_custom_fields) ? '<br/> ' + receipt_details.customer_custom_fields : ''}
                        ${(receipt_details.sales_person_label) ? '<br/>' + '<b>' + receipt_details.sales_person_label + '</b> ' + receipt_details.sales_person : ''}
                        ${(receipt_details.commission_agent_label) ? '<br/>' + '<b>' + receipt_details.commission_agent_label + '</b> ' + receipt_details.commission_agent : ''}
                        ${(receipt_details.customer_rp_label) ? '<br/>' + '<b>' + receipt_details.customer_rp_label + '</b> ' + receipt_details.customer_total_rp : ''}
                    </span>

                    <span class="pull-right text-left">
                        <b>${receipt_details.date_label}</b> ${receipt_details.invoice_date}

                        ${(receipt_details.due_date_label) ? '<br/>' + '<b>' + receipt_details.due_date_label + '</b>' + receipt_details.due_date : ''}`;

    if (receipt_details.brand_label || receipt_details.repair_brand) {
        html += `<br/>
                            ${(receipt_details.brand_label) ? '<b>' + receipt_details.brand_label + '</b>' : ''}
                            ${receipt_details.repair_brand}`;
    }

    if (receipt_details.device_label || receipt_details.repair_device) {
        html += `<br/>
                            ${(receipt_details.device_label) ? '<b>' + receipt_details.device_label + '</b>' : ''}
                            ${receipt_details.repair_device}`;
    }

    if (receipt_details.model_no_label || receipt_details.repair_model_no) {
        html += `<br/>
                            ${(receipt_details.model_no_label) ? '<b>' + receipt_details.model_no_label + '</b>' : ''}
                            ${receipt_details.repair_model_no}`;
    }



    if (receipt_details.serial_no_label || receipt_details.repair_serial_no) {
        html += `<br/>
                            ${(receipt_details.serial_no_label) ? '<b>' + receipt_details.serial_no_label + '</b>' : ''}
                            ${receipt_details.repair_serial_no}`;
    }
    if (receipt_details.repair_status_label || receipt_details.repair_status) {
        html += `<br/>
                            ${(receipt_details.repair_status_label) ? '<b>' + receipt_details.repair_status_label + '</b>' : ''}
                            ${receipt_details.repair_status}`;
    }
    if (receipt_details.repair_warranty_label || receipt_details.repair_warranty) {
        html += `<br/>
                            ${(receipt_details.repair_warranty_label) ? '<b>' + receipt_details.repair_warranty_label + '</b>' : ''}
                            ${receipt_details.repair_warranty}`;
    }

    html += `<!-- Waiter info -->`;
    if (receipt_details.service_staff_label || receipt_details.service_staff) {
        html += `<br/>
                            ${(receipt_details.service_staff_label) ? '<b>' + receipt_details.service_staff_label + '</b>' : ''}
                            ${receipt_details.service_staff}`;
    }

    html += `
                        ${(receipt_details.shipping_custom_field_1_label) ? '<br>' + '<strong>' + receipt_details.shipping_custom_field_1_label + ':</strong>' + receipt_details.shipping_custom_field_1_value ?? '' : ''}
                        ${(receipt_details.shipping_custom_field_2_label) ? '<br>' + '<strong>' + receipt_details.shipping_custom_field_2_label + ':</strong>' + receipt_details.shipping_custom_field_2_value ?? '' : ''}
                        ${(receipt_details.shipping_custom_field_3_label) ? '<br>' + '<strong>' + receipt_details.shipping_custom_field_3_label + ':</strong>' + receipt_details.shipping_custom_field_3_value ?? '' : ''}
                        ${(receipt_details.shipping_custom_field_4_label) ? '<br>' + '<strong>' + receipt_details.shipping_custom_field_4_label + ':</strong>' + receipt_details.shipping_custom_field_4_value ?? '' : ''}
                        ${(receipt_details.shipping_custom_field_5_label) ? '<br>' + '<strong>' + receipt_details.shipping_custom_field_5_label + ':</strong>' + receipt_details.shipping_custom_field_5_value ?? '' : ''}

                        
                        ${(receipt_details.sale_orders_invoice_no) ? '<br>' + '<strong>@lang(\'restaurant.order_no\'):</strong>' + receipt_details.sale_orders_invoice_no : ''}

                        
                        ${(receipt_details.sale_orders_invoice_date) ? '<br>' + '<strong>@lang(\'restaurant.order_dates\'):</strong>' + receipt_details.sale_orders_invoice_date : ''}
                        ${(receipt_details.sell_custom_field_1_value) ? '<br>' + '<strong>' + receipt_details.sell_custom_field_1_label + ':</strong>' + receipt_details.sell_custom_field_1_value : ''}
                        ${(receipt_details.sell_custom_field_2_value) ? '<br>' + '<strong>' + receipt_details.sell_custom_field_2_label + ':</strong>' + receipt_details.sell_custom_field_2_value : ''}
                        ${(receipt_details.sell_custom_field_3_value) ? '<br>' + '<strong>' + receipt_details.sell_custom_field_3_label + ':</strong>' + receipt_details.sell_custom_field_3_value : ''}
                        ${(receipt_details.sell_custom_field_4_value) ? '<br>' + '<strong>' + receipt_details.sell_custom_field_4_label + ':</strong>' + receipt_details.sell_custom_field_4_value : ''}
                    </span>
                </p>
            </div>
        </div>`;

    html += `<div class="row" style="color: #000000 !important;">`;
    if (receipt_details.repair_checklist_label || receipt_details.checked_repair_checklist) {
        html += `<div class="col-xs-12">
                            <br>
                            @if(!empty($receipt_details->repair_checklist_label))
                                <b @if($receipt_details->design != 'classic') class="color-555" @endif>
                                    {!! $receipt_details->repair_checklist_label !!}
                                </b>
                            @endif <br>
                            @if(!empty($receipt_details->repair_checklist))
                                @php
                                    $checked_repair_checklist = json_decode($receipt_details->checked_repair_checklist, true);
                                @endphp
                            @endif
                            <div class="row">
                                @foreach($receipt_details->repair_checklist as $check)
                                    <div class="col-xs-4">
                                        @if($checked_repair_checklist[$check] == 'yes')
                                            <i class="fas fa-check-square text-success"></i>
                                        @elseif($checked_repair_checklist[$check] == 'no')
                                            <i class="fas fa-window-close text-danger"></i>
                                        @elseif($checked_repair_checklist[$check] == 'not_applicable')
                                            <i class="fas fa-square"></i>
                                        @endif
                                        <span @if($receipt_details->design != 'classic') class="color-555" @endif>
                                            {{$check}}
                                        </span>
                                        <br>
                                    </div>
                                @endforeach
                            </div>
                        </div>`;
    }
    if (receipt_details.repair_checklist_label || receipt_details.checked_repair_checklist) {
        html += `<div class="col-xs-12">
                            <br>
                            <p @if($receipt_details->design != 'classic') class="color-555" @endif>
                                @if(!empty($receipt_details->defects_label))
                                    <strong>{!! $receipt_details->defects_label !!}</strong><br>
                                @endif
                                @php
                                    $defects = json_decode($receipt_details->repair_defects, true);
                                @endphp
                                @if(!empty($defects))
                                    @foreach($defects as $product_defect)
                                        {{$product_defect['value']}}
                                        @if(!$loop->last)
                                            {{','}}
                                        @endif
                                    @endforeach
                                @endif
                            </p>
                        </div>`;
    }
    html += `</div>`;

    p_width = 45;
    if (receipt_details.item_discount_label) {
        p_width -= 10;
    }
    if (receipt_details.discounted_unit_price_label) {
        p_width -= 10;
    }

    html +=
        `<div class="row" style="color: #000000 !important;">
            <div class="col-xs-12">
                <br/>
                <table class="table table-responsive table-slim">
                    <thead>
                        <tr>
                            <th width="${p_width}%">${receipt_details.table_product_label}</th>
                            <th class="text-right" width="15%">${receipt_details.table_qty_label}</th>
                            <th class="text-right" width="15%">${receipt_details.table_unit_price_label}</th>
                            ${(receipt_details.discounted_unit_price_label) ? '<th class="text-right" width="10%">' + receipt_details.discounted_unit_price_label + '</th>' : ''}
                            ${(receipt_details.item_discount_label) ? '<th class="text-right" width="10%">' + receipt_details.item_discount_label + '</th>' : ''}
                            <th class="text-right" width="15%">${receipt_details.table_subtotal_label}</th>
                        </tr>
                    </thead>
                    <tbody>`;

    if (receipt_details.lines) {
        Object.values(receipt_details.lines).forEach(function (line, index) {
            html +=
                `<tr>
                                <td>
                                    ${(line.image) ? '<img src="' + line.image + '" alt="Image" width="50" style="float: left; margin-right: 8px;">' : ''}

                                    ${line.name} ${line.product_variation} ${line.variation} 

                                    ${(line.sub_sku) ? ', ' + line.sub_sku : ''} ${(line.brand) ? ', ' + line.brand : ''} ${(line.cat_code) ? ', ' + line.cat_code : ''} 
                                    ${(line.product_custom_fields) ? ', ' + line.product_custom_fields : ''} 

                                    ${(line.product_description) ? '<small>' + line.product_description + '</small>' : ''}  

                                    ${(line.sell_line_note) ? '<br><small>' + line.sell_line_note + '</small>' : ''}  

                                    ${(line.lot_number) ? '<br>' + line.lot_number_label + ': ' + line.lot_number : ''}  

                                    ${(line.product_expiry) ? '<br>' + line.product_expiry_label + ': ' + line.product_expiry : ''}


                                    ${(line.warranty_name) ? '<br><small>' + line.warranty_name + '</small>' : ''}
                                    ${(line.warranty_exp_date) ? '<small>- ' + line.warranty_exp_date + '</small>' : ''}    

                                    ${(line.warranty_description) ? '<small>' + (line.warranty_description ?? '') + '</small>' : ''}

                                    ${(receipt_details.show_base_unit_details && line.quantity && line.base_unit_multiplier !== 1) ?
                    '<br><small> 1 ' + line.units + " = " + line.base_unit_multiplier + " " + line.base_unit_name + " <br>" >
                    +line.base_unit_price + " x " + line.orig_quantity + " = " + line.line_total + '</small>' : ''}

                                 
                                </td>
                                <td class="text-right">
                                ${line.quantity} ${line.units.short_name} 
                                    
                                    ${(receipt_details.show_base_unit_details && line.quantity && line.base_unit_multiplier !== 1) ?
                    '<br><small>' + line.quantity + " x " + line.base_unit_multiplier + " = " + line.orig_quantity + " " + line.base_unit_name + '</small>' : ''}
                                </td>
                                <td class="text-right">${line.unit_price}</td>

                                ${(receipt_details.discounted_unit_price_label) ? '<td class="text-right">' + line.warranty_description + '</td>' : ''}
                                
                                ${(receipt_details.item_discount_label) ? '<td class="text-right">' + (line.total_line_discount ?? '0.00') + (line.line_discount_percent + '%' ?? '') + '</td>' : ''}
                                
    
                                <td class="text-right">${line.line_total}</td>
                            </tr>
                            `;

            if (line.modifiers) {
                Object.values(line.modifiers).forEach(function (modifier, modifier_index) {
                    html +=
                        `<tr>
                                        <td>
                                            ${modifier.name} ${modifier.variation} 
                                            ${(modifier.sub_sku) ? ', ' + modifier.sub_sku : ''} ${(modifier.cat_code) ? ', ' + modifier.cat_code : ''} 
                                            ${(modifier.sell_line_note) ? modifier.sell_line_note : ''} 
                                        </td>

                                        <td class="text-right">${modifier.quantity} ${modifier.units} </td>
                                        <td class="text-right">${modifier.unit_price_inc_tax}</td>
                                        ${(receipt_details.discounted_unit_price_label) ? '<td class="text-right">' + modifier.unit_price_exc_tax + '</td>' : ''} 
                                        
                                        ${(receipt_details.item_discount_label) ? '<td class="text-right">0.00</td>' : ''} 

                                        <td class="text-right">${modifier.line_total}</td>
                                    </tr>`;
                });
            }
        });


    } else {
        html +=
            `<tr>
                            <td colspan="4">&nbsp;</td>
                            ${(receipt_details.discounted_unit_price_label) ? '<td></td>' : ''}
                            ${(receipt_details.item_discount_label) ? '<td></td>____' : ''}
                        </tr>`;
    }

    html += `
                    </tbody>
                </table>
            </div>
        </div>`;

    html +=
        `<div class="row" style="color: #000000 !important;">
            <div class="col-md-12"><hr/></div>
            <div class="col-xs-6">

                <table class="table table-slim">`
    if (receipt_details.payments) {
        Object.values(receipt_details.payments).forEach(function (payment, payment_index) {
            html +=
                `<tr>
                                <td>${payment.method}</td>
                                <td class="text-right" >${payment.amount}</td>
                                <td class="text-right">${payment.date}</td>
                            </tr>`;
        });

    }
    html += `
                    <!-- Total Paid-->
                    ${(receipt_details.total_paid) ? '<td><th>' + receipt_details.total_paid_label + '</th><td class="text-right">' +
            receipt_details.total_paid + '</td></tr>' : ''} 

                    <!-- Total Due-->
                    ${(receipt_details.total_due && receipt_details.total_due_label) ? '<td><th>' + receipt_details.total_due_label + '</th><td class="text-right">' +
            receipt_details.total_due + '</td></tr>' : ''} 
                    
                    ${(receipt_details.all_due) ? '<td><th>' + receipt_details.all_bal_label + '</th><td class="text-right">' +
            receipt_details.all_due + '</td></tr>' : ''} 
                </table>
            </div>

            <div class="col-xs-6">
                <div class="table-responsive">
                    <table class="table table-slim">
                        <tbody>
                            ${(receipt_details.total_quantity_label) ? '<td><th  style="width:70%">' + receipt_details.total_quantity_label + '</th><td class="text-right">' +
            receipt_details.total_quantity + '</td></tr>' : ''} 

                            ${(receipt_details.total_items_label) ? '<td><th  style="width:70%">' + receipt_details.total_items_label + '</th><td class="text-right">' +
            receipt_details.total_items + '</td></tr>' : ''} 
                            

                            <tr>
                                <th style="width:70%">
                                    ${receipt_details.subtotal_label}
                                </th>
                                <td class="text-right">
                                    ${__currency_trans_from_en(receipt_details.subtotal)}
                                </td>
                            </tr>

                            ${(receipt_details.total_exempt_uf) ? '<tr><th  style="width:70%">@lang(\'lang_v1.exempt\')</th><td class="text-right">' +
            receipt_details.total_exempt + '</td></tr>' : ''} 

                            <!-- Shipping Charges -->
                            ${(receipt_details.shipping_charges) ? '<tr><th  style="width:70%">' + receipt_details.shipping_charges_label + '</th><td class="text-right">' +
            receipt_details.shipping_charges + '</td></tr>' : ''} 


                            <!-- Discount 1111 -->
                            ${(receipt_details.discount) ? '<tr><th  style="width:10%">' + receipt_details.discount_label + '</th><td class="text-right">(-) ' +
            receipt_details.discount + '</td></tr>' : ''} 


                            ${(receipt_details.total_line_discount) ? '<tr><th>' + receipt_details.line_discount_label + '</th><td class="text-right">(-) ' +
            receipt_details.total_line_discount + '</td></tr>' : ''} 
                            `;

    if (receipt_details.additional_expenses) {
        Object.values(receipt_details.additional_expenses).forEach(function (val, key) {
            html +=
                `<tr>
                                        <td>
                                            ${key}:
                                        </td>

                                        <td class="text-right">
                                            (+) ${val}
                                        </td>
                                    </tr>`;
        });
    }

    html +=
        `
                            ${(receipt_details.reward_point_label) ? '<td><th>' + receipt_details.reward_point_label + '</th><td class="text-right">' +
            receipt_details.reward_point_amount + '</td></tr>' : ''} 
                            

                            <!-- Tax -->
                            ${(receipt_details.tax) ? '<td><th>' + receipt_details.tax_label + '</th><td class="text-right">' +
            receipt_details.tax + '</td></tr>' : ''} 

                            ${(receipt_details.round_off_amount > 0) ? '<td><th>' + receipt_details.tax_label + '</th><td class="text-right">' +
            receipt_details.round_off + '</td></tr>' : ''} 

                           

                            <!-- Total -->
                            <tr>
                                <th>
                                    ${receipt_details.total_label}
                                </th>
                                <td class="text-right">
                                    ${__currency_trans_from_en(receipt_details.total)}
                                    ${(receipt_details.total_in_words) ? '<br><small>' + receipt_details.total_in_words + '</small>' : ''} 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="border-bottom col-md-12">`;
    if (receipt_details.hide_price && receipt_details.tax_summary_label) {
        html += `<!-- tax -->`;
        if (receipt_details.taxes) {
            html +=
                `<table class="table table-slim table-bordered">
                            <tr>
                                <th colspan="2" class="text-center">${receipt_details.tax_summary_label}</th>
                            </tr>`;

            Object.values(receipt_details.taxes).forEach(function (val, key) {
                html +=
                    `<tr>
                                    <td class="text-center"><b>${key}</b></td>
                                    <td class="text-center">${val}</td>
                                </tr>`;
            });
            html += `</table>`;
        }
    }
    html += `
                </div>
                ${(receipt_details.additional_notes) ? '<div class="col-xs-12"><p>' +/*nl2br(*/receipt_details.additional_notes + '</p></div>' : ''} 
            </div>
            `;
    /************************************** */
    /*
    html+=
    `<div class="row" style="color: #000000 !important;">
        @if(!empty($receipt_details->footer_text))
        <div class="@if($receipt_details->show_barcode || $receipt_details->show_qr_code) col-xs-8 @else col-xs-12 @endif">
            {!! $receipt_details->footer_text !!}
        </div>
        @endif
        @if($receipt_details->show_barcode || $receipt_details->show_qr_code)
            <div class="@if(!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
                @if($receipt_details->show_barcode)
                    {{-- Barcode --}}
                    <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
                @endif
                
                @if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
                    <img class="center-block mt-5" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE', 3, 3, [39, 48, 54])}}">
                @endif
            </div>
        @endif
    </div>`;
    */


    return html;
}

function getReceiptDetails(sell, location, customer, sell_lines, transaction, receipt_printer_type = "browser") {
    output = Array();
    var il = location.invoice_layout;
    console.log('location', location);
    var transaction = transaction
    transaction_type = transaction.type;

    var contact = customer;



    //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
    //reconvertir de la devise du systeme vers la seconde devise de la transaction
    /*
        if (!empty(location.second_currency)) {
            $mycurrency = Currency::find(location.second_currency);
            $currency_details['symbol'] = $mycurrency->symbol;

            $transaction->total_before_tax *= $transaction->second_currency_rate;
            $transaction->tax_amount *= $transaction->second_currency_rate;


            $transaction->discount_amount = ($transaction->discount_type == 'fixed') ? $transaction->discount_amount * $transaction->second_currency_rate : $transaction->discount_amount;


            $transaction->rp_redeemed_amount *= $transaction->second_currency_rate;
            $transaction->shipping_charges *= $transaction->second_currency_rate;
            $transaction->round_off_amount *= $transaction->second_currency_rate;


            $transaction->additional_expense_value_1 *= $transaction->second_currency_rate;
            $transaction->additional_expense_value_2 *= $transaction->second_currency_rate;
            $transaction->additional_expense_value_3 *= $transaction->second_currency_rate;
            $transaction->additional_expense_value_4 *= $transaction->second_currency_rate;



            $transaction->final_total *= $transaction->second_currency_rate;
            $transaction->essentials_amount_per_unit_duration *= $transaction->second_currency_rate;
            $transaction->packing_charge *= $transaction->second_currency_rate;


            $transaction->mfg_production_cost = ($transaction->mfg_production_cost_type == 'fixed') ? $transaction->mfg_production_cost * $transaction->second_currency_rate : $transaction->mfg_production_cost;
        }
        //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////
    */

    //{{--  personnalize custom code 14042024-SHOWDEVISERATE001 -- 14042024}}
    //Afficher le taux de conversion dans la facture
    il.sub_heading_line5 = 1;
    if (il.sub_heading_line5) {
        il.sub_heading_line5 = "1" + DATA.business.currency_code + "=" + __currency_trans_from_en(location.attributes.second_currency_rate) + ' ' + location.attributes.second_currency_code;
    }
    //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////

    output.header_text = (il.header_text) ? il.header_text : '';
    output.business_name = (il.show_business_name == 1) ? DATA.business.name : '';
    output.location_name = (il.show_location_name == 1) ? location.name : '';
    output.sub_heading_line1 = (il.sub_heading_line1) ? il.sub_heading_line1.trim() : null;;
    output.sub_heading_line2 = (il.sub_heading_line2) ? il.sub_heading_line2.trim() : null;;
    output.sub_heading_line3 = (il.sub_heading_line3) ? il.sub_heading_line3.trim() : null;;
    output.sub_heading_line4 = (il.sub_heading_line4) ? il.sub_heading_line4.trim() : null;;
    output.sub_heading_line5 = (il.sub_heading_line5) ? il.sub_heading_line5.trim() : null;;
    output.table_product_label = il.table_product_label;
    output.table_qty_label = il.table_qty_label;
    output.table_unit_price_label = il.table_unit_price_label;
    output.table_subtotal_label = il.table_subtotal_label;



    output.design = il.design;

    //Display name
    output.display_name = output.business_name;
    if (output.location_name) {
        if (output.display_name) {
            output.display_name += ', ';
        }
        output.display_name += output.location_name;
    }

    //Codes
    if ((DATA.business.code_label_1) && (DATA.business.code_1)) {
        output.code_label_1 = DATA.business.code_label_1;
        output.code_1 = DATA.business.code_1;
    }

    if ((DATA.business.code_label_1) && (DATA.business.code_1)) {
        output.code_label_2 = DATA.business.code_label_2;
        output.code_2 = DATA.business.code_2;
    }

    /*
    gestion du logo
    if (il.show_letter_head == 1) {
        $output.letter_head = ($il->letter_head) &&
            file_exists(public_path('uploads/invoice_logos/' . $il->letter_head)) ?
            asset('uploads/invoice_logos/' . $il->letter_head) : null;
    }
    //Logo
    $output['logo'] = $il->show_logo != 0 && !empty($il->logo) && file_exists(public_path('uploads/invoice_logos/' . $il->logo)) ? asset('uploads/invoice_logos/' . $il->logo) : false;
    */


    //Address
    output.address = '';
    temp = [];
    if (il.show_landmark == 1) {
        temp.push(location.landmark);
    }
    if (il.show_city == 1 && (location.city)) {
        temp.push(location.city);
    }
    if (il.show_state == 1 && (location.state)) {
        temp.push(location.state);
    }
    if (il.show_zip_code == 1 && (location.zip_code)) {
        temp.push(location.zip_code);
    }
    if (il.show_country == 1 && (location.country)) {
        temp.push(location.country);
    }
    if (temp) {
        output.address += temp.join(', ');
    }

    output.website = location.website;
    output.location_custom_fields = '';

    temp = [];
    location_custom_field_settings = (il.location_custom_fields) ? il.location_custom_fields : [];
    if ((location.custom_field1) && location_custom_field_settings.includes('custom_field1')) {
        temp.push(location.custom_field1);
    }
    if ((location.custom_field2) && location_custom_field_settings.includes('custom_field2')) {
        temp.push(location.custom_field2);
    }
    if ((location.custom_field3) && location_custom_field_settings.includes('custom_field3')) {
        temp.push(location.custom_field3);
    }
    if ((location.custom_field4) && location_custom_field_settings.includes('custom_field4')) {
        temp.push(location.custom_field4);
    }
    if (temp) {
        output.location_custom_fields += temp.join(', ');
    }


    //Tax Info
    if (il.show_tax_1 == 1 && (DATA.business.tax_number_1)) {
        output.tax_label1 = (DATA.business.tax_label_1) ? DATA.business.tax_label_1 + ': ' : '';

        output.tax_info1 = DATA.business.tax_number_1;
    }
    if (il.show_tax_2 == 1 && (DATA.business.tax_number_2)) {
        if (output.tax_info1) {
            output.tax_info1 += ', ';
        }

        output.tax_label2 = (DATA.business.tax_label_2) ? DATA.business.tax_label_2 + ': ' : '';

        output.tax_info2 = DATA.business.tax_number_2;
    }

    //Shop Contact Info
    output.contact = '';
    //alert(il.show_mobile_number + " == 1 && " + location.mobile);
    if (il.show_mobile_number == 1 && (location.mobile)) {
        output.contact += '<b>' + "Mobile" + ':</b> ' + location.mobile;
    }
    if (il.show_alternate_number == 1 && (location.alternate_number)) {
        if (!output.contact) {
            output.contact += "Mobile" + ': ' + location.alternate_number;
        } else {
            output.contact += ', ' + location.alternate_number;
        }
    }
    if (il.show_email == 1 && (location.email)) {
        if (output.contact) {
            output.contact += "\n";
        }
        output.contact += '<br>' + 'Email' + ': ' + location.email;
    }



    //Customer show_customer
    output.customer_info = (customer.name) ? customer.name : '';
    output.customer_tax_number = '';
    output.customer_tax_label = '';
    output.customer_custom_fields = '';

    console.log("customercustomercustomercustomer", customer);

    //alert("phone ="+customer.mobile);

    if (il.show_customer == 1) {
        output.customer_label = (il.customer_label) ? il.customer_label : '';
        output.customer_name = (customer.name) ? customer.name : customer.supplier_business_name;
        output.customer_mobile = customer.mobile;


        if (receipt_printer_type != 'printer') {

            output.customer_info += (customer.address_line_1) ? customer.address_line_1 : '';
            if (customer.address_line_1) {
                output.customer_info += '<br>';
            }
            output.customer_info += '<br><b>' + "Mobile" + '</b>: ' + customer.mobile;
            if ((customer.landline)) {
                output.customer_info += ', ' + customer.landline;
            }

        }

        output.customer_tax_number = customer.tax_number;
        output.customer_tax_label = (il.client_tax_label) ? il.client_tax_label : '';


        temp = [];
        customer_custom_fields_settings = (il.contact_custom_fields) ? il.contact_custom_fields : [];
        contact_custom_labels = JSON.parse(DATA.business.custom_labels).contact;
        if ((customer.custom_field1) && customer_custom_fields_settings.includes('custom_field1')) {
            if (contact_custom_labels.custom_field_1) {
                temp.push(contact_custom_labels.custom_field_1 + ': ' + customer.custom_field1);
            } else {
                temp.push(customer.custom_field1);
            }
        }
        if ((customer.custom_field2) && customer_custom_fields_settings.includes('custom_field2')) {
            if (contact_custom_labels.custom_field_2) {
                temp.push(contact_custom_labels.custom_field_2 + ': ' + customer.custom_field2);
            } else {
                temp.push(customer.custom_field2);
            }
        }
        if ((customer.custom_field3) && customer_custom_fields_settings.includes('custom_field3')) {
            if (contact_custom_labels.custom_field_3) {
                temp.push(contact_custom_labels.custom_field_3 + ': ' + customer.custom_field3);
            } else {
                temp.push(customer.custom_field3);
            }
        }
        if ((customer.custom_field4) && customer_custom_fields_settings.includes('custom_field4')) {
            if (contact_custom_labels.custom_field_4) {
                temp.push(contact_custom_labels.custom_field_4 + ': ' + customer.custom_field4);
            } else {
                temp.push(customer.custom_field1);
            }
        }
        if (temp) {
            output.customer_custom_fields += temp.join('<br>');
        }


        //To be used in pdfs
        customer_address = [];
        if (customer.supplier_business_name) {
            customer_address.push(customer.supplier_business_name);
        }
        if (customer.address_line_1) {
            customer_address.push('<br>' + customer.address_line_1);
        }
        if (customer.address_line_2) {
            customer_address.push('<br>' + customer.address_line_2);
        }
        if (customer.city) {
            customer_address.push('<br>' + customer.city);
        }
        if (customer.state) {
            customer_address.push(customer.state);
        }
        if (customer.country) {
            customer_address.push(customer.country);
        }
        if (customer.zip_code) {
            customer_address.push('<br>' + customer.zip_code);
        }
        if (customer.name) {
            customer_address.push('<br>' + customer.name);
        }
        if (customer.mobile) {
            customer_address.push('<br>' + customer.mobile);
        }
        if (customer.landline) {
            customer_address.push(customer.landline);
        }


        output.customer_info_address = '';
        if (customer_address) {
            output.customer_info_address += customer_address.join(', ');
        }
    }



    if (il.show_reward_point == 1) {
        output.customer_rp_label = DATA.business.rp_name;
        output.customer_total_rp = DATA.business.total_rp;
    }



    output.client_id = '';
    output.client_id_label = '';
    if (il.show_client_id == 1) {
        output.client_id_label = (il.client_id_label) ? il.client_id_label : '';
        output.client_id = (customer.contact_id) ? customer.contact_id : '';
    }



    console.log('transaction', transaction);
    //alert(transaction.sales_person.user_full_name);

    //Sales person info
    output.sales_person = '';
    output.sales_person_label = '';
    if (il.show_sales_person == 1) {
        user_full_name = `${transaction.sales_person.surname} ${transaction.sales_person.first_name} ${transaction.sales_person.last_name} ${transaction.sales_person.guardian_name??''}`;
        output.sales_person_label = (il.sales_person_label) ? il.sales_person_label : '';
        output.sales_person = user_full_name ?? '';
    }
    

    //commission agent info
    output.commission_agent = '';
    output.commission_agent_label = '';
    if (il.show_commission_agent == 1) {
        output.commission_agent_label = (il.commission_agent_label) ? il.commission_agent_label : '';
        //output.commission_agent = (transaction.sale_commission_agent.user_full_name) ? transaction.sale_commission_agent.user_full_name : '';
        output.commission_agent = '====>';
    }

    //Invoice info
    output.invoice_no = transaction.invoice_no;
    output.invoice_no_prefix = il.invoice_no_prefix;
    output.shipping_address = (transaction.shipping_address) ? transaction.shipping_address : transaction.shipping_address;



    //Heading & invoice label, when quotation use the quotation heading.
    if (transaction_type == 'sell_return') {
        /*
        output.invoice_heading=il->cn_heading;
        output.invoice_no_prefix=il->cn_no_label;

        //Parent sell details(return_parent_id)
        output.parent_invoice_no=Transaction::find($transaction->return_parent_id)->invoice_no;
        output.parent_invoice_no_prefix'] = $il->invoice_no_prefix;
        */

    }
    /*
    elseif ($transaction->status == 'draft' && $transaction->sub_status == 'proforma' && !empty($il->common_settings['proforma_heading'])) {
        output.invoice_heading'] = $il->common_settings['proforma_heading'];
    } elseif ($transaction->status == 'draft' && $transaction->is_quotation == 1) {
        output.invoice_heading'] = $il->quotation_heading;
        output.invoice_no_prefix'] = $il->quotation_no_prefix;
    } elseif ($transaction_type == 'sales_order') {
        output.invoice_heading'] = !empty($il->common_settings['sales_order_heading']) ? $il->common_settings['sales_order_heading'] : __('lang_v1.sales_order');
        output.invoice_no_prefix'] = $il->quotation_no_prefix;
    } 
    */
    else {
        output.invoice_heading = il.invoice_heading;
        if (transaction.payment_status == 'paid' && il.invoice_heading_paid) {
            output.invoice_heading += ' ' + il.invoice_heading_paid;
        }
        else if
            ((['due', 'partial'].includes(transaction.payment_status)) && il.invoice_heading_not_paid) {
            output.invoice_heading += ' ' + il.invoice_heading_not_paid;
        }
    }


    output.date_label = il.date_label;
    output.invoice_date = sell.date;


    output.transaction_date = sell.date;
    output.date_time_format = DATA.business.date_format;

    //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
    //attache les informations des devises liées à la location
    output.currency_symbol = DATA.business.currency_symbol;
    if ((location.attributes.second_currency)) {
        output.currency_symbol = location.attributes.second_currency_symbol;
    }
    //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////




    output.hide_price = (il.common_settings.hide_price) ? true : false;

    if ((il.common_settings.show_due_date) && transaction.payment_status != 'paid') {
        output.due_date_label = (il.common_settings.due_date_label) ? il.common_settings.due_date_label : '';
        due_date = transaction.due_date;
        if (due_date && due_date.trim().length !== 0) {
            if (il.date_time_format.trim().length !== 0) {
                output.due_date = due_date;
            } else {
                output.due_date = moment(due_date).format("il->date_time_format");
            }
        }
    }

    show_currency = true;
    if (receipt_printer_type == 'printer' && DATA.business.currency_symbol.trim() != '$') {
        show_currency = false;
    }

    //Invoice product lines
    is_lot_number_enabled = DATA.business.enable_lot_number;
    is_product_expiry_enabled = DATA.business.enable_product_expiry;



    output.lines = [];
    total_exempt = 0;
    if (['sell', 'sales_order'].includes(transaction_type)) {
        sell_line_relations = ['modifiers', 'sub_unit', 'warranties'];

        if (is_lot_number_enabled == 1) {
            sell_line_relations.push('lot_details');
        }

        lines = sell_lines;
        //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
        //attache les informations des devises liées à la location
        Object.values(lines).forEach(function (line, index) {
            alert("***==========>"+line.unit_price_before_discount);
            console.log("***",lines[index]);
            lines[index].unit_price_before_discount *= location.attributes.second_currency_rate;
            lines[index].unit_price *= location.attributes.second_currency_rate;

            lines[index].line_discount_amount = (lines[index].line_discount_type == 'fixed') ? lines[index].line_discount_amount * location.attributes.second_currency_rate : lines[index].line_discount_amount;


            lines[index].unit_price_inc_tax *= location.attributes.second_currency_rate;
            lines[index].item_tax *= location.attributes.second_currency_rate;
        });

        //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////


        Object.values(lines).forEach(function (line, index) {
            lines[index].image = 'resources/images/default.png'; //image du produit






            lines[index].unit_price_before_discount *= location.attributes.second_currency_rate;
            lines[index].unit_price *= location.attributes.second_currency_rate;

            lines[index].line_discount_amount = (lines[index].line_discount_type == 'fixed') ? lines[index].line_discount_amount * location.attributes.second_currency_rate : lines[index].line_discount_amount;
            lines[index].line_discount_amount =line.unit_price_inc_tax; //line_array.total_line_discount = line.unit_price_inc_tax; //a suivre
            lines[index].line_discount_amount=0;
            
            lines[index].unit_price_inc_tax *= location.attributes.second_currency_rate;
            lines[index].item_tax *= location.attributes.second_currency_rate;
            if (line.sub_unit_id) {
                //formated_sell_line = $this -> recalculateSellLineTotals($business_details -> id, $value);

                //lines[index] = formated_sell_line;

                if (lines[index].sub_unit) {
                    multiplier = (lines[index].sub_unit.multiplier) ? lines[index].sub_unit.multiplier : 1;

                    if (lines[index].line_discount_type == 'fixed') {
                        lines[index].line_discount_amount = lines[index].line_discount_amount * multiplier;
                    }
                    lines[index].orig_quantity = lines[index].quantity;
                    lines[index].multiplier = multiplier;
                    lines[index].quantity = lines[index].quantity / multiplier;
                    lines[index].unit_price_before_discount = lines[index].unit_price_before_discount * multiplier;
                    lines[index].unit_price = lines[index].unit_price * multiplier;
                    lines[index].unit_price_inc_tax = lines[index].unit_price_inc_tax * multiplier;
                    lines[index].item_tax = lines[index].item_tax * multiplier;
                    lines[index].quantity_returned = lines[index].quantity_returned / multiplier;

                    lines[index].unit_details = lines[index].sub_unit;
                }
            }
        });





        output.item_discount_label = il.common_settings.item_discount_label ?? '';

        output.discounted_unit_price_label = il.common_settings.discounted_unit_price_label ?? '';

        output.show_base_unit_details = (il.common_settings.show_base_unit_details);

        output.tax_summary_label = il.common_settings.tax_summary_label ?? '';
        details = [];// this -> _receiptDetailsSellLines($lines, $il, $business_details);

        /******* _receiptDetailsSellLines *********/
        is_lot_number_enabled = DATA.business.enable_lot_number;
        is_product_expiry_enabled = DATA.business.enable_product_expiry;

        output_lines = [];
        //$output_taxes = ['taxes' => []];
        product_custom_fields_settings = (il.product_custom_fields) ? il.product_custom_fields : [];

        is_warranty_enabled = (DATA.business.common_settings.enable_product_warranty) ? true : false;

        Object.values(lines).forEach(function (line, index) {
            product = line.product;
            variation = line.product.variations;
            //product_variation = line.product.variations.product_variation;
            unit = line.product.unit;
            brand = line.product.brand;
            cat = line.product.category;

            unit_name = (unit) ? unit : '';
            base_unit_name = line.product.unit.actual_name;
            base_unit_multiplier = (line.product.unit.base_unit_multiplier) && line.multiplier != 1 ? line.multiplier : 1;
            //console.log("+++++++>",lines);
            //console.log("==========>",line.product.sub_units);
            //.sub_units[line.sub_unit_id].short_name
            if ((line.product.sub_units[line.sub_unit_id])) {
                unit_name = line.product.sub_units[line.sub_unit_id].short_name;
            }
            base_unit_price = line.unit_price_inc_tax / base_unit_multiplier;
            show_product_description = il.common_settings.show_product_description ?? null;


            line_array = [];

            //line_array.image = 'resources/images/default.png'; //image du produit  ca genere une erreur je doit plutot
            //line_array.product = line.product; // produit




            line_array.name = line.product.product_name; // produit


            line_array.product_description = (show_product_description) ? product.product_description : null;
            /*
            'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
            'product_variation' => (empty($product_variation->name) || $product_variation->name == 'DUMMY') ? '' : $product_variation->name,
            */
            line_array.variation = (line.product.variation_name || line.product.variation_name == 'DUMMY') ? '' : line.product.variation_name; // produit
            line_array.product_variation = (line.product.variation_name || line.product.variation_name == 'DUMMY') ? '' : line.product.variation_name; // produit



            //Field for 2nd column
            line_array.quantity = line.quantity;
            line_array.quantity_uf = line.quantity;
            line_array.units = unit_name;

            line_array.base_unit_name = base_unit_name,
                line_array.base_unit_multiplier = (line.multiplier) && line.multiplier != 1 ? line.multiplier : 1;
            line_array.orig_quantity = line.orig_quantity;
            line_array.unit_price = line.unit_price;
            line_array.unit_price_uf = line.unit_price;


            line_array.tax = line.item_tax;
            line_array.tax_id = line.tax_id;
            line_array.tax_unformatted = line.item_tax;

            all_taxes = DATA.taxes;

            tax_details = [];
            line_array.tax_id = 2;
            if (line_array.tax_id != 0) {
                Object.values(all_taxes).forEach(function (tax, tax_key) {
                    if (tax.id == line_array.tax_id) {
                        tax_details.name = tax.name;
                        tax_details.amount = tax.amount;
                    }
                });


            }

            console.log('taxe', tax_details);

            line_array.tax_name = (tax_details) ? tax_details.name : null;
            line_array.tax_percent = (tax_details) ? tax_details.amount : null;

            

            //Field for 3rd column
            line_array.unit_price_inc_tax = line.unit_price_inc_tax;
            line_array.unit_price_inc_tax_uf = line.unit_price_inc_tax;
            line_array.unit_price_exc_tax = line.unit_price;
            line_array.base_unit_price = base_unit_price;
            line_array.price_exc_tax = line.quantity * line.unit_price;
            line_array.unit_price_before_discount = line.unit_price_before_discount;
            line_array.unit_price_before_discount_uf = line.unit_price_before_discount;
            //Fields for 4th column
            line_array.line_total = line.unit_price_inc_tax * line.quantity;
            line_array.line_total_uf = line.unit_price_inc_tax * line.quantity;
            line_array.line_total_exc_tax = line.unit_price * line.quantity;
            line_array.line_total_exc_tax_uf = line.unit_price * line.quantity;
            line_array.variation_id = line.variation_id;


            temp = [];
            if ((product.product_custom_field1) && product_custom_fields_settings.includes('product_custom_field1')) {
                temp.push(product.product_custom_field1);
            }
            if ((product.product_custom_field2) && product_custom_fields_settings.includes('product_custom_field2')) {
                temp.push(product.product_custom_field2);
            }
            if ((product.product_custom_field3) && product_custom_fields_settings.includes('product_custom_field3')) {
                temp.push(product.product_custom_field3);
            }
            if ((product.product_custom_field4) && product_custom_fields_settings.includes('product_custom_field4')) {
                temp.push(product.product_custom_field4);
            }
            if (temp) {
                line_array.product_custom_fields += temp.join(',');
            }


            //Group product taxes by name.
            /*
            if (!empty($tax_details)) {
                if ($tax_details -> is_tax_group) {
                    $group_tax_details = $this -> groupTaxDetails($tax_details, $line -> quantity * $line -> item_tax);

                    $line_array['group_tax_details'] = $group_tax_details;

                    foreach($group_tax_details as $value) {
                        if (!isset($output_taxes['taxes'][$value['name']])) {
                            $output_taxes['taxes'][$value['name']] = 0;
                        }
                        $output_taxes['taxes'][$value['name']] += $value['amount'];
                    }
                } else {
                    $tax_name = $tax_details -> name;
                    if (!isset($output_taxes['taxes'][$tax_name])) {
                        $output_taxes['taxes'][$tax_name] = 0;
                    }
                    $output_taxes['taxes'][$tax_name] += ($line -> quantity * $line -> item_tax);
                }
            }
            */

            line_array.line_discount = line.line_discount_amount;
            line_array.line_discount_uf = line.line_discount_amount;
            

            if (line.line_discount_type == 'percentage') {
                line_array.line_discount += ' (' + line.line_discount_amount + '%)';

                line_array.line_discount_percent = line.line_discount_amount;
            }

            line_array.total_line_discount = line_array.line_discount_uf * line_array.quantity_uf;
            
            if (il.show_brand == 1) {
                line_array.brand = (brand.name) ? brand.name : '';
            } else {
                line_array.brand = '';
            }

            if (il.show_sku == 1) {
                line_array.sub_sku = (line.sub_sku) ? line.sub_sku : '';
            }
            /*
            if (il.show_image == 1) {
                media = variation.media;
                if (count($media)) {
                    $first_img = $media -> first();
                    $line_array['image'] = !empty($first_img -> display_url) ? $first_img -> display_url : asset('/img/default.png');
                } else {
                    $line_array['image'] = $product -> image_url;
                }
            }
            */
            /*
            
            */
            if (il.show_cat_code == 1) {
                try {
                    line_array.cat_code = (cat.short_code) ? cat.short_code : '';
                } catch (error) {
                    line_array.cat_code = 'cat_code';
                }
            } else {
                line_array.cat_code = "";
            }

            try {
                line_array.product_custom_fields = (product.product_custom_field1) ? product.product_custom_field1 : '';
            } catch (error) {
                line_array.product_custom_fields = 'product_custom_field1';
            }


            if (il.show_sale_description == 1) {
                line_array.sell_line_note = (line.sell_line_note) ? (line.sell_line_note) : '';
            }
            if (is_lot_number_enabled == 1 && il.show_lot == 1) {
                line_array.lot_number = (line.lot_details.lot_number) ? line.lot_details.lot_number : null;
                line_array.lot_number_label = "Lot";
            }

            if (is_product_expiry_enabled == 1 && il.show_expiry == 1) {
                line_array.product_expiry = (line.lot_details.exp_date) ? line.lot_details.exp_date : null;
                line_array.product_expiry_label = "Expiration";
            }

            /*
            //Set warranty data if enabled
            if (is_warranty_enabled && ($line -> warranties -> first())) {
                warranty = $line -> warranties -> first();
                if (!empty($il -> common_settings['show_warranty_name'])) {
                    $line_array['warranty_name'] = $warranty -> name;
                }
                if (!empty($il -> common_settings['show_warranty_description'])) {
                    $line_array['warranty_description'] = $warranty -> description;
                }
                if (!empty($il -> common_settings['show_warranty_exp_date'])) {
                    $line_array['warranty_exp_date'] = $warranty -> getEndDate($line -> transaction -> transaction_date);
                }
            }
            */

            //If modifier is set set modifiers line to parent sell line
            /*
            if (!empty($line -> modifiers)) {
                foreach($line -> modifiers as $modifier_line) {
                    $product = $modifier_line -> product;
                    $variation = $modifier_line -> variations;
                    $unit = $modifier_line -> product -> unit;
                    $brand = $modifier_line -> product -> brand;
                    $cat = $modifier_line -> product -> category;

                    $modifier_line_array = [
                        //Field for 1st column
                        'name' => $product -> name,
                        'variation' => (empty($variation -> name) || $variation -> name == 'DUMMY') ? '' : $variation -> name,
                        //Field for 2nd column
                        'quantity' => $this -> num_f($modifier_line -> quantity, false, $business_details),
                        'units' => !empty($unit -> short_name) ? $unit -> short_name : '',

                        //Field for 3rd column
                        'unit_price_inc_tax' => $this -> num_f($modifier_line -> unit_price_inc_tax, false, $business_details),
                        'unit_price_exc_tax' => $this -> num_f($modifier_line -> unit_price, false, $business_details),
                        'price_exc_tax' => $modifier_line -> quantity * $modifier_line -> unit_price,

                        //Fields for 4th column
                        'line_total' => $this -> num_f($modifier_line -> unit_price_inc_tax * $line -> quantity, false, $business_details),
                    ];

                    if ($il -> show_sku == 1) {
                        $modifier_line_array['sub_sku'] = !empty($variation -> sub_sku) ? $variation -> sub_sku : '';
                    }
                    if ($il -> show_cat_code == 1) {
                        $modifier_line_array['cat_code'] = !empty($cat -> short_code) ? $cat -> short_code : '';
                    }
                    if ($il -> show_sale_description == 1) {
                        $modifier_line_array['sell_line_note'] = !empty($line -> sell_line_note) ? nl2br($line -> sell_line_note) : '';
                    }

                    $line_array['modifiers'][] = $modifier_line_array;
                }
            }
            */
            output_lines.push(line_array);
        });

        details.lines = output_lines;
        /**************************************** */



        output.lines = details.lines;
        output.taxes = [];
        total_quantity = 0;
        total_line_discount = 0;
        total_line_taxes = 0;
        subtotal_exc_tax = 0;
        unique_items = [];

        Object.values(details.lines).forEach(function (line, index) {
            if (line.group_tax_details) {
                Object.values(line.group_tax_details).forEach(function (tax_group_detail, index2) {
                    if (output.taxes[tax_group_detail.name]) {
                        output.taxes[tax_group_detail.name] = 0;
                    }
                    output.taxes[tax_group_detail.name] += tax_group_detail.calculated_tax;
                });
            } else if (line.tax_id) {
                if (!output.taxes[line.tax_name]) {
                    output.taxes[line.tax_name] = 0;
                }

                output.taxes[line.tax_name] += (line.tax_unformatted * line.quantity_uf);
            }

            if (line.tax_id && line.tax_percent == 0) {
                total_exempt += line.line_total_uf;
            }
            subtotal_exc_tax += line.line_total_exc_tax_uf;
            total_quantity += Number(line.quantity_uf);
            total_line_discount += (line.line_discount_uf * line.quantity_uf);
            total_line_taxes += (line.tax_unformatted * line.quantity_uf);
            if ((line.variation_id) && !unique_items.includes(line.variation_id)) {
                unique_items.push(line.variation_id);
            }
        });

        if (il.common_settings.total_quantity_label) {
            output.total_quantity_label = il.common_settings.total_quantity_label;
            
            output.total_quantity = total_quantity;
        }

        if (il.common_settings.total_items_label) {
            output.total_items_label = il.common_settings.total_items_label;
            output.total_items = unique_items.length;
        }
        
        output.subtotal_exc_tax = subtotal_exc_tax + output.currency_symbol;
        output.total_line_discount = total_line_discount ? total_line_discount + output.currency_symbol : 0;
        output.total_line_discount=__currency_trans_from_en(output.total_line_discount);
    }
    /*
    elseif($transaction_type == 'sell_return') {
        $parent_sell = Transaction:: find($transaction -> return_parent_id);
        $lines = $parent_sell -> sell_lines;

        foreach($lines as $key => $value) {
            if (!empty($value -> sub_unit_id)) {
                $formated_sell_line = $this -> recalculateSellLineTotals($business_details -> id, $value);

                $lines[$key] = $formated_sell_line;
            }
        }

        $details = $this -> _receiptDetailsSellReturnLines($lines, $il, $business_details);
        $output['lines'] = $details['lines'];

        $output['taxes'] = [];
        foreach($details['lines'] as $line) {
            if (!empty($line['group_tax_details'])) {
                foreach($line['group_tax_details'] as $tax_group_detail) {
                    if (!isset($output['taxes'][$tax_group_detail['name']])) {
                        $output['taxes'][$tax_group_detail['name']] = 0;
                    }
                    $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                }
            }
        }




    }
    */




    //show cat code
    output.show_cat_code = il.show_cat_code;
    output.cat_code_label = il.cat_code_label;

    //Subtotal
    output.subtotal_label = il.sub_total_label + ':';
    output.subtotal = (output.subtotal_exc_tax != 0) ? output.subtotal_exc_tax  : 0;
    //alert("==>"+output.subtotal );
    output.subtotal_unformatted = (output.final_total != 0) ? output.subtotal_exc_tax : 0;

    //round off
    output.round_off_label = (il.round_off_label) ? il.round_off_label + ':' : "Compléter" + ':';
    output.round_off = output.currency_symbol;
    output.round_off_amount = transaction.round_off_amount;
    output.total_exempt = output.currency_symbol;
    output.total_exempt_uf = total_exempt;

    taxed_subtotal = output.subtotal_unformatted - total_exempt;
    output.taxed_subtotal = output.currency_symbol;




    //Discount
    discount_amount = transaction.discount_amount;
    output.line_discount_label = il.discount_label;
    output.discount_label = il.discount_label;
    output.discount_label += (transaction.discount_type == 'percentage') ? ' <small>(' + transaction.discount_amount + output.currency_symbol + '%)</small> :' : '';

    if (transaction.discount_type == 'percentage') {
        discount = (transaction.discount_amount / 100) * transaction.total_before_tax;
    } else {
        discount = transaction.discount_amount;
    }
    output.discount = (discount != 0) ? discount + output.currency_symbol : 0;

    //reward points
    if (DATA.business.enable_rp == 1 && (transaction.rp_redeemed)) {
        output.reward_point_label = DATA.business.rp_name;
        output.reward_point_amount = transaction.rp_redeemed_amount + output.currency_symbol;
    }

    //Format tax
    if (output.taxes) {
        total_tax = 0;
        Object.values(output.taxes).forEach(function (tax, tax_key) {
            total_tax += tax;
            output.taxes[tax_key] = tax + output.currency_symbol;
        });
        output.taxes["Taxe total"] = total_tax + output.currency_symbol;


    }


    //Order Tax
    tax = transaction.tax;
    output.tax_label = il.tax_label;
    output.line_tax_label = il.tax_label;
    if ((tax) && (tax.name)) {
        output.tax_label += ' (' + tax.name + ')';
    }
    output.tax_label += ':';
    output.tax = (transaction.tax_amount != 0) ? transaction.tax_amount + output.currency_symbol : 0;






    /**************GESTION DES GROUPES DES TAXES********* */
    /*
    if (transaction.tax_amount != 0 && tax.is_tax_group) {
        transaction_group_tax_details = $this->groupTaxDetails($tax, $transaction->tax_amount);

        $output['group_tax_details'] = [];
        foreach ($transaction_group_tax_details as $value) {
            $output['group_tax_details'][$value['name']] = $this->num_f(
                $value['calculated_tax'],
                $show_currency,
                $business_details,
                false,
                $output['currency_symbol']
                
            );
        }
    }
    */

    //Shipping charges
    output.shipping_charges = (transaction.shipping_charges != 0) ? transaction.shipping_charges + output.currency_symbol : 0;
    output.shipping_charges_label = "Frais d'expédition";
    //Shipping details
    output.shipping_details = transaction.shipping_details;
    output.delivered_to = transaction.delivered_to;
    output.shipping_details_label = "Les détails d'expédition";
    output.packing_charge_label = "Frais autres services";
    output.packing_charge = (transaction.packing_charge != 0) ? transaction.packing_charge + output.currency_symbol : 0;

    //Total
    if (transaction_type == 'sell_return') {

        output.total_label = il.cn_amount_label + ':';
        output.total = transaction.final_total + output.currency_symbol;
    } else {
        output.total_label = il.total_label + ':';
        output.total = transaction.final_total + output.currency_symbol;
    }
    /********** NUM TO WORD  **************************************/
    /*
    if (il.common_settings.show_total_in_words) {
        word_format = isset($il->common_settings['num_to_word_format']) ? $il->common_settings['num_to_word_format'] : 'international';
        $output['total_in_words'] = $this->numToWord($transaction->final_total, null, $word_format);
    }
        */
    output.total_unformatted = transaction.final_total;



    //Paid & Amount due, only if final
    if (transaction_type == 'sell' && transaction.status == 'final') {
        console.log("***=>",sell.payload);
        paid_amount = getTotalPaid(sell.payload);
        /*
        //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
        //reconvertir de la devise du systeme vers la seconde devise de la transaction

        //dd($paid_amount);
        if (!empty($transaction -> second_currency)) {
            $paid_amount *= $transaction -> second_currency_rate;
        }
        //dd($paid_amount);
        //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////
        */
       //alert(paid_amount);
        due = transaction.final_total.replace(__currency_thousand_separator,'').replace(__currency_decimal_separator,'.') - ((paid_amount+" ").replace(__currency_thousand_separator,'').replace(__currency_decimal_separator,'.'));
        //alert(transaction.final_total+" - "+paid_amount+"="+due);

        output.total_paid = (paid_amount == 0) ? 0 : paid_amount + output.currency_symbol;
        output.total_paid_label = il.paid_label;
        output.total_due = (due == 0) ? 0 : due + output.currency_symbol;
        output.total_due_label = il.total_due_label;

        if (il.show_previous_bal == 1) {
            all_due = contact.due;

            /*
            all_due *= $transaction -> second_currency_rate;/// personnalize custom code 06022024-MULTIDEVISE030 /
            */

            if (all_due) {
                output.all_bal_label = il.prev_bal_label;
                output.all_due = all_due;
            }
        }

        //Get payment details
        console.log('sellsellsellsellsellsell',sell);
        output.payments = [];
        transaction.payment_lines = sell.payload.payment;
        console.log('transaction -> payment_lines', transaction);
        if (il.show_payments == 1) {
            payments = transaction.payment_lines;
            //payment_types = $this -> payment_types($transaction -> location_id, true);
            if (payments) {

                Object.values(payments).forEach(function (payment) {
                    method = (payment.method) ? payment.method : '';

                    //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
                    //reconvertir de la devise du systeme vers la seconde devise de la transaction
                    if ((location.attributes.second_currency)) {
                        //$value['amount'] *= $transaction->second_currency_rate;
                        //dd($value);
                        payment.amount *= transaction.second_currency_rate;
                    }
                    //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////

                    if(payment.amount){
                        if (payment.method == 'cash') {
                            output.payments.push(
                                {
                                    'method': method + (payment.is_return == 1 ? ' (' + il.change_return_label + ')(-)' : ''),
                                    'amount': payment.amount + output.currency_symbol,
                                    'date': __current_datetime(),
                                });
                            if (payment.is_return == 1) {
                            }
                        } else if (payment.method == 'card') {
                            output.payments.push(
                                {
                                    'method': method + (payment.card_transaction_number == 1 ? (', Transaction Number:' + payment.card_transaction_number) : ''),
                                    'amount': payment.amount + output.currency_symbol,
                                    'date': __current_datetime(),
                                });
    
                        }
                        else if (payment.method == 'cheque') {
                            output.payments.push(
                                {
                                    'method': method + (payment.cheque_number == 1 ? (', cheque_number Number:' + payment.cheque_number) : ''),
                                    'amount': payment.amount + output.currency_symbol,
                                    'date': __current_datetime(),
                                });
    
                        }
                        else if (payment.method == 'bank_transfer') {
                            output.payments.push(
                                {
                                    'method': method + (payment.bank_account_number == 1 ? (', Account Number:' + payment.bank_account_number) : ''),
                                    'amount': payment.amount + output.currency_symbol,
                                    'date': __current_datetime(),
                                });
    
                        }
                        else if (payment.method == 'advance') {
                            output.payments.push(
                                {
                                    'method': method,
                                    'amount': payment.amount + output.currency_symbol,
                                    'date': __current_datetime(),
                                });
    
                        }
                        else if (payment.method == 'other') {
                            output.payments.push(
                                {
                                    'method': method,
                                    'amount': payment.amount + output.currency_symbol,
                                    'date': __current_datetime(),
                                });
    
                        }
                    }
                    



                    i = 0;
                    for (i = 1; i < 8; i++) {
                        if (payment.method == "custom_pay_{" + i + "}") {
                            output.payments.push(
                                {
                                    'method': method + (payment.transaction_no == 1 ? (', Transaction Number:' + payment.transaction_no) : ''),
                                    'amount': payment.amount + output.currency_symbol,
                                    'date': __current_datetime(),
                                });

                        }
                    }
                });


            }
        }
        //dd($output);
    }









    return output;
}

function getTotalPaid(value) {
    var totalpaid = 0;
    Object.values(value.payment).forEach(function (payment) {
        totalpaid += (payment.amount) ? parseFloat(payment.amount) : 0;
    });
    return totalpaid;
}