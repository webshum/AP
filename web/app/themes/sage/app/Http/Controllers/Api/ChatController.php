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

	public function welcome(Request $request)
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
		$lang = $request->input('lang', 'uk');

		$response = $this->architect($messages, $lang);

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

		if (trim($response) === 'ask') {
		 	$messages[] = [
		 		'role' => 'system',
		 		'content' => "Ти все ще Архітектор Технологій (AI-модератор). 
				Продовжуй відповідь користувачу лише в темах опалення, водопостачання, вентиляції, сонячної енергії та електрики. 
				Не виходь за ці теми. 
				Якщо запит користувача недостатньо зрозумілий, сформулюй **коротке уточнююче питання** українською, щоб залишатися в контексті агента. 
				Не генеруй сторонній текст, привітання або пояснення поза цими темами."
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

	/*public function orchestrator(Request $request)
    {
		$messages = $request->input('message', []);
		$lang = $request->input('lang', 'uk');
		$userMessage = end($messages)['content'] ?? '';

		$intent = $this->smalltalk($userMessage, $lang);

		if ($intent === 'smalltalk') {
			$selectedAgents = ['architect' => 1.0];
		} else {
			$selectedAgents = $this->classifier($userMessage, $lang);
		}

		$agentResponses = [];

		foreach ($selectedAgents as $key => $similarity) {
			if ($key != 'architect') {
				$prompt = $this->agents[$key][$lang] ?? '';

				$messages[] = [
					'role' => 'system',
					'content' => $prompt
				];
				
				$content = $this->agent($messages);
				$agentResponses[$key] = $content;

				$messages[] = [
					'role' => 'assistant',
					'name' => $key,
					'content' => $content,
				];
			}
		}

		$finalResponse = $this->architect($messages, $agentResponses, $lang);

		if (trim($finalResponse) === 'successfully') return $messages;

		if (in_array(trim($finalResponse), $this->listAgents)) {
			$messages[] = [
				'role' => 'assistant',
				'name' => 'architect',
				'content' => "Потрібно уточнити відповідь від агента: $finalResponse",
			];

			$content = $this->agent($messages);

			$messages[] = [
				'role' => 'assistant',
				'name' => $finalResponse,
				'content' => $content,
			];

			return $messages;
		}

		$messages[] = [
			'role' => 'assistant',
			'name' => 'architect',
			'content' => $finalResponse,
		];

		return $messages;
    }

	private function smalltalk(string $message, $lang): string
	{
		if (empty(trim($message))) {
			return 'smalltalk';
		}

		$response = Http::withToken($this->api_key)
			->post("{$this->url}/chat/completions", [
				'model' => 'gpt-4o-mini',
				'messages' => [
					[
						'role' => 'system',
						'content' => config("settings.smalltalk.{$lang}")
					],
					['role' => 'user', 'content' => $message]
				],
			]);

		$result = strtolower(trim($response->json('choices.0.message.content') ?? ''));
		return in_array($result, ['smalltalk', 'domain']) ? $result : 'domain';
	}

	private function agent(array $messages) {
		$res = Http::withToken($this->api_key)
			->post("{$this->url}/chat/completions", [
				'model' => 'gpt-4o-mini',
				'messages' => $messages,
			]);
		
		return $res->json()['choices'][0]['message']['content'] ?? '';
	}

    private function classifier(string $userMessage, string $lang): array
    {
		$this->agentEmbeddings = Cache::remember('agent_embeddings', now()->addHours(24), function() {
			$embeddings = [];

			foreach ($this->agents as $key => $agent) {
				$response = Http::withToken($this->api_key)
					->post("{$this->url}/embeddings", [
						'model' => 'text-embedding-3-large',
						'input' => $agent[$lang]
					]);

				$embeddings[$key] = $response->json()['data'][0]['embedding'] ?? [];
			}

			return $embeddings;
 		});

        $response = Http::withToken($this->api_key)
            ->post("{$this->url}/embeddings", [
                'model' => 'text-embedding-3-large',
                'input' => $userMessage
            ]);
		
		$userEmbedding = $response->json()['data'][0]['embedding'] ?? [];

		$result = [];
        foreach ($this->agentEmbeddings as $key => $agentEmbedding) {
            $similarity = $this->cosineSimilarity($userEmbedding, $agentEmbedding);
            $result[$key] = $similarity;
        }

        arsort($result);

        $maxSim = reset($result);
        $threshold = $maxSim * 0.9;
        $filtered = array_filter($result, fn($v) => $v >= $threshold);

        if (count($filtered) > 3) {
            $filtered = array_slice($filtered, 0, 3, true);
        }

        if (empty($filtered)) {
            $filtered = array_slice($result, 0, 1, true);
        }

        return $filtered;
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        if (empty($a) || empty($b)) {
            return 0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] ** 2;
            $normB += $b[$i] ** 2;
        }

        $denominator = sqrt($normA) * sqrt($normB);
        return $denominator ? $dot / $denominator : 0;
    }

	private function architect(array $messages, array $agentResponses, string $lang): string
    {
        $prompt = $this->agents['architect'][$lang];

        $agentSummary = "";
        foreach ($agentResponses as $agentName => $content) {
            $agentSummary .= "Agent {$agentName} answer: {$content}\n";
        }

        $prompt .= "\n" . $agentSummary;
        $prompt .= "\n" . config("agents.architect.{$lang}");
		$prompt .= "\n - fire: " . config("agents.fire.{$lang}");
		$prompt .= "\n - water: " . config("agents.water.{$lang}");
		$prompt .= "\n - air: " . config("agents.air.{$lang}");
		$prompt .= "\n - sun: " . config("agents.sun.{$lang}");
		$prompt .= "\n - lightning: " . config("agents.lightning.{$lang}");

        $messages[] = [
            'role' => 'system',
            'content' => $prompt
        ];

        $resArchitect = Http::withToken($this->api_key)
            ->post("{$this->url}/chat/completions", [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
            ]);

        return $resArchitect->json()['choices'][0]['message']['content'] ?? '';
    }

	private function isJson($string)
	{
		json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	}*/

	private function isJson($string)
	{
		json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	}
}