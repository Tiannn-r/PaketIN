<a class="dashboard-card" href="{{ route($menu['route']) }}">
    <span class="dashboard-card__icon" aria-hidden="true">{{ $menu['symbol'] }}</span>
    <span class="dashboard-card__content">
        <strong>{{ $menu['title'] }}</strong>
        <small>{{ $menu['description'] }}</small>
    </span>
    <span class="dashboard-card__arrow" aria-hidden="true">→</span>
</a>
