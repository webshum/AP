@php
    $subcategories = [];
    $current_category = null;
    $gallery = null;

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
    
    if (!empty($current_category)) {
        $args = [
            'taxonomy'   => 'category',
            'child_of'   => $current_category->term_id,
            'hide_empty' => true,
        ];

        $subcategories = get_categories($args);

        $findCategoriesGallery = get_categories([
            'taxonomy' => 'category',
            'child_of' => $current_category->term_id,
            'hide_empty' => false,
        ]);

        $gallery = collect($findCategoriesGallery)->firstWhere('slug', 'gallery');
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
                <a href="/category/{{ $current_category->slug }}/#sub-category-{{ $count }}">
                    <span>{{ $subcategory->name }}</span>
                </a>
                <a href="/category/{{ $current_category->slug }}/#sub-category-{{ $count }}" class="ic-hexagon"></a>
            </li>
            @php $count++ @endphp
        @endforeach

        @if(!empty($gallery))
        <li data-id="gallery">
            <a href="/category/{{ $current_category->slug }}/#gallery-category"><span>Gallery</span></a>
            <a href="/category/{{ $current_category->slug }}/#gallery-category" class="ic-hexagon"></a>
        </li>
        @endif
    </ul>
@endif