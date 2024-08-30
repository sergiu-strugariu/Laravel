@extends('layouts.app')

@section('content')
    <div class="prices-page">
        <div class="header-content">
            <div class="tag-name">
                Prices - Defaults
            </div>

            <button class="add-client save_prices hidden">
                <div class="ion-compose">
                    Save Changes
                </div>
            </button>
        </div>

        <div class="box prices-box">
            <div class="box-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Languages</th>
                            @foreach($pricingTypes as $type)
                                <th>{{$type['name']}}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($languages as $language)
                            <tr>
                                <th class="leading-column">{{$language['name']}}</th>
                                @foreach($pricingTypes as $type)
                                    <?php
                                        // Speaking exception
                                        $paperType = ($type['id'] == 3 || $type['id'] == 9) ? 4 : $type['id'] ;

                                        // Writing exception
                                        $paperType = ($paperType == 1) ? 2 : $paperType ;

                                        $hasPricingType = false;
                                    ?>

                                    @foreach($language['language_paper_type'] as $languagePaper)
                                        @if ($pricingTypesMap[$languagePaper['paper_type_id']] == $paperType)
                                            <?php $hasPricingType = true;?>
                                        @endif
                                    @endforeach
                                    <td>
                                    @if (!empty($groupedPrices[$language['id']]) && !empty($groupedPrices[$language['id']][$type['id']]))
                                        <input
                                            class="price-input form-control" type="text"
                                            data-language-id="{{$language['id']}}"
                                            data-pricing-type="{{$type['id']}}"
                                            @if (!$hasPricingType)
                                                    disabled="disabled"
                                            @endif
                                            data-type-id="{{$groupedPrices[$language['id']][$type['id']]['id']}}"
                                            data-init-value="{{$groupedPrices[$language['id']][$type['id']]['price']}}"
                                            value="{{$groupedPrices[$language['id']][$type['id']]['price']}}"
                                        >
                                    @else
                                        <input
                                            class="price-input form-control" type="text"
                                            data-language-id="{{$language['id']}}"
                                            data-init-value="0"
                                            @if (!$hasPricingType)
                                                disabled="disabled"
                                            @endif
                                            data-pricing-type="{{$type['id']}}" value="0"
                                        >
                                    @endif

                                    </td>
                                @endforeach
                                </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection

@section('footer')
@endsection