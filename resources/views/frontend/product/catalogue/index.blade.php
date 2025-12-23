@extends('frontend.homepage.layout')
@section('content')
    <div class="product-catalogue page-wrapper">
        @include('frontend.component.breadcrumb', ['model' => $productCatalogue, 'breadcrumb' => $breadcrumb])
        <div class="uk-container uk-container-center mt20">
            @if(!empty($productCatalogue->image))
            <div class="category-banner mb20" style="position:relative;height:360px;border-radius:6px;overflow:hidden;">
                <div style="position:absolute;inset:0;background:url('{{ $productCatalogue->image }}') center/cover no-repeat;filter:blur(14px) brightness(0.95);"></div>
                <img src="{{ $productCatalogue->image }}" alt="{{ $productCatalogue->languages->first()->pivot->name }}" style="position:relative;z-index:1;max-height:100%;max-width:92%;width:auto;height:auto;display:block;margin:0 auto;">
            </div>
            @endif
           
            <div class="panel-body">
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-1-4 uk-hidden-small">
                        <div class="aside">
                            @if(isset($categories['categories']))
                            <div class="aside-category">
                                <div class="aside-heading">Danh mục sản phẩm</div>
                                <div class="aside-body">
                                    <ul class="uk-list uk-clearfix">
                                        @foreach($categories['categories']->object as $category)
                                        @php
                                            $name = $category->languages->first()->pivot->name;
                                            $canonical = write_url($category->languages->first()->pivot->canonical);
                                            $icon = $category->icon ?? ''; // Lấy icon
                                        @endphp
                                        <li>
                                            <a href="{{ $canonical }}" title="{{ $name }}">
                                                @if(!empty($icon))
                                                <img src="{{ $icon }}" alt="{{ $name }}" style="width: 20px; height: 20px; margin-right: 8px; vertical-align: middle;">
                                                @endif
                                                {{ $name }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif

                            <div class="aside-category aside-product mt20">
                                <div class="aside-heading">Sản phẩm nổi bật</div>
                                <div class="aside-body">
                                    @foreach($widgets['products-hl']->object as $product)
                                    @php
                                        $name = $product->languages->first()->pivot->name;
                                        $canonical = write_url($product->languages->first()->pivot->canonical);
                                        $image  = $product->image;
                                        $price = getPrice($product);
                                    @endphp
                                    <div class="aside-product uk-clearfix">
                                        <a href="" class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></a>
                                        <div class="info">
                                            <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
                                            {!! $price['html'] !!}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="uk-width-large-3-4">
                        <div class="wrapper ">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between mb10">
                                <h1 class="heading-2"><span>{{ $productCatalogue->languages->first()->pivot->name }}</span></h1>
                                @include('frontend.product.catalogue.component.filter')
                            </div>
                            @if(!empty($productCatalogue->languages->first()->pivot->description))
                                <div class="catalogue-intro mb20" style="color:#555;line-height:1.6;">
                                    {!! $productCatalogue->languages->first()->pivot->description !!}
                                </div>
                            @endif
                            @include('frontend.product.catalogue.component.filterContent')
                            @if(!is_null($products))
                                <div class="product-list">
                                    <div class="uk-grid uk-grid-medium">
                                        @foreach($products as $product)
                                            <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-4 mb20">
                                                @include('frontend.component.product-item', ['product'  => $product])
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="uk-flex uk-flex-center">
                                    @include('frontend.component.pagination', ['model' => $products])
                                </div>
                            @endif
                            @if(!empty($productCatalogue->languages->first()->pivot->content))
                                <div class="product-catalogue-content mt20" id="catalogueContent" style="max-height:220px;overflow:hidden;position:relative;">
                                    {!! $productCatalogue->languages->first()->pivot->content !!}
                                    <div id="contentFade" style="position:absolute;left:0;right:0;bottom:0;height:60px;background:linear-gradient(180deg, rgba(255,255,255,0) 0%, #fff 70%);"></div>
                                </div>
                                <div class="uk-text-center mt10">
                                    <button id="toggleContent" class="uk-button" style="background:#eaeaea;border-radius:4px;padding:6px 14px;">Xem thêm</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

