<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatController {
	private $api_key;
	private $url;
	private $agents;
	private $model;
	private $agentEmbeddings = [];
	private array $listAgents = ['fire', 'water', 'air', 'sun', 'lightning'];

	public function __construct()
    {
        $this->agents = config('agents');
        $this->api_key = env('OPENAI_API_KEY');
		$this->model = env('OPENAI_MODEL');
        $this->url = 'https://api.openai.com/v1';
    }

	public function welcome(Request $request): array
	{
		$lang = $request->input('lang', 'uk');

		$messages = [
			[
				'role' => 'system',
				'content' => config("settings.welcome.{$lang}")
			]
		];

		$response = Http::withToken($this->api_key)
			->post("{$this->url}/chat/completions", [
				'model' => $this->model,
				'messages' => $messages
			]);

		$messages[] = [
			'role' => 'assistant',
			'name' => 'architect',
			'content' => $response->json()['choices'][0]['message']['content'] ?? ''
		];

		return $messages;
	}

	public function orchestrator(Request $request): array {
		$messages = $request->input('message', []);
		$userMessage = $request->input('userMessage', '');
		$lang = $request->input('lang', 'uk');

		if (!empty($userMessage)) {
			$lang = $this->detectLanguageByText($userMessage);
		}

		$response = $this->architect($messages, $lang);

		// if return agent
		if (in_array(trim($response), $this->listAgents)) {
			$prompt = $this->agents[$response][$lang] ?? '';
			$messages[] = [
				'role' => 'system',
				'content' => $prompt
			];

			$content = $this->agent($messages);

			$messages[] = [
				'role' => 'assistant',
				'name' => $response,
				'content' => $content,
			];

			return $messages;
		}

		// if return list agents
		if (str_starts_with($response, '{') && str_ends_with($response, '}')) {
			$requestedAgents = array_map('trim', explode(',', trim($response, '{}')));
			$validAgents = array_intersect($requestedAgents, $this->listAgents);

			foreach ($validAgents as $agent) {
				$prompt = $this->agents[$agent][$lang] ?? '';
				$messages[] = [
					'role' => 'system',
					'content' => $prompt
				];

				$content = $this->agent($messages);

				$messages[] = [
					'role' => 'assistant',
					'name' => $agent,
					'content' => $content,
				];
			}

			return $messages;
		}

		// if return ask
		if (trim($response) === 'ask') {
		 	$messages[] = [
		 		'role' => 'system',
		 		'content' => config("settings.ask.{$lang}")
		 	];

		 	$response = Http::withToken($this->api_key)
		 		->post("{$this->url}/chat/completions", [
		 			'model' => 'gpt-4o-mini',
		 			'messages' => $messages,
		 		]);

		 	$response = $response->json()['choices'][0]['message']['content'] ?? '';
		}

		$messages[] = [
			'role' => 'assistant',
			'name' => 'architect',
			'content' => $response
		];

		return $messages;
	}

	private function architect(array $messages, string $lang): string {
		$messages[] = [
			'role' => 'system',
			'content' => config("agents.architect.{$lang}")
		];

		$response = Http::withToken($this->api_key)
            ->post("{$this->url}/chat/completions", [
                'model' => $this->model,
                'messages' => $messages,
            ]);

        return $response->json()['choices'][0]['message']['content'] ?? '';
	}

	private function agent(array $messages): string {
		$res = Http::withToken($this->api_key)
			->post("{$this->url}/chat/completions", [
				'model' => $this->model,
				'messages' => $messages,
			]);
		
		return $res->json()['choices'][0]['message']['content'] ?? '';
	}

	protected function detectLanguageByText(string $userMessage): string {
		$messages = [
			[
				'role' => 'system',
				'content' => config("settings.definition") . $userMessage
			]
		];

		$res = Http::withToken($this->api_key)
			->post("{$this->url}/chat/completions", [
				'model' => $this->model,
				'messages' => $messages
			]);

		$lang = $res->json()['choices'][0]['message']['content'] ?? 'uk';
		$lang = strtolower(trim($lang));

		return $lang;
	}
}