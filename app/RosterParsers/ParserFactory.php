<?php

declare(strict_types=1);

namespace App\RosterParsers;

interface ParserFactory
{
    public function createParser(): Parser;
}
