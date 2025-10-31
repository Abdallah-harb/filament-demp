<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()
                    ->maxLength(150)
                    ->unique(
                        ignoreRecord: true
                    ),
                Toggle::make('active')
                    ->onColor('success')
                    ->offColor('danger'),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->maxLength(1000)
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strikeThrough',
                        'bulletList',
                        'numberList',
                        'link',
                        'quote',
                        'code',
                    ]),
                FileUpload::make('attachment')->multiple()->directory('categories')
                    ->maxParallelUploads(4)
                    ->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('attachment')->circular()
                    ->stacked()
                    ->limit(3),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('active', true))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Category $record) {
                        if ($record->products()->count() > 0) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete category')
                                ->body('This category has ' . $record->products()->count() . ' product(s). Please delete or reassign the products first.')
                                ->persistent()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Tables\Actions\DeleteBulkAction $action, $records) {
                            $hasProducts = false;
                            foreach ($records as $record) {
                                if ($record->products()->count() > 0) {
                                    $hasProducts = true;
                                    break;
                                }
                            }

                            if ($hasProducts) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Cannot delete categories')
                                    ->body('One or more categories have products. Please delete or reassign the products first.')
                                    ->persistent()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
            'view' => Pages\ViewCategory::route('/{record}'),
        ];
    }
}
