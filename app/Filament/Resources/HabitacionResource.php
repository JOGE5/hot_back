<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HabitacionResource\Pages;
use App\Models\Habitacion;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class HabitacionResource extends Resource
{
    protected static ?string $model = Habitacion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Habitaciones';

    protected static ?string $modelLabel = 'habitación';

    protected static ?string $pluralModelLabel = 'habitaciones';

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA']);
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();
        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Filament::auth()->user();
        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN']);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('numero')
                    ->label('Número de habitación')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(10)
                    ->regex('/^[a-zA-Z0-9\-]+$/')
                    ->validationMessages([
                        'regex' => 'Solo se permiten letras, números y guiones.',
                    ])
                    ->placeholder('Ej. 101')
                    ->helperText('Ingrese un número o código corto de habitación.'),

                Select::make('tipo')
                    ->label('Tipo de habitación')
                    ->options([
                        'Simple' => 'Simple',
                        'Doble' => 'Doble',
                        'Matrimonial' => 'Matrimonial',
                        'Familiar' => 'Familiar',
                        'Suite' => 'Suite',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->in(['Simple', 'Doble', 'Matrimonial', 'Familiar', 'Suite']),

                TextInput::make('capacidad')
                    ->label('Capacidad')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(10)
                    ->step(1)
                    ->inputMode('numeric')
                    ->helperText('Capacidad máxima permitida: 10 personas.'),

                TextInput::make('precio_noche')
                    ->label('Precio por noche')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10000)
                    ->step(0.01)
                    ->prefix('Bs.')
                    ->helperText('Ingrese el precio por noche en bolivianos.'),

                Select::make('estado')
                    ->label('Estado')
                    ->required()
                    ->native(false)
                    ->options([
                        'Disponible' => 'Disponible',
                        'Ocupada' => 'Ocupada',
                        'Reservada' => 'Reservada',
                        'Mantenimiento' => 'Mantenimiento',
                        'Inactiva' => 'Inactiva',
                    ])
                    ->in(['Disponible', 'Ocupada', 'Reservada', 'Mantenimiento', 'Inactiva']),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->nullable()
                    ->maxLength(255)
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Máximo 255 caracteres.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
                '2xl' => 4,
            ])
            ->recordClasses('!bg-transparent !shadow-none !ring-0 !p-0')
            ->columns([
                Stack::make([
                    TextColumn::make('numero')
                        ->searchable()
                        ->extraAttributes(['class' => 'hidden']),

                    TextColumn::make('tipo')
                        ->searchable()
                        ->extraAttributes(['class' => 'hidden']),

                    TextColumn::make('estado')
                        ->searchable()
                        ->extraAttributes(['class' => 'hidden']),

                    View::make('filament.tables.columns.habitacion-card'),
                ])->space(0),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Disponible' => 'Disponible',
                        'Ocupada' => 'Ocupada',
                        'Reservada' => 'Reservada',
                        'Mantenimiento' => 'Mantenimiento',
                        'Inactiva' => 'Inactiva',
                    ]),

                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'Simple' => 'Simple',
                        'Doble' => 'Doble',
                        'Matrimonial' => 'Matrimonial',
                        'Familiar' => 'Familiar',
                        'Suite' => 'Suite',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver'),

                EditAction::make()
                    ->label('Editar')
                    ->visible(fn ($record): bool => self::canEdit($record)),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHabitacions::route('/'),
            'create' => Pages\CreateHabitacion::route('/create'),
            'view' => Pages\ViewHabitacion::route('/{record}'),
            'edit' => Pages\EditHabitacion::route('/{record}/edit'),
        ];
    }
}
