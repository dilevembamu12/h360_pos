<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Traits;
use Illuminate\Http\Response;

trait OpenAiApiTrait
{
    private $audioUrl = 'https://api.openai.com/v1/audio/transcriptions';

    public function aiKey()
    {
        $apiKey = apiKey('openai');

        if (empty($apiKey)) {
            throw new \Exception(__("There's an issue with the :x API key. Kindly reach out to the administration for assistance.", ['x' => __('OpenAi')]), Response::HTTP_UNAUTHORIZED);
        }

        return $apiKey;
    }
 
    public function client()
    {
        return \OpenAI::client($this->aiKey());
    }

    public function chat()
    {
        return $this->client()->chat()->create($this->processedData);
    }

    public function chatStream()
    {
        return $this->client()->chat()->createStreamed($this->processedData);
    }

    public function embeddings()
    {
        return $this->client()->embeddings()->create($this->processedData);
    }

    public function generate($headers, $url) 
    {
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => config('openAI.ssl_verify_host'),
            CURLOPT_SSL_VERIFYPEER => config('openAI.ssl_verify_peer'),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->processedData,
            CURLOPT_HTTPHEADER => array_merge($headers, [
                "Authorization: Bearer " . $this->aiKey(),
            ]),
        ));
        
        // Make API request
        $response = curl_exec($curl);
        $err = curl_error($curl);
        // Close cURL session
        curl_close($curl);

        $response = !empty($response) ? $response : $err;

        return json_decode($response, true);
    }

    public function audio()
    {
        $headers = [
            'Content-Type: multipart/form-data',
        ];

        return $this->generate($headers, $this->audioUrl);
    }

    public function images()
    {
        return $this->client()->images()->create($this->processedData);
    }

}
    
