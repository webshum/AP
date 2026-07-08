@extends('layouts.app')
@section('content')

<x-categories/>

<div class="center">
    <div class="main-content">
        <aside>
            <x-subcategories/>
        </aside>

        <div class="content">
            @if(!empty($subcategories_with_posts) && sizeof($subcategories_with_posts))
                @foreach($subcategories_with_posts as $index => $item)
                    <div class="post-category" id="sub-category-{{ $index }}">
                        <div class="label">{{ $item['subcategory']->name }}</div>

                        @foreach($item['posts'] as $postItem)
                            @php setup_postdata($postItem); @endphp
                            @include('partials.article')
                        @endforeach
                        @php wp_reset_postdata(); @endphp
                    </div>
                @endforeach

                @if(!empty($current_category))
                    <x-gallery :category="$current_category"/>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
