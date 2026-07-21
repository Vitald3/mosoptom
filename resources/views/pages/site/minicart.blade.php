<div class="modal_cart" id="modal_cart">
	<div class="cart_items relative">
		@if($products)
			<div class="flex-2 title_fixed">
				<span>{!! $cart_count_text !!}</span>
				<a href="#" class="close_cart" data-close-modal="#modal_cart">
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
			</div>
			<div class="overflow-y">
				@foreach($products as $product)
					<div class="cart_item relative flex-2 col-12">
						<div class="flex-2 wrap flex-start mh150">
							<a href="{{ $product['url'] }}" class="relative">
								<img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="wid_image border-24" />
							</a>
							<div class="caption relative">
								<a href="#" class="product_wishlist product_wishlist-{{ $product['id'] }}" onclick="wishlist.add({{ $product['id'] }});return false;">
									<svg xmlns="http://www.w3.org/2000/svg" width="17" height="14" viewBox="0 0 17 14" fill="none">
										<path d="M15.1668 1.32286C13.4028 -0.440714 10.533 -0.440714 8.76946 1.32286L8.35983 1.73224L7.95045 1.32286C6.18688 -0.440953 3.3169 -0.440953 1.55332 1.32286C-0.174444 3.05063 -0.185664 5.78932 1.5273 7.69349C3.08965 9.42961 7.69742 13.1804 7.89292 13.3392C8.02564 13.4471 8.18534 13.4996 8.34408 13.4996C8.34933 13.4996 8.35458 13.4996 8.3596 13.4993C8.52383 13.507 8.68925 13.4506 8.82627 13.3392C9.02177 13.1804 13.63 9.42961 15.1928 7.69325C16.9056 5.78932 16.8944 3.05063 15.1668 1.32286ZM14.1282 6.73532C12.9101 8.08855 9.56173 10.8795 8.3596 11.8699C7.15746 10.8798 3.80983 8.08903 2.59194 6.73556C1.39697 5.40739 1.38575 3.51587 2.56592 2.3357C3.16866 1.7332 3.96021 1.43171 4.75177 1.43171C5.54332 1.43171 6.33488 1.73296 6.93761 2.3357L7.83802 3.2361C7.9452 3.34328 8.08031 3.40725 8.2221 3.42969C8.45222 3.47911 8.7019 3.41489 8.88093 3.23634L9.78182 2.3357C10.9875 1.13046 12.9488 1.1307 14.1537 2.3357C15.3339 3.51587 15.3227 5.40739 14.1282 6.73532Z" fill="#BED0D6"/>
									</svg>
								</a>
								<div class="product_name">
									<a href="{{ $product['url'] }}">{{ $product['name'] }}</a>
									@if($product['options'])
										<div class="option_cart option_cart-{{ $product['id'] }} overflow-y">
											@foreach($product['options'] as $option)
												<div>
													@if(!empty($option['values']))
														&nbsp; <small> - {{ $option['name'] }}:
															<?php $values = []; ?>
															@foreach($option['values'] as $value)
																<?php $values[] = $value['value']; ?>
															@endforeach
															{{ implode(',', $values) }}
														</small>
													@else
														&nbsp; <small> - {{ $option['name'] }}: {{ $option['value'] }}</small>
													@endif
												</div>
											@endforeach
										</div>
									@endif
								</div>
								<a href="#" class="cart_remove" onclick="$(this).closest('.cart_item').toggleClass('active');return false;">
									<svg style="margin-right: 14px" xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
										<g clip-path="url(#clip0_506_46341)">
											<path d="M10.2329 4.29707C10.192 4.29043 10.1506 4.28737 10.1092 4.2879H3.05437L3.2082 4.21636C3.35857 4.14519 3.49536 4.04833 3.61246 3.93016L5.5908 1.95181C5.85135 1.70309 5.89513 1.30297 5.69455 1.00378C5.46109 0.684965 5.0134 0.615741 4.69455 0.849193C4.6688 0.868064 4.64431 0.888635 4.6213 0.91077L1.04383 4.48824C0.764252 4.76751 0.764006 5.22053 1.04327 5.50011C1.04345 5.50029 1.04365 5.50049 1.04383 5.50067L4.6213 9.07814C4.90111 9.35716 5.35413 9.35653 5.63317 9.07673C5.65513 9.05471 5.67563 9.03128 5.69455 9.00659C5.89513 8.7074 5.85135 8.30729 5.5908 8.05856L3.61603 6.07664C3.51106 5.97155 3.39036 5.88344 3.25829 5.81549L3.04364 5.71889H10.0698C10.4353 5.73247 10.756 5.47715 10.8246 5.11788C10.8879 4.72782 10.623 4.36035 10.2329 4.29707Z" fill="#BED0D6"/>
										</g>
										<defs>
											<clipPath id="clip0_506_46341">
												<rect width="10" height="10" fill="white" transform="translate(0.833984)"/>
											</clipPath>
										</defs>
									</svg>
									<svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" viewBox="0 0 13 12" fill="none">
										<path d="M10.7715 1.5H8.70898V1.125C8.70898 0.50468 8.2043 0 7.58398 0H6.08398C5.46366 0 4.95898 0.50468 4.95898 1.125V1.5H2.89648C2.37955 1.5 1.95898 1.92056 1.95898 2.4375V3.75C1.95898 3.95709 2.12689 4.125 2.33398 4.125H2.53892L2.8629 10.9285C2.89152 11.5294 3.38511 12 3.98661 12H9.68136C10.2829 12 10.7765 11.5294 10.8051 10.9285L11.129 4.125H11.334C11.5411 4.125 11.709 3.95709 11.709 3.75V2.4375C11.709 1.92056 11.2884 1.5 10.7715 1.5ZM5.70898 1.125C5.70898 0.918234 5.87722 0.75 6.08398 0.75H7.58398C7.79075 0.75 7.95898 0.918234 7.95898 1.125V1.5H5.70898V1.125ZM2.70898 2.4375C2.70898 2.33412 2.7931 2.25 2.89648 2.25H10.7715C10.8749 2.25 10.959 2.33412 10.959 2.4375V3.375C10.8434 3.375 3.18788 3.375 2.70898 3.375V2.4375ZM10.0559 10.8928C10.0464 11.0931 9.88184 11.25 9.68136 11.25H3.98661C3.7861 11.25 3.62157 11.0931 3.61205 10.8928L3.28977 4.125H10.3782L10.0559 10.8928Z" fill="#EB5757"/>
										<path d="M6.83398 10.5C7.04108 10.5 7.20898 10.3321 7.20898 10.125V5.25C7.20898 5.04291 7.04108 4.875 6.83398 4.875C6.62689 4.875 6.45898 5.04291 6.45898 5.25V10.125C6.45898 10.3321 6.62687 10.5 6.83398 10.5Z" fill="#EB5757"/>
										<path d="M8.70898 10.5C8.91608 10.5 9.08398 10.3321 9.08398 10.125V5.25C9.08398 5.04291 8.91608 4.875 8.70898 4.875C8.50189 4.875 8.33398 5.04291 8.33398 5.25V10.125C8.33398 10.3321 8.50187 10.5 8.70898 10.5Z" fill="#EB5757"/>
										<path d="M4.95898 10.5C5.16608 10.5 5.33398 10.3321 5.33398 10.125V5.25C5.33398 5.04291 5.16608 4.875 4.95898 4.875C4.75189 4.875 4.58398 5.04291 4.58398 5.25V10.125C4.58398 10.3321 4.75187 10.5 4.95898 10.5Z" fill="#EB5757"/>
									</svg>
								</a>
								<div class="product_name">
									<div class="flex">
										<div class="quant flex-2 hid-xs">
								<span class="minus_qw">
									<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
<g opacity="0.5" clip-path="url(#clip0_506_46364)">
<path d="M8.55871 7.2877L7.26308 7.28778L1.75633 7.28771C1.39805 7.28779 1.10855 7.57729 1.10855 7.93548C1.10847 8.29376 1.39805 8.58334 1.75633 8.58326L7.26308 8.58333L8.55863 8.58333L14.0655 8.58332C14.2441 8.58332 14.4063 8.51093 14.5236 8.39365C14.6408 8.27638 14.7132 8.11423 14.7132 7.93555C14.7133 7.57727 14.4237 7.28769 14.0655 7.28778L8.55871 7.2877Z" fill="#484848"/>
</g>
<defs>
<clipPath id="clip0_506_46364">
<rect width="10" height="10" fill="white" transform="translate(0.833984 7.92969) rotate(-45)"/>
</clipPath>
</defs>
</svg>
								</span>
											<input type="number" value="{{ $product['quantity'] }}" class="number qw" oninput="cart.update({{ $product['cart_id'] }}, this.value);" />
											<span class="plus_qw">
									<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
<g opacity="0.5" clip-path="url(#clip0_506_46362)">
<path d="M8.55871 7.2877L8.55864 1.78095C8.55872 1.42267 8.26914 1.13309 7.91086 1.13318C7.55267 1.13318 7.26309 1.42275 7.26309 1.78095L7.26308 7.28778L1.75633 7.28771C1.39805 7.28779 1.10855 7.57729 1.10855 7.93548C1.10847 8.29376 1.39805 8.58334 1.75633 8.58326L7.26308 8.58333L7.26315 14.0901C7.26307 14.4484 7.55265 14.7379 7.91093 14.7379C8.08952 14.7379 8.25176 14.6655 8.36903 14.5482C8.48631 14.4309 8.5587 14.2688 8.5587 14.0901L8.55863 8.58333L14.0655 8.58332C14.2441 8.58332 14.4063 8.51093 14.5236 8.39365C14.6408 8.27638 14.7132 8.11423 14.7132 7.93555C14.7133 7.57727 14.4237 7.28769 14.0655 7.28778L8.55871 7.2877Z" fill="#484848"/>
</g>
<defs>
<clipPath id="clip0_506_46362">
<rect width="10" height="10" fill="white" transform="translate(0.833984 7.92969) rotate(-45)"/>
</clipPath>
</defs>
</svg>
								</span>
										</div>
										<div>
											<span class="price_new" style="padding-right: 0">{{ $product['total'] }}</span>
										</div>
									</div>
									<div class="sht flex flex-start hid-xs">
										<span class="price_one">{{ format_price($product['price'], session('currency')) }}/{{ __('locale.text_sht') }}</span>
										@if($product['discounts'])
											<div class="podrob">
												<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
													<circle cx="6.5" cy="6.5" r="6.5" fill="#54B0AC"/>
													<path d="M7.95339 9.24458L7.85559 9.64441C7.56217 9.76022 7.32778 9.84836 7.15325 9.90897C6.97852 9.96976 6.77551 10 6.5442 10C6.18898 10 5.91269 9.91298 5.71563 9.74014C5.51857 9.56661 5.42001 9.34672 5.42001 9.07996C5.42001 8.9767 5.42717 8.87048 5.44203 8.76226C5.45703 8.65392 5.48087 8.53189 5.51349 8.3955L5.88019 7.0976C5.91281 6.97332 5.94054 6.85556 5.96275 6.7442C5.98527 6.63354 5.99613 6.53171 5.99613 6.44005C5.99613 6.2743 5.96181 6.15837 5.89349 6.09318C5.82517 6.02819 5.6948 5.99519 5.50126 5.99519C5.40646 5.99519 5.30903 6.01037 5.20972 6.03973C5.10997 6.06922 5.02471 6.0977 4.95312 6.12417L5.05118 5.72404C5.2914 5.62623 5.52102 5.54248 5.74066 5.47296C5.9603 5.40326 6.16784 5.36838 6.36414 5.36838C6.71691 5.36838 6.98913 5.45364 7.18029 5.62416C7.37145 5.79481 7.46699 6.01602 7.46699 6.28861C7.46699 6.34501 7.46072 6.44438 7.44723 6.58642C7.43406 6.72877 7.40953 6.8592 7.37377 6.97784L7.00864 8.27053C6.97871 8.37436 6.9518 8.49306 6.9284 8.62663C6.90424 8.75931 6.8927 8.8607 6.8927 8.9287C6.8927 9.10035 6.93097 9.21754 7.0077 9.27984C7.08499 9.34214 7.21812 9.37313 7.40721 9.37313C7.49598 9.37313 7.59698 9.35739 7.70922 9.32646C7.82114 9.29553 7.90276 9.26836 7.95339 9.24458ZM8.04599 3.81746C8.04599 4.04269 7.96111 4.23504 7.79053 4.3932C7.62039 4.55192 7.41536 4.63135 7.17552 4.63135C6.93492 4.63135 6.7294 4.55192 6.55731 4.3932C6.38553 4.23498 6.29946 4.04269 6.29946 3.81746C6.29946 3.59268 6.38553 3.40001 6.55731 3.23984C6.72908 3.07993 6.93498 3 7.17552 3C7.4153 3 7.62039 3.08012 7.79053 3.23984C7.96124 3.40001 8.04599 3.59274 8.04599 3.81746Z" fill="white"/>
												</svg>
												<ul class="list-un-styled">
													@foreach($product['discounts'] as $discount)
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
								</div>
							</div>
							<div class="flex-2 col-12 hid-sm" style="padding-top: 10px">
								<div class="quant flex-2">
								<span class="minus_qw">
									<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
<g opacity="0.5" clip-path="url(#clip0_506_46364)">
<path d="M8.55871 7.2877L7.26308 7.28778L1.75633 7.28771C1.39805 7.28779 1.10855 7.57729 1.10855 7.93548C1.10847 8.29376 1.39805 8.58334 1.75633 8.58326L7.26308 8.58333L8.55863 8.58333L14.0655 8.58332C14.2441 8.58332 14.4063 8.51093 14.5236 8.39365C14.6408 8.27638 14.7132 8.11423 14.7132 7.93555C14.7133 7.57727 14.4237 7.28769 14.0655 7.28778L8.55871 7.2877Z" fill="#484848"/>
</g>
<defs>
<clipPath id="clip0_506_46364">
<rect width="10" height="10" fill="white" transform="translate(0.833984 7.92969) rotate(-45)"/>
</clipPath>
</defs>
</svg>
								</span>
									<input type="number" value="{{ $product['quantity'] }}" class="number qw" oninput="cart.update({{ $product['cart_id'] }}, this.value);" />
									<span class="plus_qw">
									<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
<g opacity="0.5" clip-path="url(#clip0_506_46362)">
<path d="M8.55871 7.2877L8.55864 1.78095C8.55872 1.42267 8.26914 1.13309 7.91086 1.13318C7.55267 1.13318 7.26309 1.42275 7.26309 1.78095L7.26308 7.28778L1.75633 7.28771C1.39805 7.28779 1.10855 7.57729 1.10855 7.93548C1.10847 8.29376 1.39805 8.58334 1.75633 8.58326L7.26308 8.58333L7.26315 14.0901C7.26307 14.4484 7.55265 14.7379 7.91093 14.7379C8.08952 14.7379 8.25176 14.6655 8.36903 14.5482C8.48631 14.4309 8.5587 14.2688 8.5587 14.0901L8.55863 8.58333L14.0655 8.58332C14.2441 8.58332 14.4063 8.51093 14.5236 8.39365C14.6408 8.27638 14.7132 8.11423 14.7132 7.93555C14.7133 7.57727 14.4237 7.28769 14.0655 7.28778L8.55871 7.2877Z" fill="#484848"/>
</g>
<defs>
<clipPath id="clip0_506_46362">
<rect width="10" height="10" fill="white" transform="translate(0.833984 7.92969) rotate(-45)"/>
</clipPath>
</defs>
</svg>
								</span>
								</div>
								<span class="price_one">{{ format_price($product['price'], session('currency')) }}/{{ __('locale.text_sht') }}</span>
								<span class="price_new_mob">{{ $product['total'] }}</span>
							</div>
						</div>
						<div class="cart_rem hid-sm hid-lg" onclick="cart.remove({{ $product['cart_id'] }});return false;">
							{{ __('locale.text_cart_mob') }}
						</div>
					</div>
				@endforeach
			</div>
			<div class="totals_cart flex-2">
				<a href="{{ route(session('route_url') . '_checkout') }}" class="btn-default">{{ __('locale.text_checkout_button') }}</a>
				@foreach($totals as $total)
					@if($total['code'] === 'total')
						<div id="totals_{{ $total['code'] }}" class="flex">
							<span class="total_name">{{ $total['title'] }}:</span>
							<span class="total_value">{{ $total['value'] }}</span>
						</div>
					@endif
				@endforeach
			</div>
		@else
			<a href="#" class="close_cart" data-close-modal="#modal_cart">
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
			<div class="overflow-y" style="padding:0">
				<div class="empty_cart">
					<div class="text_empty">{{ __('locale.text_cart_empty') }}</div>
					<p>{{ __('locale.text_cart_continue') }}</p>
					<a href="#" class="btn-default" data-close-modal="#modal_cart">{{ __('locale.text_buy_go') }}</a>
				</div>
				@isset($saleday)
					{!! $saleday !!}
				@endif
			</div>
		@endif
	</div>
</div>