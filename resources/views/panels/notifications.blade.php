<li class="dropdown dropdown-notification nav-item{!! isset($show) ? ' show' : '' !!}">
    <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
        <i class="ficon bx bx-bell bx-tada bx-flip-horizontal"></i>
        <span class="badge badge-pill badge-danger badge-up bup">{{ isset($notifications) && count($notifications) ? count($notifications) : 0 }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
        <li class="dropdown-menu-header">
            <div class="dropdown-header px-1 py-75 d-flex justify-content-between">
                @if(isset($notifications))
                    <span class="notification-title"><span id="tet">{{ count($notifications) }}</span> {{ Lang::choice('уведомление|уведомлений|уведомления', count($notifications)) }}</span>
                @endif
                @if(isset($notifications) && count($notifications))
                <span class="text-bold-400 cursor-pointer clear_notifications">Отметить все</span>
                @endif
            </div>
        </li>
        @if(isset($notifications))
            @foreach($notifications as $n)
                <li class="scrollable-container media-list ps">
                    <a data-href="{{ $n->href }}" data-id="{{ $n->notification_id }}" class="d-flex justify-content-between read-notification cursor-pointer">
                        <div class="media d-flex align-items-center">
                            <div class="media-body">
                                <h6 class="media-heading"><span class="text-bold-500">{{ $n->heading }}</span></h6><small class="notification-text">{{ mb_substr($n->text, 0, 50) }}...</small>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        @endif
    </ul>
</li>