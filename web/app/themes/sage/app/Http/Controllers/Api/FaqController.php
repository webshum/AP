<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class FaqController {
	public function index() {
		return 'all';
	}

	public function store(Request $request) {
		$title = $request[0]['content'] ?? null;
		$content = $request[1]['content'] ?? null;

		$existing_post = get_page_by_title($title, OBJECT, 'faq');

		if ($existing_post) {
			return response()->json([
	            'status' => 'error',
	            'message' => 'FAQ with this title already exists',
	            'id' => $existing_post->ID
	        ], 409);
		}

		$post_id = wp_insert_post([
			'post_type' => 'faq',
			'post_title' => $title,
			'post_content' => $content,
			'post_status' => 'publish'
		]);

		if (is_wp_error($post_id)) {
			return response()->json([
				'status' => 'error',
				'message' => $post_id->get_error_message()
			], 500);
		}

		return response()->json([
			'status' => 'success',
			'id' => $post_id
		], 201);
	}

	public function show($id) {
		return 'show';
	}

	public function update(Request $request, $id) {
		return 'update';
	}

	public function destroy($id) {
		return 'delete';
	}
}