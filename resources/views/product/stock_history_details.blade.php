@php
    $common_settings = session()->get('business.common_settings');
@endphp
<div class="row">
    <div class="col-md-12">
        <h4>{{ $stock_details['variation'] }}</h4>
    </div>
    <div class="col-md-4 col-xs-4">
        <strong>@lang('lang_v1.quantities_in')</strong>
        <table class="table table-condensed">
            <tr>
                <th>@lang('report.total_purchase')</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_purchase'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_purchase'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.opening_stock')</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_opening_stock'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_opening_stock'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.total_sell_return')</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_sell_return'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_sell_return'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.in'))</th>
                <td>

                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_purchase_transfer'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_purchase_transfer'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 col-xs-4">
        <strong>@lang('lang_v1.quantities_out')</strong>
        <table class="table table-condensed">
            <tr>
                <th>@lang('lang_v1.total_sold')</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_sold'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_sold'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif
                </td>

            </tr>
            <tr>
                <th>@lang('report.total_stock_adjustment')</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_adjusted'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_adjusted'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif


                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.total_purchase_return')</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_purchase_return'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_purchase_return'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif
                </td>
            </tr>

            <tr>
                <th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.out'))</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['total_sell_transfer'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['total_sell_transfer'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif

                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-4 col-xs-4">
        <strong>@lang('lang_v1.totals')</strong>
        <table class="table table-condensed">
            <tr>
                <th>@lang('report.current_stock')</th>
                <td>
                    @if ($show_by_box == 'true')
                        <span class="display_currency"
                            data-is_quantity="true">{{ (float) ($stock_details['current_stock'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                        {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                    @else
                        <span class="display_currency"
                            data-is_quantity="true">{{ $stock_details['current_stock'] }}</span>
                        {{ $stock_details['unit'] }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <hr>
        <table class="table table-slim" id="stock_history_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.type')</th>
                    <th>@lang('lang_v1.quantity_change')</th>
                    @if (!empty($common_settings['enable_secondary_unit']))
                        <th>@lang('lang_v1.quantity_change') (@lang('lang_v1.secondary_unit'))</th>
                    @endif
                    <th>@lang('lang_v1.new_quantity')</th>
                    @if (!empty($common_settings['enable_secondary_unit']))
                        <th>@lang('lang_v1.new_quantity') (@lang('lang_v1.secondary_unit'))</th>
                    @endif
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('purchase.ref_no')</th>
                    <th>@lang('lang_v1.customer_supplier_info')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stock_history as $history)
                    {{-- custom_code 07062024 ---}}
					{{-- Surbrillance des stock d'inventaire --}}
					@php
                        $from_inventory_attribute = '';
                        if (
                            str_contains($history['additional_notes'] , 'INV-') &&
                            str_contains($history['additional_notes'] , ': INVENTORY BY') &&
                            str_contains($history['additional_notes'] , '[ID:')
                        ) {
                            $from_inventory_attribute = 'yellow';
							$history['type_label']="**Inventaire";
                        }
                    @endphp
					{{--------------------------------------------}}
                    <tr style="background-color: {{$from_inventory_attribute}}">
                        <td>{{ $history['type_label'] }}</td>
                        @if ($history['quantity_change'] > 0)
                            @if ($show_by_box == 'true')
                                <td class="text-success"> +<span class="display_currency"
                                        data-is_quantity="true">{{ (float) ($history['quantity_change'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                                    {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}</span>
                                </td>
                            @else
                                <td class="text-success"> +<span class="display_currency"
                                        data-is_quantity="true">{{ $history['quantity_change'] }}</span>
                                    {{ $stock_details['unit'] }}
                                </td>
                            @endif
                        @else
                            @if ($show_by_box == 'true')
                                <td class="text-danger"><span class="display_currency text-danger"
                                        data-is_quantity="true">{{ (float) ($history['quantity_change'] / array_reverse($sub_units)[0]['multiplier']) }}</span>
                                    {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                                </td>
                            @else
                                <td class="text-danger"><span class="display_currency text-danger"
                                        data-is_quantity="true">{{ $history['quantity_change'] }}</span>
                                    {{ $stock_details['unit'] }}
                                </td>
                            @endif
                        @endif

                        @if (!empty($common_settings['enable_secondary_unit']))
                            @if ($history['quantity_change'] > 0)
                                <td class="text-success">
                                    @if (!empty($history['purchase_secondary_unit_quantity']))
                                        +<span class="display_currency"
                                            data-is_quantity="true">{{ $history['purchase_secondary_unit_quantity'] }}</span>
                                        {{ $stock_details['second_unit'] }}
                                    @endif
                                </td>
                            @else
                                <td class="text-danger">
                                    @if (!empty($history['sell_secondary_unit_quantity']))
                                        -<span class="display_currency"
                                            data-is_quantity="true">{{ $history['sell_secondary_unit_quantity'] }}</span>
                                        {{ $stock_details['second_unit'] }}
                                    @endif
                                </td>
                            @endif
                        @endif


                        @if ($show_by_box == 'true')
                            <td>
                                <span class="display_currency"
                                    data-is_quantity="true">{{ $history['stock'] / array_reverse($sub_units)[0]['multiplier'] }}</span>
                                {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                            </td>
                        @else
                            <td>
                                <span class="display_currency" data-is_quantity="true">{{ $history['stock'] }}</span>
                                {{ $stock_details['unit'] }}
                            </td>
                        @endif

                        @if (!empty($common_settings['enable_secondary_unit']))
                            <td>
                                @if (!empty($stock_details['second_unit']))
                                    <span class="display_currency"
                                        data-is_quantity="true">{{ $history['stock_in_second_unit'] }}</span>
                                    {{ $stock_details['second_unit'] }}
                                @endif
                            </td>
                        @endif
                        <td>{{ @format_datetime($history['date']) }}</td>
                        <td>
                            {{ $history['ref_no'] }}

                            @if (!empty($history['additional_notes']))
                                @if (!empty($history['ref_no']))
                                    <br>
                                @endif
                                {{-- - custom - --}}
                                <p><abbr title="{{ $history['additional_notes'] }}" class="initialism">**Voir
                                        note</abbr></p>
                                {{-- - endcustom - --}}
                            @endif
                        </td>
                        <td>
                            {{ $history['contact_name'] ?? '--' }}
                            @if (!empty($history['supplier_business_name']))
                                - {{ $history['supplier_business_name'] }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @lang('lang_v1.no_stock_history_found')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
