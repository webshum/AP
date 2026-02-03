@php 
    $gallery = null;
    $gallery_categories = null;

    if (!empty($category)) {
        $categories = get_categories([
            'taxonomy' => 'category',
            'child_of' => $category->term_id,
            'hide_empty' => false
        ]);

        $gallery = collect($categories)->firstWhere('slug', 'gallery');

        if ($gallery) {
            $gallery_categories = get_categories([
                'taxonomy' => 'category',
                'child_of' => $gallery->term_id,
                'hide_empty' => false
            ]);
        }
    }
@endphp

@if(!empty($gallery_categories) && sizeof($gallery_categories))
    <div id="gallery-category" class="post-category post-gallery">
        <gallery :categories="{{ json_encode($gallery_categories) }}"/>
    </div>
@endif