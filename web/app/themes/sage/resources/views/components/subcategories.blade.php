@php
    $subcategories = [];
    $current_category = null;

    if (!empty(get_queried_object()->term_id)) {
        $current_category = get_queried_object();
    } else {
        $current_category = get_category(get_the_category()[0]->parent);
    }
    
    if (!empty($current_category)) {
        $args = [
            'taxonomy'   => 'category',
            'child_of'   => $current_category->term_id,
            'hide_empty' => true,
        ];

        $subcategories = get_categories($args);
    }
@endphp

@if (!empty($subcategories) && sizeof($subcategories))
    @php $count = 0; @endphp
    <ul class="hexagon-categories">
        @foreach ($subcategories as $subcategory)
            @php
                $active = ($current_category->slug == $subcategory->slug) ? 'active' : '';
            @endphp

            <li class="{{ $active }}" data-id="sub-category-{{ $count }}">
                <a href="#sub-category-{{ $count }}">
                    <span>{{ $subcategory->name }}</span>
                </a>
                <a href="#sub-category-{{ $count }}" class="ic-hexagon"></a>
            </li>
            @php $count++ @endphp
        @endforeach
    </ul>
@endif