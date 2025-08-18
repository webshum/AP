@extends('layouts.app')
@php
    $subcategories = [];
    $posts = [];
    $current_category = get_queried_object();
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
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

        $query = new WP_Query([
            'cat'            => $current_category->term_id,
            'post_status'    => 'publish',
            'posts_per_page' => get_option('posts_per_page'),
            'paged'          => $paged,
        ]);
    }
@endphp

@section('content')
<div class="center">
    <div class="main-stars">
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
		<div class="firefly"></div>
	</div>
    
    <div class="category-header hidden">
        @if(!empty($current_category->name))
        <h1 class="title">
            {{ $current_category->name }}
        </h1>
        @endif

        @if (category_description())
        <div class="description">
            {!! category_description() !!}
        </div>
        @endif
    </div>
    
    @include('components.categories')

    @if($query->have_posts())
        <div class="posts">
            @while($query->have_posts())
                @php $query->the_post() @endphp
                <div class="post">
                    {!! get_the_post_thumbnail(get_the_ID(), 'medium') !!}
                    <div class="foot">
                        <h2>
                            <a href="{{ get_permalink() }}">{{ get_the_title() }}</a>
                        </h2>
                    </div>
                    <a class="btn-more" href="{{ get_permalink() }}">
                        <svg><use xlink:href="#arrow"></use></svg>
                    </a>
                </div>
            @endwhile
        </div>
        
        <div class="pagination">
            {!! paginate_links([
                'total' => $query->max_num_pages,
                'current' => $paged,
                'prev_text' => __('«'),
                'next_text' => __('»'),
            ]) !!}
        </div>
        @php wp_reset_postdata() @endphp
    @endif
</div>
@endsection