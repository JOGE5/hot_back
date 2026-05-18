<?php

namespace App\Filament\Resources\Platos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PlatoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del plato')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Select::make('categoria')
                            ->label('Categoría')
                            ->required()
                            ->options([
                                'Desayuno' => 'Desayuno',
                                'Almuerzo' => 'Almuerzo',
                                'Cena' => 'Cena',
                                'Bebida' => 'Bebida',
                                'Postre' => 'Postre',
                                'Especial' => 'Especial',
                            ]),

                        Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->default('Disponible')
                            ->options([
                                'Disponible' => 'Disponible',
                                'No disponible' => 'No disponible',
                                'En preparación' => 'En preparación',
                                'Archivado' => 'Archivado',
                            ]),

                        Select::make('chef_id')
                            ->label('Chef responsable')
                            ->relationship('chef', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Detalle gastronómico')
                    ->schema([
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->nullable()
                            ->columnSpanFull(),

                        TextInput::make('precio')
                            ->label('Precio')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->step('0.01'),

                        TextInput::make('tiempo_preparacion')
                            ->label('Tiempo de preparación en minutos')
                            ->nullable()
                            ->numeric()
                            ->minValue(1)
                            ->step(1),

                        FileUpload::make('imagen')
                            ->label('Imagen del plato')
                            ->image()
                            ->disk('public')
                            ->directory('platos')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/jpg',
                                'image/jfif',
                            ])
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                $extension = $file->getClientOriginalExtension();
                                $nombreArchivo = uniqid('plato_', true) . '.' . $extension;

                                return $file->storeAs('platos', $nombreArchivo, 'public');
                            })
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Observación')
                    ->schema([
                        Textarea::make('observacion')
                            ->label('Observación')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}