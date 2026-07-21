@if ($paginator->hasPages())
    <nav class="mt30">
        <ul class="pagination wrap">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled prev_pag" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">{{ __('locale.prev') }}</span>
                </li>
            @else
                <li class="page-item prev_pag">
					<?php $url = str_replace(['?page=', '/page/1"', '/page/1'], ['/page/', '"', ''], preg_replace('/\/page\/[0-9]{1,20}/i', '', $paginator->previousPageUrl())) . (request()->get('search') ? '?search=' . request()->get('search') : ''); ?>
                    <a class="page-link" href="{{ $url }}" rel="prev" aria-label="@lang('pagination.previous')">{{ __('locale.prev') }}</a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <?php $url = str_replace(['?page=', '/page/1"', '/page/1'], ['/page/', '"', ''], preg_replace('/\/page\/[0-9]{1,20}/i', '', $url)) . (request()->get('search') ? '?search=' . request()->get('search') : ''); ?>
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item next_pag">
					<?php $url = str_replace(['?page=', '/page/1"', '/page/1'], ['/page/', '"', ''], preg_replace('/\/page\/[0-9]{1,20}/i', '', $paginator->nextPageUrl())) . (request()->get('search') ? '?search=' . request()->get('search') : ''); ?>
                    <a class="page-link" href="{{ $url }}" rel="next" aria-label="@lang('pagination.next')">{{ __('locale.next') }}</a>
                </li>
            @else
                <li class="page-item disabled next_pag" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">{{ __('locale.next') }}</span>
                </li>
            @endif
        </ul>
    </nav>
@endif