@extends('layouts.base')
@section('meta')
    <title>{{ $meta_title }}</title>
    @if($meta_description)
        <meta name="description" content="{{ $meta_description }}" />
    @endif
    @if($meta_keywords)
        <meta name="keywords" content="{{ $meta_keywords }}" />
    @endif
@endsection
@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/site/css/account/account.css') }}" />
@endsection
@section('content')
    <section id="content">
        <div class="container">
            <h1 class="name_product">{!! $title !!}</h1>
            <div class="flex top_product">
                <div class="articul">{!! __('locale.sku') !!}<span>{{ $model }}</span></div>
                <div class="reting_otzv">
                    <div class="rating">
                        @foreach([1, 2, 3, 4, 5] as $r)
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M11.7397 5.21017C11.9759 4.97998 12.0593 4.64213 11.9574 4.32807C11.8553 4.01402 11.5893 3.78983 11.2625 3.74225L8.35694 3.32006C8.23319 3.30204 8.12626 3.22443 8.071 3.11221L6.772 0.479616C6.62614 0.183821 6.33011 0 6.00019 0C5.67051 0 5.37448 0.183821 5.22862 0.479616L3.92939 3.11245C3.87412 3.22467 3.76695 3.30228 3.6432 3.3203L0.737635 3.74249C0.411084 3.78983 0.144844 4.01426 0.0427215 4.32831C-0.0591608 4.64237 0.0242193 4.98022 0.260423 5.21041L2.36271 7.25959C2.45233 7.34706 2.49342 7.47297 2.47228 7.59599L1.97632 10.4895C1.93235 10.7443 1.99915 10.992 2.16399 11.1873C2.42013 11.4918 2.86731 11.5845 3.22486 11.3966L5.82334 10.0304C5.93195 9.97341 6.06867 9.97389 6.17704 10.0304L8.77576 11.3966C8.90215 11.4632 9.03695 11.4968 9.17608 11.4968C9.43007 11.4968 9.67084 11.3839 9.83639 11.1873C10.0015 10.992 10.068 10.7438 10.0241 10.4895L9.52786 7.59599C9.50672 7.47273 9.54781 7.34706 9.63743 7.25959L11.7397 5.21017Z" fill="{{ $r <= $rating ? '#54B0AC' : '#E3EEF1' }}"></path></svg>
                        @endforeach
                    </div>
                    <div class="otzv"><a href="#reviews" class="fade">{{ $review_count }}</a></div>
                </div>
                <div class="nalic">
                    @if($stock_id == 4)
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_432_56826)">
                                <path d="M4.59502 10.4821C4.48047 10.5973 4.32419 10.6616 4.16185 10.6616C3.99951 10.6616 3.84322 10.5973 3.72868 10.4821L0.269259 7.02209C-0.089753 6.66307 -0.089753 6.08091 0.269259 5.72258L0.702429 5.28929C1.06155 4.93028 1.64304 4.93028 2.00205 5.28929L4.16185 7.4492L9.99792 1.61301C10.357 1.254 10.9391 1.254 11.2975 1.61301L11.7307 2.04629C12.0897 2.4053 12.0897 2.98735 11.7307 3.3458L4.59502 10.4821Z" fill="#54B0AC"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_432_56826">
                                    <rect width="12" height="12" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    @else
                        <span class="color_status" style="background: {{ $stock_color }}"></span>
                    @endif
                    <span>{{ $stock }}</span>
                </div>
                @if($created <= 30)
                    <div class="new">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.48 2.95771C12.4091 1.10173 10.0267 0.464441 8.17205 1.53536L0 6.25313L3.8856 12.9834L12.0577 8.26521C13.9136 7.19429 14.5514 4.81323 13.48 2.95771ZM11.7806 7.78401L4.08926 12.2252L0.758741 6.45679L8.4496 2.0161C10.04 1.09803 12.0812 1.64481 12.9997 3.23526C13.9173 4.8257 13.3706 6.86641 11.7806 7.78401Z" fill="#54B0AC"/>
                            <path d="M8.72621 2.4972L1.51562 6.66035L4.29152 11.4672L11.5016 7.3041C12.8293 6.53751 13.2837 4.84038 12.5176 3.51316C11.751 2.18548 10.0534 1.73107 8.72621 2.4972ZM5.99696 8.57221L5.14909 8.06238C4.91311 7.92107 4.64249 7.74466 4.41574 7.57194L4.40882 7.58026C4.5686 7.83009 4.73162 8.10071 4.91495 8.41751L5.25299 9.00307L4.84891 9.23582L3.72766 7.29302L4.24026 6.99608L5.05488 7.47543C5.28855 7.61397 5.54393 7.78945 5.75682 7.95801L5.76559 7.95293C5.58872 7.71279 5.42894 7.45603 5.26084 7.16464L4.93296 6.59708L5.33612 6.36295L6.4583 8.30621L5.99696 8.57221ZM6.83928 8.08685L5.71757 6.14359L6.91687 5.45135L7.12468 5.81155L6.36686 6.24888L6.59915 6.6525L7.31448 6.23965L7.5209 6.59662L6.80557 7.00993L7.07203 7.47127L7.87049 7.00993L8.0783 7.36968L6.83928 8.08685ZM9.75464 6.40312L9.10812 5.66239C8.95849 5.48783 8.82134 5.32528 8.67033 5.11562L8.66433 5.11885C8.75623 5.35853 8.83058 5.56126 8.898 5.78338L9.19632 6.72592L8.71559 7.00439L7.13207 5.32805L7.60172 5.05651L8.21176 5.77276C8.38817 5.97873 8.57289 6.20686 8.71882 6.38373L8.72436 6.3805C8.63523 6.1519 8.54564 5.89145 8.45651 5.62406L8.16142 4.73325L8.62877 4.46355L9.25728 5.19274C9.43138 5.39501 9.58285 5.58481 9.73709 5.78385L9.74264 5.78015C9.64612 5.54694 9.55884 5.29434 9.46555 5.04081L9.16446 4.15368L9.61241 3.89507L10.2321 6.12835L9.75464 6.40312ZM11.6868 4.68707C11.3991 4.85378 11.0311 4.75495 10.8657 4.46679C10.7004 4.17954 10.7983 3.81149 11.0865 3.6457C11.3733 3.48038 11.7413 3.57828 11.9071 3.86552C12.0729 4.15368 11.9745 4.52174 11.6868 4.68707Z" fill="#54B0AC"/>
                        </svg>
                        <div class="news_text"><span>{{ __('locale.text_new_product') }}</span> {{ $day_new }} {{ __('locale.text_product_1') }}</div>
                    </div>
                @endif
                <a href="#" class="soc" data-target-modal="#popup2">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.1171 1.99997C11.1171 2.89746 10.3896 3.62503 9.49216 3.62503C8.59467 3.62503 7.86719 2.89746 7.86719 1.99997C7.86719 1.10257 8.59467 0.375 9.49216 0.375C10.3896 0.375 11.1171 1.10257 11.1171 1.99997Z" fill="#54B0AC"/>
                        <path d="M9.49216 4.00003C8.38913 4.00003 7.49219 3.103 7.49219 1.99997C7.49219 0.897034 8.38913 0 9.49216 0C10.5952 0 11.4921 0.897034 11.4921 1.99997C11.4921 3.103 10.5952 4.00003 9.49216 4.00003ZM9.49216 0.75C8.80267 0.75 8.24219 1.31104 8.24219 1.99997C8.24219 2.689 8.80267 3.25003 9.49216 3.25003C10.1816 3.25003 10.7421 2.689 10.7421 1.99997C10.7421 1.31104 10.1816 0.75 9.49216 0.75Z" fill="#54B0AC"/>
                        <path d="M11.1171 10.0001C11.1171 10.8975 10.3896 11.625 9.49216 11.625C8.59467 11.625 7.86719 10.8975 7.86719 10.0001C7.86719 9.10257 8.59467 8.375 9.49216 8.375C10.3896 8.375 11.1171 9.10257 11.1171 10.0001Z" fill="#54B0AC"/>
                        <path d="M9.49216 12C8.38913 12 7.49219 11.103 7.49219 10.0001C7.49219 8.89703 8.38913 8 9.49216 8C10.5952 8 11.4921 8.89703 11.4921 10.0001C11.4921 11.103 10.5952 12 9.49216 12ZM9.49216 8.75C8.80267 8.75 8.24219 9.31104 8.24219 10.0001C8.24219 10.689 8.80267 11.25 9.49216 11.25C10.1816 11.25 10.7421 10.689 10.7421 10.0001C10.7421 9.31104 10.1816 8.75 9.49216 8.75Z" fill="#54B0AC"/>
                        <path d="M4.11722 5.99997C4.11722 6.89746 3.38965 7.62494 2.49216 7.62494C1.59476 7.62494 0.867188 6.89746 0.867188 5.99997C0.867188 5.10248 1.59476 4.375 2.49216 4.375C3.38965 4.375 4.11722 5.10248 4.11722 5.99997Z" fill="#54B0AC"/>
                        <path d="M2.49216 7.99994C1.38922 7.99994 0.492188 7.103 0.492188 5.99997C0.492188 4.89694 1.38922 4 2.49216 4C3.59518 4 4.49222 4.89694 4.49222 5.99997C4.49222 7.103 3.59518 7.99994 2.49216 7.99994ZM2.49216 4.75C1.80267 4.75 1.24219 5.31094 1.24219 5.99997C1.24219 6.689 1.80267 7.24994 2.49216 7.24994C3.18173 7.24994 3.74222 6.689 3.74222 5.99997C3.74222 5.31094 3.18173 4.75 2.49216 4.75Z" fill="#54B0AC"/>
                        <path d="M3.67237 5.75892C3.49833 5.75892 3.32933 5.66837 3.23732 5.50641C3.10081 5.26691 3.18486 4.9614 3.42436 4.82435L8.06379 2.17939C8.30329 2.04188 8.60881 2.12592 8.74586 2.36634C8.88237 2.60584 8.79832 2.91135 8.55882 3.04841L3.91929 5.69336C3.84129 5.73786 3.75633 5.75892 3.67237 5.75892Z" fill="#54B0AC"/>
                        <path d="M8.31081 9.88579C8.22677 9.88579 8.1418 9.86473 8.0638 9.82023L3.42429 7.17527C3.18479 7.03876 3.10083 6.73325 3.23734 6.49319C3.37329 6.25323 3.67926 6.16873 3.91931 6.30624L8.55883 8.95121C8.79833 9.08772 8.88228 9.39323 8.74578 9.63328C8.65331 9.79524 8.4843 9.88579 8.31081 9.88579Z" fill="#54B0AC"/>
                    </svg>
                    <span>{{ __('locale.text_product_2') }}</span>
                </a>
            </div>
            <div class="flex flex_product">
                <div class="left_product">
                    <div class="flex foto_product popup-gallery">
                        <div class="dop_foto">
                            @if($thumb)
                                <div class="dop_foto2 activ">
                                    <a href="#" data-index="0"><img src="{{ $thumb }}" alt="{!! $title !!}"></a>
                                </div>
                            @endif
                            @foreach($thumbs as $key => $thumb_image)
                               @php($key++)
                                <div class="dop_foto2"{!! $key >= 3 ? ' style="display: none"' : '' !!}>
                                    <a href="#" data-index="{{ $key }}"><img src="{{ $thumb_image['image'] }}" alt="{{ $thumb_image['alt'] }}"></a>
                                </div>
                            @endforeach
                            @if(count($thumbs) > 4)
                                <a class="load_image" onclick="$('.osnov_foto a').trigger('click');return false;" href="#">{{ __('locale.text_product_3') }} {{ count($thumbs) - 4 }}</a>
                            @endif
                        </div>
                        <div class="osnov_foto">
                            <a href="#" data-index="0"><img src="{{ $image }}" class="main_foto" alt="{!! $title !!}"></a>
                        </div>
                    </div>
                    <div class="flex-2 hid-ms hid-sm hid-lg">
                        <div class="reting_otzv">
                            <div class="rating">
                                @foreach([1, 2, 3, 4, 5] as $r)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M11.7397 5.21017C11.9759 4.97998 12.0593 4.64213 11.9574 4.32807C11.8553 4.01402 11.5893 3.78983 11.2625 3.74225L8.35694 3.32006C8.23319 3.30204 8.12626 3.22443 8.071 3.11221L6.772 0.479616C6.62614 0.183821 6.33011 0 6.00019 0C5.67051 0 5.37448 0.183821 5.22862 0.479616L3.92939 3.11245C3.87412 3.22467 3.76695 3.30228 3.6432 3.3203L0.737635 3.74249C0.411084 3.78983 0.144844 4.01426 0.0427215 4.32831C-0.0591608 4.64237 0.0242193 4.98022 0.260423 5.21041L2.36271 7.25959C2.45233 7.34706 2.49342 7.47297 2.47228 7.59599L1.97632 10.4895C1.93235 10.7443 1.99915 10.992 2.16399 11.1873C2.42013 11.4918 2.86731 11.5845 3.22486 11.3966L5.82334 10.0304C5.93195 9.97341 6.06867 9.97389 6.17704 10.0304L8.77576 11.3966C8.90215 11.4632 9.03695 11.4968 9.17608 11.4968C9.43007 11.4968 9.67084 11.3839 9.83639 11.1873C10.0015 10.992 10.068 10.7438 10.0241 10.4895L9.52786 7.59599C9.50672 7.47273 9.54781 7.34706 9.63743 7.25959L11.7397 5.21017Z" fill="{{ $r <= $rating ? '#54B0AC' : '#E3EEF1' }}"></path></svg>
                                @endforeach
                            </div>
                            <div class="otzv"><a href="#reviews" class="fade">{{ $review_count }}</a></div>
                        </div>
                        <a href="#" class="soc" data-target-modal="#popup2">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.1171 1.99997C11.1171 2.89746 10.3896 3.62503 9.49216 3.62503C8.59467 3.62503 7.86719 2.89746 7.86719 1.99997C7.86719 1.10257 8.59467 0.375 9.49216 0.375C10.3896 0.375 11.1171 1.10257 11.1171 1.99997Z" fill="#54B0AC"/>
                                <path d="M9.49216 4.00003C8.38913 4.00003 7.49219 3.103 7.49219 1.99997C7.49219 0.897034 8.38913 0 9.49216 0C10.5952 0 11.4921 0.897034 11.4921 1.99997C11.4921 3.103 10.5952 4.00003 9.49216 4.00003ZM9.49216 0.75C8.80267 0.75 8.24219 1.31104 8.24219 1.99997C8.24219 2.689 8.80267 3.25003 9.49216 3.25003C10.1816 3.25003 10.7421 2.689 10.7421 1.99997C10.7421 1.31104 10.1816 0.75 9.49216 0.75Z" fill="#54B0AC"/>
                                <path d="M11.1171 10.0001C11.1171 10.8975 10.3896 11.625 9.49216 11.625C8.59467 11.625 7.86719 10.8975 7.86719 10.0001C7.86719 9.10257 8.59467 8.375 9.49216 8.375C10.3896 8.375 11.1171 9.10257 11.1171 10.0001Z" fill="#54B0AC"/>
                                <path d="M9.49216 12C8.38913 12 7.49219 11.103 7.49219 10.0001C7.49219 8.89703 8.38913 8 9.49216 8C10.5952 8 11.4921 8.89703 11.4921 10.0001C11.4921 11.103 10.5952 12 9.49216 12ZM9.49216 8.75C8.80267 8.75 8.24219 9.31104 8.24219 10.0001C8.24219 10.689 8.80267 11.25 9.49216 11.25C10.1816 11.25 10.7421 10.689 10.7421 10.0001C10.7421 9.31104 10.1816 8.75 9.49216 8.75Z" fill="#54B0AC"/>
                                <path d="M4.11722 5.99997C4.11722 6.89746 3.38965 7.62494 2.49216 7.62494C1.59476 7.62494 0.867188 6.89746 0.867188 5.99997C0.867188 5.10248 1.59476 4.375 2.49216 4.375C3.38965 4.375 4.11722 5.10248 4.11722 5.99997Z" fill="#54B0AC"/>
                                <path d="M2.49216 7.99994C1.38922 7.99994 0.492188 7.103 0.492188 5.99997C0.492188 4.89694 1.38922 4 2.49216 4C3.59518 4 4.49222 4.89694 4.49222 5.99997C4.49222 7.103 3.59518 7.99994 2.49216 7.99994ZM2.49216 4.75C1.80267 4.75 1.24219 5.31094 1.24219 5.99997C1.24219 6.689 1.80267 7.24994 2.49216 7.24994C3.18173 7.24994 3.74222 6.689 3.74222 5.99997C3.74222 5.31094 3.18173 4.75 2.49216 4.75Z" fill="#54B0AC"/>
                                <path d="M3.67237 5.75892C3.49833 5.75892 3.32933 5.66837 3.23732 5.50641C3.10081 5.26691 3.18486 4.9614 3.42436 4.82435L8.06379 2.17939C8.30329 2.04188 8.60881 2.12592 8.74586 2.36634C8.88237 2.60584 8.79832 2.91135 8.55882 3.04841L3.91929 5.69336C3.84129 5.73786 3.75633 5.75892 3.67237 5.75892Z" fill="#54B0AC"/>
                                <path d="M8.31081 9.88579C8.22677 9.88579 8.1418 9.86473 8.0638 9.82023L3.42429 7.17527C3.18479 7.03876 3.10083 6.73325 3.23734 6.49319C3.37329 6.25323 3.67926 6.16873 3.91931 6.30624L8.55883 8.95121C8.79833 9.08772 8.88228 9.39323 8.74578 9.63328C8.65331 9.79524 8.4843 9.88579 8.31081 9.88579Z" fill="#54B0AC"/>
                            </svg>
                            <span>{{ __('locale.text_product_2') }}</span>
                        </a>
                    </div>
                    <div class="decript_atribut">
                        <div class="left_decript">
                            @if(strip_tags($description))
                                <span class="activ_descript" data-target="#product_description">{{ __('locale.tab_description') }}</span>
                            @endif
                            @if(!$attributes->isEmpty())
                                <span data-target="#product_attributes">{{ __('locale.tab_attribute') }}</span>
                            @endif
                            <span data-target="#product_reviews">{{ __('locale.tab_reviews') }}</span>
                        </div>
                        <div class="right_decript">
                            @if(strip_tags($description))
                                <span class="activ_descript hid-lg" data-target="#product_description">{{ __('locale.tab_description') }}</span>
                                <div class="descript" id="product_description">
                                    {!! $description !!}
                                </div>
                            @endif
                            @if(!$attributes->isEmpty())
                                <div class="atributs" id="product_attributes">
                                    <span data-target="#product_attributes" class="activ_descript hid-lg">{{ __('locale.tab_attribute') }}</span>
                                    @foreach($attributes as $group => $attribute)
                                        <div class="group_atribut">
                                            <div class="name_gruop">{{ $group }}</div>
                                            @foreach($attribute as $attr)
                                                <div class="atribut">
                                                    <div class="brder_atribut"></div>
                                                    <div class="name_atribut">{{ $attr->name }}</div>
                                                    <div class="znac_atribut">{{ $attr->text }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                    <div class="predup">{{ __('locale.text_product_12') }}</div>
                                </div>
                            @endif
                            <div class="rewievs_left" id="product_reviews">
                                <span data-target="#product_reviews" class="activ_descript hid-lg">{{ __('locale.tab_reviews') }}</span>
                                <div class="top_revievs wrap">
                                    @if(session('customer_id'))
                                        <a href="#" class="btn-default" data-target-modal="#popup4">{{ __('locale.text_product_6') }}</a>
                                    @else
                                        <div class="review_add flex-2 mb20">
                                            <span>{!! __('locale.text_product_28') !!}</span>
                                            <a href="#" onclick="$(this).parent().remove();return false;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><g clip-path="url(#clip0_506_47034)"><path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"></path></g><defs><clipPath id="clip0_506_47034"><rect width="20" height="20" fill="white"></rect></clipPath></defs></svg></a>
                                        </div>
                                    @endif
                                    @if(!$reviews->isEmpty())
                                        <div class="form_group mb0 green_theme">
                                            <select class="selectize" onchange="review_update(this.value);">
                                                <option value="desc" selected>{{ __('locale.text_product_4') }}</option>
                                                <option value="asc">{{ __('locale.text_product_5') }}</option>
                                            </select>
                                        </div>
                                    @endif
                                </div>
                                <div id="review" data-page="1" class="relative">
                                    @if(!$reviews->isEmpty())
                                        @include('pages.site.product_reviews', ['reviews' => $reviews])
                                    @endif
                                </div>
                                @if(!$reviews->isEmpty() && $next_review)
                                    <a href="#" data-page="{{ $next_review }}" class="zagruz">{{ __('locale.text_load_more') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_product" id="product" style="position: relative">
                    <div class="modal2 overflow-y" style="display: none" id="popup3">
                        <div class="modal-content">
                            <a href="#" class="close" data-close-modal="#popup3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <g clip-path="url(#clip0_506_47034)">
                                        <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_506_47034">
                                            <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                            <div class="write_title flex-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <g clip-path="url(#clip0_506_47762)">
                                        <path d="M7.65836 17.4727C7.46745 17.6647 7.20698 17.7719 6.93641 17.7719C6.66584 17.7719 6.40537 17.6647 6.21446 17.4727L0.448765 11.7061C-0.149588 11.1077 -0.149588 10.1375 0.448765 9.54023L1.17072 8.81809C1.76926 8.21974 2.7384 8.21974 3.33675 8.81809L6.93641 12.4179L16.6632 2.69095C17.2617 2.0926 18.2318 2.0926 18.8292 2.69095L19.5512 3.41309C20.1495 4.01144 20.1495 4.98152 19.5512 5.57894L7.65836 17.4727Z" fill="#1D726D"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_506_47762">
                                            <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <span>{{ __('locale.text_product_23') }}</span>
                            </div>
                            <div class="flex flex-start mh150 wrap">
                                <img src="{{ $thumb }}" alt="{!! $title !!}" class="widm border-24" />
                                <div class="caption relative">
                                    <div class="product_name mb30">{!! $title !!}</div>
                                    <div class="option_product option_cart overflow-y"></div>
                                    <div class="flex-2 flex-start">
                                        <span class="one_price"></span>
                                        <span class="one_qw"></span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route(session('route_url') . '_checkout') }}" class="btn-default col-12 mb20">{{ __('locale.text_product_27') }}</a>
                            <a href="#" class="btn-invert col-12 mb20" data-close-modal="#popup3">{{ __('locale.text_product_24') }}</a>
                            <div class="minim" style="display: none">
                                <div class="h4_green">{{ __('locale.text_product_25') }}</div>
                                <p>{{ __('locale.text_product_26') }}</p>
                            </div>
                            <div class="hide_22">{!! __('locale.text_product_22') !!}</div>
                        </div>
                    </div>
                    <div class="modal2 overflow-y" style="display: none" id="one_click">
                        <div class="modal-content">
                            <a href="#" class="close" data-close-modal="#one_click">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <g clip-path="url(#clip0_506_47034)">
                                        <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_506_47034">
                                            <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                            <div class="write_title">{{ __('locale.text_product_17') }}</div>
                            <div class="flex flex-start mh150 wrap">
                                <img src="{{ $thumb }}" alt="{!! $title !!}" class="widm border-24" />
                                <div class="caption relative">
                                    <div class="product_name mb30">{!! $title !!}</div>
                                    <div class="flex-2 flex-start">
                                        <span class="one_price"></span>
                                        <span class="one_qw"></span>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('oneclick') }}" method="post" enctype="multipart/form-data" class="validate_js" data-step="#one_click_success" novalidate>
                                <div class="form_group">
                                    <input type="text" id="one_name" name="name" value="{{ !empty($customer['firstname']) ? $customer['firstname'] . (!empty($customer['lastname']) ? $customer['lastname'] : '') : '' }}" class="input" required />
                                    <label class="required" for="one_name">{{ __('locale.text_product_18') }}</label>
                                </div>
                                <div class="form_group">
                                    <input type="text" id="one_phone" name="phone" value="{{ !empty($customer['phone']) ? $customer['phone'] : '' }}" class="input" required />
                                    <label class="required" for="one_phone">{{ __('locale.text_write_phone') }}</label>
                                </div>
                                <div class="form_group">
                                    <input type="email" id="one_email" name="email" value="{{ !empty($customer['email']) ? $customer['email'] : '' }}" class="input" required />
                                    <label class="required" for="one_email">{{ __('locale.text_write_email') }}</label>
                                </div>
                                <input type="submit" value="{{ __('locale.text_checkout_button') }}" class="btn-default col-12" />
                            </form>
                        </div>
                    </div>
                    <div class="modal2 overflow-y" style="display: none" id="one_click_success">
                        <div class="modal-content">
                            <a href="#" class="close" data-close-modal="#one_click_success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <g clip-path="url(#clip0_506_47034)">
                                        <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_506_47034">
                                            <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                            <div class="write_title">{{ __('locale.text_product_19') }}</div>
                            <p>{{ __('locale.text_product_20') }}</p>
                            <div class="flex flex-start mh150 wrap">
                                <img src="{{ $thumb }}" alt="{!! $title !!}" class="widm border-24" />
                                <div class="caption relative">
                                    <div class="product_name mb30">{!! $title !!}</div>
                                </div>
                            </div>
                            {!! __('locale.text_product_21') !!}
                            <div class="hide_22">{!! __('locale.text_product_22') !!}</div>
                        </div>
                    </div>
                    <input type="hidden" name="product_id" value="{{ $product_id }}" />
                    @if($price || !$discounts->isEmpty())
                        <div class="colon1">
                            <div class="flex">
                                <div class="prices_pro flex-2 wrap">
                                    <span>{{ isset($discounts[$discounts->count()-1]) ? format_price($discounts[$discounts->count()-1]->price, session('currency')) : $price }}{{ isset($discounts[0]) && $discounts[$discounts->count()-1] != $discounts[0] ? ' - ' : '' }}</span>
                                    <span>&nbsp;{{ isset($discounts[0]) && $discounts[$discounts->count()-1] != $discounts[0] ? format_price($discounts[0]->price, session('currency')) : '' }}</span>
                                </div>
                                @if(!$discounts->isEmpty())
                                    <div class="podrob">
                                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="6.5" cy="6.5" r="6.5" fill="#54B0AC"/>
                                            <path d="M7.95339 9.24458L7.85559 9.64441C7.56217 9.76022 7.32778 9.84836 7.15325 9.90897C6.97852 9.96976 6.77551 10 6.5442 10C6.18898 10 5.91269 9.91298 5.71563 9.74014C5.51857 9.56661 5.42001 9.34672 5.42001 9.07996C5.42001 8.9767 5.42717 8.87048 5.44203 8.76226C5.45703 8.65392 5.48087 8.53189 5.51349 8.3955L5.88019 7.0976C5.91281 6.97332 5.94054 6.85556 5.96275 6.7442C5.98527 6.63354 5.99613 6.53171 5.99613 6.44005C5.99613 6.2743 5.96181 6.15837 5.89349 6.09318C5.82517 6.02819 5.6948 5.99519 5.50126 5.99519C5.40646 5.99519 5.30903 6.01037 5.20972 6.03973C5.10997 6.06922 5.02471 6.0977 4.95312 6.12417L5.05118 5.72404C5.2914 5.62623 5.52102 5.54248 5.74066 5.47296C5.9603 5.40326 6.16784 5.36838 6.36414 5.36838C6.71691 5.36838 6.98913 5.45364 7.18029 5.62416C7.37145 5.79481 7.46699 6.01602 7.46699 6.28861C7.46699 6.34501 7.46072 6.44438 7.44723 6.58642C7.43406 6.72877 7.40953 6.8592 7.37377 6.97784L7.00864 8.27053C6.97871 8.37436 6.9518 8.49306 6.9284 8.62663C6.90424 8.75931 6.8927 8.8607 6.8927 8.9287C6.8927 9.10035 6.93097 9.21754 7.0077 9.27984C7.08499 9.34214 7.21812 9.37313 7.40721 9.37313C7.49598 9.37313 7.59698 9.35739 7.70922 9.32646C7.82114 9.29553 7.90276 9.26836 7.95339 9.24458ZM8.04599 3.81746C8.04599 4.04269 7.96111 4.23504 7.79053 4.3932C7.62039 4.55192 7.41536 4.63135 7.17552 4.63135C6.93492 4.63135 6.7294 4.55192 6.55731 4.3932C6.38553 4.23498 6.29946 4.04269 6.29946 3.81746C6.29946 3.59268 6.38553 3.40001 6.55731 3.23984C6.72908 3.07993 6.93498 3 7.17552 3C7.4153 3 7.62039 3.08012 7.79053 3.23984C7.96124 3.40001 8.04599 3.59274 8.04599 3.81746Z" fill="white"/>
                                        </svg>
                                        <ul class="list-un-styled">
                                            @foreach($discounts as $discount)
                                                <li class="flex-2 change_quantity" data-quantity="{{ $discount->quantity }}">
                                                    <div class="count_dis">{{ __('locale.text_filter_price_from') }} {{ $discount->quantity }} {{ __('locale.text_sht') }}</div>
                                                    <div class="price_dis"><span>{{ format_price($discount->price, session('currency')) }}</span>/{{ __('locale.text_sht') }}</div>
                                                    <div><span class="dis_plus">+ </span>{{ __('locale.text_product_v') }}</div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <div class="flex">
                                <div class="quantity_inner">
                                    <button class="bt_minus">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g opacity="0.5" clip-path="url(#clip0_432_57031)">
                                                <path d="M7.72668 6.42832L6.43105 6.42841L0.924298 6.42833C0.566017 6.42842 0.276523 6.71791 0.276523 7.07611C0.276439 7.43439 0.566015 7.72397 0.924296 7.72388L6.43105 7.72396L7.72659 7.72396L13.2334 7.72395C13.4121 7.72395 13.5743 7.65155 13.6915 7.53428C13.8088 7.41701 13.8812 7.25485 13.8812 7.07617C13.8813 6.71789 13.5917 6.42832 13.2334 6.4284L7.72668 6.42832Z" fill="#484848"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_432_57031">
                                                    <rect width="10" height="10" fill="white" transform="translate(0 7.07031) rotate(-45)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </button>
                                    <input type="text" name="quantity" class="quantity" value="1" size="1">
                                    <button class="bt_plus">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g opacity="0.5" clip-path="url(#clip0_432_57029)">
                                                <path d="M7.72668 6.42832L7.7266 0.921574C7.72669 0.563293 7.43711 0.273716 7.07883 0.273801C6.72063 0.273801 6.43106 0.563379 6.43106 0.921575L6.43105 6.42841L0.924298 6.42833C0.566017 6.42842 0.276523 6.71791 0.276523 7.07611C0.276439 7.43439 0.566015 7.72397 0.924296 7.72388L6.43105 7.72396L6.43112 13.2307C6.43104 13.589 6.72062 13.8786 7.0789 13.8785C7.25749 13.8786 7.41973 13.8061 7.537 13.6888C7.65428 13.5715 7.72667 13.4094 7.72667 13.2307L7.72659 7.72396L13.2334 7.72395C13.4121 7.72395 13.5743 7.65155 13.6915 7.53428C13.8088 7.41701 13.8812 7.25485 13.8812 7.07617C13.8813 6.71789 13.5917 6.42832 13.2334 6.4284L7.72668 6.42832Z" fill="#484848"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_432_57029">
                                                    <rect width="10" height="10" fill="white" transform="translate(0 7.07031) rotate(-45)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </button>
                                </div>
                                <div class="pric_sht" data-price="{{ isset($discounts[0]) ? format_price($discounts[0]->price, session('currency')) : $price }}"><span>{{ isset($discounts[0]) ? format_price($discounts[0]->price, session('currency')) : $price }}</span>/{{ __('locale.text_sht') }}</div>
                            </div>
                        </div>
                    @endif
                    @if(!$options->isEmpty())
                        <div class="colon2 options">
                            @foreach($options as $option)
                                <div id="option-{{ $option->id }}" class="option">
                                    <label for="input-option-{{ $option->id }}" class="edit mb15 col-12 {{ $option->required ? 'required' : '' }}" style="display: block">{{ $option->name }}</label>
                                    @if($option->type == 'color')
                                        <div class="form_option">
                                            @foreach($option->product_option_values as $key => $product_option_values)
                                                <div class="form_option-item item-1">
                                                    <input id="input-option-{{ $product_option_values->id }}" type="radio" name="option[{{ $option->option_id }}][{{ $option->id }}]" value="{{ $product_option_values->id }}"{{ $key == 0? ' checked' : '' }}>
                                                    @if($product_option_values->image)
                                                        <label for="input-option-{{ $product_option_values->id }}">
                                                            <img src="{{ asset($product_option_values->image) }}" alt="{{ $product_option_values->metaLang->name }} " />
                                                        </label>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($option->type == 'checkbox')
                                        @foreach($option->product_option_values as $key => $product_option_values)
                                            <div class="flex flex-start check">
                                                <div class="custom_checkbox mt0">
                                                    <input id="input-value-option-{{ $product_option_values->id }}" type="checkbox" name="option[{{ $option->option_id }}][{{ $option->id }}][]" value="{{ $product_option_values->id }}">
                                                    <span></span>
                                                </div>
                                                <label for="input-value-option-{{ $product_option_values->id }}">{{ $product_option_values->metaLang->name }}</label>
                                            </div>
                                        @endforeach
                                    @elseif($option->type == 'radio')
                                        @foreach($option->product_option_values as $key => $product_option_values)
                                            <div class="flex flex-start check">
                                                <div class="custom_radio mt0">
                                                    <input id="input-value-option-{{ $product_option_values->id }}" type="radio" name="option[{{ $option->option_id }}][{{ $option->id }}]" value="{{ $product_option_values->id }}">
                                                    <span></span>
                                                </div>
                                                <label for="input-value-option-{{ $product_option_values->id }}">{{ $product_option_values->metaLang->name }}</label>
                                            </div>
                                        @endforeach
                                    @elseif($option->type == 'select')
                                        <div class="form_group">
                                            <select id="input-option-{{ $option->id }}" name="option[{{ $option->option_id }}][{{ $option->id }}]" class="selectize green_theme"{{ $option->required ? ' required' : '' }}>
                                                @foreach($option->product_option_values as $key => $product_option_values)
                                                    <option value="{{ $product_option_values->id }}">{{ $product_option_values->metaLang->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif($option->type == 'text')
                                        <input type="text" id="input-option-{{ $option->id }}" name="option[{{ $option->option_id }}][{{ $option->id }}]" value="{{ $option->value }}" placeholder="{{ $option->name }}" class="input"{{ $option->required ? ' required' : '' }}>
                                    @elseif($option->type == 'date')
                                        <input type="date" id="input-option-{{ $option->id }}" name="option[{{ $option->option_id }}][{{ $option->id }}]" value="{{ $option->value }}" placeholder="{{ $option->name }}" class="input"{{ $option->required ? ' required' : '' }}>
                                    @elseif($option->type == 'datetime')
                                        <input type="datetime" id="input-option-{{ $option->id }}" name="option[{{ $option->option_id }}][{{ $option->id }}]" value="{{ $option->value }}" placeholder="{{ $option->name }}" class="input"{{ $option->required ? ' required' : '' }}>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="colon3">
                        <a href="#" class="btn-default" id="button-cart">{{ __('locale.text_product_10') }}</a>
                        <div class="flex">
                            <a href="#" class="b_zakaz" onclick="one_click({{ $product_id }});return false;">{{ __('locale.text_product_11') }}</a>
                            <a href="#" class="izbr product_wishlist-{{ $product_id }}" onclick="wishlist.add({{ $product_id }});return false;">
                                <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.8891 1.76381C17.537 -0.587618 13.7107 -0.587618 11.3593 1.76381L10.8131 2.30965L10.2672 1.76381C7.91582 -0.587936 4.08919 -0.587936 1.73776 1.76381C-0.565925 4.06749 -0.580884 7.71908 1.70307 10.258C3.78619 12.5728 9.92988 17.5739 10.1905 17.7855C10.3675 17.9294 10.5804 17.9994 10.7921 17.9994C10.7991 17.9994 10.8061 17.9994 10.8128 17.9991C11.0317 18.0093 11.2523 17.9342 11.435 17.7855C11.6957 17.5739 17.84 12.5728 19.9238 10.2576C22.2074 7.71908 22.1924 4.06749 19.8891 1.76381ZM18.5042 8.98041C16.8801 10.7847 12.4156 14.506 10.8128 15.8265C9.20994 14.5063 4.74643 10.7854 3.12258 8.98073C1.52929 7.20984 1.51433 4.68781 3.08789 3.11425C3.89154 2.31093 4.94694 1.90894 6.00235 1.90894C7.05775 1.90894 8.11316 2.31061 8.9168 3.11425L10.1173 4.31479C10.2602 4.4577 10.4404 4.543 10.6294 4.57292C10.9363 4.6388 11.2692 4.55318 11.5079 4.31511L12.7091 3.11425C14.3167 1.50728 16.9316 1.5076 18.5383 3.11425C20.1119 4.68781 20.0969 7.20983 18.5042 8.98041Z" fill="#BED0D6"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="tabl_opt">
                        <div class="dis_on color_link pointer" onclick="fadeToggle($(this).next())">{!! __('locale.text_product_discout_on') !!}</div>
                        @if(!$discounts->isEmpty())
                            <div class="tabl_dis">
                                <div class="zag_tabl">{{ __('locale.text_product_9') }}</div>
                                @foreach($discounts as $discount)
                                    <div class="flex_opt flex">
                                        <div class="left_opt">{{ __('locale.text_filter_price_from') }} {{ $discount->quantity }} {{ __('locale.text_sht') }}</div>
                                        <div class="rig_opt">{{ format_price($discount->price, session('currency')) }}/{{ __('locale.text_sht') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="desh pointer" data-target-modal="#popup1">{!! __('locale.text_product_8') !!}</div>
                    </div>

                    @if(!empty($delivery_block))
                        <div class="dop_info">
                            {!! $delivery_block !!}
                        </div>
                    @endif
                </div>
            </div>
            @if(!$products->isEmpty())
                <div class="product_related relative">
                    <div class="h2">{{ __('locale.text_product_32') }}</div>
                    <div class="owl-carousel">
                        @foreach($products as $product)
                            @include('pages.site.product_item', ['product' => $product, 'width' => 238, 'height' => 238])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
    <div class="float_product">
        <div class="container flex-2">
            <div class="flex flex-start col-6">
                <img src="{{ $thumb2 }}" alt="{!! $title !!}" class="flimg border-24 mr30"/>
                <span class="name fbold">{{ \Str::limit($title, 34, '...') }}</span>
            </div>
            <div class="flex">
                <div class="quantity_inner">
                    <button class="bt_minus">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.5" clip-path="url(#clip0_432_57031)">
                                <path d="M7.72668 6.42832L6.43105 6.42841L0.924298 6.42833C0.566017 6.42842 0.276523 6.71791 0.276523 7.07611C0.276439 7.43439 0.566015 7.72397 0.924296 7.72388L6.43105 7.72396L7.72659 7.72396L13.2334 7.72395C13.4121 7.72395 13.5743 7.65155 13.6915 7.53428C13.8088 7.41701 13.8812 7.25485 13.8812 7.07617C13.8813 6.71789 13.5917 6.42832 13.2334 6.4284L7.72668 6.42832Z" fill="#484848"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_432_57031">
                                    <rect width="10" height="10" fill="white" transform="translate(0 7.07031) rotate(-45)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                    <input type="text" name="quantity" class="quantity" value="1" size="1">
                    <button class="bt_plus">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.5" clip-path="url(#clip0_432_57029)">
                                <path d="M7.72668 6.42832L7.7266 0.921574C7.72669 0.563293 7.43711 0.273716 7.07883 0.273801C6.72063 0.273801 6.43106 0.563379 6.43106 0.921575L6.43105 6.42841L0.924298 6.42833C0.566017 6.42842 0.276523 6.71791 0.276523 7.07611C0.276439 7.43439 0.566015 7.72397 0.924296 7.72388L6.43105 7.72396L6.43112 13.2307C6.43104 13.589 6.72062 13.8786 7.0789 13.8785C7.25749 13.8786 7.41973 13.8061 7.537 13.6888C7.65428 13.5715 7.72667 13.4094 7.72667 13.2307L7.72659 7.72396L13.2334 7.72395C13.4121 7.72395 13.5743 7.65155 13.6915 7.53428C13.8088 7.41701 13.8812 7.25485 13.8812 7.07617C13.8813 6.71789 13.5917 6.42832 13.2334 6.4284L7.72668 6.42832Z" fill="#484848"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_432_57029">
                                    <rect width="10" height="10" fill="white" transform="translate(0 7.07031) rotate(-45)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                </div>
                <div class="pric" data-price="{{ isset($discounts[0]) ? format_price($discounts[0]->price, session('currency')) : $price }}">{{ isset($discounts[0]) ? format_price($discounts[0]->price, session('currency')) : $price }}</div>
                <a href="#" class="btn-default float_cart" onclick="$('#button-cart').trigger('click');return false;">
                    <svg class="hid-sm hid-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <g clip-path="url(#clip0_506_45561)">
                            <path d="M19.3275 16.2566H10.9506C9.74131 16.2566 8.4467 15.3753 8.00369 14.2498L4.61842 5.65683C4.43969 5.20301 3.80984 4.77425 3.32209 4.77425H0.88725C0.397283 4.7745 0 4.37722 0 3.88725C0 3.39728 0.397283 3 0.88725 3H3.32233C4.53188 3 5.82625 3.88135 6.26926 5.00682L9.65453 13.5998C9.83326 14.0536 10.4631 14.4824 10.9506 14.4824H19.3275C19.8032 14.4824 20.4031 14.0583 20.5616 13.6101L22.1935 8.99859C22.2393 8.86952 22.2277 8.79085 22.2152 8.7729C22.2024 8.75495 22.1321 8.71808 21.9951 8.71808H10.442C9.952 8.71808 9.55472 8.32079 9.55472 7.83083C9.55472 7.34086 9.952 6.94358 10.442 6.94358H21.9951C22.6941 6.94358 23.3023 7.23712 23.6642 7.74872C24.0258 8.26032 24.0996 8.93147 23.8663 9.59033L22.2344 14.2019C21.8267 15.3539 20.5498 16.2566 19.3275 16.2566Z" fill="#54B0AC"/>
                            <path d="M8.67089 21.2773C9.75981 21.2773 10.6426 20.3946 10.6426 19.3057C10.6426 18.2167 9.75981 17.334 8.67089 17.334C7.58196 17.334 6.69922 18.2167 6.69922 19.3057C6.69922 20.3946 7.58196 21.2773 8.67089 21.2773Z" fill="#54B0AC"/>
                            <path d="M21.3115 21.2773C22.4004 21.2773 23.2832 20.3946 23.2832 19.3057C23.2832 18.2167 22.4004 17.334 21.3115 17.334C20.2226 17.334 19.3398 18.2167 19.3398 19.3057C19.3398 20.3946 20.2226 21.2773 21.3115 21.2773Z" fill="#54B0AC"/>
                            <path d="M19.2959 10.846H11.4591C11.214 10.846 11.0156 10.6473 11.0156 10.4025C11.0156 10.1574 11.2143 9.95898 11.4591 9.95898H19.2959C19.541 9.95898 19.7394 10.1576 19.7394 10.4025C19.7394 10.6473 19.541 10.846 19.2959 10.846Z" fill="#54B0AC"/>
                            <path d="M18.6959 13.0647H12.258C12.0131 13.0647 11.8145 12.8661 11.8145 12.6212C11.8145 12.3761 12.0131 12.1777 12.258 12.1777H18.6961C18.9412 12.1777 19.1396 12.3764 19.1396 12.6212C19.1394 12.8661 18.941 13.0647 18.6959 13.0647Z" fill="#54B0AC"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_506_45561">
                                <rect width="24" height="24" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                    <span>{{ __('locale.text_product_10') }}</span>
                </a>
                <a href="#" class="izbr product_wishlist-{{ $product_id }}" onclick="wishlist.add({{ $product_id }});return false;">
                    <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.8891 1.76381C17.537 -0.587618 13.7107 -0.587618 11.3593 1.76381L10.8131 2.30965L10.2672 1.76381C7.91582 -0.587936 4.08919 -0.587936 1.73776 1.76381C-0.565925 4.06749 -0.580884 7.71908 1.70307 10.258C3.78619 12.5728 9.92988 17.5739 10.1905 17.7855C10.3675 17.9294 10.5804 17.9994 10.7921 17.9994C10.7991 17.9994 10.8061 17.9994 10.8128 17.9991C11.0317 18.0093 11.2523 17.9342 11.435 17.7855C11.6957 17.5739 17.84 12.5728 19.9238 10.2576C22.2074 7.71908 22.1924 4.06749 19.8891 1.76381ZM18.5042 8.98041C16.8801 10.7847 12.4156 14.506 10.8128 15.8265C9.20994 14.5063 4.74643 10.7854 3.12258 8.98073C1.52929 7.20984 1.51433 4.68781 3.08789 3.11425C3.89154 2.31093 4.94694 1.90894 6.00235 1.90894C7.05775 1.90894 8.11316 2.31061 8.9168 3.11425L10.1173 4.31479C10.2602 4.4577 10.4404 4.543 10.6294 4.57292C10.9363 4.6388 11.2692 4.55318 11.5079 4.31511L12.7091 3.11425C14.3167 1.50728 16.9316 1.5076 18.5383 3.11425C20.1119 4.68781 20.0969 7.20983 18.5042 8.98041Z" fill="#BED0D6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    @if($images)
    <div class="modal max_size overflow-y" id="fancy">
        <div class="modal-content" style="padding: 3% 5%;height: 100vh">
            <a href="#" class="close" data-close-modal="#fancy">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <g clip-path="url(#clip0_506_47034)">
                        <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_506_47034">
                            <rect width="20" height="20" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
            <div class="dop_fotos overflow-y">
                @if($thumb)
                    <div class="dop_foto2 activ">
                        <a href="#" data-index="0"><img src="{{ $thumb }}" alt="{!! $title !!}"></a>
                    </div>
                @endif
                @foreach($thumbs as $key => $thumb_image)
                    @php($key++)
                    <div class="dop_foto2">
                        <a href="#" data-index="{{ $key }}"><img src="{{ $thumb_image['image'] }}" alt="{{ $thumb_image['alt'] }}"></a>
                    </div>
                @endforeach
            </div>
            <div class="owl-carousel slider_images">
                <div class="item">
                    <img src="{{ $popup }}" alt="{!! $title !!}" />
                    <div class="fancybox__caption"><span>{{ $name }}</span><span>{!! $title !!}</span></div>
                </div>
                @foreach($images as $key => $i)
                   @php($key++)
                    <div class="item">
                        <img src="{{ $i['popup'] }}" alt="{!! $title !!}" />
                        <div class="fancybox__caption"><span>{{ $name }}</span><span>{!! $title !!}</span></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <script src="https://yastatic.net/share2/share.js"></script>
    <div class="ya-share2" style="display: none" data-curtain data-shape="round" data-color-scheme="whiteblack" data-services="twitter,vkontakte,facebook,odnoklassniki,telegram,viber,whatsapp"></div>
    <div class="modal overflow-y" id="popup1">
        <div class="modal-content">
            <a href="#" class="close" data-close-modal="#popup1">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <g clip-path="url(#clip0_506_47034)">
                        <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_506_47034">
                            <rect width="20" height="20" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
            <div class="write_title">{{ __('locale.text_product_13') }}</div>
            <p>{{ __('locale.text_product_14') }}</p>
            <div class="flex flex-start mh150 wrap">
                <img src="{{ $thumb }}" alt="{!! $title !!}" class="widm border-24" />
                <div class="caption relative">
                    <div class="product_name">{!! $title !!}</div>
                    @if($price)
                        @if(!$discounts->isEmpty())
                            <div class="flex-2 flex-start">
                                @endif
                                <div class="price">
                                    <span class="price_new">{{ isset($discounts[0]) ? format_price($discounts[0]->price, session('currency')) : format_price($price, session('currency')) }}</span>
                                </div>
                                @if(!$discounts->isEmpty())
                                    <span class="price_discount">{{ __('locale.text_filter_price_from') }} {{ format_price($discounts[$discounts->count()-1]->price, session('currency')) }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <p class="edit mb20">{{ __('locale.text_product_15') }}</p>
            <form action="{{ route(session('route_url') . '_form_action') }}" method="post" enctype="multipart/form-data" class="validate_js" novalidate>
                <input type="hidden" name="type" value="free" />
                <div class="form_group">
                    <input type="text" id="free_name" name="name" value="{{ !empty($customer['firstname']) ? $customer['firstname'] : '' }}" class="input" required />
                    <label class="required" for="free_name">{{ __('locale.text_write_name') }}</label>
                </div>
                <div class="form_group">
                    <input type="text" id="free_phone" name="phone" value="{{ !empty($customer['phone']) ? $customer['phone'] : '' }}" class="input" required />
                    <label class="required" for="free_phone">{{ __('locale.text_write_phone') }}</label>
                </div>
                <div class="form_group">
                    <input type="email" id="free_email" name="email" value="{{ !empty($customer['email']) ? $customer['email'] : '' }}" class="input" required />
                    <label class="required" for="free_email">{{ __('locale.text_write_email') }}</label>
                </div>
                <div class="form_group">
                    <input type="text" id="free_text" name="text" class="input" required />
                    <label class="required" for="free_text">{{ __('locale.text_product_16') }}</label>
                </div>
                <div class="flex-2">
                    <div class="policy">{!! sprintf(__('locale.text_write_policy'), $policy) !!}</div>
                    <input type="submit" value="{{ __('locale.text_write_button') }}" class="btn-default" />
                </div>
            </form>
        </div>
    </div>
    <div class="modal overflow-y" id="popup2">
        <div class="modal-content">
            <a href="#" class="close" data-close-modal="#popup2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <g clip-path="url(#clip0_506_47034)">
                        <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_506_47034">
                            <rect width="20" height="20" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
            <div class="write_title">{{ __('locale.text_product_2') }}</div>
            <div class="flex_ss">
                <a href="#" class="viber" onclick="$('.ya-share2__item_service_viber').trigger('click');$('.ya-share2__item_service_viber').trigger('click');return false;">
                    <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_107_5929)">
                            <path d="M6.32788 19.5292C6.29688 20.9332 6.12388 22.5374 7.39888 22.9246C8.64888 23.3146 9.37388 22.3515 11.0039 20.3965C17.0819 20.7942 22.4469 19.1564 23.1649 13.2847C23.8079 8.06468 22.9769 4.15947 20.8379 2.30126C17.7009 -0.579489 7.48988 -0.982948 3.75188 2.44118C2.21288 3.97547 1.46288 6.22659 1.39288 9.52613C1.33588 12.221 1.21988 17.7314 6.32788 19.5292ZM2.89288 9.55584C2.95488 6.63963 3.57088 4.69518 4.80988 3.45893C7.93088 0.601177 17.1799 0.931802 19.8149 3.35447C21.5739 4.88109 22.2529 8.43938 21.6759 13.117C21.1519 17.4065 18.2699 18.1387 17.1849 18.4137C14.9379 18.982 12.7959 19.1056 10.7409 18.9351C10.1999 18.8795 9.98688 19.3701 9.78988 19.5484C8.81088 20.6763 8.10488 21.4909 7.92188 21.6002C7.78688 21.4076 7.80788 20.4962 7.82388 19.764C7.77488 19.4343 8.08288 18.523 7.27988 18.3169C2.73488 17.0308 2.83788 12.1692 2.89288 9.55584Z" fill="#54B0AC"/>
                            <path d="M5.76179 7.64728C6.56279 9.17774 7.34379 11.8822 10.3428 14.2358C11.3838 15.0581 14.4998 16.9556 15.7198 16.9556C17.4628 16.9556 19.4538 14.3278 17.9958 13.1558C17.3398 12.5415 16.4168 11.9368 15.8248 11.6042C15.8238 11.6033 15.8228 11.6033 15.8228 11.6033C14.2858 10.7513 13.4438 12.3192 13.3438 12.3805C11.0918 14.3125 8.31779 10.9564 10.2618 9.33299C10.5008 9.04836 11.9268 8.38903 11.0808 6.8902C10.3308 5.58974 9.83579 5.1422 9.55579 4.77132C8.07879 3.00416 4.88279 5.93953 5.76179 7.64728ZM14.9358 12.7964C15.0378 12.8577 15.9268 13.2305 16.9878 14.2205C17.1228 14.3288 16.2228 15.5823 15.6498 15.5104C15.5368 15.4615 14.7198 15.2747 12.9648 14.2464C14.6308 13.7039 14.6168 12.8539 14.9358 12.7964ZM8.26979 5.68078C8.33179 5.64916 8.36379 5.64724 8.36079 5.63957C8.64879 6.02387 9.07879 6.38995 9.76479 7.57924C9.94679 7.9022 9.49779 8.02774 9.20779 8.31141C8.79479 8.66216 8.49179 9.07999 8.30579 9.54095C7.69179 8.43024 7.41679 7.63003 7.13679 7.07036C7.02479 6.56053 7.79379 5.94145 8.26979 5.68078Z" fill="#54B0AC"/>
                            <path d="M12.8112 8.19384C12.8222 8.1948 13.9102 8.328 14.1052 9.45021C14.2702 10.4028 15.7462 10.1469 15.5842 9.21446C15.2812 7.47125 13.7502 6.85025 12.9812 6.76592C11.9962 6.65955 11.8272 8.08459 12.8112 8.19384Z" fill="#54B0AC"/>
                            <path d="M12.0315 5.09463C12.0485 5.09463 14.4095 5.05438 15.9465 6.62988C17.4645 8.18621 17.1295 10.4488 17.1265 10.469C16.9735 11.4014 18.4515 11.6487 18.6075 10.6922C18.6275 10.5725 19.0685 7.72525 17.0425 5.64758C15.0315 3.58525 12.1065 3.64946 11.9854 3.65713C10.9604 3.68971 11.0844 5.16938 12.0315 5.09463Z" fill="#54B0AC"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_107_5929">
                                <rect width="24" height="23" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>

                </a>
                <a href="#" class="whap" onclick="$('.ya-share2__item_service_whatsapp').trigger('click');$('.ya-share2__item_service_whatsapp').trigger('click');return false;">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_107_5934)">
                            <path d="M12.2185 16.5698C15.931 17.4535 17.2373 14.2782 16.5828 13.0929C16.3206 12.6199 13.9648 11.7115 13.8502 11.6675C12.8382 11.299 12.4642 11.9865 11.7629 12.8912C10.4163 12.2175 9.51343 11.6867 8.58577 10.1779C8.63802 10.1128 8.70768 10.0358 8.75993 9.97716C9.08443 9.61416 9.69218 8.93766 9.27785 8.10991C8.92952 7.416 8.75627 6.932 8.62885 6.57725C8.25943 5.54966 7.97802 5.271 6.70568 5.271C5.3811 5.271 4.35352 7.01175 4.35352 8.5105C4.35352 11.7821 8.75168 15.7421 12.2185 16.5698ZM6.70568 6.646C6.98068 6.646 7.12002 6.65516 7.18968 6.66616C7.33818 6.98058 7.48852 7.63691 8.04218 8.65808C7.95602 8.97983 6.73227 9.73425 7.29968 10.7096C8.45927 12.6786 9.61977 13.3615 11.2826 14.1883C12.3899 14.6888 12.7438 13.7822 13.4459 12.9857C13.7713 13.1177 14.8695 13.6035 15.3517 13.8473C15.3086 14.797 14.3919 15.6733 12.5375 15.2315C9.35485 14.4716 5.7276 10.8911 5.7276 8.50958C5.72852 7.60391 6.36377 6.646 6.70568 6.646Z" fill="#54B0AC"/>
                            <path d="M0.0764727 21.1613C-0.0436107 21.5958 0.281806 22.0312 0.739223 22.0312C0.904223 22.0312 0.634723 22.0734 6.09989 20.6562C8.32464 21.7892 10.0159 21.7535 11.0957 21.8552C20.7711 21.8552 25.5103 10.055 18.8516 3.18912C9.72439 -5.63288 -4.49036 5.33046 1.45789 16.1389L0.0764727 21.1613ZM17.7571 4.04529C17.9157 4.33495 22.4642 8.43704 19.9315 14.5668C18.3722 18.3435 14.7743 20.6709 10.7428 20.4527C7.46297 20.2703 6.61414 19.0988 6.01006 19.2592L1.71731 20.3775L2.85856 16.2305C2.90806 16.0527 2.88331 15.863 2.79164 15.7035C-2.69278 6.23979 9.70881 -3.49063 17.7571 4.04529Z" fill="#54B0AC"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_107_5934">
                                <rect width="22" height="22" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>

                </a>
                <a href="#" class="teleg" onclick="$('.ya-share2__item_service_telegram').trigger('click');$('.ya-share2__item_service_telegram').trigger('click');return false;">
                    <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_107_5923)">
                            <path d="M0.396924 10.2629L6.02138 12.9441C6.23892 13.0468 6.49576 13.0395 6.70371 12.9231L11.71 10.1391L8.6798 12.7076C8.52551 12.8387 8.43734 13.0257 8.43734 13.2228V19.4791C8.43734 20.1391 9.31805 20.4168 9.73109 19.8925L12.1624 16.8006L18.175 20.0758C18.5966 20.3077 19.1371 20.0749 19.2349 19.6138L22.9848 1.73881C23.097 1.20165 22.5383 0.763481 22.0188 0.963314L0.456341 9.00706C-0.122493 9.2234 -0.159868 9.99798 0.396924 10.2629ZM21.3173 2.70131L18.0216 18.4102L12.3243 15.3064C12.0138 15.1368 11.6142 15.212 11.3938 15.4906L9.87484 17.4221V13.5317L18.0695 6.58706C18.7183 6.03798 17.9728 5.06173 17.232 5.4779L6.31655 11.5481L2.49088 9.72481L21.3173 2.70131Z" fill="#54B0AC"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_107_5923">
                                <rect width="23" height="22" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>

                </a>
                <a href="#" class="vk" onclick="$('.ya-share2__item_service_vkontakte').trigger('click');$('.ya-share2__item_service_vkontakte').trigger('click');return false;">
                    <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_107_5937)">
                            <path d="M13.6631 21.9375C17.5691 21.9375 16.1764 19.4648 16.4779 18.7537C16.4734 18.2227 16.4689 17.712 16.4869 17.4015C16.7344 17.4713 17.3182 17.7671 18.5243 18.9394C20.3861 20.8181 20.862 21.9375 22.3661 21.9375H25.1347C26.0122 21.9375 26.469 21.5741 26.6974 21.2693C26.9179 20.9745 27.1339 20.457 26.8976 19.6515C26.28 17.712 22.6777 14.3629 22.4539 14.0096C22.4876 13.9444 22.5416 13.8577 22.5697 13.8127H22.5675C23.2785 12.8734 25.992 8.80762 26.3914 7.18087C26.3925 7.17862 26.3936 7.17525 26.3936 7.17188C26.6096 6.42937 26.4116 5.94788 26.2069 5.67563C25.8986 5.26838 25.4081 5.0625 24.7455 5.0625H21.9769C21.0499 5.0625 20.3468 5.52937 19.9913 6.381C19.3961 7.89412 17.7244 11.0059 16.4711 12.1073C16.4329 10.5469 16.4587 9.3555 16.479 8.47012C16.5195 6.74325 16.65 5.0625 14.8579 5.0625H10.5064C9.38363 5.0625 8.30925 6.28875 9.4725 7.7445C10.4895 9.02025 9.83812 9.73125 10.0575 13.2705C9.2025 12.3536 7.6815 9.8775 6.606 6.71288C6.3045 5.85675 5.84775 5.06362 4.56187 5.06362H1.79325C0.6705 5.06362 0 5.67562 0 6.7005C0 9.00225 5.09513 21.9375 13.6631 21.9375ZM4.56187 6.75112C4.806 6.75112 4.83075 6.75113 5.01188 7.26525C6.11325 10.5086 8.58375 15.3079 10.3883 15.3079C11.7439 15.3079 11.7439 13.9185 11.7439 13.3954L11.7428 9.23063C11.6685 7.8525 11.1668 7.16625 10.8371 6.75L14.7836 6.7545C14.7859 6.77363 14.7611 11.3614 14.7949 12.4729C14.7949 14.0512 16.0481 14.9558 18.0045 12.9758C20.0689 10.6459 21.4965 7.16288 21.5539 7.02113C21.6383 6.81863 21.7114 6.75 21.9769 6.75H24.7455H24.7568C24.7556 6.75338 24.7556 6.75675 24.7545 6.76012C24.5014 7.94137 22.0028 11.7056 21.1669 12.8745C21.1534 12.8925 21.141 12.9116 21.1286 12.9308C20.7607 13.5315 20.4615 14.1952 21.1793 15.129H21.1804C21.2456 15.2078 21.4155 15.3923 21.663 15.6488C22.4325 16.443 25.0717 19.1587 25.3057 20.2387C25.1505 20.2635 24.9818 20.2455 22.3661 20.2511C21.8092 20.2511 21.3739 19.4186 19.7122 17.7424C18.2183 16.2889 17.2485 15.6949 16.3654 15.6949C14.6509 15.6949 14.7758 17.0865 14.7915 18.7695C14.7971 20.5942 14.7859 20.0171 14.7983 20.1319C14.6981 20.1713 14.4113 20.25 13.6631 20.25C6.525 20.25 1.8765 8.92013 1.69762 6.7545C1.7595 6.74888 2.61112 6.75225 4.56187 6.75112Z" fill="#54B0AC"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_107_5937">
                                <rect width="27" height="27" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>

                </a>
                <a href="#" class="ok" onclick="$('.ya-share2__item_service_odnoklassniki').trigger('click');$('.ya-share2__item_service_odnoklassniki').trigger('click');return false;">
                    <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_107_5939)">
                            <path d="M19.2217 11.9636C18.6157 10.8299 17.1407 10.3996 15.6967 11.4873C15.6827 11.4978 14.2527 12.5491 12.0017 12.5491C9.75066 12.5491 8.32066 11.4969 8.30666 11.4873C6.86866 10.3986 5.38166 10.8251 4.77966 11.9665C4.77766 11.9693 4.77666 11.9722 4.77466 11.976C3.87966 13.7308 5.35766 14.645 6.54666 15.38C7.28166 15.8333 8.22166 16.1755 9.35066 16.4016L6.14566 19.4798C4.21066 21.3303 7.24266 24.2504 9.19766 22.419L12.0117 19.7088C13.0947 20.7486 14.1037 21.7194 14.8317 22.4295C16.7617 24.2456 19.8137 21.3763 17.8897 19.4836C15.5947 17.2862 15.1007 16.8118 14.6667 16.4026C17.0627 15.911 20.3957 14.3 19.2217 11.9636ZM13.1967 15.1788C12.6787 14.9402 11.9907 15.8467 12.5567 16.4102L12.5627 16.416C12.5637 16.417 12.5657 16.4189 12.5667 16.4198C12.5687 16.4217 12.5697 16.4237 12.5717 16.4246C12.8597 16.6997 16.7347 20.4094 16.8207 20.4927C17.3587 21.0227 16.4107 21.8986 15.8897 21.4118C14.9597 20.5052 13.4847 19.0897 12.5397 18.1822C12.2477 17.9014 11.7707 17.8995 11.4777 18.1822L8.13966 21.3964C7.61066 21.8919 6.66766 21.0074 7.20366 20.4947L11.4477 16.4198C11.4497 16.4179 11.4517 16.416 11.4527 16.4141C11.9797 15.9177 11.4477 14.9977 10.8277 15.173C9.35066 15.0073 8.15366 14.6613 7.35666 14.1706C5.81666 13.2171 5.89366 13.0494 6.11666 12.6114C6.76466 11.4221 7.91266 13.9866 11.9997 13.9866C14.7987 13.9866 16.5547 12.6689 16.6237 12.6162C17.0797 12.2731 17.6147 12.1131 17.8777 12.6028C18.1007 13.0465 18.1837 13.2123 16.6397 14.1735C15.5857 14.8165 14.0957 15.0763 13.1967 15.1788Z" fill="#54B0AC"/>
                            <path d="M11.999 9.58084C13.953 9.58084 15.542 8.05517 15.542 6.18067C15.542 4.29851 13.953 2.76709 11.999 2.76709C10.046 2.76709 8.45703 4.29851 8.45703 6.18067C8.45603 8.05517 10.045 9.58084 11.999 9.58084ZM11.999 4.20459C13.125 4.20459 14.042 5.09105 14.042 6.18067C14.042 7.26263 13.125 8.14334 11.999 8.14334C10.873 8.14334 9.95603 7.26263 9.95603 6.17972C9.95603 5.09105 10.872 4.20459 11.999 4.20459Z" fill="#54B0AC"/>
                            <path d="M11.9995 12.3462C15.5485 12.3462 18.4365 9.5795 18.4365 6.17933C18.4355 2.77246 15.5485 0 11.9995 0C8.4505 0 5.5625 2.77246 5.5625 6.17933C5.5625 9.5795 8.4495 12.3462 11.9995 12.3462ZM11.9995 1.4375C14.7215 1.4375 16.9365 3.565 16.9365 6.17933C16.9365 8.78696 14.7225 10.9087 11.9995 10.9087C9.2765 10.9087 7.0625 8.78696 7.0625 6.17933C7.0625 3.565 9.2765 1.4375 11.9995 1.4375Z" fill="#54B0AC"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_107_5939">
                                <rect width="24" height="23" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>

                </a>
                <a href="#" class="fecb" onclick="$('.ya-share2__item_service_facebook').trigger('click');$('.ya-share2__item_service_facebook').trigger('click');return false;">
                    <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_107_5945)">
                            <path d="M6.5293 13.3563H8.94238V22.2803C8.94238 22.677 9.26342 22.999 9.66113 22.999L13.5012 23C13.8989 23 14.2199 22.677 14.2199 22.2812V13.3572H16.5075C16.8697 13.3572 17.1754 13.087 17.2205 12.7276L17.6977 8.89429C17.7514 8.46688 17.4169 8.08642 16.9847 8.08642H14.2199C14.3282 5.71837 13.803 5.01783 15.3431 5.01783C16.3858 4.89325 18.0303 5.42129 18.0303 4.29908V0.871125C18.0303 0.51175 17.7648 0.207958 17.4093 0.159083C17.1083 0.117875 15.919 0 14.4442 0C7.72626 0 9.10913 7.44817 8.94334 8.08546H6.5293C6.13255 8.08546 5.81055 8.40746 5.81055 8.80421V12.6375C5.81055 13.0343 6.13255 13.3563 6.5293 13.3563ZM7.24805 9.52392H9.66113C10.0579 9.52392 10.3799 9.20192 10.3799 8.80517V5.78546C10.3799 3.02258 11.8605 1.43846 14.4432 1.43846C15.2846 1.43846 16.059 1.47967 16.5918 1.52183V3.58129C16.2065 3.77775 12.7815 2.73412 12.7815 6.16592V8.80612C12.7815 9.20288 13.1035 9.52487 13.5002 9.52487H16.1701L15.8711 11.9207H13.5002C13.1035 11.9207 12.7815 12.2427 12.7815 12.6395V21.5625H10.3808V12.6385C10.3808 12.2417 10.0588 11.9197 9.66209 11.9197H7.24805V9.52392Z" fill="#54B0AC"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_107_5945">
                                <rect width="23" height="23" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                </a>
                <a href="#" class="twi" onclick="$('.ya-share2__item_service_twitter').trigger('click');$('.ya-share2__item_service_twitter').trigger('click');return false;">
                    <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.473603 18.779C2.6956 20.1302 5.2816 20.8442 7.9516 20.8442C11.8616 20.8442 15.4446 19.4048 18.0416 16.7914C20.5266 14.2902 21.8936 10.9293 21.8256 7.51857C22.7676 6.74615 23.8756 5.27128 23.8756 3.83378C23.8756 3.28274 23.2516 2.93295 22.7456 3.21374C21.8606 3.71207 21.0536 3.8424 20.2236 3.61911C18.5286 2.03595 16.0056 1.70245 13.8796 2.8007C12.0216 3.75903 10.9886 5.51278 11.0816 7.42945C7.9426 7.0624 5.0426 5.55399 3.0206 3.2099C2.6886 2.82753 2.0586 2.87257 1.7946 3.30574C0.820603 4.90424 0.830603 6.7567 1.6776 8.25265C1.2746 8.3207 1.0256 8.64557 1.0256 8.99728C1.0256 10.5009 1.7316 11.8828 2.8686 12.8258C2.6566 13.0213 2.5866 13.3117 2.6766 13.5704C3.1766 15.0099 4.3086 16.1349 5.7236 16.6984C4.1846 17.4028 2.4826 17.6376 0.967603 17.4594C0.183603 17.3578 -0.203397 18.3679 0.473603 18.779ZM8.1566 16.9447C8.7176 16.5317 8.4196 15.6711 7.7156 15.6567C6.4756 15.6318 5.3466 15.0463 4.6436 14.1253C4.9826 14.1042 5.3336 14.0544 5.6676 13.9682C6.4286 13.7707 6.3926 12.7185 5.6196 12.5699C4.2166 12.2997 3.1156 11.3203 2.7026 10.0591C3.0796 10.1482 3.4636 10.1981 3.8466 10.2048C4.6056 10.2086 4.8926 9.27615 4.2736 8.88611C2.8786 8.0054 2.2836 6.45003 2.6776 4.98761C5.1136 7.35278 8.4186 8.77782 11.9146 8.93882C12.4156 8.96853 12.7916 8.51715 12.6816 8.06003C12.2066 6.08682 13.3566 4.70395 14.5916 4.06665C15.8136 3.43415 17.7756 3.23674 19.2796 4.74899C19.7266 5.20036 21.2346 5.21761 22.0016 5.04607C21.6576 5.66707 21.1286 6.25645 20.6336 6.58803C20.4226 6.72986 20.3016 6.96561 20.3146 7.2119C20.4756 10.36 19.2516 13.4899 16.9566 15.7986C14.6446 18.1244 11.4476 19.4057 7.9526 19.4057C6.5626 19.4057 5.1996 19.1892 3.9116 18.7713C5.4516 18.4857 6.9146 17.8609 8.1566 16.9447Z" fill="#54B0AC"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="modal overflow-y" id="popup4">
        <div class="modal-content">
            <a href="#" class="close" data-close-modal="#popup4">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <g clip-path="url(#clip0_506_47034)">
                        <path d="M11.8323 10.0156L19.6199 2.22773C20.1267 1.72116 20.1267 0.902116 19.6199 0.39555C19.1134 -0.111017 18.2943 -0.111017 17.7878 0.39555L9.9999 8.18338L2.21229 0.39555C1.70548 -0.111017 0.886671 -0.111017 0.380103 0.39555C-0.126701 0.902116 -0.126701 1.72116 0.380103 2.22773L8.16772 10.0156L0.380103 17.8034C-0.126701 18.31 -0.126701 19.129 0.380103 19.6356C0.632557 19.8883 0.964495 20.0152 1.2962 20.0152C1.6279 20.0152 1.9596 19.8883 2.21229 19.6356L9.9999 11.8477L17.7878 19.6356C18.0404 19.8883 18.3721 20.0152 18.7038 20.0152C19.0355 20.0152 19.3672 19.8883 19.6199 19.6356C20.1267 19.129 20.1267 18.31 19.6199 17.8034L11.8323 10.0156Z" fill="#BED0D6"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_506_47034">
                            <rect width="20" height="20" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>
            </a>
            <div class="write_title">{{ __('locale.text_product_29') }}</div>
            <form action="{{ route('review_write') }}" method="post" enctype="multipart/form-data" class="validate_js col-12 account_right" novalidate>
                <input type="hidden" name="id" value="{{ $product_id }}" />
                <div class="flex-2 mode wrap">
                    <div class="mode_left">
                        <div class="h5">{{ __('locale.text_account_review_8') }}</div>
                        <textarea name="disadvantages" rows="5" class="textarea"></textarea>
                        <div class="h5">{{ __('locale.text_account_review_9') }}</div>
                        <textarea name="dignities" rows="1" class="textarea"></textarea>
                    </div>
                    <div class="text-center">
                        <div class="rtext" style="margin-bottom: 10px">{{ __('locale.text_account_reviews_2') }}<span class="color_link rat"></span></div>
                        <div class="rating mb30">
                            @foreach([1, 2, 3, 4, 5] as $rating)
                                <label for="rat-{{ $rating }}" class="star">
                                    <input id="rat-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" style="display: none" />
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                                        <path d="M20.4167 9.06366C20.8274 8.66321 20.9725 8.07549 20.7953 7.52916C20.6177 6.98282 20.1551 6.59282 19.5867 6.51005L14.5336 5.77561C14.3184 5.74426 14.1325 5.60925 14.0364 5.41404L11.7772 0.834343C11.5236 0.319776 11.0088 0 10.435 0C9.86165 0 9.34681 0.319776 9.09315 0.834343L6.83363 5.41445C6.73752 5.60966 6.55114 5.74468 6.33593 5.77603L1.28283 6.51047C0.71492 6.59282 0.2519 6.98324 0.0742973 7.52957C-0.102887 8.07591 0.04212 8.66363 0.452904 9.06408L4.109 12.6288C4.26488 12.781 4.33634 13 4.29956 13.2141L3.43704 18.2477C3.36057 18.6908 3.47674 19.1218 3.76341 19.4616C4.20888 19.9912 4.98657 20.1526 5.60839 19.8257L10.1274 17.4489C10.3163 17.3498 10.5541 17.3507 10.7426 17.4489L15.262 19.8257C15.4818 19.9415 15.7163 20 15.9582 20C16.3999 20 16.8186 19.8035 17.1066 19.4616C17.3937 19.1218 17.5094 18.69 17.4329 18.2477L16.57 13.2141C16.5332 12.9996 16.6047 12.781 16.7606 12.6288L20.4167 9.06366Z" fill="#E3EEF1"/>
                                    </svg>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="h5">{{ __('locale.text_account_review_7') }}</div>
                <div class="form_group">
                    <textarea name="text" rows="5" class="textarea"></textarea>
                </div>
                <input type="submit" value="{{ __('locale.text_account_review_10') }}" class="btn-default" />
            </form>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        var section = document.querySelectorAll(".right_decript > div");
        var sections = {};
        var i = 0;
        var colon = $('.colon3').offset().top, colon_height = colon + $('.colon3').outerHeight();

        Array.prototype.forEach.call(section, function(e) {
            sections[e.id] = e.offsetTop;
        });

        function scroll_product() {
            var scrollPosition = document.documentElement.scrollTop || document.body.scrollTop;

            for (i in sections) {
                if (sections[i] <= scrollPosition) {
                    document.querySelector('.activ_descript').setAttribute('class', '');
                    document.querySelector('span[data-target*=' + i + ']').setAttribute('class', 'activ_descript');
                }
            }

            if (scrollPosition >= colon_height) {
                $('.float_product').fadeIn();
            } else {
                $('.float_product').fadeOut();
            }
        }

        function review_update(val) {
            $('#review').addClass('preload');

            $.ajax({
                url: '{{ route('get_reviews') }}',
                type: 'POST',
                dataType: 'json',
                data: 'sort=' + val + ($('#review').attr('data-page') > 1 ? '&page=' + $('#review').attr('data-page') : ''),
                success: function(json) {
                    if (json.html) {
                        $('#review').html(json.html);
                    } else {
                        $('.zagruz').fadeOut(0);
                    }

                    $('#review').removeClass('preload');
                }
            })
        }

        $(document).on('click', '.zagruz', function(){
            var page = $(this).attr('data-page');
            var sort = $('.top_revievs select').val();

            $('#review').addClass('preload');

            $.ajax({
                url: '{{ route('get_reviews') }}',
                type: 'POST',
                dataType: 'json',
                data: 'sort=' + sort + '&page=' + page,
                success: function(json) {
                    if (json.html) {
                        if (page == '{{ $last_review }}') {
                            $('.zagruz').fadeOut(0);
                        }

                        $('#review').attr('data-page', page);
                        $('#review').append(json.html);
                    } else {
                        $('.zagruz').fadeOut(0);
                    }

                    $('#review').removeClass('preload');
                }
            });

            return false;
        });

        function descr_resize() {
            var descr = $('.right_decript').detach();

            if ($(window).width() < 640) {
                if (!$('.right_product .right_decript').length) {
                    $('.dop_info').after(descr);
                }
            } else if ($(window).width() < 960) {
                if (!$('.right_product .right_decript').length) {
                    $('.tabl_opt').after(descr);
                }
            } else {
                if (!$('.decript_atribut .right_decript').length) {
                    $('.left_decript').after(descr);
                }
            }
        }

        $(window).on('resize', function(){
            descr_resize();
        });

        $(document).ready(function() {
            descr_resize()

            if ($(window).width() > 639) {
                window.onscroll = function () {
                    scroll_product();
                }
            }

            scroll_product();

            @if($images)
            var slider_images = $('.slider_images');

            slider_images.owlCarousel({
                loop: true,
                animateOut: "fadeOut",
                items: 1,
                nav: true,
                navText: ['<span style="display: inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="20" fill="white"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>', '<span style="display: inline-block;transform: rotate(180deg)"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="20" fill="white"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>'],
                dots: false
            }).on('translated.owl.carousel', function(e) {
                let targetIndex = e.item.index;
                targetIndex -=  e.relatedTarget.clones().length / 2;

                $('.dop_foto2').removeClass('activ');
                $('.dop_foto2 a[data-index="' + targetIndex + '"]').parent().addClass('activ');
            });

            $(document).on('click', '.osnov_foto a', function(e){
                var index = $(this).attr('data-index');
                slider_images.trigger('to.owl.carousel', [index])
                modal_show('#fancy');
                return false;
            });

            $(document).on('click', '.dop_foto2 a', function(){
                $('.dop_foto2').removeClass('activ');
                var src = $(this).find('img').attr('src');
                var index = $(this).attr('data-index');
                slider_images.trigger('to.owl.carousel', [index])
                $('img[src="' + src + '"]').parent().parent().addClass('activ');
                $('.osnov_foto').find('img').attr('src', src);
                $('.osnov_foto').find('a').attr('data-index', index);
                return false;
            });
            @endif

            @if(!$products->isEmpty())
            var product_related = $('.product_related .owl-carousel');

            product_related.owlCarousel({
                loop: true,
                items: 5,
                margin: 40,
                nav: true,
                navText: ['<span style="display: inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="20" fill="white"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>', '<span style="display: inline-block;transform: rotate(180deg)"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="20" fill="white"/><g clip-path="url(#clip0_432_54444)"><path d="M16.5815 20C16.5815 19.7849 16.6637 19.5699 16.8275 19.4059L21.9873 14.2462C22.3155 13.9179 22.8477 13.9179 23.1758 14.2462C23.5039 14.5743 23.5039 15.1063 23.1758 15.4346L18.6101 20L23.1756 24.5654C23.5037 24.8936 23.5037 25.4256 23.1756 25.7537C22.8476 26.0821 22.3154 26.0821 21.9872 25.7537L16.8274 20.594C16.6635 20.43 16.5815 20.215 16.5815 20Z" fill="#54B0AC"/></g><defs><clipPath id="clip0_432_54444"><rect width="12" height="12" fill="white" transform="translate(26 14) rotate(90)"/></clipPath></defs></svg></span>'],
                dots: false,
                responsiveClass: true,
                responsive: {
                    320: {
                        items: 3,
                        margin: 10,
                        autoWidth: true
                    },
                    640: {
                        items: 3,
                        margin: 30,
                        autoWidth: true
                    },
                    960: {
                        items: 4,
                        margin: 30,
                        autoWidth: true
                    },
                    1200: {
                        items: 5,
                        margin: 30
                    }
                }
            });
            @endif
            $('#button-cart').on('click', function(e){
                e.preventDefault();

                $.ajax({
                    url: '{{ route('cart_add') }}',
                    type: 'post',
                    dataType: 'json',
                    data: $('#product input[type="radio"]:checked, #product input[type="checkbox"]:checked, #product input[type="text"], #product input[type="date"], #product input[type="datetime"], #product textarea, #product select, #product input[type="hidden"]'),
                    success: function (json) {
                        $('.text-danger').remove();
                        $('.option').removeClass('error-text');

                        if (json.error) {
                            $('html, body').animate({ scrollTop: $('#product').offset().top}, 'slow');

                            for (i in json.error.option) {
                                $('#option-' + i).addClass('error-text').after('<div class="text-danger">' + json.error.option[i] + '</div>');
                            }
                        } else {
                            $('#modal_cart .cart_items').html($(json['html']).find('.cart_items').html());
                            $('#cart_total').text(json['total']);
                            $('.option_product').html($('.cart_items').find('.option_cart-{{ $product_id}}').html());

                            if (json['price'] < 5000) {
                                $('.minim').fadeIn(0);
                            } else {
                                $('.minim').fadeOut(0);
                            }

                            $('.one_price').text($('.pric_sht > span').text());
                            $('.one_qw').html('*&nbsp;&nbsp;&nbsp;' + $('.quantity_inner input').val() + ' {{ __('locale.text_sht') }}');

                            modal_show('#popup3', 0);

                            if ($('#timer_js').length) {
                                timerOr('#timer_js', 5)
                            }

                            wishlist.getList();
                        }
                    }
                });

                return false;
            });
        });

        $(document).on('click', '.left_decript span', function(){
            var href = $(this).attr('data-target');

            if (typeof href !== undefined && href && $(href).length) {
                $('html, body').animate({ scrollTop: $(href).offset().top - $('header').outerHeight() - 10}, 'slow');
            }
        });

        var discounts = JSON.parse('{!! $discounts->keyBy('quantity') !!}');

        function getNumber(dict, value) {
            var key, found;

            for (key in dict) {
                if (value - key >= 0) {
                    found = key;
                }
            }

            return dict[found];
        }

        $(document).on('click', '.bt_plus', function(){
            var input = $(this).parent().find('input');
            var qw = parseInt(input.val()) + 1;
            $('.quantity_inner [name="quantity"]').val(qw).trigger('change');
            var price = getNumber(discounts, qw);

            if (typeof price != "undefined") {
                $('.pric_sht').text(new Intl.NumberFormat('ru-RU').format(price['price']) + ' {{ session('currency.symbol') }}/{{ __('locale.text_sht') }}').attr('data-price', new Intl.NumberFormat('ru-RU').format(price['price']) + ' {{ session('currency.symbol') }}');
            }
        });

        $(document).on('input', '.quantity_inner [name="quantity"]', function(){
            var qw = $(this).val();
            var price = getNumber(discounts, qw);

            if (typeof price != "undefined") {
                $('.pric_sht').text(new Intl.NumberFormat('ru-RU').format(price['price']) + ' {{ session('currency.symbol') }}/{{ __('locale.text_sht') }}').attr('data-price', new Intl.NumberFormat('ru-RU').format(price['price']) + ' {{ session('currency.symbol') }}');
            }
        });

        $(document).on('click', '.bt_minus', function(){
            var input = $(this).parent().find('input');
            var qw = parseInt(input.val()) - 1;
            if (qw <= 0) qw = 1;
            $('.quantity_inner [name="quantity"]').val(qw).trigger('change');
            var price = getNumber(discounts, qw);

            if (typeof price != "undefined") {
                $('.pric_sht').text(new Intl.NumberFormat('ru-RU').format(price['price']) + ' {{ session('currency.symbol') }}/{{ __('locale.text_sht') }}').attr('data-price', new Intl.NumberFormat('ru-RU').format(price['price']) + ' {{ session('currency.symbol') }}');
            }
        });

        $(document).on('click', '.colon1 .change_quantity', function(){
            var qw = $(this).attr('data-quantity');

            if ($(this).hasClass('active')) {
                $('.colon1 .change_quantity').removeClass('active');
                $('.pric_sht').html($('.price_dis').attr('data-price'));
                $('.pric').html($('.pric').text());
                qw = $('.colon1 input.quantity').attr('data-quantity');
            } else {
                $(this).addClass('active');
                $('.pric_sht').html($(this).find('.price_dis').html());
                $('.pric').html($(this).find('.price_dis > span').html());
                $('.colon1 input.quantity').attr('data-quantity', $('input.quantity').val());
            }

            $('.colon1 input.quantity').val(qw);
        });

        $(document).on('change', 'input.quantity', function(){
            if ($(this).val() == 0) $(this).val(1);
        });

        $('.rating label').hover(
            function(){
                var stars = $(this).find('input').val();
                $('.rating label').addClass('ret1');
                $('.rating label:nth-child('+stars+')').nextAll('label').removeClass('ret1');
            },
            function(){
                if ($('.ret2').length) {
                    $('.ret2').prevAll('label').addClass('ret1');
                    $('.ret2').nextAll('label').removeClass('ret1');
                    $('.ret2').addClass('ret1');
                } else {
                    $('.rating label').removeClass('ret1');
                }
            });

        $(document).on('click', '.rating label', function(e){
            $(this).addClass('ret2');
            $(this).prevAll('label').removeClass('ret2');
            $(this).nextAll('label').removeClass('ret1 ret2');
            $('.star').removeClass('error');
            $('.rat').text($(this).find('input').val());
            $(this).find('input').prop('checked', true);
            return false;
        });

        function one_click() {
            $('#one_click form').trigger('reset');

            $.ajax({
                url: '{{ route('cart_add') }}?oneclick=1',
                type: 'post',
                dataType: 'json',
                data: $('#product input[type="radio"]:checked, #product input[type="checkbox"]:checked, #product input[type="text"], #product input[type="date"], #product input[type="datetime"], #product textarea, #product select, #product input[type="hidden"]'),
                success: function (json) {
                    $('.text-danger').remove();
                    $('.option').removeClass('error-text');

                    if (json.error) {
                        $('html, body').animate({ scrollTop: $('#product').offset().top}, 'slow');

                        for (i in json.error.option) {
                            $('#option-' + i).addClass('error-text').after('<div class="text-danger">' + json.error.option[i] + '</div>');
                        }
                    } else {
                        $('.one_price').text($('.pric_sht > span').text());
                        $('.one_qw').html('*&nbsp;&nbsp;&nbsp;' + $('.quantity_inner input').val() + ' {{ __('locale.text_sht') }}');
                        modal_show('#one_click', 1);
                        wishlist.getList();
                        if ($('#timer_js').length) {
                            timerOr('#timer_js', 5)
                        }
                    }
                }
            });
        }
    </script>
@endsection