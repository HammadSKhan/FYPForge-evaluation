<?php

namespace App\Filament\Advisor\Resources;

use App\Filament\Advisor\Resources\MyProjectResource\Pages;
use App\Models\Advisor;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'My Projects';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('advisor_id', Advisor::authUser()->id);
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->words(10)
                    ->wrap()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('students.name')
                    ->bulleted(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('evaluationPanel.description')
                    ->placeholder('No Panel')
                    ->words(10)
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_evaluation')
                    ->placeholder('Not Scheduled')
                    ->toggleable()
                    ->html()
                    ->state(function (Project $record) {
                        $event = $record->latestEvaluationEvent();
                        if ($event != null) {
                            return $event->name.'<br>'.$event->pivot->evaluation_date->diffForHumans(short: true, parts: 2);
                        } else {
                            return null;
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListMyProjects::route('/'),
            'view' => Pages\ViewMyProject::route('/{record}'),
        ];
    }
}
