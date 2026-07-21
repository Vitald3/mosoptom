<div style="position: relative;border: 1px dotted #eee;margin-bottom: 15px;margin-top: 15px;padding: 15px" id="look">
    @if($code != 'img')
    <{{ $code }} id="canvas">
        @if(in_array($code, ['ol', 'ul']))
            <li>( ПРОСМОТР ЭЛЕМЕНТА )</li>
            <li>( ПРОСМОТР ЭЛЕМЕНТА )</li>
        @else
        ( ПРОСМОТР ЭЛЕМЕНТА )
        @endif
    </{{ $code }}>
    @elseif($code == 'img')
        <img src="/images/settings/Logo.svg" id="canvas" />
    @endif
</div>
<div class="form-group" id="elem">
    <div class="controls">
        <h1>{{ $name }}</h1>
        @if(!empty($params))
            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                @foreach(['320 - 767', '768 - 991', '992 - 1199', '1200 >', 'Общие', 'Наведение'] as $key => $l)
                    <li class="nav-item">
                        <a class="nav-link{{ $key == 0 ? ' active' : '' }}" id="label-{{ $key }}" data-toggle="tab" href="#lid-{{ $key }}" role="tab" aria-controls="lid-{{ $key }}" aria-selected="true">
                            {{ $l }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <!-- Tab panes -->
            <div class="tab-content pt-1 styles">
                @foreach([0, 1, 2, 3, 4, 5] as $row => $l)
                    <div class="tab-pane{{ $row == 0 ? ' active' : '' }}" id="lid-{{ $row }}" role="tabpanel" aria-labelledby="label-{{ $row }}">
                        @foreach($params as $key => $param)
                            <div class="col-12 col-xs-12">
                                @if($key == 'font')
                                    @foreach($param as $key2 => $font)
                                        <div class="row form-group closest">
                                            <label>{{ $font['placeholder'] }}</label>
                                            @if(!empty($font['select']))
                                                <select name="setting[{{ $l }}][{{ $key2 }}]" data-name="{{ $key2 }}" class="form-control">
                                                    <option value="">Выбрать</option>
                                                    @foreach($font['select'] as $select)
                                                        <option value="{{ $select['value'] }}"{{ isset($setting[$l][$key2]) && $setting[$l][$key2] == $select['value'] ? ' selected' : '' }}>{{ $select['text'] }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif(!empty($font['text']))
                                                <div style="width: 100%;display: flex;align-items: center;justify-content: space-between">
                                                    @foreach($font['text'] as $x => $type)
                                                        <div>
                                                            <label for="radio-{{ $row . '-' . $x }}">{{ $type }}</label>
                                                            @if($type == 'auto')
                                                                <input type="checkbox" data-name="{{ $key }}:{{ $type }}" name="setting[{{ $l }}][{{ $key }}:{{ $type }}]"{{ isset($setting[$l][$key . ':' . $type]) && $setting[$l][$key . ':' . $type] == 1 ? ' checked' : '' }} value="1" id="radio-{{ $row . '-' . $x }}" class="form-control {{ $type }}" placeholder="{{ $type }}" />
                                                            @else
                                                                <input type="text" data-name="{{ $key }}-{{ $type }}" name="setting[{{ $l }}][{{ $key }}-{{ $type }}]" value="{{ isset($setting[$l][$key . '-' . $type]) ? $setting[$l][$key . '-' . $type] : '' }}" id="radio-{{ $x }}" class="form-control {{ $key }}" placeholder="{{ $type }}" />
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <input type="text" data-name="{{ $key2 }}" name="setting[{{ $l }}][{{ $key2 }}]" value="{{ isset($setting[$l][$key2]) ? $setting[$l][$key2] : '' }}" class="form-control {{ $key2 }}" placeholder="{{ $font['placeholder'] }}" />
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row form-group closest">
                                        @if(isset($param['placeholder']))
                                            <label>{{ $param['placeholder'] }}</label>
                                        @endif
                                        @if(isset($param['placeholder']) && !empty($param['select']))
                                            <select name="setting[{{ $l }}][{{ $key }}]" data-name="{{ $key }}" class="form-control">
                                                <option value="">Выбрать</option>
                                                @foreach($param['select'] as $select)
                                                    <option value="{{ $select['value'] }}"{{ isset($setting[$l][$key]) && $setting[$l][$key] == $select['value'] ? ' selected' : '' }}>{{ $select['text'] }}</option>
                                                @endforeach
                                            </select>
                                        @elseif(!empty($param['text']))
                                            <div style="width: 100%;display: flex;align-items: center;justify-content: space-between">
                                                @foreach($param['text'] as $x => $type)
                                                    <div>
                                                        <label for="radio2-{{ $row . '-' . $x }}">{{ $type }}</label>
                                                        @if($type == 'auto')
                                                            <input type="checkbox" data-name="{{ $key }}:{{ $type }}" name="setting[{{ $l }}][{{ $key }}:{{ $type }}]"{{ isset($setting[$l][$key . ':' . $type]) && $setting[$l][$key . ':' . $type] == 1 ? ' checked' : '' }} value="1" id="radio2-{{ $row . '-' . $x }}" class="form-control {{ $type }}" placeholder="{{ $type }}" />
                                                        @else
                                                            <input type="text" data-name="{{ $key }}-{{ $type }}" onchange="$(this).closest('.closest').find('.auto').prop('checked', false)" name="setting[{{ $l }}][{{ $key }}-{{ $type }}]" value="{{ isset($setting[$l][$key . '-' . $type]) ? $setting[$l][$key . '-' . $type] : '' }}" id="radio2-{{ $x }}" class="form-control {{ $key }}" placeholder="{{ $type }}" />
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            @if($key == 'background-image')
                                                <section id="dropzone-examples{{ $l }}" style="width: 100%" data-name="{{ $key }}">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card">
                                                                <div class="card-content">
                                                                    <div action="{{ url('admin/add_image') }}?_token={{ csrf_token() }}" method="post" class=" dropzone-area" id="dpz-single-file{{ $l }}">
                                                                        <div class="dz-message">Перетащите файл или нажмите здесь</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(isset($setting[$l][$key]))
                                                    <input type="hidden" data-name="{{ $key }}" name="setting[{{ $l }}][{{ $key }}]" value="{{ $setting[$l][$key] }}" />
                                                    @endif
                                                </section>
                                            @else
                                                <input type="text" data-name="{{ $key }}" name="setting[{{ $l }}][{{ $key }}]" value="{{ isset($setting[$l][$key]) ? $setting[$l][$key] : '' }}" class="form-control {{ $key }}" placeholder="{{ isset($param['placeholder']) ? $param['placeholder'] : '' }}" />
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <!-- Tab panes -->
        @endif
    </div>
</div>