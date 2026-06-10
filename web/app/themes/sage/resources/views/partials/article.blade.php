<article class="article">
    @if(has_post_thumbnail($postItem->ID))
        <div class="image">
            {!! get_the_post_thumbnail($postItem->ID) !!}
        </div>
    @endif

    <div>
        <h2>
            <a href="{{ get_permalink($postItem->ID) }}">
                {{ get_the_title($postItem->ID) }}
            </a>
        </h2>
        <div class="descr">
            {!! wp_trim_words(get_the_excerpt($postItem), 30) !!}
        </div>
        <a href="{{ get_permalink($postItem->ID) }}" class="read-more">
            <svg><use xlink:href="#arrow"></use></svg>
        </a>
    </div>
</article>
