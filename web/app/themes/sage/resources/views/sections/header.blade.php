<header class="header">
    <div class="center">
        <a href="/" class="account-user">
            <div class="ic-hexagon"></div>
        </a>
        
        <a class="brand" href="{{ home_url('/') }}">
          {!! $siteName !!}
        </a>

        @if (has_nav_menu('primary_navigation'))
          <nav class="nav-primary" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
            {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'echo' => false]) !!}
          </nav>
        @endif

        <a href="#" class="account-installator">
            <div class="ic-hexagon"></div>
        </a>

        <button class="menu-open">
          <span></span>
          <span></span>
          <span></span>
        </button>
    </div>
</header>

