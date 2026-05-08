<?php

namespace App\Filament\Resources\LogoRowResource\Pages;

use App\Filament\Resources\LogoRowResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLogoRow extends CreateRecord
{
    protected static string $resource = LogoRowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['placement'] = 'logo_row';
        return $data;
    }
}
