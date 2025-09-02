@extends('layouts.app')
@php
    $subcategories = [];
    $current_category = get_queried_object();
    
    if (!empty($current_category)) {
        $args = [
            'taxonomy'   => 'category',
            'child_of'   => $current_category->term_id,
            'hide_empty' => true,
        ];

        $subcategories = get_categories($args);
    }
@endphp

@section('content')
<div class="center">
    @include('components.categories')

    <div class="main-content">
        <aside>
            @include('components.subcategories')
        </aside>

        <div class="content">
            @php $count = 0; @endphp

            @if(!empty($subcategories) && sizeof($subcategories))
                @foreach($subcategories as $subcategory)
                    <div class="post-category" id="sub-category-{{ $count }}">
                        @php 
                            $posts = new WP_Query([
                                'post_type' => 'post',
                                'posts_per_page' => -1,
                                'tax_query' => [
                                    [
                                        'taxonomy' => 'category',
                                        'field'    => 'term_id',
                                        'terms'    => $subcategory->term_id,
                                    ]
                                ]
                            ]);
                        @endphp

                        @if($posts->have_posts())
                            @while($posts->have_posts()) @php $posts->the_post(); @endphp
                                @include('partials.article')
                            @endwhile

                            @php wp_reset_postdata() @endphp
                        @endif
                    </div>

                    @php $count++; @endphp
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection