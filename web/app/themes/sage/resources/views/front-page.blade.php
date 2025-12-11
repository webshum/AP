@extends('layouts.app')

@section('content')
<div id="categories">
    <category-list></category-list>
</div>

<div id="ai">
	<div class="center">
		<ai-assistant :design="'frontend'"></ai-assistant>
	</div>
</div>

<x-preloader/>
@endsection
