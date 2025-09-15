<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController {
	public function handle(Request $request) {
		$endpoint = 'https://api.openai.com/v1/chat/completions';
		$message = $request->get('message');

		$response = Http::withToken(env('OPENAI_API_KEY'))
			->post($endpoint, [
				'model' => 'gpt-4o-mini',
				'messages' => [
                    [
                    	'role' => 'system', 
                    	'content' => 'Ти — AI асистент для сайту webshum. Відповідай коротко і зрозуміло.'
                   	],
                    [
                    	'role' => 'user', 
                    	'content' => $message
                    ],
                ],
			]);

		return $response->json();
	}
}