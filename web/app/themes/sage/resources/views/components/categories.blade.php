@if (!empty($subcategories) && sizeof($subcategories))
    <div class="scroll-horizontal">
        <ul class="subcategories">
            @foreach ($subcategories as $subcategory)
                @php
                    $image = get_field('image', "term_{$subcategory->term_id}");
                    $active = ($current_category->slug == $subcategory->slug) ? 'active' : '';
                @endphp
                
                <li class="{{ $active }}">
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
    </div>
@endif