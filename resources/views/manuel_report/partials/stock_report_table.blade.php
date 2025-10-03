@php
    //dd($detailled_business_locations[0]);
    //dd($data->original['data']);
    
    $formatted_array = [];
    //$formatted_array = ['PRODUCT 1'=>['product' => 'PRODUCT 1', 'stock retour' => 12, 'stock vendu' => 23, 'solde' => 23]];
    
    foreach ($data->original['data'] as $key => $item) {
        $product_name = $item['product'];
        if (!empty($item['variation'])) {
            $product_name .= ' (' . explode('-', $item['variation'])[1] . ')';
        }
    
        if (isset($formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['category_name']][$product_name])) {
            $formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['category_name']][$product_name] = $formatted_array[$product_name] + $item;
        } else {
            $formatted_array[$item['location_name'] . '::' . $item['location_id']][$item['category_name']][$product_name] = $item;
        }
    }
    
    //dd($formatted_array);
    
    //all used product
    $formatted_array_all_product = [];
    foreach ($formatted_array as $key => $value) {
        foreach ($value as $key2 => $value2) {
            foreach ($value2 as $key3 => $value3) {
                $formatted_array_all_product[$key3] = 0;
            }
        }
    }
    //dd($formatted_array_all_product);
    
    //je crée un tableau de depot ayant des canopy
    $deposit_with_canopy = [];
    foreach ($detailled_business_locations as $key => $value) {
        if ($value->custom_field1 == 1) {
            //si c'est un depot ayant un canopie
        $deposit_with_canopy[$value->name] = ['id' => $value->id, 'is_deposit_with_canopy' => true, 'canopy_ids' => explode(',', $value->custom_field2)];
        }
    }
    
    //dd($deposit_with_canopy);
    
@endphp



@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
    $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
    $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
    $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
    $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
@endphp


@php
    //dd($deposit_with_canopy);
    $resume_all = []; //le resumé de tout
@endphp

@foreach ($deposit_with_canopy as $key => $item)
    @php
        $resume_all[$key . '::' . $item['id']] = 1;
    @endphp
@endforeach

@php
    //dd($resume_all);
@endphp



@php
    $whole_locations = []; //pour tous le congo
@endphp
@foreach ($deposit_with_canopy as $key => $item)
    @component('components.filters', ['title' => $key, 'class' => 'box-solid', 'id' => $key])
        <div class="row">
            @php
                $tout_canopy_stock = 0;
                $tout_canopy_total_sold = 0;
                $tout_canopy_unit_price = 0;
                
                $canopy_array = [];
            @endphp

            @foreach ($item['canopy_ids'] as $key2 => $item2)
                @foreach ($formatted_array as $key3 => $item3)
                    @if (explode('::', $key3)[1] == $item2)
                        {{-- cest ca --}}



                        @if (!empty($key3))
                            @php
                                $canopy_array[explode('::', $key3)[0]] = $formatted_array_all_product; //rajoute les dsd canopy
                            @endphp

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <table class="table table-bordered table-striped">
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
                                            <th>@lang('report.current_stock')</th>
                                            <th>@lang('report.total_unit_sold')</th>
                                            <th>P.U</th>
                                            <th>Solde</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $tout_stock = 0;
                                            $tout_total_sold = 0;
                                            $tout_unit_price = 0;
                                            $tout_total_unit_price = 0;
                                        @endphp
                                        @foreach ($item3 as $key4 => $item4)
                                            @php
                                                $stock = 0;
                                                $total_sold = 0;
                                                $unit_price = 0;
                                                $total_unit_price = 0;
                                            @endphp
                                            @foreach ($item4 as $key5 => $item5)
                                                <tr>
                                                    <th>{{ $key5 }}</th>
                                                    <th>{{ $item5['stock'] }}</th>
                                                    <th>{{ $item5['total_sold'] }}</th>
                                                    <th>{{ $item5['unit_price'] }}</th>
                                                    <th>{{ (float) $item5['total_sold'] * (float) $item5['unit_price'] }}
                                                    </th>
                                                </tr>
                                                @php
                                                    $stock += $item5['stock'];
                                                    $total_sold += $item5['total_sold'];
                                                    $unit_price = (float) $item5['unit_price'];
                                                    $total_unit_price += (float) $item5['total_sold'] * (float) $item5['unit_price'];
                                                    
                                                    $canopy_array[explode('::', $key3)[0]][$key5] = $item5['total_sold'];
                                                @endphp
                                            @endforeach
                                            <tr class="bg-gray font-17 text-center footer-total">
                                                <th>Total {{ $key4 }}</th>
                                                <th>{{ $stock }}</th>
                                                <th>{{ $total_sold }}</th>
                                                <th>--</th>
                                                <th>{{ $total_unit_price }}</th>
                                            </tr>

                                            @php
                                                $tout_stock += $stock;
                                                $tout_total_sold += $total_sold;
                                                $tout_total_unit_price += $total_unit_price;
                                                $tout_unit_price = $unit_price;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-warning font-17 text-center footer-total">
                                            <td colspan="1"><strong>@lang('sale.total'):</strong></td>
                                            <td class="footer_total_stock"><strong>{{ $tout_stock }}</strong></td>
                                            <td class="footer_total_sold"><strong>{{ $tout_total_sold }}</strong></td>
                                            <td class="footer_stock">
                                                <strong>--</strong>
                                            <td class="footer_stock">
                                                <strong>{{ $tout_total_unit_price }}</strong>
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
            //dd($canopy_array);
            
            $canopy_array_title_for_datatable = [];
            $canopy_array_data_for_datatable = [];
            $canopy_array_title_for_datatable[] = ['title' => '####'];
            foreach ($canopy_array as $_key => $_value) {
                foreach ($_value as $key2 => $value2) {
                    $canopy_array_title_for_datatable[] = ['title' => $key2];
                }
            }
            //on enleve les elements dupliqués
            $_data = [];
            foreach ($canopy_array_title_for_datatable as $v) {
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
            $canopy_array_title_for_datatable = $_data2;
            
            //dd($canopy_array_title_for_datatable);
            $v = [];
            $w[] = $key; //pour tous le congo
            /*
            foreach ($canopy_array_title_for_datatable as $key => $value) {
                foreach ($canopy_array as $key => $value) {
                    if (!empty($key)) {
                        $v[] = $key;
                        foreach ($value as $key2 => $value2) {
                            if (!empty($key2)) {
                            //$v[] = 1;
                            }
                        }
                        //dd($v);*
                        $canopy_array_data_for_datatable[] = $v;
                        $v = [];
                    }
                }
            }
            */
            foreach ($canopy_array as $_key => $_value) {
                if (!empty($_key)) {
                    $v[] = $_key;
                    foreach ($_value as $key2 => $value2) {
                        if (!empty($key2)) {
                            $v[] = $value2;
                        }
                    }
                    //dd($v);*
                    $canopy_array_data_for_datatable[] = $v;
            
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
            }
            
            $whole_locations[$key] = $w;
            //dd($w);
            //dd($canopy_array_data_for_datatable);
            //dd($canopy_array_title_for_datatable);
        @endphp

        @php
            
        @endphp

        {{-- les resumé du canopy --}}
        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <table id="tableID-{{ $item['id'] }}" class="table table-bordered table-striped"
                    style="font-weight:bold">
                </table>

            </div>

        </div>
    @endcomponent

    @php
        $resume_all[$key . '::' . $item['id']] = ['title' => $canopy_array_title_for_datatable, 'data' => $canopy_array_data_for_datatable];
    @endphp

    @php
        //dd($resume_all);
    @endphp
@endforeach

@php
    //dd($whole_locations);
    //dd($resume_all);
@endphp


{{-- --------- ADDITION DE TOUT --------- --}}
@php
    /*
    $whole_report_title = [];
    $whole_report_title[] = ['title' => '####'];
    foreach ($canopy_array as $_key => $_value) {
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
    foreach ($resume_all as $key => $value) {
        # code...
    }
    
    //dd($whole_report_title);
    $whole_report_data = [];
    foreach ($resume_all as $key => $value) {
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
    //dd($canopy_array);
    foreach ($canopy_array as $_key => $_value) {
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
            $canopy_array_data_for_datatable[] = $v;
            $v = [];
        }
    }
    */
    //dd($whole_locations);
    //dd($canopy_array_title_for_datatable);
@endphp


<div class="row">
    <div class="col-md-12">
        {{-- j'hydrate la base de données --}}
        @php
            $_whole_locations = $whole_locations;
            $whole_locations = [];
            
            $whole_locations = [];
            foreach ($_whole_locations as $key => $value) {
                $v[] = $key;
                foreach ($value as $key2 => $value2) {
                    if (!empty($key2)) {
                        $v[] = $value2;
                    }
                }
                $whole_locations[] = $v;
                $v = [];
            }
            //dd($whole_locations)
        @endphp

        @component('components.filters', ['title' => 'Tous les rapports'])
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <table id="tableID-all" class="table table-bordered table-striped" style="font-weight:bold">
                    </table>

                </div>

            </div>
        @endcomponent
    </div>
</div>


@php
//dd($whole_locations);
@endphp

@section('mycustom_js')
    <script>
        // Initialize the DataTable
        
        $(document).ready(function() {
            @foreach ($deposit_with_canopy as $key => $item)
                @php
                    //dd($resume_all[$key."::".$item['id']]['title']);
                @endphp
                $('#tableID-{{ $item['id'] }}').DataTable({

                    // Add the data created above
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    fixedColumns: {
                        left: 2
                    },
                    data: @json($resume_all[$key . '::' . $item['id']]['data']),
                    columns: @json($resume_all[$key . '::' . $item['id']]['title']),
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
                                $('td:eq(' + index + ')', row).css('color', 'red');
                            }

                            //$(row).css('font-weight', 'bold');
                        });


                    },

                    // Enable the processing indicator
                    // of the DataTable
                    processing: true,
                });
            @endforeach

            //pour tous les depost (all)
            {{--
            $('#tableID-all').DataTable({

                // Add the data created above
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    left: 2
                },
                data: @json($whole_locations),
                columns: @json($canopy_array_title_for_datatable),
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
                            $('td:eq(' + index + ')', row).css('color', 'red');
                        }

                        //$(row).css('font-weight', 'bold');
                    });


                },

                // Enable the processing indicator
                // of the DataTable
                processing: true,
            });
            --}}
        });
    </script>
@endsection
