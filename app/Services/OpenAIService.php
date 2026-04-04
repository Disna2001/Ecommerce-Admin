<?php

namespace App\Services;

use App\Models\SiteSetting;
use OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $apiKey = SiteSetting::get('ai_api_key') ?: env('OPENAI_API_KEY');
        $this->client = !empty($apiKey) ? OpenAI::client($apiKey) : null;
    }

    /**
     * Get the OpenAI client instance
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Make a chat completion request
     */
    public function chat($messages, $model = 'gpt-3.5-turbo', $temperature = 0.7, $maxTokens = 500)
    {
        try {
            if (!$this->client) {
                throw new \RuntimeException('AI API key is not configured. Add an API key in Admin Settings > AI Operations.');
            }

            $selectedModel = SiteSetting::get('ai_model', $model ?: 'gpt-4o-mini');
            $response = $this->client->chat()->create([
                'model' => $selectedModel,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('OpenAI Chat Error: ' . $e->getMessage());
            throw new \RuntimeException($this->normalizeChatError($e));
        }
    }

    private function normalizeChatError(\Exception $e): string
    {
        $message = trim($e->getMessage());
        $lower = strtolower($message);

        if (str_contains($lower, 'rate limit') || str_contains($lower, 'too many requests')) {
            return 'The AI service is busy right now because the request limit was reached. Please wait a moment and try again.';
        }

        if (str_contains($lower, 'incorrect api key') || str_contains($lower, 'invalid api key') || str_contains($lower, 'unauthorized')) {
            return 'The AI API key looks invalid or unauthorized. Check Admin Settings > AI Operations.';
        }

        if (str_contains($lower, 'model') && (str_contains($lower, 'not found') || str_contains($lower, 'does not exist') || str_contains($lower, 'unsupported'))) {
            return 'The configured AI model is not available for this account. Choose another model in Admin Settings > AI Operations.';
        }

        if (str_contains($lower, 'quota')) {
            return 'The AI usage quota has been exhausted for the configured provider. Review your provider billing or usage limits.';
        }

        return $message !== '' ? $message : 'The AI service is temporarily unavailable.';
    }

    /**
     * Suggest model numbers based on brand and model name
     */
    public function suggestModelNumbers($brandName, $modelName, $existingModels = [])
    {
        try {
            $existingModelsText = !empty($existingModels) 
                ? "\nExisting model numbers in database: " . implode(", ", array_slice($existingModels, 0, 5)) . "" 
                : "";

            $prompt = "Based on the brand '{$brandName}' and model name '{$modelName}', suggest 8 realistic model numbers that would be appropriate for this product.{$existingModelsText}

Consider common patterns like:
- Alphanumeric combinations (e.g., XR-7500, PRO-24-2, MK-2024)
- Year-based codes (e.g., 2024-MB001, 24-MODEL-001)
- Brand-specific naming conventions (e.g., iPhone uses Axxxx, ThinkPad uses 20XX)
- Model-specific identifiers with prefixes/suffixes

Return ONLY the model numbers as a comma-separated list, nothing else. Each model number should be realistic and follow industry standards for {$brandName} products.";

            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a product catalog expert who suggests realistic model numbers for products. You have deep knowledge of how different brands format their model numbers.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.8, 300);

            $content = trim($response->choices[0]->message->content);
            
            // Clean up and parse the response
            $suggestions = array_map(function($item) {
                return trim($item, " \t\n\r\0\x0B\"'");
            }, explode(',', $content));
            
            // Filter out empty suggestions and limit to 10
            $suggestions = array_filter($suggestions);
            $suggestions = array_slice(array_unique($suggestions), 0, 10);
            
            return array_values($suggestions); // Re-index array
            
        } catch (\Exception $e) {
            Log::error('OpenAI Model Suggestion Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search for model numbers based on query
     */
    public function searchModelNumbers($brandName, $searchQuery)
    {
        try {
            $prompt = "Based on the brand '{$brandName}' and search query '{$searchQuery}', suggest 8 relevant model numbers that match this search. 

Consider:
- Partial matches (e.g., searching 'pro' might return 'PRO-1000', 'PROMAX-2000')
- Similar patterns to existing models
- Common naming conventions for {$brandName}

Return realistic model numbers that would be associated with this brand and search term. Return ONLY the model numbers as a comma-separated list, nothing else.";

            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a product catalog search expert who helps users find model numbers.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.7, 250);

            $content = trim($response->choices[0]->message->content);
            
            // Clean up and parse the response
            $suggestions = array_map(function($item) {
                return trim($item, " \t\n\r\0\x0B\"'");
            }, explode(',', $content));
            
            return array_slice(array_filter($suggestions), 0, 10);
            
        } catch (\Exception $e) {
            Log::error('OpenAI Model Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Validate if a model number format is correct for a brand
     */
    public function validateModelNumber($brandName, $modelNumber)
    {
        try {
            $prompt = "For the brand '{$brandName}', is the model number '{$modelNumber}' formatted correctly according to their typical naming convention? 

Consider:
- Does it match the brand's pattern? (e.g., Apple uses Axxxx, Dell uses alphanumeric with dashes)
- Is it a realistic format for this brand?
- Would this be a valid model number for {$brandName}?

Return a JSON response with fields:
- valid: boolean (true/false)
- confidence: number (0-100)
- reasoning: string (brief explanation)
- suggested_format: string (if invalid, suggest proper format)";

            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a product validation expert who knows model number formats for different brands.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.3, 200);

            $content = $response->choices[0]->message->content;
            
            // Try to parse JSON response
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
            
            // Fallback response
            return [
                'valid' => true,
                'confidence' => 50,
                'reasoning' => 'Could not validate definitively',
                'suggested_format' => null
            ];
            
        } catch (\Exception $e) {
            Log::error('OpenAI Model Validation Error: ' . $e->getMessage());
            return [
                'valid' => true,
                'confidence' => 0,
                'reasoning' => 'Validation service unavailable',
                'suggested_format' => null
            ];
        }
    }

    /**
     * Generate model number patterns based on brand
     */
    public function generateModelPatterns($brandName)
    {
        try {
            $prompt = "What are the typical model number patterns used by the brand '{$brandName}'? 

Provide examples of their common formats, such as:
- Apple: Axxxx (e.g., A2487)
- Dell: [Series]-[Number] (e.g., XPS-13-9310)
- Samsung: SM-[Model] (e.g., SM-G998B)
- Lenovo: [Series][Type][Model] (e.g., 20XW0012US)

Return a JSON object with:
- brand: string
- patterns: array of pattern descriptions
- examples: array of example model numbers
- regex_pattern: string (optional regex pattern if applicable)";

            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a brand pattern recognition expert who understands model number formats.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.5, 300);

            $content = $response->choices[0]->message->content;
            
            // Try to parse JSON response
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
            
            return [
                'brand' => $brandName,
                'patterns' => ['Alphanumeric combinations'],
                'examples' => [$brandName . '-001', $brandName . '-PRO-2024'],
                'regex_pattern' => null
            ];
            
        } catch (\Exception $e) {
            Log::error('OpenAI Pattern Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get pricing suggestion for a product
     */
    public function suggestPrice($productName, $category, $costPrice, $competitorPrices = [])
    {
        try {
            $prompt = $this->buildPricingPrompt($productName, $category, $costPrice, $competitorPrices);
            
            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a pricing expert for retail inventory. Provide accurate market-based pricing suggestions.'],
                ['role' => 'user', 'content' => $prompt],
            ]);

            return $this->parsePricingResponse($response->choices[0]->message->content);
            
        } catch (\Exception $e) {
            Log::error('OpenAI Price Suggestion Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate product description
     */
    public function generateDescription($productName, $category, $features = [])
    {
        try {
            $prompt = "Write a compelling product description for a {$productName} in the {$category} category.";
            if (!empty($features)) {
                $prompt .= " Features: " . implode(', ', $features);
            }
            
            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a professional copywriter for e-commerce products.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.8, 300);

            return $response->choices[0]->message->content;
            
        } catch (\Exception $e) {
            Log::error('OpenAI Description Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Predict demand for a product
     */
    public function predictDemand($productName, $category, $historicalSales = [], $season = null)
    {
        try {
            $prompt = "Based on historical sales data, predict the demand for {$productName} in {$category} category.";
            if ($season) {
                $prompt .= " Consider that it's {$season} season.";
            }
            
            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a demand forecasting expert for retail inventory.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.5, 400);

            return $response->choices[0]->message->content;
            
        } catch (\Exception $e) {
            Log::error('OpenAI Demand Prediction Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Categorize product automatically
     */
    public function categorizeProduct($productName, $description)
    {
        try {
            $prompt = "Based on the product name '{$productName}' and description '{$description}', suggest the most appropriate category.";
            
            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are a product categorization expert. Respond only with the category name.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.3, 50);

            return $response->choices[0]->message->content;
            
        } catch (\Exception $e) {
            Log::error('OpenAI Categorization Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate SEO keywords for product
     */
    public function generateSeoKeywords($productName, $category, $description)
    {
        try {
            $prompt = "Generate 10 SEO keywords for a product named '{$productName}' in category '{$category}'. Description: {$description}";
            
            $response = $this->chat([
                ['role' => 'system', 'content' => 'You are an SEO expert. Provide comma-separated keywords only.'],
                ['role' => 'user', 'content' => $prompt],
            ], 'gpt-3.5-turbo', 0.6, 150);

            return explode(',', $response->choices[0]->message->content);
            
        } catch (\Exception $e) {
            Log::error('OpenAI SEO Keywords Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Build pricing prompt
     */
    private function buildPricingPrompt($productName, $category, $costPrice, $competitorPrices)
    {
        $prompt = "Suggest an optimal selling price for a product:\n";
        $prompt .= "Product: {$productName}\n";
        $prompt .= "Category: {$category}\n";
        $prompt .= "Cost Price: \${$costPrice}\n";
        
        if (!empty($competitorPrices)) {
            $prompt .= "Competitor Prices: " . implode(', ', $competitorPrices) . "\n";
        }
        
        $prompt .= "\nProvide response in JSON format with fields: suggested_price, confidence_score, reasoning, market_trend";
        
        return $prompt;
    }

    /**
     * Parse pricing response
     */
    private function parsePricingResponse($response)
    {
        try {
            // Try to parse as JSON first
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
            
            // If not JSON, extract information
            preg_match('/\$?(\d+\.?\d*)/', $response, $matches);
            $price = $matches[1] ?? null;
            
            return [
                'suggested_price' => $price ? floatval($price) : null,
                'raw_response' => $response
            ];
            
        } catch (\Exception $e) {
            return ['raw_response' => $response];
        }
    }
}
