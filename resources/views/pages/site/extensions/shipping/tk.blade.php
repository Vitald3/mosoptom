@if(!empty($tk))
    <p>{{ __('locale.text_checkout_6') }}</p>
    <div class="col-12">
        <div class="form_group">
            <input type="text" name="tk[address]" id="account_strana" value="{{ old('fields.shipping.address') }}" class="input" required>
            <label for="account_strana" class="required">{{ __('locale.text_checkout_7') }}</label>
        </div>
    </div>
    <div class="flex">
        <div class="left_f">
            <div class="form_group">
                <input type="text" name="tk[kv]" id="account_kv" value="{{ old('fields.shipping.country') }}" class="input" required>
                <label for="account_kv" class="required">{{ __('locale.text_checkout_8') }}</label>
            </div>
        </div>
        <div class="right_f">
            <div class="form_group">
                <select name="tk[tk]" onchange="set_shipping();" class="selectize" data-text="{{ __('locale.text_checkout_tk') }}">
                    @foreach($tk as $t)
                        <option value="{{ str_slug($t['name']) }}"{{ old('fields.shipping.tk') == str_slug($t['name']) ? ' selected' : '' }}>{{ $t['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form_group">
            <input type="text" name="tk[comment]" id="account_coment" value="{{ old('fields.shipping.country') }}" class="input" required>
            <label for="account_coment" class="required">{{ __('locale.text_checkout_9') }}</label>
        </div>
    </div>
@endif