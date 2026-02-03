<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class GalleryController {
    public function index(Request $request) {
        $id = $request->input('id', 0);
        $images = get_field('gallery', 'term_' . $id);

        return $images;
    }
}