<?php

use Roots\Acorn\Application;
ini_set('display_errors', 0);
/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

Application::configure()
    ->withProviders([
        App\Providers\ThemeServiceProvider::class,
    ])
    ->withRouting(web: base_path('routes/web.php'))
    ->boot();

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file)
            );
        }
    });


/*
|--------------------------------------------------------------------------
| Api
|--------------------------------------------------------------------------
*/
add_action('rest_api_init', function () {
    register_rest_route('categories/v1', '/list', [
        'methods'             => 'GET',
        'callback'            => 'get_categories_api',
        'permission_callback' => '__return_true',
    ]);
});

function get_categories_api() {
    $categories = get_categories([
        'hide_empty' => false,
        'parent'     => 0,
    ]);

    $data = [];

    foreach ($categories as $category) {
        $image = get_field('image', 'term_' . $category->term_id);
        $image_url = is_array($image) ? $image['url'] : null;

        $data[] = [
            'id'          => $category->term_id,
            'name'        => $category->name,
            'slug'        => $category->slug,
            'description' => $category->description,
            'count'       => $category->count,
            'image'       => $image_url,
        ];
    }

    return rest_ensure_response($data);
}

/*
|--------------------------------------------------------------------------
| Seeder posts
|--------------------------------------------------------------------------
*/
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('seed-posts', \App\Seeders\PostSeeder::class);
}

/*
|--------------------------------------------------------------------------
| Register post type
|--------------------------------------------------------------------------
*/
add_action('init', function() {
    $labels = [
        'name'               => 'FAQ',
        'singular_name'      => 'FAQ',
        'menu_name'          => 'FAQ',
        'name_admin_bar'     => 'FAQ',
        'add_new'            => 'Додати новий',
        'add_new_item'       => 'Додати новий FAQ',
        'new_item'           => 'Новий FAQ',
        'edit_item'          => 'Редагувати FAQ',
        'view_item'          => 'Переглянути FAQ',
        'all_items'          => 'Всі FAQ',
        'search_items'       => 'Шукати FAQ',
        'not_found'          => 'FAQ не знайдено',
        'not_found_in_trash' => 'У кошику FAQ не знайдено',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'rest_base'          => 'faqs',
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'faq'],
        'supports'           => ['title', 'editor'],
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-editor-help',
    ];

    register_post_type('faq', $args);
});

/*
|--------------------------------------------------------------------------
| Add current category for body
|--------------------------------------------------------------------------
*/
add_filter('body_class', function ($classes) {
    if (is_single()) {
        $categories = get_the_category();

        if (!empty($categories)) {
            $cat = $categories[0];
            $parent = $cat->parent ? get_category($cat->parent) : $cat;

            $classes[] = 'category-' . sanitize_html_class($parent->slug);
        }
    }

    if (is_category()) {
        $current = get_queried_object();
        $parent = $current->parent ? get_category($current->parent) : $current;
        $classes[] = 'category-' . sanitize_html_class($parent->slug);
    }

    return $classes;
});

/*
|--------------------------------------------------------------------------
| Get background categorie
|--------------------------------------------------------------------------
*/
function get_background_category() {
    $term = get_queried_object();
    $background = null;

    if ($term && isset($term->term_id)) {
        $background = get_field('background', 'term_' . $term->term_id);
    }
    
    echo $background ? "style='{$background}'" : '';
}

add_action('init', function() {
    wp_dequeue_script('wc-cart-fragments');
    remove_action('wp_head', array('WC_Cart', 'get_cart_fragments'));
}, 999);