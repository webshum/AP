@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')
    <div class="center e-content">
      @includeFirst(['partials.content-page', 'partials.content'])
    </div>
  @endwhile
@endsection
