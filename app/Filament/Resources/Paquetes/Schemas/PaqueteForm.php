<?php

namespace App\Filament\Resources\Paquetes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaqueteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del paquete')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Select::make('tipo_habitacion')
                            ->label('Tipo de habitación')
                            ->options([
                                'Simple' => 'Simple',
                                'Doble' => 'Doble',
                                'Matrimonial' => 'Matrimonial',
                                'Familiar' => 'Familiar',
                                'Suite' => 'Suite',
                            ])
                            ->searchable()
                            ->native(false)
                            ->nullable(),

                        Select::make('tour_id')
                            ->label('Tour incluido')
                            ->relationship('tour', 'nombre')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable(),

                        TextInput::make('duracion_dias')
                            ->label('Duración en días')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->step(1),

                        TextInput::make('precio_total')
                            ->label('Precio total')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->step('0.01')
                            ->prefix('Bs.'),

                        Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->default('Borrador')
                            ->native(false)
                            ->options([
                                'Borrador' => 'Borrador',
                                'Publicado' => 'Publicado',
                                'Archivado' => 'Archivado',
                            ])
                            ->in(['Borrador', 'Publicado', 'Archivado']),
                    ])
                    ->columns(2),

                Section::make('Incluye')
                    ->schema([
                        Toggle::make('incluye_desayuno')
                            ->label('Desayuno')
                            ->default(false),

                        Toggle::make('incluye_almuerzo')
                            ->label('Almuerzo')
                            ->default(false),

                        Toggle::make('incluye_cena')
                            ->label('Cena')
                            ->default(false),
                    ])
                    ->columns(3),

                Section::make('Multimedia')
                    ->schema([
                        FileUpload::make('imagen')
                            ->label('Imagen')
                            ->image()
                            ->disk('public')
                            ->directory('paquetes')
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
