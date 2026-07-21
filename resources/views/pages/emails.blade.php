@extends('layouts.contentLayoutMaster')
@section('title','Почта')
@section('vendor-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
@section('content')
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    @if(session('error'))
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>{{ session('error') }}</strong></span>
                    @endif
                    @if($errors->all())
                        <span class="btn btn-danger invalid-feedback" role="alert" style="margin-bottom:20px;display: block"><strong>Проверьте форму на наличие ошибок</strong></span>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success" role="alert" style="margin-bottom:20px"><strong>{{ session('success') }}</strong></div>
                    @endif
                    <form action="{{ $action }}" method="post" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Кому</label>
                                        <select name="to" class="form-control" onchange="edit(this.value);">
                                            <option value="newsletter"{{ old('to') === 'newsletter' ? ' selected' : '' }}>Все подписчики на рассылку</option>
                                            <option value="customer_all"{{ old('to') === 'customer_all' ? ' selected' : '' }}>Все клиенты</option>
                                            <option value="customer_group"{{ old('to') === 'customer_group' ? ' selected' : '' }}>Группа клиентов</option>
                                            <option value="customer"{{ old('to') === 'customer' ? ' selected' : '' }}>Клиенты</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group to" id="to-customer" style="display: {{ old('to') === 'customer' ? 'block' : 'none' }}">
                                    <div class="controls">
                                        <label>Клиент</label>
                                        <div>
                                            <input type="text" name="customer" placeholder="Клиент" class="form-control">
                                            <div class="well well-sm" id="customer" style="height: 150px; overflow: auto;">
                                                @foreach((array)old('customer') as $customer)
                                                    @isset($customers[$customer])
                                                        <div id="customer{{ $customer }}">
                                                            <i class="minus">-</i>
                                                            {{ $customers[$customer]->customer }}<input type="hidden" name="customer[]" value="{{ $customer }}" />
                                                        </div>
                                                    @endisset
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group to" id="to-customer-group" style="display: {{ old('to') === 'customer_group' ? 'block' : 'none' }}">
                                    <div class="controls">
                                        <label>Кому</label>
                                        <select name="customer_group_id" class="form-control">
                                            <option value="">Выберите группы клиентов</option>
                                            @foreach($customer_groups as $customer_group)
                                                <option value="{{ $customer_group->id }}"{{ $customer_group->id == old('customer_group_id') ? ' selected' : '' }}>{{ $customer_group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Тема</label>
                                        <input type="text" name="subject" class="form-control" placeholder="Тема" value="{{ old('subject') }}" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Сообщение</label>
                                        <textarea name="text" class="tinymce">{!! old('text') !!}</textarea>
                                    </div>
                                </div>
                            </div>
                            @role('edit|create|content_edit')
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Отправить</button>
                            </div>
                            @endrole
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-scripts')
    <script src="{{asset('assets/admin/js/tinymce/jquery.tinymce.min.js')}}"></script>
@endsection

@section('page-scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        function edit(val) {
            $('.to').fadeOut();
            $('select[name="customer_group_id"]').val('');
            $('#customer').html('');

            if (val === 'customer') {
                $('#to-customer').fadeIn();
            } else if (val === 'customer_group') {
                $('#to-customer-group').fadeIn();
            }
        }

        $(document).ready(function() {
            $('#customer').delegate('.minus', 'click', function() {
                $(this).parent().remove();
            });

            $('[name="customer"]').autocomplete({
                source: function(request, response) {
                    $.ajax( {
                        url: '{{ asset('admin/customer_autocomplete') }}',
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    value: item['customer'],
                                    id: item['id']
                                }
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $('[name="customer"]').val('');

                    $('#customer' + ui.item.id).remove();

                    $('#customer').append('<div id="customer' + ui.item.id + '"><i class="minus">-</i> ' + ui.item.value + '<input type="hidden" name="customer[]" value="' + ui.item.id + '" /></div>');
                }
            });
        });
    </script>
@endsection