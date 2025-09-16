<div class="main-content">
    <aside>
        @include('components.subcategories')
    </aside>

    <article @php(post_class('h-entry'))>
        <div class="e-content">
            <div class="thumb">
                {!! the_post_thumbnail() !!}
            </div>

            <h1 class="p-name">{!! $title !!}</h1>
            
            @include('partials.entry-meta')

            @php(the_content())
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
</div>
