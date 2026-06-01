<?php

namespace App\Filament\Resources\Huespeds\Schemas;

use App\Models\Huesped;
use App\Models\User;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class HuespedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombres')
                    ->label('Nombres')
                    ->required()
                    ->minLength(2)
                    ->maxLength(60)
                    ->rules(['regex:/^[\pL\pM]+(?:\s+[\pL\pM]+)*$/u'])
                    ->validationMessages([
                        'regex' => 'El nombre solo puede contener letras y espacios.',
                    ]),

                TextInput::make('apellido_paterno')
                    ->label('Apellido paterno')
                    ->required()
                    ->minLength(2)
                    ->maxLength(60)
                    ->rules(['regex:/^[\pL\pM]+(?:\s+[\pL\pM]+)*$/u'])
                    ->validationMessages([
                        'regex' => 'El apellido solo puede contener letras y espacios.',
                    ]),

                TextInput::make('apellido_materno')
                    ->label('Apellido materno')
                    ->nullable()
                    ->minLength(2)
                    ->maxLength(60)
                    ->rules(['regex:/^[\pL\pM]+(?:\s+[\pL\pM]+)*$/u'])
                    ->validationMessages([
                        'regex' => 'El apellido solo puede contener letras y espacios.',
                    ]),

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
                    ->maxLength(15)
                    ->rules(['regex:/^\+?[0-9]+$/'])
                    ->validationMessages([
                        'regex' => 'El teléfono solo puede contener números y opcionalmente el signo + al inicio.',
                        'max' => 'El teléfono no debe superar los 15 caracteres.',
                    ]),

                TextInput::make('correo_electronico')
                    ->label('Correo electrónico')
                    ->email()
                    ->nullable()
                    ->dehydrateStateUsing(fn (?string $state): ?string => self::normalizarCorreo($state))
                    ->rules(fn (?Huesped $record): array => [
                        function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                            $correo = self::normalizarCorreo(is_string($value) ? $value : null);

                            if (! $correo) {
                                if ($record?->user_id) {
                                    $fail('El correo electrónico es obligatorio para un huésped con cuenta de acceso.');
                                }

                                return;
                            }

                            $existeHuesped = Huesped::withTrashed()
                                ->whereRaw('LOWER(correo_electronico) = ?', [$correo])
                                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($existeHuesped) {
                                $fail('El correo electrónico ya está registrado en otro huésped.');

                                return;
                            }

                            $existeUsuario = User::withTrashed()
                                ->whereRaw('LOWER(email) = ?', [$correo])
                                ->when($record?->user_id, fn ($query) => $query->whereKeyNot($record->user_id))
                                ->exists();

                            if ($existeUsuario) {
                                $fail('El correo electrónico ya pertenece a un usuario del sistema.');
                            }
                        },
                    ])
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

                Placeholder::make('advertencia_edad_minima')
                    ->hiddenLabel()
                    ->content(new HtmlString(
                        '<div style="border: 1px solid #fca5a5; background: #fef2f2; color: #991b1b; border-radius: 8px; padding: 12px 14px; font-size: 14px; font-weight: 700;">' .
                        '<div style="display: flex; align-items: center; gap: 8px;">' .
                        '<span aria-hidden="true" style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 9999px; background: #dc2626; color: #ffffff; font-size: 12px; font-weight: 800;">!</span>' .
                        '<span style="color: #991b1b;">Solo se permite registrar huéspedes mayores de 21 años.</span>' .
                        '</div>' .
                        '</div>'
                    )),

                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento')
                    ->required()
                    ->maxDate(now()->subYears(21)),

                Toggle::make('estado')
                    ->label('Activo')
                    ->default(true)
                    ->required(),
            ]);
    }

    private static function normalizarCorreo(?string $correo): ?string
    {
        $correo = trim((string) $correo);

        return $correo === '' ? null : mb_strtolower($correo);
    }
}
