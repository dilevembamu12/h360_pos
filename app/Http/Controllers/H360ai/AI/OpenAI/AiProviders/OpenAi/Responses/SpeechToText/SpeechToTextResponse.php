<?php 

namespace Modules\OpenAI\AiProviders\OpenAi\Responses\SpeechToText;

use Modules\OpenAI\Contracts\Responses\SpeechToText\SpeechResponseContract;
use Exception;

class SpeechToTextResponse implements SpeechResponseContract
{
    /**
     * The AI response data.
     *
     * @var array
     */
    public $response;

    /**
     * The extracted text from the AI response.
     *
     * @var string
     */
    public $text;

    /**
     * The word count of the extracted text.
     *
     * @var int
     */
    public $word;

     /**
     * The duration of the audio in minutes.
     *
     * @var string
     */
    public $duration;

    /**
     * Constructor initializes the response and processes text, duration, and word count.
     *
     * @param array $aiResponse The AI response containing speech-to-text data.
     */
    public function __construct($aiResponse) 
    {
        $this->response = $aiResponse;
        $this->text();
        $this->duration();
        $this->words();
    }

    /**
     * Extracts the text from the AI response.
     *
     * @return string The extracted text.
     * @throws \Exception If the response contains an error or text is not available.
     */
    public function text(): string
    {
        if (isset($this->response['text'])) {
            return $this->text = $this->response['text'];
        }

        return isset($this->response['error']) ? $this->handleException($this->response['error']['message']) : $this->handleException($this->response);
    }

    /**
     * Calculates the duration of the audio in minutes.
     *
     * @return string The duration in minutes.
     */
    public function duration(): string
    {
        return $this->duration = $this->response['duration'] / 60; // In minute
    }


     /**
     * Counts the number of words in the extracted text.
     *
     * @return int The word count.
     */
    public function words(): int
    {
        return $this->word = str_word_count($this->response['text']);
    }

    /**
     * Returns the full AI response.
     *
     * @return mixed The original AI response data.
     */
    public function response(): mixed
    {
        return $this->response;
    }

    /**
     * Handles exceptions by throwing an Exception with the provided message.
     *
     * @param string $message The exception message.
     * @return Exception
     * @throws Exception Always throws an exception with the given message.
     */
    public function handleException(string $message): Exception
    {
        throw new Exception($message);
    }
}