@php
    $category = get_the_category();
    $current_category = get_categories(['hide_empty' => false, 'parent' => 0]);
    $current_category = $current_category[0];

    if (!empty($current_category)) {
        $args = [
            'taxonomy'   => 'category',
            'child_of'   => $current_category->term_id,
            'hide_empty' => false,
        ];

        $subcategories = get_categories($args);

        if (!empty($subcategories)) {
            $middleIndex = floor(count($subcategories) / 2);
            array_splice($subcategories, $middleIndex, 0, [$current_category]);
        } else {
            $subcategories = [$current_category];
        }
    }
@endphp

@include('components.categories')

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

    <div class="e-content">
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
