<?php

namespace App\Filament\Resources\Reservacions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReservacionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('huesped.id')
                    ->label('Huesped'),
                TextEntry::make('habitacion.id')
                    ->label('Habitacion'),
                TextEntry::make('origen_reservacion'),
                TextEntry::make('fecha_entrada')
                    ->date(),
                TextEntry::make('fecha_salida')
                    ->date(),
                TextEntry::make('cantidad_personas')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('estado_reservacion'),
                TextEntry::make('metodo_pago')
                    ->placeholder('-'),
                TextEntry::make('estado_pago'),
                TextEntry::make('codigo_checkin')
                    ->placeholder('-'),
                TextEntry::make('observacion')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
