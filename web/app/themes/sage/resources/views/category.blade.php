@extends('layouts.app')
@php
    $subcategories = [];
    $posts = [];
    $current_category = get_queried_object();

    if (!empty($current_category)) {
        $args = array(
            'taxonomy'     => 'category',
            'child_of'     => $current_category->term_id,
            'hide_empty'   => false,
        );

        $subcategories = get_categories($args);
        $posts = get_posts([
            'category' => $current_category->term_id,
            'numberposts' => -1,
            'post_status' => 'publish',
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
   
    <div class="category-header">
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

    @if (!empty($subcategories) && sizeof($subcategories))
        <ul class="subcategories">
            @foreach ($subcategories as $subcategory)
                @php
                    $image = get_field('image', "term_{$subcategory->term_id}")
                @endphp

                <li>
                    <a href="{{ get_category_link($subcategory) }}">
                        @if($image)
                            <div class="image">
                                <img width="100" src="{{ $image['url'] }}" alt="{{ $subcategory->name }}" />
                            </div>
                        @endif
                        <span>{{ $subcategory->name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @if(!empty($posts) && sizeof($posts))
        <div class="posts">
            @foreach ($posts as $post)
                @php setup_postdata($post) @endphp
                <div class="post">
                    {!! get_the_post_thumbnail($post->ID, 'medium') !!}
    
                    <div class="foot">
                        <h2>
                            <a href="{{ get_permalink() }}">
                                {{ get_the_title() }}
                            </a>
                        </h2>
                    </div>

                    <a class="btn-more" href="{{ get_permalink() }}">
                        <svg><use xlink:href="#arrow"></use></svg>
                    </a>
                </div>
            @endforeach
            @php wp_reset_postdata() @endphp
        </div>
    @endif
</div>
@endsection