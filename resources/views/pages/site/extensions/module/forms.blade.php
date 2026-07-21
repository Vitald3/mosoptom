<div class="forms">
    <div class="flex-2">
        <div class="first_div"{{ !$image ? ' style="width: 100%"' : '' }}>
            <div class="h2 mb10">
                {{ $title }}
            </div>
            @if($text)
            <div class="text">{{ $text }}</div>
            @endif
            <form method="post" action="{{ route(session('route_url') . '_forms_action') }}" class="form_action" id="form-{{ $module }}">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}" />
                <input type="hidden" name="url" value="{{ url()->current() }}" />
                @foreach($fields as $key => $field)
                    <div class="flex wrap">
                        <div class="fake_input col-12 flex"{{ count($fields) > 1 ? ' style="margin-bottom: 15px;width: 100%"' : '' }}>
                            @if($field['type'] == 'tel')
                                <div class="btn-shadow active" style="background: #fff;border: 1px solid #fff;margin: 20px 0">
                                    <div class="form_group">
                                        <input id="field-{{ $key }}" type="{{ $field['type'] }}" name="field[{{ $key }}]" data-mask="{{ $languages[session('lang')]->mask }}" placeholder="{{ $field['placeholder'] }}" class="input"{{ $field['required'] ? ' required' : '' }} />
                                    </div>
                                    <a href="#" class="btn btn-default sends">{{ $text_button }}</a>
                                </div>
                            @endif
                            @if(in_array($field['type'], ['text', 'number', 'email']))
                                <input id="field-{{ $key }}" type="{{ $field['type'] }}" name="field[{{ $key }}]" placeholder="{{ $field['placeholder'] }}" class="input"{{ $field['required'] ? ' required' : '' }} />
                            @elseif($field['type'] == 'textarea')
                                <textarea id="field-{{ $key }}" name="field[{{ $key }}]" placeholder="{{ $field['placeholder'] }}" class="input"{{ $field['required'] ? ' required' : '' }}></textarea>
                            @elseif($field['type'] != 'tel')
                                <select id="field-{{ $key }}" name="field[{{ $key }}]"{{ $field['required'] ? ' required' : '' }}>
                                    @foreach($field['values'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        @if($field['type'] != 'tel' && $text_button)
                            <a href="#" class="btn btn-default sends">{{ $text_button }}</a>
                        @endif
                    </div>
                @endforeach
                @if($field['type'] != 'tel' && $text_button)
                    <a href="#" class="btn btn-default sends">{{ $text_button }}</a>
                @endif
            </form>
            @if($policy)
                <div class="agree">{{ $policy }}</div>
            @endif
        </div>
        @if($image)
            <div class="photo_project" style="background: url('{{ $image }}') no-repeat left bottom">
            </div>
        @endif
    </div>
</div>