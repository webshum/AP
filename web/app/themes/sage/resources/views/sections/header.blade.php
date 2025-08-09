<header class="header">
    <div class="center">
        <a href="/">
            <svg width="35" height="35"><use xlink:href="#home"></use></svg>
        </a>
        
        <a class="brand" href="{{ home_url('/') }}">
          {!! $siteName !!}
        </a>

        {{-- @if (has_nav_menu('primary_navigation'))
          <nav class="nav-primary" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
            {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'echo' => false]) !!}
          </nav>
        @endif --}}

        <a href="#">
            <svg width="35" height="35"><use xlink:href="#loginin"></use></svg>
        </a>
    </div>
</header>
