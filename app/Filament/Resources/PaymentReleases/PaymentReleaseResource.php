<?php

namespace App\Filament\Resources\PaymentReleases;

use App\Filament\Resources\PaymentReleases\Pages\CreatePaymentRelease;
use App\Filament\Resources\PaymentReleases\Pages\EditPaymentRelease;
use App\Filament\Resources\PaymentReleases\Pages\ListPaymentReleases;
use App\Filament\Resources\PaymentReleases\Pages\ViewPaymentRelease;
use App\Filament\Resources\PaymentReleases\Schemas\PaymentReleaseForm;
use App\Filament\Resources\PaymentReleases\Schemas\PaymentReleaseInfolist;
use App\Filament\Resources\PaymentReleases\Tables\PaymentReleasesTable;
use App\Models\PaymentRelease;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentReleaseResource extends Resource
{
    protected static ?string $model = PaymentRelease::class;

    protected static ?string $navigationLabel = 'Payment Releases';

    protected static string|\UnitEnum|null $navigationGroup = 'Donations and BMS';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function form(Schema $schema): Schema
    {
        return PaymentReleaseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentReleaseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentReleasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['campaign', 'releasedBy'])
            ->withCount('donations');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentReleases::route('/'),
            'create' => CreatePaymentRelease::route('/create'),
            'view' => ViewPaymentRelease::route('/{record}'),
            'edit' => EditPaymentRelease::route('/{record}/edit'),
        ];
    }
}
