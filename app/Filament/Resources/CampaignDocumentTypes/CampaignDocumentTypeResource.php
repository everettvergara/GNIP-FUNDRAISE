<?php

namespace App\Filament\Resources\CampaignDocumentTypes;

use App\Filament\Concerns\AuthorizesAdminModule;
use App\Filament\Resources\CampaignDocumentTypes\Pages\CreateCampaignDocumentType;
use App\Filament\Resources\CampaignDocumentTypes\Pages\EditCampaignDocumentType;
use App\Filament\Resources\CampaignDocumentTypes\Pages\ListCampaignDocumentTypes;
use App\Filament\Resources\CampaignDocumentTypes\Schemas\CampaignDocumentTypeForm;
use App\Filament\Resources\CampaignDocumentTypes\Tables\CampaignDocumentTypesTable;
use App\Models\CampaignDocumentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CampaignDocumentTypeResource extends Resource
{
    use AuthorizesAdminModule;

    protected static ?string $model = CampaignDocumentType::class;

    protected static ?string $navigationLabel = 'Document Types';

    protected static string|\UnitEnum|null $navigationGroup = 'Campaign Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static function moduleKey(): string
    {
        return 'campaign_document_types';
    }

    public static function form(Schema $schema): Schema
    {
        return CampaignDocumentTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignDocumentTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignDocumentTypes::route('/'),
            'create' => CreateCampaignDocumentType::route('/create'),
            'edit' => EditCampaignDocumentType::route('/{record}/edit'),
        ];
    }
}
