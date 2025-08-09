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
