<?php

namespace App\Filament\Resources\LogUsers\Pages;

use App\Filament\Resources\LogUsers\LogUserResource;
use Filament\Resources\Pages\ListRecords;

class ListLogUsers extends ListRecords
{
    protected static string $resource = LogUserResource::class;
}
