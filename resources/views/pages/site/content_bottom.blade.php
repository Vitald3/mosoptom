<div class="content_bottom flex-2 wrap">
    @foreach($modules as $module)
        {!! html_entity_decode($module) !!}
    @endforeach
</div>