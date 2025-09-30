@php
    //    dd($data);
@endphp


@if (!empty($product))
    <tr id="{{ $variations->id }}">
        <td>{{ $product->name . ($variations->name == 'DUMMY' ? '' : ' ( ' . $variations->name . ' )') }}</td>
        <td>{{ $variations->sub_sku }}</td>
        <td id="productQuantity_{{ $variations->id }}">{{ $productQuantity }}</td>
        <td onchange="updateInventoryAmount(this , {{ $variations->id }})">
            <input type="hidden" value="{{ $variations->id }}" name="variation_id">
            <input type="hidden" value="{{ $product->id }}" name="product_id">
            <input value="0" type="text" id="productAfterInventory_{{ $variations->id }}" />
        </td>
        <td id="difference_{{ $variations->id }}"></td>
        <td>
            <button class="btn btn-danger delete_row" name="delete">
                <i class="fa-solid fa-trash-can"></i>
                <span class="m-2">@lang('inventorymanagement::inventory.delete')</span>
            </button>
            <i class="fa-thin fa-badge-check"></i>
        </td>
    </tr>
    <i class="fa-thin fa-badge-check"></i>
@else
    @foreach ($variations as $variation)
        @php
            $productQuantity = $variation->qty_left;
        @endphp
        <tr id="{{ $variation->var_id }}">
            <td>{{ $variation->name . ($variation->var_name == 'DUMMY' ? '' : ' ( ' . $variation->var_name . ' )') }}</td>
            <td>{{ $variation->sub_sku }}</td>
            <td id="productQuantity_{{ $variation->var_id }}">{{ $productQuantity }}</td>
            <td onchange="updateInventoryAmount(this , {{ $variation->var_id }})">
                <input type="hidden" value="{{ $variation->var_id }}" name="variation_id">
                <input type="hidden" value="{{ $variation->id }}" name="product_id">
                <input value="0" type="text" id="productAfterInventory_{{ $variation->var_id }}" />
            </td>
            <td id="difference_{{ $variation->var_id }}"></td>
            <td>
                <button class="btn btn-danger delete_row" name="delete">
                    <i class="fa-solid fa-trash-can"></i>
                    <span class="m-2">@lang('inventorymanagement::inventory.delete')</span>
                </button>
                <i class="fa-thin fa-badge-check"></i>
            </td>
        </tr>
        <i class="fa-thin fa-badge-check"></i>
    @endforeach

@endif


{{--
    
    --}}
