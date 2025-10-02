<?php 

namespace Modules\OpenAI\Services\v2;

use ZipArchive;
use DOMDocument;
use SimpleXMLElement;
use Smalot\PdfParser\Parser;

class EmbeddedService
{
    /**
     * Convert CSV file to text.
     *
     * @param string $path The path to the CSV file.
     *
     * @return string The extracted text content.
     */
    public static function csvToText($path)
    {
        $response = '';

        if (file_exists($path) && is_readable($path) && ($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $response .= implode(' ', $data);
            }
            fclose($handle);
        }

        return $response;
    }

    /**
     * Convert DOCX file to text.
     *
     * @param string $path The path to the DOCX file.
     *
     * @return string The extracted text content.
     */
    public static function docxToText($path)
    {
        $text = '';

        if (! file_exists($path)) {
            return $text;
        }

        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $content = $zip->getFromName('word/document.xml');

            if ($content !== false) {
                $xml = new SimpleXMLElement($content);

                foreach ($xml->xpath('//w:t') as $element) {
                    $text .= trim($element) . ' ';
                }

                $text = str_replace("\xC2\xA0", ' ', $text);

                return $text;
            }

            $zip->close();
        }

        return $text;
    }

    /**
     * Convert PDF file to text.
     *
     * @param string $path The path to the PDF file.
     *
     * @return string The extracted text content.
     */
    public static function pdfToText($path)
    {
        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($path);

        return $pdf->getText();
    }

    /**
     * Convert DOC file to text.
     *
     * @param string $path The path to the DOC file.
     *
     * @return string The extracted text content.
     */
    public static function docToText($path)
    {
        $fileContent = file_get_contents($path);
        $response = '';

        $fileContent = strip_tags($fileContent);

        $pattern = '/[a-zA-Z0-9\s,\.\-@\(\)_\/]+/';

        preg_match_all($pattern, $fileContent, $matches);
        $response = implode(' ', $matches[0]);

        return str_replace('Export HTML to Word Document with JavaScript', '', $response);
    }

    /**
     * Convert XLSX file to text.
     *
     * @param string $path The path to the XLSX file.
     *
     * @return string The extracted text content.
     */
    public static function xlsxToText($path)
    {
        $zip_handle   = new ZipArchive();
        $response     = '';
        if ($zip_handle->open($path) === true) {

            if (($xml_index = $zip_handle->locateName('xl/sharedStrings.xml')) !== false) {
                $doc = new DOMDocument();
                $xml_data   = $zip_handle->getFromIndex($xml_index);
                $doc->loadXML($xml_data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $response   = strip_tags($doc->saveXML());
            }

            $zip_handle->close();
        }

        return $response;
    }

    /**
     * URL scraper.
     *
     * @return mixed The result of parsing the URL.
     */
    public function parseUrl()
    {
        $web = new \Spekulatius\PHPScraper\PHPScraper();
        $web->go(request('url'));

        $fileInfo = [
            'file_path_name' => $this->getDomainName(request('url')),
            'originalName' => request('url'),
        ];
        return [
            'content' => $this->urlContent($web->paragraphs),
            'fileInfo' => $fileInfo,
        ];
    }

     /**
     * Domain name parser.
     *
     * @param string $url The URL to parse.
     *
     * @return string The extracted domain name.
     */
    public function getDomainName($url)
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];

        // Remove 'www.' if it exists
        $host = preg_replace('/^www\./', '', $host);

        return explode('.', $host)[0];
    }

     /**
     * Retrieve content from URL.
     *
     * @param array $contents The contents retrieved from the URL.
     *
     * @return string The concatenated text content.
     */
    public function urlContent($contents)
    {
        $text = '';
        foreach ($contents as $content) {
            $text .= trim($content) . ' ';
        }

        return $text;
    }
}
