{{-- 
	Template Name: Page Faq
--}}
@php
	$faq = new WP_Query([
		'post_type' => 'faq',
		'posts_per_page' => -1
	]);
@endphp

@extends('layouts.app')

@section('content')
<div class="center">
	@include('partials.page-header')
	
	<ul class="accordeon faq-accordeon">
	@while($faq->have_posts()) @php($faq->the_post())
	    <li class="item-accordeon">
	        <div class="btn-accordeon">
	            <span>{{ get_the_title() }}</span>
	            <div class="ic-plus"></div>
	        </div>
	
	        <div class="content-accordeon">
	            <div class="inner">
	                {!! get_the_content() !!}
	            </div>
	        </div>
	    </li>
	@endwhile
	</ul> 
</div>
@endsection