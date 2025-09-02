@if (!empty($subcategories) && sizeof($subcategories))
    @php $count = 0; @endphp
    <ul class="hexagon-categories">
        @foreach ($subcategories as $subcategory)
            @php
                $active = ($current_category->slug == $subcategory->slug) ? 'active' : '';
            @endphp

            <li class="{{ $active }}" data-id="sub-category-{{ $count }}">
                <a href="#sub-category-{{ $count }}">
                    <span>{{ $subcategory->name }}</span>
                </a>
                <a href="#sub-category-{{ $count }}" class="ic-hexagon"></a>
            </li>
            @php $count++ @endphp
        @endforeach
    </ul>
@endif