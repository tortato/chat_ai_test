<?php
namespace App\Services;

use GuzzleHttp\Client;

class AIService
{
    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Query AI prompt stream
     *
     * @param string $message
     * @param callable $callback
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queryAIStream(string $message, callable $callback)
    {
        $prompt = $this->generateEnergyMarketPrompt($message);
        $finalResponse = '';

        $response = $this->client->post('https://api.openai.com/v1/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ],
            'json' => [
                'model' => env('OPENAI_API_MODEL'),
                'prompt' => $prompt,
                'max_tokens' => 150,
                'stream' => true,
            ],
            'stream' => true,
        ]);

        foreach ($response->getBody() as $chunk) {
            $data = json_decode($chunk, true);
            if (isset($data['choices'][0]['text'])) {
                $text = $this->filterEnergyMarketResponse($data['choices'][0]['text']);
                $callback($text);
                $finalResponse .= $text;
            }
        }

        return trim($finalResponse);
    }

    /**
     * Enforce AI's prompt to Energy Market
     *
     * @param $message
     * @return string
     */
    private function generateEnergyMarketPrompt($message)
    {
        return "You are an person specialized in the energy market. Respond to the following question focusing only on energy market-related information:\n\nQuestion: $message";
    }

    /**
     * Filter result response to enforce "energy market" keyword
     *
     * @param $response
     * @return string
     */
    private function filterEnergyMarketResponse(string $response): string
    {
        // Simple keyword-based filtering
        if (stripos($response, 'energy') !== false) {
            return $response;
        }

        return "[Filtered] Non-energy market-related content was removed.";
    }
}
