<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\BlogCategoryResource;
use Filament\Resources\Pages\ListRecords;

class ListBlogCategories extends ListRecords
{
    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nueva categoría'),
        ];
    }
}
