<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObjectifResource\Pages;
use App\Models\Objectif;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ObjectifResource extends Resource
{
    protected static ?string $model = Objectif::class;

    public static function getNavigationGroup(): string
    {
        return 'Pilotage';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-flag';
    }

    public static function getNavigationLabel(): string
    {
        return 'Objectifs';
    }

    public static function getModelLabel(): string
    {
        return 'Objectif';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Objectifs';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('libelle')->label('Libellé')->searchable(),
            TextColumn::make('exercice_fiscal_id')->label('Exercice'),
            TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObjectifs::route('/'),
        ];
    }
}
