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
    <link rel="stylesheet" href="{{ asset('assets/site/css/media/account.css') }}" />
@endsection
@section('content')
    <section id="success" class="container">
        <div class="account_container flex-2">
            <div class="account_left">
                @include('pages.site.account.menu')
            </div>
            <form action="{{ route('review_save') }}" method="post" enctype="multipart/form-data" class="validate_js account_right" novalidate>
                <input type="hidden" name="id" value="{{ $review->id }}" />
                <div class="flex-2 wrap">
                    <h1>{{ $title }}</h1>
                    <a href="{{ url()->previous() }}" class="color_link flex-2">
                        <svg style="margin-right: 10px;margin-top: 2px" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <g clip-path="url(#clip0_432_70869)">
                                <path d="M3.22497 7.50094C3.22497 7.23211 3.32761 6.96331 3.53247 6.75835L9.9822 0.308689C10.3925 -0.101594 11.0577 -0.101594 11.4678 0.308689C11.8779 0.718805 11.8779 1.38388 11.4678 1.79419L5.76073 7.50094L11.4676 13.2077C11.8777 13.618 11.8777 14.283 11.4676 14.6931C11.0575 15.1036 10.3923 15.1036 9.982 14.6931L3.53227 8.24352C3.32738 8.03846 3.22497 7.76967 3.22497 7.50094Z" fill="#ACBDC0"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_432_70869">
                                    <rect width="15" height="15" fill="white" transform="translate(15) rotate(90)"/>
                                </clipPath>
                            </defs>
                        </svg>
                        {{ __('locale.link_back') }}
                    </a>
                </div>
                <div class="flex-2 mode wrap">
                    <div class="mode_left">
                        <div class="h5">{{ __('locale.text_account_review_8') }}</div>
                        <textarea name="disadvantages" rows="5" class="textarea">{{ $review->disadvantages }}</textarea>
                        <div class="h5">{{ __('locale.text_account_review_9') }}</div>
                        <textarea name="dignities" rows="1" class="textarea">{{ $review->dignities }}</textarea>
                    </div>
                    <div class="text-center col-xs-12 tl">
                        <div class="rtext" style="margin-bottom: 10px">{{ __('locale.text_account_reviews_2') }}<span class="color_link rat">{{ $review->rating }}</span></div>
                        <div class="rating mb30">
                            @foreach([1, 2, 3, 4, 5] as $rating)
                                <label for="rat-{{ $rating }}" class="star{{ $rating <= $review->rating ? ' ret1' : '' }}">
                                    <input id="rat-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}"{{ $rating == $review->rating? ' checked' : '' }} style="display: none" />
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                                        <path d="M20.4167 9.06366C20.8274 8.66321 20.9725 8.07549 20.7953 7.52916C20.6177 6.98282 20.1551 6.59282 19.5867 6.51005L14.5336 5.77561C14.3184 5.74426 14.1325 5.60925 14.0364 5.41404L11.7772 0.834343C11.5236 0.319776 11.0088 0 10.435 0C9.86165 0 9.34681 0.319776 9.09315 0.834343L6.83363 5.41445C6.73752 5.60966 6.55114 5.74468 6.33593 5.77603L1.28283 6.51047C0.71492 6.59282 0.2519 6.98324 0.0742973 7.52957C-0.102887 8.07591 0.04212 8.66363 0.452904 9.06408L4.109 12.6288C4.26488 12.781 4.33634 13 4.29956 13.2141L3.43704 18.2477C3.36057 18.6908 3.47674 19.1218 3.76341 19.4616C4.20888 19.9912 4.98657 20.1526 5.60839 19.8257L10.1274 17.4489C10.3163 17.3498 10.5541 17.3507 10.7426 17.4489L15.262 19.8257C15.4818 19.9415 15.7163 20 15.9582 20C16.3999 20 16.8186 19.8035 17.1066 19.4616C17.3937 19.1218 17.5094 18.69 17.4329 18.2477L16.57 13.2141C16.5332 12.9996 16.6047 12.781 16.7606 12.6288L20.4167 9.06366Z" fill="#E3EEF1"/>
                                    </svg>
                                </label>
                            @endforeach
                        </div>
                        @if($review->status == 1)
                            <svg style="margin-right: 10px" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                <g clip-path="url(#clip0_432_70729)">
                                    <path d="M5.74377 13.1036C5.60059 13.2476 5.40523 13.3279 5.20231 13.3279C4.99938 13.3279 4.80403 13.2476 4.66085 13.1036L0.336574 8.77858C-0.112191 8.32982 -0.112191 7.60212 0.336574 7.1542L0.878037 6.61259C1.32694 6.16383 2.0538 6.16383 2.50257 6.61259L5.20231 9.31247L12.4974 2.01724C12.9463 1.56847 13.6739 1.56847 14.1219 2.01724L14.6634 2.55884C15.1122 3.00761 15.1122 3.73517 14.6634 4.18323L5.74377 13.1036Z" fill="#54B0AC"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_432_70729">
                                        <rect width="15" height="15" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                            <span>{{ __('locale.text_account_reviews_3') }}</span>
                        @elseif(is_null($review->status))
                            <svg style="margin-right: 10px" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                <path d="M7.49998 0C3.35785 0 0 3.35785 0 7.49998C0 11.6421 3.35785 15 7.49998 15C11.6421 15 15 11.6421 15 7.49998C15 3.35785 11.6421 0 7.49998 0ZM1.86686 7.49998C1.86686 4.38888 4.38891 1.86686 7.49998 1.86686C8.71892 1.86686 9.84737 2.25412 10.7692 2.91218L2.91218 10.7692C2.25412 9.8474 1.86686 8.71895 1.86686 7.49998ZM7.49998 13.1331C6.28178 13.1331 5.15398 12.7464 4.23247 12.0891L12.089 4.2325C12.7464 5.154 13.1331 6.28184 13.1331 7.50001C13.1331 10.6111 10.6111 13.1331 7.49998 13.1331Z" fill="#EB5757"/>
                            </svg>
                            <span>{{ __('locale.text_account_reviews_4') }}</span>
                        @else
                            <svg style="margin-right: 10px" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                <g clip-path="url(#clip0_432_70762)">
                                    <path d="M7.15686 7.15815C7.15686 7.34676 7.30973 7.49867 7.4977 7.49867C7.68568 7.49867 7.83855 7.34581 7.83855 7.15815C7.83887 6.75922 7.98057 6.37146 8.23779 6.06636C8.81002 5.38787 9.23894 4.65224 9.51053 3.87992C9.54723 3.77556 9.53128 3.66003 9.46745 3.56971C9.4033 3.4794 9.2999 3.42578 9.18916 3.42578H5.80593C5.69519 3.42578 5.59147 3.4794 5.52732 3.56971C5.46349 3.66003 5.44753 3.77556 5.48424 3.87992C5.75646 4.65224 6.18475 5.38787 6.75698 6.06636C7.01452 6.37146 7.15654 6.75954 7.15686 7.15815Z" fill="#56CCF2"/>
                                    <path d="M7.84078 8.52412C7.84078 8.33582 7.68791 8.18359 7.49993 8.18359C7.31196 8.18359 7.15909 8.33646 7.15909 8.52412C7.15877 9.01624 6.86484 9.50293 6.33155 9.89771C5.19094 10.7399 4.33723 11.6549 3.79405 12.6168C3.73437 12.7228 3.73533 12.8517 3.79693 12.9557C3.85756 13.0607 3.9699 13.1249 4.09117 13.1249H10.9093C11.0303 13.1249 11.1423 13.0607 11.2036 12.9557C11.2645 12.8517 11.2655 12.7228 11.2061 12.6168C10.6633 11.6549 9.80924 10.7399 8.66895 9.89771C8.13502 9.50293 7.84109 9.01624 7.84078 8.52412Z" fill="#56CCF2"/>
                                    <path d="M13.4644 13.6366H13.2604C13.0262 10.9836 11.4726 8.94683 10.265 7.50016C11.4729 6.05381 13.0259 4.01672 13.2611 1.36369H13.465C13.8416 1.36369 14.1467 1.05828 14.1467 0.681688C14.1467 0.3051 13.8416 0 13.465 0H1.53325C1.15666 0 0.851562 0.305419 0.851562 0.681688C0.851562 1.05828 1.15666 1.36369 1.53325 1.36369H1.73718C1.97175 4.01704 3.5247 6.05413 4.73265 7.50016C3.52502 8.94651 1.97175 10.9833 1.73686 13.6366H1.53325C1.15666 13.6366 0.851562 13.9414 0.851562 14.318C0.851562 14.6949 1.15666 15.0003 1.53325 15.0003H2.38568H12.6123H13.465C13.8416 15.0003 14.1467 14.6949 14.1467 14.318C14.1467 13.9414 13.8413 13.6366 13.4644 13.6366ZM3.10375 13.6366C3.36002 11.2396 4.91839 9.39363 6.08294 8.0143L6.14517 7.94058C6.35964 7.68686 6.35964 7.31538 6.14517 7.06134L6.08326 6.98794C4.91903 5.60829 3.36002 3.76237 3.10375 1.36497H11.8942C11.6379 3.76237 10.0789 5.60829 8.91469 6.98794L8.8531 7.06134C8.63863 7.31506 8.63863 7.68654 8.8531 7.94058L8.91533 8.0143C10.0796 9.39363 11.6379 11.2396 11.8942 13.6366H3.10375Z" fill="#56CCF2"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_432_70762">
                                        <rect width="15" height="15" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                            <span>{{ __('locale.text_account_reviews_5') }}</span>
                        @endif
                    </div>
                </div>
                <div class="h5">{{ __('locale.text_account_review_7') }}</div>
                <div class="form_group">
                    <textarea name="text" rows="5" class="textarea">{{ $review->text }}</textarea>
                </div>
                <input type="submit" value="{{ __('locale.text_account_review_10') }}" class="btn-default" />
            </form>
        </div>
    </section>
@endsection
@section('page-scripts')
    <script>
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
    </script>
@endsection