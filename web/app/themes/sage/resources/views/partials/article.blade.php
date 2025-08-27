<article class="article">
    @if(has_post_thumbnail())
        <div class="image">
            {!! get_the_post_thumbnail() !!}
        </div>
    @endif

    <div>
        <h2>
            <a href="{{ get_permalink() }}">
                {{ get_the_title() }}
            </a>
        </h2>

        <div class="descr">
            {!! get_the_excerpt() !!}
        </div>

        <a href="{{ get_permalink() }}" class="read-more">
            <svg><use xlink:href="#arrow"></use></svg>
        </a>
    </div>
</article>