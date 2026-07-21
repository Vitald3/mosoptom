<div class="content_top flex-2 wrap">
    @foreach($modules as $module)
        {!! html_entity_decode($module) !!}
    @endforeach
</div>