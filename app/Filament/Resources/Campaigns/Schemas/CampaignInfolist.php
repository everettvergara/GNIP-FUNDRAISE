<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    ViewEntry::make('campaign_preview')
                        ->hiddenLabel()
                        ->view('filament.resources.campaigns.view-campaign-content')
                        ->columnSpanFull(),
                ])
                    ->maxWidth(Width::Large),
            ]);
    }
}
