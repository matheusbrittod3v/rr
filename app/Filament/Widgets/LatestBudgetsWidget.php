<?php

namespace App\Filament\Widgets;

use App\Models\Orcamento;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestBudgetsWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimos Orçamentos Gerados';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $query = Orcamento::query();
        
        $user = auth()->user();
        if ($user && !$user->can('view_all_data') && !$user->hasRole('admin')) {
             $query->where('email', $user->email);
        }
        
        $query->latest()->limit(5);

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email do Cliente')
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('service.nome_servico')
                    ->label('Serviço')
                    ->badge()
                    ->color('primary')
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->paginated(false);
    }
}
