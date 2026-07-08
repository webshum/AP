<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TaxonomyController {
    public function index() {
        return 'index';
    }

    public function create(Request $request) {
        return 'create';
    }

    public function store(Request $request) {
        return 'request';
    }

    /**
     * GET /taxonomies/{taxonomy}
     * GET /taxonomies/{taxonomy}?parent=0&orderby=name
     */
    public function show(string $taxonomy, Request $request) {
        $args = [
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ];

        if ($request->has('parent')) {
            $args['parent'] = (int) $request->get('parent');
        }

        $terms = get_categories($args);
        
        $normalized = array_map(function($term) {
            return [
                'id' => $term->term_id ?? $term['cat_ID'] ?? null,
                'name' => $term->name ?? $term->cat_name ?? '',
                'slug' => $term->slug ?? $term->category_nicename ?? '',
                'taxonomy' => $term->taxonomy ?? 'category',
                'description' => $term->description ?? $term->category_description ?? '',
                'parent' => $term->parent ?? $term->category_parent ?? 0,
                'count' => $term->count ?? $term->category_count ?? 0,
                'image' => get_field('image', 'term_' . $term->term_id) ?? null,
            ];
        }, $terms ?? []);

        return response()->json($normalized);
    }

    public function edit(int $id) {
        return 'edit';
    }

    public function destroy(int $id) {
        return 'destroy';
    }
}