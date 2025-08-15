@php
    $category = get_the_category();
    $categories = get_categories(['hide_empty' => false, 'parent' => 0]);
@endphp

<article @php(post_class('h-entry'))>
    <header>
        <h1 class="p-name">
            {!! $title !!}
        </h1>
        
        <div class="image">
            {!! the_post_thumbnail() !!}
        </div>

        @include('partials.entry-meta')
    </header>

    <div class="single-content">
        <aside>
            @if (!empty($category) && sizeof($category))
                <ul class="hexagon-categories">
                    @foreach ($category as $category)
                        <li class="current">
                            <a href="{{ get_category_link($category) }}">{{ $category->name }}</a>
                            <a class="ic-hexagon" href="{{ get_category_link($category) }}">#</a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if (!empty($categories) && sizeof($categories))
                <ul class="hexagon-categories">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ get_category_link($category) }}">{{ $category->name }}</a>
                            <a class="ic-hexagon" href="{{ get_category_link($category) }}">#</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>

        <div class="e-content">
            @php(the_content())
        </div>
    </div>

    @if ($pagination())
        <footer>
            <nav class="page-nav" aria-label="Page">
            {!! $pagination !!}
            </nav>
        </footer>
    @endif

    {{-- @php(comments_template()) --}}
</article>
