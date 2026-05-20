<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TourForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del tour')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('ubicacion')
                            ->label('Ubicación')
                            ->maxLength(255)
                            ->nullable(),

                        TextInput::make('duracion')
                            ->label('Duración')
                            ->maxLength(255)
                            ->nullable(),

                        TextInput::make('precio')
                            ->label('Precio')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->prefix('Bs.'),

                        TextInput::make('cupos_disponibles')
                            ->label('Cupos disponibles')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->step(1)
                            ->nullable(),

                        Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->default('Disponible')
                            ->native(false)
                            ->options([
                                'Disponible' => 'Disponible',
                                'No disponible' => 'No disponible',
                                'Archivado' => 'Archivado',
                            ])
                            ->in(['Disponible', 'No disponible', 'Archivado']),
                    ])
                    ->columns(2),

                Section::make('Multimedia')
                    ->schema([
                        FileUpload::make('imagen')
                            ->label('Imagen')
                            ->image()
                            ->disk('public')
                            ->directory('tours')
                            ->visibility('public')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Descripción')
                    ->schema([
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->nullable()
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('observacion')
                            ->label('Observación')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
