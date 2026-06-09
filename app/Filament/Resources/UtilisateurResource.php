<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UtilisateurResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UtilisateurResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationGroup(): string
    {
        return 'Administration';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-circle';
    }

    public static function getNavigationLabel(): string
    {
        return 'Utilisateurs';
    }

    public static function getModelLabel(): string
    {
        return 'Utilisateur';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Utilisateurs';
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
            TextColumn::make('name')->label('Nom')->searchable(),
            TextColumn::make('email')->label('Email')->searchable(),
            TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUtilisateurs::route('/'),
        ];
    }
}
