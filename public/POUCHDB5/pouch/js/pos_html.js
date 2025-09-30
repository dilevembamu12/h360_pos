return true;

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







// function creation
let interval = setInterval(function () {
	if (Object.keys(DATA).length > 0) {
		clearInterval(interval);

		var html = get_html_body();
		$(document).ready(function () {
			//console.log('html', html);
			//$('#pos_html').replaceWith(html);
		});
	}
}, 500);

function get_html_body() {
	var can_close_cash_register = false;
	var can_view_cash_register = false;
	var can_expense_add = false;
	Object.values(DATA.user.roles[0].permissions).forEach(function (permission, permission_key) {
		//console.log(permission_key,permission);
		if (permission.name == 'close_cash_register' || DATA.user.roles[0].name.split('#')[0] == "Admin") {
			can_close_cash_register = true;
		}
		if (permission.name == 'view_cash_register' || DATA.user.roles[0].name.split('#')[0] == "Admin") {
			can_view_cash_register = true;
		}
		if (permission.name == 'expense.add' || DATA.user.roles[0].name.split('#')[0] == "Admin") {
			can_expense_add = true;
		}
	});

	html = '';
	html +=
		`<input type="hidden" name="transaction_sub_type" id="transaction_sub_type" value="${transaction_sub_type}">

<div class="col-md-12 no-print pos-header">
<input type="hidden" id="pos_redirect_url" value="${pos_redirect_url}">
<div class="row">
	<div class="col-md-6">
		<div class="m-6 mt-5" style="display: flex;">
			<p><strong>@lang('sale.location'): &nbsp;</strong>`;

	if (Object.keys(DATA.businesslocations).length > 1) {
		html += `<div style="width: 28%;margin-bottom: 5px;">
		<select class="form-control input-sm" id="select_location_id" required="" autofocus=""
									name="select_location_id">`;

		businesslocations.forEach(function (location, index) {
			alert(location.name+"===>");
			html += `<option value="1" selected="selected" data-receipt_printer_type="` + location.attributes['data-receipt_printer_type'] + `"
										data-default_payment_accounts="`+ location.attributes['data-default_payment_accounts'] + `"
										data-default_sale_invoice_scheme_id="`+ location.attributes['data-default_sale_invoice_scheme_id'] + `"
										data-default_invoice_scheme_id="`+ location.attributes['data-default_invoice_scheme_id'] + `"
										data-default_invoice_layout_id="`+ location.attributes['data-default_invoice_layout_id'] + `"
										second_currency="2"
										second_currency_rate="1.000000000000"
										second_currency_code="USD"
										second_currency_symbol="$">${location.name} (${location.location_id})</option>`;
		});

		html += `</select></div>`;

	} else {
		html += businesslocations[0].name;
	}


	html += `<span class="curr_datetime"> ${__current_datetime()}</span>
				<i class="fa fa-keyboard hover-q text-muted" aria-hidden="true" data-container="body"
					data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')"
					data-html="true" data-trigger="hover" data-original-title="" title=""></i>
			</p>
		</div>
	</div>
	<div class="col-md-6">
		<a href="${go_back_url}" title="{{ __('lang_v1.go_back') }}"
			class="btn btn-info btn-flat m-6 btn-xs m-5 pull-right">
			<strong><i class="fa fa-backward fa-lg"></i></strong>
		</a>
		${DATA.business.pos_settings && DATA.business.pos_settings.inline_service_staff ?
			'<button not-available=1 style="display:none" type="button" id="show_service_staff_availability" title="{{ __(\'lang_v1.service_staff_availability\') }}" class="btn btn-primary btn-flat m-6 btn-xs m-5 pull-right" data-container=".view_modal" data-href="{{ action([\App\Http\Controllers\SellPosController::class, \'showServiceStaffAvailibility\']) }}"> <strong><i class="fa fa-users fa-lg"></i></strong> </button>' : ''}
		
			
	${can_close_cash_register ? '<button type="button" id="close_register" title="{{ __(\'cash_register.close_register\') }}" class="btn btn-danger btn-flat m-6 btn-xs m-5 btn-modal pull-right" data-container=".close_register_modal" data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, \'getCloseRegister\']) }}"><strong><i class="fa fa-window-close fa-lg"></i></strong></button>' : ''}

	${can_view_cash_register ? '<button type="button" id="register_details" title="{{ __(\'cash_register.register_details\') }}"class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right"data-container=".register_details_modal"data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, \'getRegisterDetails\']) }}"><strong><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></strong></button>' : ''}


		<button title="**Calculatrice" id="btnCalculator" type="button"
			class="btn btn-success btn-flat pull-right m-5 btn-xs mt-10 popover-default" data-toggle="popover"
			data-trigger="click" data-content='@include('layouts.partials.calculator')' data-html="true"
			data-placement="bottom">
			<strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
		</button>
		

		<button type="button" title="{{ __('lang_v1.full_screen') }}"
			class="btn btn-primary btn-flat m-6 hidden-xs btn-xs m-5 pull-right" id="full_screen">
			<strong><i class="fa fa-window-maximize fa-lg"></i></strong>
		</button>

		<button type="button" id="view_suspended_sales" title="{{ __('lang_v1.view_suspended_sales') }}"
			class="btn bg-yellow btn-flat m-6 btn-xs m-5 btn-modal pull-right" data-container=".view_modal"
			data-href="{{ $view_suspended_sell_url }}">
			<strong><i class="fa fa-pause-circle fa-lg"></i></strong>
		</button>
		
		${DATA.business.pos_settings && DATA.business.pos_settings.hide_product_suggestion && isMobile() ?
			'<button type="button" title="__(\'lang_v1.view_products\')" data-placement="bottom" class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right" data-toggle="modal" data-target="#mobile_product_suggestion_modal"><strong><i class="fa fa-cubes fa-lg"></i></strong></button>' : ''}
		
		${can_expense_add ?
			'<button type="button" title="{{ __(\'expense.add_expense\') }}" data-placement="bottom"class="btn bg-purple btn-flat m-6 btn-xs m-5 btn-modal pull-right" id="add_expense"><strong><i class="fa fas fa-minus-circle"></i> @lang(\'expense.add_expense\')</strong></button>' : ''}
		
	

	</div>

</div>
</div>
`;

	html += `<input type="hidden" id="__is_localhost" value="true"></input>

<input type="hidden" id="__business_id" value="${DATA.business.id}">

<input type="hidden" id="__code" value="${DATA.business.currency_code}">
<input type="hidden" id="__symbol" value="${DATA.business.currency_symbol}">
<input type="hidden" id="__thousand" value="${DATA.business.thousand_separator}">
<input type="hidden" id="__decimal" value="${DATA.business.decimal_separator}">
<input type="hidden" id="__symbol_placement" value="${DATA.business.currency_symbol_placement}">
<input type="hidden" id="__precision" value="${DATA.business.currency_precision ?? 3}">
<input type="hidden" id="__quantity_precision" value="${DATA.business.quantity_precision ?? 3}">
${isMobile() ? '<input type="hidden" id="__is_mobile">' : ''}`;

	return html;
}


