{{--
@php
    use Carbon\Carbon;
    Carbon::setLocale('fr');
    setlocale(LC_TIME, 'French');

    $selles_collect = collect($selles_array);
    //dd($selles_collect->first()['sell']->location->name );
    //dd($business_details);
    //dd($permitted_locations);
    function format_currency($_business_details, $value)
    {
        return $value . ' ' . $_business_details->currency_code;
    }
@endphp
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ $business_details->name }}</title>
</head>

<body>
    @php
        $i = 0;
        $total_sell = 0;
    @endphp

    <table>
        <tbody>
            <tr><td>
            @foreach ($selles_array as $key => $sell)

                        \n\n

                        \n## Vente Numéro de la facture : {{$key}}
                        \n Site commercial (point d'affaire)
                        :{{ $sell['sell']->location->name}}({{ $sell['sell']->location->location_id }})

                        \nQuel est la date de la facturation ou de vente de la facture
                        numéro {{ $key }} :
                        {{ Carbon::parse($sell['sell']->created_at)->translatedFormat('l jS F Y à h:i:s') }}


                        \nQuel est le status de la facture numéro
                        {{ $key }} : {{ $sell['sell']->status }}
                        \nQuel est le status de paiment de la facture numéro
                        {{ $key }} : {{ $sell['sell']->status }}

                        \nQuel est le numéro de code du client de la facture numéro
                        {{ $key }} :
                        {{ $sell['sell']->contact->contact_id }}
                        \nQuel est le nom du client de la facture numéro
                        {{ $key }} : {{ $sell['sell']->contact->name }}

                        \nQuel est l'adresse du client de la facture numéro
                        {{ $key }} :
                        {{ $sell['sell']->contact->contact_address }}
                        \nQuel est le numéro du client de la facture numéro
                        {{ $key }} :
                        {{ $sell['sell']->contact->mobile }}


                        \nQuels sont les produits ou services vendu dans la facture numéro
                        {{ $key }} :
                        Les produits ou services vendus dans la facture numéro
                        {{ $key }} sont les suivants :

                        @foreach ($sell['sell']->sell_lines as $key_sell_line => $sell_line)
                                @php
                                    //dd($sell['sell']->sell_lines);
                                @endphp
                                \n{{ $sell_line->product->name }} @if ($sell_line->product->type == 'variable')
                                    (-
                                    {{ $sell_line->variations->product_variation->name ?? '' }}
                                    - {{ $sell_line->variations->name ?? '' }})
                                @endif:

                                \nDescription du produit
                                :{{ $sell_line->product->product_description }}

                                \nLe variable du produit :
                                @if ($sell_line->product->type == 'variable')
                                    -
                                    {{ $sell_line->variations->product_variation->name ?? '' }}
                                    - {{ $sell_line->variations->name ?? '' }}
                                @else
                                    le produit n'a pas de variable
                                @endif

                                \nNature
                                :{{ $sell_line->product->enable_stock == 1 ? "C'est un produit" : "C'est un
                                                                service" }}

                                \nUnité de vente du produit :
                                @if (!empty($sell_line->sub_unit))
                                    {{ $sell_line->sub_unit->short_name }}
                                @else
                                    {{ $sell_line->product->unit->short_name }}
                                @endif

                                \nQuantité vendue
                                :{{ $sell_line->quantity }} @if (!empty($sell_line->sub_unit))
                                    {{ $sell_line->sub_unit->short_name }}
                                @else
                                    {{ $sell_line->product->unit->short_name }}
                                @endif


                                \nNote qui accompagne la vente du
                                produit
                                :{{ $sell_line->sell_line_note }}
                                \nIdentifiant du produit dans le
                                systeme : {{ $sell_line->id }}
                                \nLe code bar ou SKU du produit
                                :{{ $sell_line->variations->sub_sku ?? 'aucun code barre trouvé' }}




                                \nPrix unitaire
                                :{{ format_currency($business_details, $sell_line->unit_price) }}

                                \nPrix unitaire avant impots et taxes
                                :{{ format_currency(
                                $business_details,
                                $sell_line->unit_price_before_discount
                            ) }}

                                \nPrix unitaire après impots et taxes
                                :{{ format_currency($business_details, $sell_line->unit_price_inc_tax) }}

                                \nRemise ou discount sur l'unité du
                                produit
                                :{{ format_currency($business_details, $sell_line->get_discount_amount()) }}


                                \nPrix de vente total du produit ou
                                service : le prix de vente total est le
                                Prix unitaire après impots et taxes multiplié par la
                                quantié vendu est c'est
                                {{ format_currency($business_details, $sell_line->quantity *
                                $sell_line->unit_price_inc_tax) }}



                                \nGarantie sur le produit :
                                @if (
                                    $sell['is_warranty_enabled'] &&
                                    !empty($sell_line->warranties->first())
                                )
                                        {{ $sell_line->warranties->first()->display_name ?? '' }}
                                        -
                                        {{
                                        @format_date($sell_line->warranties->first()->getEndDate($sell->transaction_date))
                                        }}
                                        @if (!empty($sell_line->warranties->first()->description))
                                            {{ $sell_line->warranties->first()->description ?? '' }}
                                        @endif
                                @else
                                    Le produit ne bénéficie d'aucune garantie
                                @endif


                                \nTaille du produit :
                                {{ empty($sell_line->weight ? $sell_line->weight : "La taille du produit n'a
                                                                pas été spécifiée") }}


                        @endforeach


                        
                        @php
                            $i++;
                            $total_sell += $sell['sell']->final_total;
                        @endphp
            @endforeach
            </td></tr>
            <caption>
                ###Liste complete de toutes les ventes de l'entreprise "{{ $business_details->name }}" à la date du
                {{ Carbon::parse($selles_collect->first()['sell']->created_at)->translatedFormat('l jS F Y à h:i:s') }}
                au
                {{ Carbon::parse($selles_collect->last()['sell']->created_at)->translatedFormat('l jS F Y à h:i:s') }} :
                soit {{count($selles_array)}} ventes equivalent à {{ format_currency($business_details, $total_sell) }}\n\n\n 
            </caption>
        </tbody>
    </table>

</body>

</html>
--}}

@php
use Carbon\Carbon;
Carbon::setLocale('fr');
setlocale(LC_TIME, 'French');

$selles_collect=collect($selles_array);
//dd($selles_collect->first()['sell']->location->name );
//dd($business_details);
//dd($permitted_locations);
function format_currency($_business_details, $value)
{
return $value . ' ' . $_business_details->currency_code;
}
@endphp
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>NDIAKA ETS</title>
</head>

<body>
    @php
    $i=0;
    $total_sell = 0;
    @endphp



    <table>

        <tbody>
            @foreach ($selles_array as $key => $sell)
            <tr>
                <td>
                    <ul>
                        <li>## Vente Numéro de la facture : {{$key}}</li>
                        <li> Site commercial (point d'affaire) :{{ $sell['sell']->location->name}}({{
                            $sell['sell']->location->location_id }})</li>

                        <li>Quel est la date de la facturation ou de vente de la facture
                            numéro {{ $key }} :
                            {{ Carbon::parse($sell['sell']->created_at)->translatedFormat('l jS F Y à h:i:s') }}
                        </li>

                        <li>Quel est le status de la facture numéro
                            {{ $key }} : {{ $sell['sell']->status }}</li>
                        <li>Quel est le status de paiment de la facture numéro
                            {{ $key }} : {{ $sell['sell']->status }}</li>

                        <li>Quel est le numéro de code du client de la facture numéro
                            {{ $key }} :
                            {{ $sell['sell']->contact->contact_id }}</li>
                        <li>Quel est le nom du client de la facture numéro
                            {{ $key }} : {{ $sell['sell']->contact->name }}
                        </li>
                        <li>Quel est l'adresse du client de la facture numéro
                            {{ $key }} :
                            {!! $sell['sell']->contact->contact_address !!}</li>
                        <li>Quel est le numéro du client de la facture numéro
                            {{ $key }} :
                            {{ $sell['sell']->contact->mobile }}</li>


                        <li>Quels sont les produits ou services vendu dans la facture numéro
                            {{ $key }} :
                            Les produits ou services vendus dans la facture numéro
                            {{ $key }} sont les suivants :
                            <ol>
                                @foreach ($sell['sell']->sell_lines as $key_sell_line => $sell_line)
                                @php
                                //dd($sell['sell']->sell_lines);
                                @endphp
                                <li>{{ $sell_line->product->name }} @if ($sell_line->product->type == 'variable')
                                    (-
                                    {{ $sell_line->variations->product_variation->name ?? '' }}
                                    - {{ $sell_line->variations->name ?? '' }})
                                    @endif:
                                    <ol>
                                        <li>Description du produit
                                            :{!! $sell_line->product->product_description !!} </li>

                                        <li>Le variable du produit :
                                            @if ($sell_line->product->type == 'variable')
                                            -
                                            {{ $sell_line->variations->product_variation->name ?? '' }}
                                            - {{ $sell_line->variations->name ?? '' }}
                                            @else
                                            le produit n'a pas de variable
                                            @endif
                                        </li>
                                        <li>Nature
                                            :{{ $sell_line->product->enable_stock == 1 ? "C'est un produit" : "C'est un
                                            service" }}
                                        </li>
                                        <li>Unité de vente du produit :
                                            @if (!empty($sell_line->sub_unit))
                                            {{ $sell_line->sub_unit->short_name }}
                                            @else
                                            {{ $sell_line->product->unit->short_name }}
                                            @endif
                                        </li>
                                        <li>Quantité vendue
                                            :{{ $sell_line->quantity }} @if (!empty($sell_line->sub_unit))
                                            {{ $sell_line->sub_unit->short_name }}
                                            @else
                                            {{ $sell_line->product->unit->short_name }}
                                            @endif
                                        </li>

                                        <li>Note qui accompagne la vente du
                                            produit
                                            :{{ $sell_line->sell_line_note }} </li>
                                        <li>Identifiant du produit dans le
                                            systeme : {{ $sell_line->id }} </li>
                                        <li>Le code bar ou SKU du produit
                                            :{{ $sell_line->variations->sub_sku ?? 'aucun code barre trouvé' }}
                                        </li>



                                        <li>Prix unitaire
                                            :{{ format_currency($business_details, $sell_line->unit_price) }}
                                        </li>
                                        <li>Prix unitaire avant impots et taxes
                                            :{{ format_currency($business_details,
                                            $sell_line->unit_price_before_discount) }}
                                        </li>
                                        <li>Prix unitaire après impots et taxes
                                            :{{ format_currency($business_details, $sell_line->unit_price_inc_tax) }}
                                        </li>
                                        <li>Remise ou discount sur l'unité du
                                            produit
                                            :{{ format_currency($business_details, $sell_line->get_discount_amount()) }}
                                        </li>

                                        <li>Prix de vente total du produit ou
                                            service : le prix de vente total est le
                                            Prix unitaire après impots et taxes multiplié par la
                                            quantié vendu est c'est
                                            {{ format_currency($business_details, $sell_line->quantity *
                                            $sell_line->unit_price_inc_tax) }}
                                        </li>


                                        <li>Garantie sur le produit :
                                            @if ($sell['is_warranty_enabled'] &&
                                            !empty($sell_line->warranties->first()))
                                            {{ $sell_line->warranties->first()->display_name ?? '' }}
                                            -
                                            {{
                                            @format_date($sell_line->warranties->first()->getEndDate($sell->transaction_date))
                                            }}
                                            @if (!empty($sell_line->warranties->first()->description))
                                            {{ $sell_line->warranties->first()->description ?? '' }}
                                            @endif
                                            @else
                                            Le produit ne bénéficie d'aucune garantie
                                            @endif
                                        </li>

                                        <li>Taille du produit :
                                            {{ empty($sell_line->weight ? $sell_line->weight : "La taille du produit n'a
                                            pas été spécifiée") }}
                                        </li>


                                    </ol>
                                </li>
                                @endforeach

                            </ol>
                        </li>
                    </ul>




                </td>


            </tr>

            @php
            $i++;
            $total_sell+=$sell['sell']->final_total;
            @endphp
            @endforeach
        </tbody>
        <caption>
            ###Liste complete de toutes les ventes de l'entreprise "{{ $business_details->name }}" à la date du {{
            Carbon::parse($selles_collect->first()['sell']->created_at)->translatedFormat('l jS F Y à h:i:s') }} au {{
            Carbon::parse($selles_collect->last()['sell']->created_at)->translatedFormat('l jS F Y à h:i:s') }} : soit
            {{$i}} ventes equivalent à {{ format_currency($business_details, $total_sell) }}\n\n\n
        </caption>
    </table>


</body>

</html>