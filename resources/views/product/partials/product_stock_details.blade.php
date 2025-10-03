<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-condensed bg-gray">
                <thead>
                    <tr class="bg-green">
                        <th>SKU</th>
                        <th>@lang('business.product')</th>
                        <th>@lang('business.location')</th>
                        <th>@lang('sale.unit_price')</th>
                        <th>@lang('report.current_stock')</th>
                        <th>@lang('lang_v1.total_stock_price')</th>
                        <th>@lang('report.total_unit_sold')</th>
                        <th>@lang('lang_v1.total_unit_transfered')</th>
                        <th>@lang('lang_v1.total_unit_adjusted')</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ------ custom code --- --}}
                    @php
                        $stock = 0;
                        $stock_value = 0;
                        $total_sold = 0;
                        $total_transfered = 0;
                        $total_adjusted = 0;
                    @endphp
                    @foreach ($product_stock_details as $product)
                        @php
                            
                            $sub_units = json_decode(base64_decode($product->subunits), true);
                            //dd($sub_units);
                            foreach ($sub_units as $sub_unit_key => $sub_unit) {
                                if ($sub_unit_key == $product->box_unit_id) {
                                    $sub_units = [];
                                    $sub_units[$sub_unit_key] = $sub_unit;
                                    break;
                                }
                            }
                        @endphp

                        @php
                            if ($show_by_box == 'true') {
                                $stock += $product->stock / array_reverse($sub_units)[0]['multiplier'];
                                $total_sold += $product->total_sold / array_reverse($sub_units)[0]['multiplier'];
                                $total_transfered += $product->total_transfered / array_reverse($sub_units)[0]['multiplier'];
                                $total_adjusted += $product->total_adjusted / array_reverse($sub_units)[0]['multiplier'];
                            } else {
                                $stock += $product->stock;
                                $total_sold += $product->total_sold;
                                $total_transfered += $product->total_transfered;
                                $total_adjusted += $product->total_adjusted;
                            }
                            $stock_value += $product->unit_price * $product->stock;
                        @endphp
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>
                                @php
                                    $name = $product->product;
                                    if ($product->type == 'variable') {
                                        $name .= ' - ' . $product->product_variation . '-' . $product->variation_name;
                                    }
                                @endphp
                                {{ $name }}
                            </td>
                            <td>{{ $product->location_name }}</td>
                            <td>
                                @if ($show_by_box == 'true')
                                    <span class="display_currency"
                                        data-currency_symbol=true>{{ $product->unit_price * array_reverse($sub_units)[0]['multiplier'] ?? 0 }}
                                    </span>
                                @else
                                    <span
                                        class="display_currency"data-currency_symbol=true>{{ $product->unit_price ?? 0 }}</span>
                                @endif


                            </td>
                            <td>
                                @if ($show_by_box == 'true')
                                    <span class="display_currency"
                                        data-is_quantity="true">{{ $product->stock / array_reverse($sub_units)[0]['multiplier'] ?? 0 }}
                                    </span>
                                    {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                                @else
                                    <span data-is_quantity="true" class="display_currency"data-currency_symbol=false>
                                        {{ $product->stock ?? 0 }}</span>{{ $product->unit }}
                                @endif

                            </td>
                            <td>

                                <span
                                    class="display_currency"data-currency_symbol=true>{{ $product->unit_price * $product->stock }}</span>
                            </td>
                            <td>
                                @if ($show_by_box == 'true')
                                    <span data-is_quantity="true"
                                        class="display_currency"data-currency_symbol=false>{{ $product->total_sold / array_reverse($sub_units)[0]['multiplier'] ?? 0 }}
                                    </span>
                                    {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                                @else
                                    <span data-is_quantity="true"
                                        class="display_currency"data-currency_symbol=false>{{ $product->total_sold ?? 0 }}</span>{{ $product->unit }}
                                @endif



                            </td>
                            <td>
                                @if ($show_by_box == 'true')
                                    <span data-is_quantity="true"
                                        class="display_currency"data-currency_symbol=false>{{ $product->total_transfered / array_reverse($sub_units)[0]['multiplier'] ?? 0 }}
                                    </span>
                                    {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                                @else
                                    <span data-is_quantity="true"
                                        class="display_currency"data-currency_symbol=false>{{ $product->total_transfered ?? 0 }}</span>{{ $product->unit }}
                                @endif
                            </td>
                            <td>
                                @if ($show_by_box == 'true')
                                    <span data-is_quantity="true"
                                        class="display_currency"data-currency_symbol=false>{{ $product->total_adjusted / array_reverse($sub_units)[0]['multiplier'] ?? 0 }}
                                    </span>
                                    {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                                @else
                                    <span data-is_quantity="true"
                                        class="display_currency"data-currency_symbol=false>{{ $product->total_adjusted ?? 0 }}</span>{{ $product->unit }}
                                @endif



                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4">**TOTAL(equlibre:{{$stock+$total_sold+$total_adjusted}}
							@if ($show_by_box == 'true')
                                {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                            @else
                                {{ $product->unit }}
                            @endif
						)
						</td>
                        <td>
                            <span data-is_quantity="true" class="display_currency"data-currency_symbol=false>
                                {{ $stock ?? 0 }}</span>
                            @if ($show_by_box == 'true')
                                {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                            @else
                                {{ $product->unit }}
                            @endif

                        </td>
						<td>
                            <span class="display_currency" data-currency_symbol="true">
                                {{ $stock_value ?? 0 }}</span>

                        </td>
						
						<td>
                            <span data-is_quantity="true" class="display_currency"data-currency_symbol=false>
                                {{ $total_sold ?? 0 }}</span>
                            @if ($show_by_box == 'true')
                                {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                            @else
                                {{ $product->unit }}
                            @endif

                        </td>
						<td>
                            <span data-is_quantity="true" class="display_currency"data-currency_symbol=false>
                                {{ $total_transfered ?? 0 }}</span>
                            @if ($show_by_box == 'true')
                                {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                            @else
                                {{ $product->unit }}
                            @endif

                        </td>
						<td>
                            <span data-is_quantity="true" class="display_currency"data-currency_symbol=false>
                                {{ $total_adjusted ?? 0 }}</span>
                            @if ($show_by_box == 'true')
                                {{ explode(' ', array_reverse($sub_units)[0]['name'])[0] }}
                            @else
                                {{ $product->unit }}
                            @endif

                        </td>
                    </tr>
                    {{-- ---------------------- --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
