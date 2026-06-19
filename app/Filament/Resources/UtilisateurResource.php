<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UtilisateurResource\Pages;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
        return $schema->components([
            TextInput::make('name')->label('Nom')->required()->maxLength(255),
            TextInput::make('email')->label('Email')->email()->required()
                ->unique(ignoreRecord: true)->maxLength(255),
            Select::make('agent_id')
                ->label('Agent rattaché')
                ->relationship('agent', 'matricule')
                ->getOptionLabelFromRecordUsing(fn ($record) => trim(
                    ($record->matricule ?? '') . ' — ' . ($record->nom ?? '') . ' ' . ($record->prenoms ?? '')
                ))
                ->searchable()
                ->preload()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nom')->searchable(),
            TextColumn::make('email')->label('Email')->searchable(),
            TextColumn::make('agent.matricule')->label('Agent')->placeholder('—'),
            TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y'),
        ])->recordActions([
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUtilisateurs::route('/'),
            'edit'  => Pages\EditUtilisateur::route('/{record}/edit'),
        ];
    }
}
