<style>
    .box-header .box-title,
    {
    display: inline-block;
    font-size: 78px;
    margin: 0;
    line-height: 1;
    color: blue
    }
</style>

@php
    //dd($data->original);
    //dd($detailled_business_locations);
    //dd($detailled_business_locations[0]);
    //dd($data->original['data']);

    $my_unit = '';
    $amount_net_by_invoice = []; //enregistre toutes les dettes
    foreach ($data->original['data'] as $key => $item) {
        //dd($item);

        if (!is_null($item['invoice_no'])) {
            //echo $item['invoice_no']."|||";
            $amount_net_by_invoice[$item['location_name'] . '::' . $item['location_id']][$item['invoice_no']] = [
                'invoice_final_total' => 0, // $item['invoice_final_total'],
                'invoice_total_due' => 0, //, $item['invoice_total_due'],
                'discount_amount_by_invoice' => 0, // $item['discount_amount_by_invoice'],

                //'_total_invoice' => $item['_total_invoice'],
                //'_invoice_paid' => $item['_invoice_paid'],
            ];
            //dd($item['invoice_no']);
        }
    }
    //dd($amount_net_by_invoice);

    //je classe par raaport aux depots
    $formatted_array = [];
    foreach ($data->original['data'] as $key => $item) {
        $product_name = $item['product_name'];

        if (!empty($item['variation'])) {
            $product_name .= ' (' . explode('-', $item['variation'])[1] . ')';
        }

        $category;

        if (!isset($formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['brand_name']][$product_name])) {
            if (empty($item['location_name']) && empty($item['location_id'])) {
                $item['location_name'] = 'NOT DEFINI';
                $item['location_id'] = '00';
            }

            //$formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['brand_name']]=[$product_name=>[]];
            $formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['brand_name']][$product_name] = $item;
        } else {
            //dd(212345678);
            /*
             */

            $arrayB = $formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['brand_name']][$product_name];
            $arrayA = $item;

            //dd($arrayB);

            $sums_array = [];
            $no_calc = array_keys(array_diff_key($item, array_flip(['subtotal', 'invoice_total_due', 'sell_qty', 'unit_price', 'unit_sale_price'])));
            //dd($no_calc);
            foreach (array_keys($arrayA + $arrayB) as $item_key) {
                if (in_array($item_key, $no_calc)) {
                    $sums_array[$item_key] = $arrayA[$item_key];
                    continue;
                }
                $sums_array[$item_key] = (isset($arrayA[$item_key]) ? $arrayA[$item_key] : 0) + (isset($arrayB) ? $arrayB[$item_key] : 0);
            }
            $formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['brand_name']][$product_name] = $sums_array;

            //dd($sums_array);
        }
        //dd($formatted_array);

        //dd($formatted_array);
    }
    //dd($formatted_array);
    //dd($formatted_array['NOT DEFINI::00']);
    $formatted_array_all_product = [];
    foreach ($formatted_array as $key => $value) {
        foreach ($value as $key2 => $value2) {
            foreach ($value2 as $key3 => $value3) {
                $formatted_array_all_product[$key3] = 0;
            }
        }
    }

    /****custom code personnalize 05022023 : utiliser pour preparer la transition , les canopy sont reperés de maniere automatique**/
    $deposit_with_canopy = [];
    //dd($detailled_business_locations);
    //je prends d'abord en charge les codes passés de 2023 pour eviter les cassures brusque (gerer jusqu'à Mars )
    foreach ($detailled_business_locations as $key => $value) {
        //reonnaitre si c'est un depot central
    if ($value->custom_field1 == 1) {
        //gestion ancien code (2023)
        //si c'est un depot ayant un canopie
            $deposit_with_canopy[$value->name] = ['id' => $value->id, 'is_deposit_with_canopy' => true, 'canopy_ids' => explode(',', $value->custom_field2)];
        } else {
            //reconnaitre si c'est un depot central
        if ($value->is_deposit == 1) {
            //gestion nouveau code (05022024)
            //dd($value->canopy_ids);
            $deposit_with_canopy[$value->name] = ['id' => $value->id, 'is_deposit_with_canopy' => true, 'canopy_ids' => $value->canopy_ids->toArray()];
            //dd($deposit_with_canopy[$value->name]);
        }
    }
}
//dd($deposit_with_canopy);
/*************************************************************/
/****custom code personnalize , 2023 (n'est plus utilisé) **/
    /*
    $deposit_with_canopy = [];
    dd($detailled_business_locations);
    foreach ($detailled_business_locations as $key => $value) {
        if ($value->custom_field1 == 1) {
            //si c'est un depot ayant un canopie
        $deposit_with_canopy[$value->name] = ['id' => $value->id, 'is_deposit_with_canopy' => true, 'canopy_ids' => explode(',', $value->custom_field2)];
        }
    }
    */
    //dd($deposit_with_canopy);
    /*************************************************************/

    /*************************--------------------*************************/
@endphp

@php
    //dd($deposit_with_canopy);
    $resume_all_by_money = []; //le resumé de tout
    $resume_all_by_sell_qty = []; //le resumé de tout
    $resume_all_by_stay_qty = []; //le resumé de tout
@endphp

@foreach ($deposit_with_canopy as $key => $item)
    @php
        $resume_all_by_money[$key . '::' . $item['id']] = 1;
        $resume_all_by_sell_qty[$key . '::' . $item['id']] = 1;
        $resume_all_by_stay_qty[$key . '::' . $item['id']] = 1;
    @endphp
@endforeach

@php
    //dd($data);
@endphp
@php
    $whole_locations_money = []; //pour tous le congo
    $whole_locations_stock = []; //pour tous le congo
    $whole_locations_stay_stock = []; //pour tous le congo
@endphp
@php
//dd(5512);
//dd($deposit_with_canopy);
@endphp
@foreach ($deposit_with_canopy as $key => $item)
    
    @component('components.widget', [
        'title' => "$key",
        'class' => 'box-primary',
        'style' => 'color:red !important;font-size: 78px !important;',
        'id' => $key,
    ])
        <div class="row" @if (Request::get('view_mode') != 1) style="display:none" @endif>
            <h1>{{ $key }}</h1>

            @php
                $tout_canopy_stock = 0;
                $tout_canopy_subtotal = 0;
                $tout_canopy_unit_price = 0;

                $canopy_money_array = [];
                $canopy_sell_qty_array = [];
                $canopy_stay_qty_array = [];
            @endphp

            @foreach ($item['canopy_ids'] as $key2 => $item2)
                @foreach ($formatted_array as $key3 => $item3)
                    @if (explode('::', $key3)[1] == $item2)
                        {{-- cest ca --}}



                        @if (!empty($key3))
                            @php
                                $canopy_money_array[explode('::', $key3)[0]] = $formatted_array_all_product; //rajoute les dsd canopy
                                $canopy_sell_qty_array[explode('::', $key3)[0]] = $formatted_array_all_product;
                                $canopy_stay_qty_array[explode('::', $key3)[0]] = $formatted_array_all_product;

                                //dd($canopy_money_array);
                            @endphp

                            <div class="col-md-6 col-sm-6 col-xs-12 responsive-table" style="overflow-x:auto;">
                                <table class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr class="bg-red text-white">
                                            <td colspan="5"></td>
                                        </tr>
                                        <tr class="bg-red text-white">
                                            <td colspan="5">
                                                <h1><strong>{{ $key3 }}</strong></h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('business.product')</th>
                                            <th>STOCK RETOUR</th>
                                            <th>VENTE</th>
                                            {{-- <th>P.U</th> --}}
                                            <th>MONTANT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php

                                            $tout_stock = 0;
                                            $tout_sell_qty = 0;
                                            $tout_subtotal = 0;
                                            $tout_subtotal_net = 0;
                                            $tout_unit_price = 0;
                                            $tout_total_unit_price = 0;
                                        @endphp
                                        @foreach ($item3 as $key4 => $item4)
                                            @php
                                                $stock = 0;
                                                $sell_qty = 0;
                                                $subtotal = 0;
                                                $unit_price = 0;
                                                $total_unit_price = 0;

                                            @endphp
                                            @foreach ($item4 as $key5 => $item5)
                                                @php
                                                    //dd($item5)
                                                @endphp
                                                <tr>
                                                    <th>{{ $key5 }}</th>
                                                    <th
                                                        style="color:{{ $item5['stock_history_available'] == 0 ? 'red' : '' }}  {{ $item5['unit_allow_decimal'] == 0 && fmod($item5['stock_history_available'], 1) !== 0.0 ? '#985c1a' : '' }}">
                                                        {{ number_format($item5['stock_history_available'], 7, '.', '') }}
                                                    </th>
                                                    <th
                                                        style="color:{{ $item5['sell_qty'] == 0 ? 'red' : '' }} {{ $item5['unit_allow_decimal'] == 0 && fmod($item5['sell_qty'], 1) !== 0.0 ? '#985c1a' : '' }}">
                                                        {{ number_format($item5['sell_qty'], 7, '.', '') }}</th>
                                                    {{-- <th>{{ $item5['unit_price'] }}</th> --}}
                                                    <th><span class="display_currency" data-currency_symbol=true>
                                                            {{ number_format((float) $item5['subtotal'], 7, '.', '') }}</span>
                                                    </th>
                                                </tr>
                                                @php
                                                    //dd($item5['unit_allow_decimal']);
                                                    //dd(base64_decode($item5['subunits']));

                                                    $stock += number_format($item5['stock_history_available'], 7, '.', '');
                                                    $sell_qty += number_format($item5['sell_qty'], 7, '.', '');
                                                    $subtotal += number_format($item5['subtotal'], 7, '.', '');
                                                    $unit_price = number_format((float) $item5['unit_price'], 7, '.', '');
                                                    $total_unit_price += number_format((float) $item5['subtotal'], 7, '.', '');

                                                    $canopy_stay_qty_array[explode('::', $key3)[0]][$key5] = number_format($item5['stock_history_available'], 7, '.', ''); //quantité stock
                                                    $canopy_sell_qty_array[explode('::', $key3)[0]][$key5] = number_format($item5['sell_qty'], 7, '.', ''); //quantité stock
                                                    $canopy_money_array[explode('::', $key3)[0]][$key5] = number_format($item5['subtotal'], 7, '.', '');

                                                    $my_unit = $item5['unit'];

                                                @endphp
                                            @endforeach
                                            <tr class="bg-gray font-17 text-center footer-total">
                                                <th>TOTAL {{ $key4 }}</th>
                                                <th>{{ $stock }} </th>
                                                <th>{{ $sell_qty }} </th>
                                                {{-- <th>--</th> --}}
                                                <th><span class="display_currency"
                                                        data-currency_symbol=true>{{ $total_unit_price }}</span></th>
                                            </tr>

                                            @php
                                                $tout_stock += $stock;
                                                $tout_sell_qty += $sell_qty;
                                                $tout_subtotal += $subtotal;
                                                $tout_total_unit_price += $total_unit_price;
                                                $tout_unit_price = $unit_price;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        @php
                                            //recherche des discount
                                            $discount_amount_by_invoice = 0;
                                            $invoice_total_due = 0;
                                            $invoice_final_total = 0; //montant arrondi
                                            $arrondissement_difference = 0;

                                            /*------------
                                            foreach ($amount_net_by_invoice[$key3] as $key10 => $value10) {
                                                $discount_amount_by_invoice += $value10['discount_amount_by_invoice'];
                                                $invoice_total_due += $value10['invoice_total_due'];
                                                $invoice_final_total += $value10['invoice_final_total'];
                                            }
                                            */

                                            /*
                                            $arrondissement_difference = $invoice_final_total - $tout_total_unit_price; //differnece de l'arrondissement, on le fait avec le total bien avant d'appliquer le discount ou la dette
                                            $arrondissement_difference = number_format($arrondissement_difference, 7, '.', '');
                                            //$arrondissement_difference=0;
                                            $tout_subtotal_net = number_format($tout_total_unit_price - $invoice_total_due - $discount_amount_by_invoice, 7, '.', '');
                                            $tout_subtotal_net = number_format($tout_subtotal_net + $arrondissement_difference, 7, '.', ''); //paiement final
                                            //$arrondissement_difference=$invoice_final_total-$tout_total_unit_price;
                                            */
                                            $tout_subtotal_net = $tout_total_unit_price - $invoice_total_due - $discount_amount_by_invoice;
                                            //dd($tout_subtotal_net);
                                            $arrondissement_difference = $invoice_final_total - $tout_total_unit_price + $discount_amount_by_invoice; //differnece de l'arrondissement, on le fait avec le total bien avant d'appliquer le discount ou la dette
                                            $arrondissement_difference = number_format($arrondissement_difference, 7, '.', '');
                                            $arrondissement_difference = 0; //<<<<===

                                            $tout_subtotal_net = $tout_subtotal_net + $arrondissement_difference; //paiement final

                                        @endphp


                                        <tr class="bg-warning font-17 footer-total">
                                            <td colspan="1"><strong>@lang('sale.total') BRUTE:</strong></td>
                                            <td class="footer_total_stock"
                                                style="color:{{ $tout_stock == 0 ? 'red' : '' }} {{ $item5['unit_allow_decimal'] == 0 && fmod($tout_stock, 1) !== 0.0 ? '#985c1a' : '' }}">
                                                <strong>{{ $tout_stock }} {{ $item5['unit'] }}</strong>
                                            </td>
                                            <td class="footer_subtotal"
                                                style="color:{{ $tout_sell_qty == 0 ? 'red' : '' }} {{ $item5['unit_allow_decimal'] == 0 && fmod($tout_sell_qty, 1) !== 0.0 ? '#985c1a' : '' }}">
                                                <strong>{{ $tout_sell_qty }} {{ $item5['unit'] }}</strong>
                                            </td>
                                            {{-- <td class="footer_stock">
                                                <strong>--</strong></td> --}}
                                            <td class="footer_stock">
                                                <strong><span class="display_currency"
                                                        data-currency_symbol=true>{{ $tout_total_unit_price }}</span></strong>
                                            </td>
                                        </tr>

                                        <tr class="bg-warning font-17 footer-total">
                                            <td colspan="3"><strong>@lang('sale.total') DISCOUNT:</strong></td>

                                            <td class="footer_stock">
                                                <strong><span class="display_currency"
                                                        style="color:{{ $discount_amount_by_invoice > 0 ? 'red' : '' }}"
                                                        data-currency_symbol=true>-{{ $discount_amount_by_invoice }}</span></strong>
                                            </td>
                                        </tr>
                                        <tr class="bg-warning font-17 footer-total">
                                            <td colspan="3"><strong>@lang('sale.total') DETTE:</strong></td>

                                            {{-- <td class="footer_stock">
                                                <strong>--</strong></td> --}}
                                            <td class="footer_stock">
                                                <strong><span class="display_currency"
                                                        style="color:{{ $invoice_total_due > 0 ? 'red' : '' }}"
                                                        data-currency_symbol=true>-{{ $invoice_total_due }}</span></strong>
                                            </td>
                                        </tr>
                                        <tr class="bg-warning font-17 footer-total">
                                            <td colspan="3"><strong>Arrondissement Facture:</strong></td>

                                            {{-- <td class="footer_stock">
                                                <strong>--</strong></td> --}}
                                            <td class="footer_stock">
                                                <strong><span
                                                        style="color:{{ $arrondissement_difference >= 0 ? 'green' : 'red' }}">{{ $arrondissement_difference > 0 ? '+' : '' }}{{ $arrondissement_difference < 0 ? '' : '' }}</span><span
                                                        class="display_currency"
                                                        style="color:{{ $arrondissement_difference >= 0 ? 'green' : 'red' }}"
                                                        data-currency_symbol=true>{{ $arrondissement_difference }}</span></strong>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray font-17 footer-total">
                                            <td colspan="1"><strong>@lang('sale.total') NET:</strong></td>
                                            <td class="footer_total_stock"
                                                style="color:{{ $tout_stock == 0 ? 'red' : '' }} {{ $item5['unit_allow_decimal'] == 0 && fmod($tout_stock, 1) !== 0.0 ? '#985c1a' : '' }}">
                                                <strong>{{ $tout_stock }} {{ $item5['unit'] }}</strong>
                                            </td>
                                            <td class="footer_subtotal"
                                                style="color:{{ $tout_sell_qty == 0 ? 'red' : 'black' }} {{ $item5['unit_allow_decimal'] == 0 && fmod($tout_sell_qty, 1) !== 0.0 ? '#985c1a' : '' }}">
                                                <strong>{{ $tout_sell_qty }} {{ $item5['unit'] }}</strong>
                                            </td>
                                            {{-- <td class="footer_stock">
                                                <strong>--</strong></td> --}}
                                            <td>
                                                <strong><span class="display_currency"
                                                        data-currency_symbol=true>{{ $tout_subtotal_net }}</span></strong>
                                            </td>
                                        </tr>
                                    </tfoot>

                                </table>
                            </div>
                        @endif
                    @endif
                @endforeach
            @endforeach

        </div>

        @php
            //dd($canopy_money_array);

            $canopy_money_array_title_for_datatable = [];
            $canopy_money_array_stock_data_for_datatable = [];
            $canopy_money_array_money_data_for_datatable = [];
            $canopy_money_array_title_for_datatable[] = ['title' => '####'];

            $canopy_stay_qty_array_title_for_datatable[] = ['title' => '####'];
            $canopy_stay_qty_array_stock_data_for_datatable = [];
            $canopy_sell_qty_array_title_for_datatable[] = ['title' => '####'];
            $canopy_sell_qty_array_stock_data_for_datatable = [];

            foreach ($canopy_money_array as $_key => $_value) {
                foreach ($_value as $key2 => $value2) {
                    $canopy_money_array_title_for_datatable[] = ['title' => $key2];
                    $canopy_sell_qty_array_title_for_datatable[] = ['title' => $key2];
                    $canopy_stay_qty_array_title_for_datatable[] = ['title' => $key2];
                }
            }
            $canopy_money_array_title_for_datatable[] = ['title' => 'TOTAL'];
            $canopy_sell_qty_array_title_for_datatable[] = ['title' => 'TOTAL'];
            $canopy_stay_qty_array_title_for_datatable[] = ['title' => 'TOTAL'];

            //on enleve les elements dupliqués
            $_data = [];
            foreach ($canopy_money_array_title_for_datatable as $v) {
                if (isset($_data[$v['title']])) {
                    // found duplicate
                    continue;
                }
                // remember unique item
                $_data[$v['title']] = $v;
            }
            $_data2 = [];
            foreach ($_data as $_key => $_value) {
                $_data2[] = $_value;
            }
            $canopy_money_array_title_for_datatable = $_data2;
            $canopy_sell_qty_array_title_for_datatable = $_data2;
            $canopy_stay_qty_array_title_for_datatable = $_data2;

            //dd($canopy_money_array_title_for_datatable);
            $v = [];
            $w[] = $key; //pour tous le congo
            /*
            foreach ($canopy_money_array_title_for_datatable as $key => $value) {
                foreach ($canopy_money_array as $key => $value) {
                    if (!empty($key)) {
                        $v[] = $key;
                        foreach ($value as $key2 => $value2) {
                            if (!empty($key2)) {
                            //$v[] = 1;
                            }
                        }
                        //dd($v);*
                        $canopy_money_array_stock_data_for_datatable[] = $v;
                        $v = [];
                    }
                }
            }
            */
            //dd($item);
            //dd($amount_net_by_invoice);
            //dd($canopy_money_array);
            $w = [];
            foreach ($canopy_money_array as $_key => $_value) {
                //retouver les discount et dettes
                $discount_amount_by_invoice = 0;
                $invoice_total_due = 0;
                $invoice_final_total = 0; //montant arrondi

                /*---------
                foreach ($amount_net_by_invoice as $key10 => $value10) {
                    if (explode('::', $key10)[0] == $_key) {
                        foreach ($value10 as $key11 => $value11) {
                            $discount_amount_by_invoice += $value11['discount_amount_by_invoice'];
                            $invoice_total_due += $value11['invoice_total_due'];
                            $invoice_final_total += $value11['invoice_final_total'];
                        }
                        //dd($value10);
                    }
                }
                */

                //dd($_key);
                /*
                foreach ($amount_net_by_invoice[$_key] as $key10 => $value10) {
                    
                }
                dd($invoice_total_due);
                */
                /*********************************/
                if (!empty($_key)) {
                    $v[] = $_key;
                    foreach ($_value as $key2 => $value2) {
                        if (!empty($key2)) {
                            $v[] = $value2;
                        }
                    }

                    /*******************************************/
                    $tout_subtotal_net = array_sum($v) - $invoice_total_due - $discount_amount_by_invoice;
                    //dd($tout_subtotal_net);
                    $arrondissement_difference = $invoice_final_total - array_sum($v) + $discount_amount_by_invoice; //differnece de l'arrondissement, on le fait avec le total bien avant d'appliquer le discount ou la dette
                    $arrondissement_difference = number_format($arrondissement_difference, 7, '.', '');
                    //$arrondissement_difference=0;

                    $tout_subtotal_net = number_format($tout_subtotal_net + $arrondissement_difference, 7, '.', ''); //paiement final
                    $v[] = $tout_subtotal_net;
                    //$v[] = $arrondissement_difference;

                    $canopy_money_array_stock_data_for_datatable[] = $v;

                    array_shift($v); //remove the first element of aaray
                    /*
                    $w = array_map(
                        function () {
                            return array_sum(func_get_args());
                        },
                        $w,
                        $v,
                    );
                    */
                    $keys = array_keys($v);
                    //dd($keys);
                    foreach ($keys as $index) {
                        $w[$index] = (empty($w[$index]) ? 0 : $w[$index]) + (empty($v[$index]) ? 0 : $v[$index]);
                    }
                    $v = [];
                }
            }
            /*
            dd($w);
            $a = [];
            /*
            foreach ($canopy_money_array as $key22 => $value22) {
                $a = $a + $value22;
            }
            */
            /*
            $r = [];
            $keys = array_keys($a);
            
            //dd($keys);
            foreach ($canopy_money_array as $key22 => $value22) {
                //$a = $a + $value22;
                $keys = array_keys($value22);
                foreach ($keys as $v) {
                    $a[$v] = (empty($a[$v]) ? 0 : $a[$v]) + (empty($value22[$v]) ? 0 : $value22[$v]);
                }
            }
            
            dd($a);
            dd($w);
            //dd($canopy_sell_qty_array);
            */

            $whole_locations_money[$key] = $w;
            //dd(end($whole_locations_money[$key]));
            //dd($whole_locations_money);

            //on retrouve aussi pour le stock
            /**************** CUSTOM  *******************
            $_whole_locations_stock = $canopy_sell_qty_array;
            $_whole_locations_stock = [];
            
            $whole_locations_stock = [];
            $array_key=[]; //already
            foreach ($canopy_sell_qty_array as $_key => $_value) {
                $v[] = $key;
                foreach ($_value as $key2 => $value2) {
                    if (!empty($key2)) {
                        $v[] = $value2;
                    }
                }
                if(empty($whole_locations_stock)){
                    $whole_locations_stock[] = $v;
                    $v = [];
                }else{
                    $arrayB = $formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['brand_name']][$product_name];
                    $arrayA = $item;
            
                    //dd($arrayB);
            
                    $sums_array = [];
                    $no_calc = array_keys(array_diff_key($item, array_flip(['subtotal', 'sell_qty', 'unit_price', 'unit_sale_price'])));
                    //dd($no_calc);
                    foreach (array_keys($arrayA + $arrayB) as $item_key) {
                        if (in_array($item_key, $no_calc)) {
                            $sums_array[$item_key] = $arrayA[$item_key];
                            continue;
                        }
                        $sums_array[$item_key] = (isset($arrayA[$item_key]) ? $arrayA[$item_key] : 0) + (isset($arrayB) ? $arrayB[$item_key] : 0);
                    }
                    $formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['brand_name']][$product_name] = $sums_array;
            
                }
                $whole_locations_stock[] = $v;
                $v = [];
            }
            
            dd($whole_locations_stock);
            //dd($whole_locations_money);
            
            /********************************************/
            $w = [];
            //dd($canopy_sell_qty_array);
            foreach ($canopy_stay_qty_array as $_key => $_value) {
                if (!empty($_key)) {
                    $v[] = $_key;
                    foreach ($_value as $key2 => $value2) {
                        if (!empty($key2)) {
                            $v[] = (float) $value2;
                        }
                    }
                    $v[] = array_sum($v); //on ajoute pout le total
                    $canopy_sell_qty_array_stock_data_for_datatable[] = $v;
                    /*******************************************/
                    //dd($v);
                    array_shift($v); //remove the first element of aaray
                    //$w = [];
                    /*
                    $w = array_map(
                        function () {
                            return array_sum(func_get_args());
                        },
                        $w,
                        $v,
                    );
                    */

                    //$w = [];
                    $keys = array_keys($v);
                    //dd($keys);
                    foreach ($keys as $index) {
                        $w[$index] = (empty($w[$index]) ? 0 : $w[$index]) + (empty($v[$index]) ? 0 : $v[$index]);
                    }
                    $v = [];
                }

                /*
                if (!empty($_key)) {
                    $v[] = $_key;
                    foreach ($_value as $key2 => $value2) {
                        if (!empty($key2)) {
                            $v[] = $value2;
                        }
                    }
                    $v[] = array_sum($v); //on ajoute pout le total
                    //$v[] = end($whole_locations_money[$key]); //on ajoute pout le total mais venant du tableau argent de vente
            
                    //dd($v);*
                    $canopy_sell_qty_array_stock_data_for_datatable[] = $v;
            
                    array_shift($v);
                    $w = array_map(
                        function () {
                            return array_sum(func_get_args());
                        },
                        $w,
                        $v,
                    );
            
                    $v = [];
                }
                */
            }
            //dd($w);
            $whole_locations_stock[$key] = $w;

            $w = [];
            //dd($canopy_stay_qty_array);
            foreach ($canopy_stay_qty_array as $_key => $_value) {
                if (!empty($_key)) {
                    $v[] = $_key;
                    foreach ($_value as $key2 => $value2) {
                        if (!empty($key2)) {
                            $v[] = (float) $value2;
                        }
                    }
                    $v[] = array_sum($v); //on ajoute pout le total
                    $canopy_stay_qty_array_stock_data_for_datatable[] = $v;
                    /*******************************************/
                    array_shift($v); //remove the first element of aaray

                    //$w = [];
                    $keys = array_keys($v);
                    //dd($keys);
                    foreach ($keys as $index) {
                        $w[$index] = (empty($w[$index]) ? 0 : $w[$index]) + (empty($v[$index]) ? 0 : $v[$index]);
                    }
                    $v = [];
                }
            }
            //dd($w);
            $whole_locations_stay_stock[$key] = $w;

            //dd($whole_locations_stock);
            //dd($canopy_money_array_stock_data_for_datatable);
            //dd($canopy_money_array_title_for_datatable);

        @endphp

        @php

        @endphp

        {{-- les resumé du canopy --}}
        <hr>
        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <h3>STOCK (en {{ $my_unit }}).</h3>
                <table id="tableID-{{ $item['id'] }}" class="table table-bordered table-striped"
                    style="font-weight:bold">
                    <tfoot>
                        <tr class="bg-gray footer-total text-center">
                            <td><strong>@lang('sale.total')({{ $my_unit }}):</strong></td>

                            @for ($i = 0; $i < count($formatted_array_all_product) + 1; $i++)
                                <td>{{ $i }}</td>
                            @endfor

                        </tr>
                    </tfoot>
                </table>

            </div>

        </div>
        <hr>
        <div class="row" style="display:none">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <h3>Resumé en prix de vente (Dollars ($)).</h3>
                <table id="tableID-money-{{ $item['id'] }}" class="table table-bordered table-striped"
                    style="font-weight:bold">
                    <tfoot>
                        <tr class="bg-gray footer-total text-center">
                            <td><strong>@lang('sale.total'):</strong></td>

                            @for ($i = 0; $i < count($formatted_array_all_product) + 1; $i++)
                                <td>{{ $i }}</td>
                            @endfor

                        </tr>
                    </tfoot>
                </table>

            </div>

        </div>
    @endcomponent

    @php
        $resume_all_by_money[$key . '::' . $item['id']] = ['title' => $canopy_money_array_title_for_datatable, 'data' => $canopy_money_array_stock_data_for_datatable];
        $resume_all_by_sell_qty[$key . '::' . $item['id']] = ['title' => $canopy_sell_qty_array_title_for_datatable, 'data' => $canopy_sell_qty_array_stock_data_for_datatable];
        $resume_all_by_stay_qty[$key . '::' . $item['id']] = ['title' => $canopy_stay_qty_array_title_for_datatable, 'data' => $canopy_stay_qty_array_stock_data_for_datatable];

    @endphp

    @php
        //dd($resume_all_by_money);
    @endphp
@endforeach


@php
    //    dd(11111);
    //dd($resume_all_by_money);
@endphp


{{-- --------- ADDITION DE TOUT --------- --}}
@php

    $whole_report_title = [];
    $whole_report_title[] = ['title' => '####'];
    foreach ($canopy_money_array as $_key => $_value) {
        foreach ($_value as $key2 => $value2) {
            $whole_report_title[] = ['title' => $key2];
        }
    }
    //on enleve les elements dupliqués
    $_data = [];
    foreach ($whole_report_title as $v) {
        if (isset($_data[$v['title']])) {
            // found duplicate
            continue;
        }
        // remember unique item
        $_data[$v['title']] = $v;
    }
    $_data2 = [];
    foreach ($_data as $_key => $_value) {
        $_data2[] = $_value;
    }
    $whole_report_title = $_data2;
    foreach ($resume_all_by_money as $key => $value) {
        # code...
    }

    //dd($whole_report_title);
    $whole_report_data = [];
    foreach ($resume_all_by_money as $key => $value) {
        $v[] = $key;
        foreach ($value as $key2 => $value2) {
            if (!empty($key2)) {
                $v[] = $value2;
            }
        }
        $whole_report_data[] = $v;
        $v = [];
    }

    //dd($whole_report_data);
    //dd($canopy_money_array);
    foreach ($canopy_money_array as $_key => $_value) {
        if (!empty($_key)) {
            $v[] = $_key;
            foreach ($_value as $key2 => $value2) {
                //dd($_key);
                //dd($_key);
                if (!empty($key2)) {
                    $v[] = $value2;
                }
            }
            //dd($v);*
            $canopy_money_array_stock_data_for_datatable[] = $v;
            $v = [];
        }
    }

    //dd($whole_locations_money);
    //dd($canopy_money_array_title_for_datatable);

@endphp


<div class="row">
    <div class="col-md-12">
        {{-- j'hydrate la base de données --}}
        @php
            $_whole_locations = $whole_locations_money;
            $whole_locations_money = [];

            $whole_locations_money = [];
            foreach ($_whole_locations as $key => $value) {
                $v[] = $key;
                foreach ($value as $key2 => $value2) {
                    //ca generer une eereur
                    /*
                    if (!empty($key2)) {
                        
                    }
                    */
                    $v[] = $value2;
                }
                $whole_locations_money[] = $v;
                $v = [];
            }

            //dd($_whole_locations);
            //dd($whole_locations_money);

        @endphp
        @php
            $_whole_locations_stock = $whole_locations_stock;
            $whole_locations_stock = [];

            $whole_locations_stock = [];
            foreach ($_whole_locations_stock as $key => $value) {
                $v[] = $key;
                foreach ($value as $key2 => $value2) {
                    //ca generer une eereur
                    /*
                if (!empty($key2)) {
                    
                }
                */
                    $v[] = $value2;
                }
                $whole_locations_stock[] = $v;
                $v = [];
            }

            //dd($_whole_locations);
            //dd($whole_locations_money);

        @endphp

        @php

            //dd($whole_locations_money)
        @endphp

        @component('components.widget', ['title' => 'Tous les rapports'])
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <h3>STOCK GLOBAL (en {{ $my_unit }}).</h3>
                    <table id="tableID-all-stock" class="table table-bordered table-striped" style="font-weight:bold">
                        <tfoot>
                            <tr class="bg-gray footer-total text-center">
                                <td><strong>@lang('sale.total')({{ $my_unit }}):</strong></td>

                                @for ($i = 0; $i < count($formatted_array_all_product) + 1; $i++)
                                    <td>{{ $i }}</td>
                                @endfor

                            </tr>
                        </tfoot>
                    </table>

                </div>

            </div>

            <div class="row" style="display: none">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <h3>Rapport général en prix de vente (Dollars ($)).</h3>
                    <table id="tableID-all-money" class="table table-bordered table-striped" style="font-weight:bold">
                    </table>

                </div>

            </div>
        @endcomponent
    </div>
</div>


@php
    //dd($whole_locations_money);
@endphp


@php
    $whole_congo_data_by_sell_qty = [];
    foreach ($deposit_with_canopy as $key => $item) {
        foreach ($resume_all_by_sell_qty[$key . '::' . $item['id']]['data'] as $key => $value) {
            $whole_congo_data_by_sell_qty[] = $value;
        }
    }

    $i = 0;
    $arrayA = null;
    $arrayB = null;
    foreach ($deposit_with_canopy as $key => $item) {
        foreach ($resume_all_by_sell_qty[$key . '::' . $item['id']]['data'] as $key => $value) {
            if ($i == 0) {
                $arrayA = $value;
                continue;
            }
            $arrayB = $value;

            $sums_array = [];
            $no_calc = [0];
            //dd($no_calc);
            foreach (array_keys($arrayA + $arrayB) as $item_key) {
                if (in_array($item_key, $no_calc)) {
                    $sums_array[$item_key] = $arrayA[$item_key];
                    continue;
                }
                $sums_array[$item_key] = (isset($arrayA[$item_key]) ? $arrayA[$item_key] : 0) + (isset($arrayB) ? $arrayB[$item_key] : 0);
            }
            $arrayA = $sums_array;
        }
        $whole_congo_data_by_sell_qty = $arrayA;
    }

    //dd($whole_congo_data_by_sell_qty);

@endphp


@php
    $whole_congo_data_by_stay_qty = [];
    foreach ($deposit_with_canopy as $key => $item) {
        foreach ($resume_all_by_stay_qty[$key . '::' . $item['id']]['data'] as $key => $value) {
            $whole_congo_data_by_stay_qty[] = $value;
        }
    }

    $i = 0;
    $arrayA = null;
    $arrayB = null;
    foreach ($deposit_with_canopy as $key => $item) {
        foreach ($resume_all_by_stay_qty[$key . '::' . $item['id']]['data'] as $key => $value) {
            if ($i == 0) {
                $arrayA = $value;
                continue;
            }
            $arrayB = $value;

            $sums_array = [];
            $no_calc = [0];
            //dd($no_calc);
            foreach (array_keys($arrayA + $arrayB) as $item_key) {
                if (in_array($item_key, $no_calc)) {
                    $sums_array[$item_key] = $arrayA[$item_key];
                    continue;
                }
                $sums_array[$item_key] = (isset($arrayA[$item_key]) ? $arrayA[$item_key] : 0) + (isset($arrayB) ? $arrayB[$item_key] : 0);
            }
            $arrayA = $sums_array;
        }
        $whole_congo_data_by_stay_qty = $arrayA;
    }

    //dd($whole_congo_data_by_sell_qty);
    //dd($resume_all_by_stay_qty);

@endphp



@section('mycustom_js')
    <script>
        // Initialize the DataTable
        $js_deposit_total = 0;

        $(document).ready(function() {
            @foreach ($deposit_with_canopy as $key => $item)
                @php
                    //dd($resume_all_by_money[$key."::".$item['id']]['title']);
                @endphp
                $('#tableID-{{ $item['id'] }}').DataTable({

                    // Add the data created above
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    fixedColumns: {
                        left: 2
                    },
                    data: @json($resume_all_by_stay_qty[$key . '::' . $item['id']]['data']),
                    columns: @json($resume_all_by_sell_qty[$key . '::' . $item['id']]['title']),
                    createdRow: function(row, data, index) {
                        //console.log('row', row);
                        console.log('data', data);
                        // Updated Schedule Week 1 - 07 Mar 22

                        data.forEach((element, index) => {
                            //alert(index);
                            if (!(element > 0) && index != 0) {
                                //$(row).find('td:eq('+index+')').css('color', 'red');
                                //$(row[4]).css("background-color", "red");
                                //$('td:eq('+index+')', row).css('background-color', 'red');
                                $('td:eq(' + index + ')', row).css('color', 'red');
                            }

                            $js_deposit_total += element + "//";
                            //$(row).css('font-weight', 'bold');
                        });


                    },
                    footerCallback: function(tfoot, data, start, end, display) {
                        var api = this.api();
                        var nbrcol = $(this).find('thead th').length;

                        for (let index = 1; index < nbrcol; index++) {
                            $(api.column(index).footer()).html(
                                api.column(index).data().reduce(function(a, b) {
                                    return a + b;
                                }, 0)
                            );

                        }

                    },
                    // Enable the processing indicator
                    // of the DataTable
                    processing: true,
                });


                console.log('----------------------------------')
                //alert($js_deposit_total);
                $('#tableID-money-{{ $item['id'] }}').DataTable({

                    // Add the data created above
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    fixedColumns: {
                        left: 2
                    },
                    data: @json($resume_all_by_money[$key . '::' . $item['id']]['data']),
                    columns: @json($resume_all_by_money[$key . '::' . $item['id']]['title']),

                    createdRow: function(row, data, index) {
                        //console.log('row', row);
                        //console.log('data', data);
                        // Updated Schedule Week 1 - 07 Mar 22

                        data.forEach((element, index) => {
                            //alert(index);
                            if (!(element > 0) && index != 0) {
                                //$(row).find('td:eq('+index+')').css('color', 'red');
                                //$(row[4]).css("background-color", "red");
                                //$('td:eq('+index+')', row).css('background-color', 'red');
                                $('td:eq(' + index + ')', row).css('color', 'red');
                            }

                            //$(row).css('font-weight', 'bold');
                            //$(this).DataTable().row(index).data( '1223456' ).draw();
                        });

                        //$(row[index]).html(1223456);



                    },

                    // Enable the processing indicator
                    // of the DataTable
                    processing: true,
                });
            @endforeach

            //pour tous les depost (all)

            $('#tableID-all-money').DataTable({

                // Add the data created above
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    left: 2
                },
                //data: @json($whole_locations_stock),
                data: @json($whole_locations_money),

                columns: @json($canopy_money_array_title_for_datatable),
                createdRow: function(row, data, index) {
                    console.log('row', row);
                    console.log('data', data);
                    // Updated Schedule Week 1 - 07 Mar 22

                    data.forEach((element, index) => {
                        //alert(index);
                        if (!(element > 0) && index != 0) {
                            //$(row).find('td:eq('+index+')').css('color', 'red');
                            //$(row[4]).css("background-color", "red");
                            //$('td:eq('+index+')', row).css('background-color', 'red');



                            //$('td:eq(' + index + ')', row).css('color', 'red');
                        }

                        //$(row).css('font-weight', 'bold');
                    });


                },
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();


                },

                // Enable the processing indicator
                // of the DataTable
                processing: true,
            });

            $('#tableID-all-stock').DataTable({

                // Add the data created above
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    left: 2
                },
                //data: @json($whole_locations_stock),
                data: @json($whole_locations_stock),

                columns: @json($canopy_money_array_title_for_datatable),
                createdRow: function(row, data, index) {
                    //console.log('row', row);
                    //console.log('data', data);
                    // Updated Schedule Week 1 - 07 Mar 22

                    data.forEach((element, index) => {
                        //alert(index);
                        if (!(element > 0) && index != 0) {
                            //$(row).find('td:eq('+index+')').css('color', 'red');
                            //$(row[4]).css("background-color", "red");
                            //$('td:eq('+index+')', row).css('background-color', 'red');
                            $('td:eq(' + index + ')', row).css('color', 'red');
                        }

                        //$(row).css('font-weight', 'bold');
                        //$(this).DataTable().row(index).data( '1223456' ).draw();
                    });

                    //$(row[index]).html(1223456);



                },

                // Enable the processing indicator
                // of the DataTable
                footerCallback: function(tfoot, data, start, end, display) {
                    var api = this.api();
                    var nbrcol = $(this).find('thead th').length;

                    for (let index = 1; index < nbrcol; index++) {
                        $(api.column(index).footer()).html(
                            api.column(index).data().reduce(function(a, b) {
                                return a + b;
                            }, 0)
                        );

                    }
                },
                processing: true,
            });

        });
    </script>
@endsection
