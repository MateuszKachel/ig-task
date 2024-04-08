<?php

declare(strict_types=1);

namespace App\RosterParsers\Html;

use App\RosterParsers\Parser;
use App\RosterParsers\ParserFactory;
use Illuminate\Validation\ValidationException;

readonly class HtmlParserFactory implements ParserFactory
{
    public function __construct(private string $rosterSystem = 'ccnx')
    {
    }

    public function createParser(): Parser
    {
        $parserClassName = __NAMESPACE__ . '\\' . $this->getParserClassName();
        if (!class_exists($parserClassName)) {
            throw ValidationException::withMessages([
                'error' => "Unsupported roster system",
            ]);
        }
        return new $parserClassName();
    }

    /**
     * Each roster system is mapped to a specific parser class
     */
    private function getParserClassName(): string
    {
        // You can move this to a configuration file
        $formatToParserClassMap = [
            "ccnx" => "CcnxParser",
            // Add more mappings as needed
        ];

        return $formatToParserClassMap[$this->rosterSystem] ?? '';
    }
}
