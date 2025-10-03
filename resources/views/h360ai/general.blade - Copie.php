@php
    use Carbon\Carbon;
    Carbon::setLocale('fr');
    setlocale(LC_TIME, 'French');

    //dd($business_details->currency_code);
    //dd($selles_array);
    function format_currency($_business_details, $value)
    {
        return $value . ' ' . $_business_details->currency_code;
    }
@endphp
<html>
<meta charset="UTF-8">
<title>H360</title>
<style>
    /* Table of Contents - Decimal */
    .toc-decimal ol {
        list-style-type: none;
        counter-reset: item;
        margin: 0;
        padding: 0;
        font-family: monospace;
    }

    .toc-decimal ol li {
        display: table-row;
        counter-increment: item;
        margin-bottom: 0.6em;
    }

    .toc-decimal ol li:before {
        content: counters(item, ".") ". ";
        display: table-cell;
        padding-right: 0.6em;
    }

    .toc-decimal ol li li {
        margin: 0;
    }

    .toc-decimal ol li li:before {
        content: counters(item, ".") ". ";
    }


    /* Table of Contents - Upper Alpha */
    .toc-upper-alpha ol {
        list-style-type: none;
        counter-reset: item;
        margin: 0;
        padding: 0;
        font-family: monospace;
    }

    .toc-upper-alpha ol li {
        display: table-row;
        counter-increment: item;
        margin-bottom: 0.6em;
    }

    .toc-upper-alpha ol li:before {
        content: "A." counters(item, ".") ". ";
        display: table-cell;
        padding-right: 0.6em;
    }

    .toc-upper-alpha ol li li {
        margin: 0;
    }

    .toc-upper-alpha ol li li:before {
        content: "A." counters(item, ".") ". ";
    }




    table {
  border-collapse: collapse;
  border: 2px solid rgb(140 140 140);
  font-family: sans-serif;
  font-size: 0.8rem;
  letter-spacing: 1px;
}

th,
td {
  border: 1px solid rgb(160 160 160);
  padding: 8px 10px;
}

th {
  background-color: rgb(230 230 230);
}

td {
  text-align: center;
}

tr:nth-child(even) td {
  background-color: rgb(250 250 250);
}

tr:nth-child(odd) td {
  background-color: rgb(240 240 240);
}
</style>
</head>

<body>
    {{--
    <div>
        L'entreprise "H360" du 1er Janvier 2024 au 31 Décembre 2025, a généré
        {{ count($selles_array) }} ventes.
        voici un tableau non-detaillé des ventes ou factures generées.

        <table>
            <caption>
                Liste complete de toutes les ventes de l'entreprise
            </caption>
            <thead>
                <tr>
                    <td>Ordre de création</td>
                    <th>Numéro Facture</th>
                    <th>Total Payé</th>
                </tr>
            </thead>
            <tbody>
                @php
                $i=0;
                    $total_sell = 0;
                @endphp

                @foreach ($selles_array as $key => $sell)
                    @php
                    //dd($sell['sell']->final_total);
                        $i++;
                    @endphp
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$key}}</td>
                        <td>{{ format_currency($business_details, $sell['sell']->final_total) }}</td>
                    </tr>
                    @php
                        $total_sell+=$sell['sell']->final_total;
                    @endphp
                @endforeach

                
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td>Montant total payé de facture</td>
                    <td>{{ format_currency($business_details, $total_sell) }}</td>
                </tr>
            </tfoot>
        </table>

    </div>
    --}}
    <section id="sommaire" class="toc-decimal">

        <!-- Ordered List -->
        <ol>
            <li><a href="#section_introduction">Introduction</a></li>
            <li><a href="#section_methodologie">Méthodologie</a></li>
            <li><a href="#section_resume_entreprise">Résumé de l'entreprise "h360"</a></li>
            <li><a href="#section_detail_clients">Informations detaillées et livre de caisse des clients de l'entreprise
                    "h360"</a></li>
            <li><a href="#section_liste_ventes">Facturation et ventes de l'entreprise "H360" en date du 1 janvier 2024 au
                    31 decembre 2024</a>
                <ol>
                    <li><a href="#section_resume_vente">Resumé des ventes de l'entreprise "H360" en date du 1 janvier
                            2024 au 31 decembre 2024</a></li>
                    <li><a href="#section_les_ventes">Liste detaillée de ventes de l'entreprise "H360" en date du 1
                            janvier 2024 au
                            31 decembre 2024</a></li>
                    <ol>
                        @foreach ($selles_array as $key => $sell)
                            <li><a href="#section_detail_vente_{{ $key }}">Contenu de la Facture numéro
                                    {{ $key }}</a></li>
                        @endforeach
                    </ol>
            </li>
        </ol>
        </li>
        </ol>

    </section>
    <hr>
    <hr>

    <section id="contenu" class="toc-decimal">

        <!-- Ordered List -->
        <ol>
            <li>

                <section>
                    <h1>Introduction</h1>
                    <div>
                        aaaaaaaaaa
                    </div>
                </section>

            </li>
            <li>
                <section>
                    <h1>Methodologie</h1>
                    <div>
                        aaaaaaaaaa
                    </div>
                </section>
            </li>
            <li>
                <section>
                    <h1>Résumé de l'entreprise "h360"</h1>
                    <div>
                        aaaaaaaaaa
                    </div>
                </section>
            </li>
            <li>
                <section>
                    <h1>Informations detaillées et livre de caisse des clients de l'entreprise "h360"</h1>
                    <div>
                        aaaaaaaaaa
                    </div>
                </section>
            </li>
            <li>
                <section>
                    <h1>Facturation et ventes de l'entreprise "H360" en date du 1 janvier 2024 au 31 decembre 2024</h1>
                    <div>
                        aaaaaaaaaa
                    </div>
                </section>
                <ol>
                    <li>
                        <section>
                            <h2>Resumé des ventes de l'entreprise "H360" en date du 1 janvier 2024 au 31 decembre 2024
                            </h2>
                            <div>
                                aaaaaaaaaa
                            </div>
                        </section>
                    </li>
                    <li>
                        <section>
                            <h2>Liste detaillée de ventes de l'entreprise "H360" en date du 1 janvier 2024 au 31
                                decembre 2024
                                </h3>
                                <div>
                                    L'entreprise "H360" du 1er Janvier 2024 au 31 Décembre 2025, a généré
                                    {{ count($selles_array) }} ventes.
                                    voici un tableau non-detaillé des ventes ou factures generées.

                                    <table>
                                        <caption>
                                            Liste complete de toutes les ventes de l'entreprise
                                        </caption>
                                        <thead>
                                            <tr>
                                                <td>Ordre de création</td>
                                                <th>Numéro Facture</th>
                                                <th>Total Payé</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $i=0;
                                                $total_sell = 0;
                                            @endphp

                                            @foreach ($selles_array as $key => $sell)
                                                @php
                                                //dd($sell['sell']->final_total);
                                                    $i++;
                                                @endphp
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$key}}</td>
                                                    <td>{{ format_currency($business_details, $sell['sell']->final_total) }}</td>
                                                </tr>
                                                @php
                                                    $total_sell+=$sell['sell']->final_total;
                                                @endphp
                                            @endforeach

                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td>Montant total payé de facture</td>
                                                <td>{{ format_currency($business_details, $total_sell) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>
                        </section>
                        <ol>
                            
                            @foreach ($selles_array as $key => $sell)
                                @php
                                    //dd($sell['sell']->sell_lines);
                                    //dd(Carbon::parse($sell['sell']->created_at)->format('l jS \of F Y h:i:s A'));
                                    //dd(format_date($sell['sell']->created_at));
                                    Carbon::setLocale('fr');
                                @endphp
                                <li>
                                    <section id="section_detail_vente_{{ $key }}">
                                        <h3>Contenu de la Facture numéro {{ $key }}</h3>

                                        <ol>
                                            <li><strong>Quel est la date de la facturation ou de vente de la facture
                                                    numéro {{ $key }}</strong> :
                                                {{ Carbon::parse($sell['sell']->created_at)->translatedFormat('l jS F Y à h:i:s') }}
                                            </li>

                                            <li><strong>Quel est le status de la facture numéro
                                                    {{ $key }}</strong> : {{ $sell['sell']->status }}</li>
                                            <li><strong>Quel est le status de paiment de la facture numéro
                                                    {{ $key }}</strong> : {{ $sell['sell']->status }}</li>

                                            <li><strong>Quel est le numéro de code du client de la facture numéro
                                                    {{ $key }}</strong> :
                                                {{ $sell['sell']->contact->contact_id }}</li>
                                            <li><strong>Quel est le nom du client de la facture numéro
                                                    {{ $key }}</strong> : {{ $sell['sell']->contact->name }}
                                            </li>
                                            <li><strong>Quel est l'adresse du client de la facture numéro
                                                    {{ $key }}</strong> :
                                                {{ $sell['sell']->contact->contact_address }}</li>
                                            <li><strong>Quel est le numéro du client de la facture numéro
                                                    {{ $key }}</strong> :
                                                {{ $sell['sell']->contact->mobile }}</li>

                                            <li><strong>Quel est le personnel de service de la facture numéro
                                                    {{ $key }}</strong> : </li>
                                            <li><strong>Quel est le status de paiment de la facture numéro
                                                    {{ $key }}</strong> : </li>
                                            <li><strong>Quel est l'etat d'expedition de la facture numéro
                                                    {{ $key }}</strong> : </li>
                                            <li><strong>Quel est l'adresse du client de la facture numéro
                                                    {{ $key }}</strong> : </li>
                                            <li><strong>Quel est le numéro du client de la facture numéro
                                                    {{ $key }}</strong> : </li>

                                            <li><strong>Quels sont les produits ou services vendu dans la facture numéro
                                                    {{ $key }}</strong> :
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
                                                                <li><strong>Description du produit</strong>
                                                                    :{!! $sell_line->product->product_description !!} </li>

                                                                <li><strong>Le variable du produit</strong> :
                                                                    @if ($sell_line->product->type == 'variable')
                                                                        -
                                                                        {{ $sell_line->variations->product_variation->name ?? '' }}
                                                                        - {{ $sell_line->variations->name ?? '' }}
                                                                    @else
                                                                        le produit n'a pas de variable
                                                                    @endif
                                                                </li>
                                                                <li><strong>Nature</strong>
                                                                    :{{ $sell_line->product->enable_stock == 1 ? "C'est un produit" : "C'est un service" }}
                                                                </li>
                                                                <li><strong>Unité de vente du produit</strong> :
                                                                    @if (!empty($sell_line->sub_unit))
                                                                        {{ $sell_line->sub_unit->short_name }}
                                                                    @else
                                                                        {{ $sell_line->product->unit->short_name }}
                                                                    @endif
                                                                </li>
                                                                <li><strong>Quantité vendue</strong>
                                                                    :{{ $sell_line->quantity }} @if (!empty($sell_line->sub_unit))
                                                                        {{ $sell_line->sub_unit->short_name }}
                                                                    @else
                                                                        {{ $sell_line->product->unit->short_name }}
                                                                    @endif
                                                                </li>

                                                                <li><strong>Note qui accompagne la vente du
                                                                        produit</strong>
                                                                    :{{ $sell_line->sell_line_note }} </li>
                                                                <li><strong>Identifiant du produit dans le
                                                                        systeme</strong> : {{ $sell_line->id }} </li>
                                                                <li><strong>Le code bar ou SKU du produit</strong>
                                                                    :{{ $sell_line->variations->sub_sku ?? 'aucun code barre trouvé' }}
                                                                </li>



                                                                <li><strong>Prix unitaire</strong>
                                                                    :{{ format_currency($business_details, $sell_line->unit_price) }}
                                                                </li>
                                                                <li><strong>Prix unitaire avant impots et taxes</strong>
                                                                    :{{ format_currency($business_details, $sell_line->unit_price_before_discount) }}
                                                                </li>
                                                                <li><strong>Prix unitaire après impots et taxes</strong>
                                                                    :{{ format_currency($business_details, $sell_line->unit_price_inc_tax) }}
                                                                </li>
                                                                <li><strong>Remise ou discount sur l'unité du
                                                                        produit</strong>
                                                                    :{{ format_currency($business_details, $sell_line->get_discount_amount()) }}
                                                                </li>

                                                                <li><strong>Prix de vente total du produit ou
                                                                        service</strong> : le prix de vente total est le
                                                                    Prix unitaire après impots et taxes multiplié par la
                                                                    quantié vendu est c'est
                                                                    {{ format_currency($business_details, $sell_line->quantity * $sell_line->unit_price_inc_tax) }}
                                                                </li>


                                                                <li><strong>Garantie sur le produit</strong> :
                                                                    @if ($sell['is_warranty_enabled'] && !empty($sell_line->warranties->first()))
                                                                        <br><small>{{ $sell_line->warranties->first()->display_name ?? '' }}
                                                                            -
                                                                            {{ @format_date($sell_line->warranties->first()->getEndDate($sell->transaction_date)) }}</small>
                                                                        @if (!empty($sell_line->warranties->first()->description))
                                                                            <br><small>{{ $sell_line->warranties->first()->description ?? '' }}</small>
                                                                        @endif
                                                                    @else
                                                                        Le produit ne bénéficie d'aucune garantie
                                                                    @endif
                                                                </li>

                                                                <li><strong>Taille du produit</strong> :
                                                                    {{ empty($sell_line->weight ? $sell_line->weight : "La taille du produit n'a pas été spécifiée") }}
                                                                </li>


                                                            </ol>
                                                        </li>
                                                    @endforeach

                                                </ol>
                                            </li>
                                        </ol>
                                    </section>
                                </li>
                            @endforeach
                            

                        </ol>
                    </li>
                </ol>
            </li>
            <li>Depth 1</li>
        </ol>

    </section>
   









    {{-- }}
    <!-- Table of Contents - Decimal -->
    <section class="toc-decimal">

        <!-- Ordered List -->
        <ol>
            <li>Depth 1
                <ol>
                    <li>Depth 2</li>
                    <li>Depth 2</li>
                    <li>Depth 2
                        <ol>
                            <li>Depth 3</li>
                            <li>Depth 3</li>
                            <li>Depth 3
                                <ol>
                                    <li>Depth 4</li>
                                    <li>Depth 4</li>
                                    <li>Depth 4
                                        <ol>
                                            <li>Depth 5</li>
                                            <li>Depth 5</li>
                                            <li>Depth 5</li>
                                        </ol>
                                    </li>
                                </ol>
                            </li>
                        </ol>
                    </li>
                </ol>
            </li>
            <li>Depth 1</li>
        </ol>

    </section>
    {{--
    <hr>

    <!-- Table of Contents - Upper Alpha -->
    <section class="toc-upper-alpha">

        <!-- Ordered List -->
        <ol>
            <li>Depth 1
                <ol>
                    <li>Depth 2</li>
                    <li>Depth 2</li>
                    <li>Depth 2
                        <ol>
                            <li>Depth 3</li>
                            <li>Depth 3</li>
                            <li>Depth 3
                                <ol>
                                    <li>Depth 4</li>
                                    <li>Depth 4</li>
                                    <li>Depth 4
                                        <ol>
                                            <li>Depth 5</li>
                                            <li>Depth 5</li>
                                            <li>Depth 5</li>
                                        </ol>
                                    </li>
                                </ol>
                            </li>
                        </ol>
                    </li>
                </ol>
            </li>
            <li>Depth 1</li>
        </ol>

    </section>
    --}}

</body>

</html>
