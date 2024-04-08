<?php

declare(strict_types=1);

namespace App\RosterParsers;

interface Parser
{
    public function parse(string $fileContent): array;
}
