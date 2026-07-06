<?php

if (!defined('ABSPATH')) {
    exit;
}

use Gemini;
use Gemini\Exceptions\ErrorException as ExceptionsErrorException;
use Gemini\Exceptions\TransporterException;
use OpenAI;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Exceptions\TransporterException as ExceptionsTransporterException;

class WCPDP_AI
{

    /**
     * builds the prompt for ai using the products data
     */
    private function wcpdp_build_ai_prompt(array|null $product_data)
    {

        $categories_names = array();

        if (!empty($product_data['categories'])) {
            $categories_names = $product_data['categories'];
        }

        $dimensions = array();
        if ($product_data['length']) $dimensions[] = "Length : {$product_data['length']}";
        if ($product_data['width']) $dimensions[] = "Width : {$product_data['width']}";
        if ($product_data['height']) $dimensions[] = "Height : {$product_data['height']}";

        $prompt = "Write product copy for the following WooCommerce product:\n\n";
        $prompt .= "Product Name: {$product_data['name']}\n";
        $prompt .= "Product Type: {$product_data['product_type']}\n";
        $prompt .= "Virtual Product: " . ($product_data['is_virtual'] ? 'Yes' : 'No') . "\n";

        if (!empty($categories_names)) {
            $prompt .= "Categories: " . implode(', ', $categories_names) . '\n';
        }

        if ($product_data['sku']) {
            $prompt .= "SKU: {$product_data['sku']}\n";
        }

        if ($product_data['regular_price']) {
            $prompt .= "Regular Price: {$product_data['regular_price']}\n";
        }

        if ($product_data['sale_price']) {
            $prompt .= "Sale Price: {$product_data['sale_price']}\n";
        }

        if ($product_data['weight']) {
            $prompt .= "Weight: {$product_data['weight']}\n";
        }
        if (! empty($dimensions)) {
            $prompt .= "Dimensions: " . implode(', ', $dimensions) . "\n";
        }

        $prompt .= "\nGenerate TWO separate pieces of content:\n\n";
        $prompt .= "1. A SHORT DESCRIPTION: 1-2 sentences (max 30 words), punchy, highlighting the single biggest selling point. Suitable for display near the Add to Cart button.\n\n";
        $prompt .= "2. A FULL DESCRIPTION: 100-150 words, persuasive and benefit-driven, suitable for the main product page. May reference price, dimensions/weight, and category context where relevant. Do not repeat the product name excessively.\n\n";
        $prompt .= "Respond ONLY in this exact format, with no extra commentary:\n\n";
        $prompt .= "SHORT_DESCRIPTION:\n[short description here]\n\n";
        $prompt .= "FULL_DESCRIPTION:\n[full description here]";

        return $prompt;
    }

    /**
     * extract the descriptions from the AI model response
     * returns both short and full description
     */
    private function wcpdp_parse_ai_text(string $ai_text)
    {
        $short_description = '';
        $full_description = '';

        if (preg_match('/SHORT_DESCRIPTION:\s*(.*?)\s*FULL_DESCRIPTION:/is', $ai_text, $matches)) {
            $short_description = trim($matches[1]);
        }

        if (preg_match('/FULL_DESCRIPTION:\s*(.*)/is', $ai_text, $matches)) {
            $full_description = trim($matches[1]);
        }

        return array(
            'short_description' => $short_description,
            'full_description'  => $full_description,
        );
    }

    public function wcpdp_gen_desc(array $product_data)
    {

        $ai_prompt = '';
        if (!empty($product_data)) {
            $ai_prompt = $this->wcpdp_build_ai_prompt($product_data);
        }

        $ai_model = get_option('wcpdp_ai_model');

        error_log('ai_model: ' . $ai_model);

        $ai_text =  '';
        $result = array();

        switch ($ai_model) {
            case 'gemini':
                $result = $this->wcpdp_call_gemini($ai_prompt);
                break;
            case 'Groqai':
                $result = $this->wcpdp_call_groq($ai_prompt);
                break;
            case 'openrouter':
                $result = $this->wcpdp_call_openrouter($ai_prompt);
                break;
            default:
                $result = array(
                    'success' => false,
                    'content' => 'invalid model',
                );;
                break;
        }

        if ($result['content']) {
            $ai_text = $result['content'];

            $product_descriptions = $this->wcpdp_parse_ai_text($ai_text);

            $result['content'] = $product_descriptions;
        }


        return $result;
    }

    private function wcpdp_call_gemini(string $prompt)
    {
        $gemini_api_key =  get_option('wcpdp_api_key');

        if (empty($gemini_api_key)) {
            return array(
                'success' => false,
                'error' => 'Gemini API key is not set'
            );
        }

        try {

            $client = Gemini::client($gemini_api_key);

            $result = $client->generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);

            return array(
                'success' => true,
                'content' => $result->text()
            );
        } catch (ExceptionsErrorException $e) {
            return array(
                'success' => false,
                'error' => 'Gemini error: ' . $e->getMessage(),
            );
        } catch (TransporterException $e) {
            return array(
                'success' => false,
                'error' => 'Gemini error: ' . $e->getMessage(),
            );
        } catch (Throwable $e) {
            return array(
                'success' => false,
                'error' => 'Gemini error: ' . $e->getMessage(),
            );
        }
    }

    private function wcpdp_call_groq(string $prompt)
    {
        $groq_api_key = get_option('wcpdp_api_key');

        if (empty($groq_api_key)) {
            return array(
                'success' => false,
                'error' => 'Gemini API key is not set'
            );
        }

        try {
            $client = OpenAI::factory()
                ->withApiKey($groq_api_key)
                ->withBaseUri('api.groq.com/openai/v1')
                ->make();

            $response = $client->chat()->create([
                'model' => 'llama-3.3-70b-versatile',
                'message' => [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ]);

            return array(
                'success' => true,
                'content' => $response->choices[0]->message->content,
            );
        } catch (ErrorException $e) {
            return array(
                'success' => false,
                'error' => 'Groq  error: ' . $e->getMessage(),
            );
        } catch (ExceptionsTransporterException $e) {
            return array(
                'success' => false,
                'error' => 'Groq  error: ' . $e->getMessage(),
            );
        } catch (\Throwable $e) {
            return array(
                'success' => false,
                'error' => 'Groq  error: ' . $e->getMessage(),
            );
        }
    }

    private function wcpdp_call_openrouter(string $prompt)
    {
        $openrouter_api_key = get_option('wcpdp_api_key');

        if (empty($openrouter_api_key)) {
            return array(
                'success' => false,
                'error' => 'Gemini API key is not set'
            );
        }

        try {
            $client = OpenAI::factory()
                ->withApiKey($openrouter_api_key)
                ->withBaseUri('openrouter.ai/api/v1')
                ->withHttpHeader('HTTP-Referer', home_url())
                ->withHttpHeader('X-Title', 'My Plugin')
                ->make();

            $response = $client->chat()->create([
                'model'    => 'openai/gpt-4o',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return array(
                'success' => true,
                'content' => $response->choices[0]->message->content,
            );;
        } catch (ErrorException $e) {
            return array(
                'success' => false,
                'error' => 'openrouter  error: ' . $e->getMessage(),
            );
        } catch (ExceptionsTransporterException $e) {
            return array(
                'success' => false,
                'error' => 'openrouter  error: ' . $e->getMessage(),
            );
        } catch (\Throwable $e) {
            return array(
                'success' => false,
                'error' => 'openrouter  error: ' . $e->getMessage(),
            );
        }
    }
}
