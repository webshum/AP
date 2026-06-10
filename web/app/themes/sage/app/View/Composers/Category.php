<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Category extends Composer {
    protected static $views = ['category'];

    public function with() {
        $current_category = get_queried_object();
        $subcategories = [];

        if (!empty($current_category)) {
            $subcategories = get_categories([
                'taxonomy'   => 'category',
                'child_of'   => $current_category->term_id,
                'hide_empty' => true,
            ]);
        }

        $subcategories_with_posts = [];
        foreach ($subcategories as $subcategory) {
            $query = new \WP_Query([
                'post_type' => 'post',
                'posts_per_page' => -1,
                'order' => 'ASC',
                'tax_query'      => [[
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $subcategory->term_id,
                ]],
            ]);

            $subcategories_with_posts[] = [
                'subcategory' => $subcategory,
                'posts' => $query->posts,
            ];
        }

        return [
            'current_category' => $current_category,
            'subcategories_with_posts' => $subcategories_with_posts,
        ];
    }
}
