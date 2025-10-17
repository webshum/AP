@php 
$current_category = null;

if (!empty(get_queried_object()->term_id)) {
    $current_category = get_queried_object();
} else {
    $categories = get_the_category();
    
    if (!empty($categories)) {
        if (count($categories) > 1) {
            foreach ($categories as $cat) {
                if ($cat->parent > 0) {
                    $current_category = get_category($cat->parent);
                    break;
                }
            }

            if (!$current_category) {
                $current_category = get_category($categories[0]->parent);
            }
        } else {
            $current_category = get_category($categories[0]->parent);
        }
    }
}

$categories = get_categories([
    'hide_empty' => false,
    'parent'     => 0,
]);

if (!empty($categories)) {
    $ids = array_column($categories, 'term_id');
    $activeIndex = array_search($current_category->term_id ?? null, $ids);
    
    if ($activeIndex !== false) {
        $middleIndex = floor(count($categories) / 2);

        $activeCategory = $categories[$activeIndex];
        unset($categories[$activeIndex]);
        $categories = array_values($categories);

        array_splice($categories, $middleIndex, 0, [$activeCategory]);
    }
}
@endphp

@if (!empty($categories))
    <ul class="categories-horizontal">
        @foreach ($categories as $category)
            @php
                $image = get_field('image', "term_{$category->term_id}");
                $is_active = false;
                if (!empty($current_category)) {
                    $is_active = ($current_category->term_id && $current_category->term_id == $category->term_id);
                }
            @endphp

            <li class="{{ $is_active ? 'active' : '' }}">
                <a href="{{ get_category_link($category) }}">
                    @if($image)
                        <div class="image">
                            <img width="100" src="{{ $image['url'] }}" alt="{{ $category->name }}" />
                        </div>
                    @endif
                    <span>{{ $category->name }}</span>
                </a>
            </li>
        @endforeach
    </ul>
@endif
