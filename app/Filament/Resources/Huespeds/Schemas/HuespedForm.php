<?php

namespace App\Filament\Resources\Huespeds\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HuespedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombres')
                    ->label('Nombres')
                    ->required()
                    ->maxLength(255),

                TextInput::make('apellido_paterno')
                    ->label('Apellido paterno')
                    ->required()
                    ->maxLength(255),

                TextInput::make('apellido_materno')
                    ->label('Apellido materno')
                    ->nullable()
                    ->maxLength(255),

                Select::make('tipo_documento')
                    ->label('Tipo de documento')
                    ->options([
                        'CI' => 'Cédula de identidad',
                        'PASAPORTE' => 'Pasaporte',
                        'DNI' => 'DNI',
                        'OTRO' => 'Otro',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('numero_documento')
                    ->label('Número de documento')
                    ->required()
                    ->maxLength(50),

                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->nullable()
                    ->maxLength(30),

                TextInput::make('correo_electronico')
                    ->label('Correo electrónico')
                    ->email()
                    ->nullable()
                    ->maxLength(255),

                Select::make('nacionalidad')
                    ->label('Nacionalidad')
                    ->options([
                        'Bolivia' => 'Bolivia',
                        'Argentina' => 'Argentina',
                        'Brasil' => 'Brasil',
                        'Chile' => 'Chile',
                        'Colombia' => 'Colombia',
                        'Ecuador' => 'Ecuador',
                        'Paraguay' => 'Paraguay',
                        'Perú' => 'Perú',
                        'Uruguay' => 'Uruguay',
                        'Venezuela' => 'Venezuela',
                        'México' => 'México',
                        'Estados Unidos' => 'Estados Unidos',
                        'España' => 'España',
                        'Otra' => 'Otra',
                    ])
                    ->required()
                    ->native(false)
                    ->live(),

                TextInput::make('nacionalidad_otra')
                    ->label('Especificar nacionalidad')
                    ->required(fn ($get) => $get('nacionalidad') === 'Otra')
                    ->visible(fn ($get) => $get('nacionalidad') === 'Otra')
                    ->maxLength(100),

                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento')
                    ->required()
                    ->maxDate(now()->subYears(21))
                    ->helperText('Solo se permite registrar huéspedes mayores de 21 años.'),

                Toggle::make('estado')
                    ->label('Activo')
                    ->default(true)
                    ->required(),
            ]);
    }
}