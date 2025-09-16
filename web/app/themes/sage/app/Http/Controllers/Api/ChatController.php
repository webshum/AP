<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController {
	public function handle(Request $request) {
		$endpoint = 'https://api.openai.com/v1/chat/completions';
		$messages = $request->input('message', []);
		$category = $request->input('category');
		$prompts = config("prompts");
		$system = !empty($prompts[$category]) ? $prompts[$category] : $prompts['main'];
		$flatMessages = [];

		foreach ($messages as $pair) {
		    if (is_array($pair)) {
		        foreach ($pair as $message) {
		            if (isset($message['role'], $message['content'])) {
		                $flatMessages[] = $message;
		            }
		        }
		    }
		}

		array_unshift($flatMessages, [
			'role' => 'system', 
            'content' => $system
		]);

		$response = Http::withToken(env('OPENAI_API_KEY'))
			->post($endpoint, [
				'model' => 'gpt-4o-mini',
				'messages' => $flatMessages,
			]);

		return $response->json();
	}
}