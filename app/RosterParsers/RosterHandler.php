<?php

declare(strict_types=1);

namespace App\RosterParsers;

use App\Models\Airline;
use App\Models\AirlineEvent;
use App\RosterParsers\Html\HtmlParserFactory;
use Illuminate\Validation\ValidationException;

class RosterHandler
{
    protected string $airline;
    protected string $system;
    protected string $fileType;
    protected string $fileContent;

    public function __construct(array $params)
    {
        $this->airline = $params['airline'];
        $this->system = $params['system'];
        $this->fileType = $params['file_type'];
        $this->fileContent = $params['file_content'];
    }

    public function store(): ?true
    {
        $airline = Airline::firstOrCreate([
            'name' => $this->airline
        ]);

        if ($airline->whereHas('rosters', fn($query) => $query->where('hash', sha1($this->fileContent)))->exists()) {
            throw ValidationException::withMessages([
                'error' => "File already imported"
            ]);
        }

        $rosterFactory = match ($this->fileType) {
            'html' => new HtmlParserFactory($this->system),
            'default' => throw ValidationException::withMessages([
                'error' => "Unsupported roster system",
            ])
        };

        $parser = $rosterFactory->createParser();
        $parsedData = $parser->parse($this->fileContent);
        if ($parsedData) {
            foreach ($parsedData as $data) {
                $airportEvent = new AirlineEvent($data);
                $airportEvent->fill([
                    'airline_id' => $airline->id
                ]);
                $airportEvent->save();
            }
        }

        $airline->rosters()->create([
            'hash' => sha1($this->fileContent),
            'system' => $this->system,
            'file_type' => $this->fileType
        ]);

        return true;
    }
}
