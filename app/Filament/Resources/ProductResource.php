<?php

namespace App\Filament\Resources;

use App\Enum\ProductStatusEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\TagsRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $recordTitleAttribute = 'name';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(200),
                TextInput::make('price')->required()->numeric()->minValue(1)->maxValue(1000000)->step(0.01),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->native()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(
                        collect(ProductStatusEnum::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => str($case->name)->headline()])
                            ->toArray()
                    )
                    ->required(),

                Forms\Components\Select::make('tags')->multiple()
                    ->relationship(titleAttribute: 'name')
                    ->preload()
                    ->required()
                    ->required(),

                FileUpload::make('attachment')->multiple()->directory('products')
                    ->maxParallelUploads(4)
                    ->required(),

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
                Toggle::make('active')
                    ->onColor('success')
                    ->offColor('danger')
                ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('attachment')->circular()
                    ->limit(1),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('price')->money('EGP')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('tags')->label('Tags')
                    ->getStateUsing(
                        fn (Product $record) => $record->tags->pluck('name')->join(', ')
                    )
                    ->sortable()
                    ->searchable()->badge(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(
                        collect(ProductStatusEnum::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => str($case->name)->headline()])
                            ->toArray()
                    ),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                Tables\Filters\Filter::make('active')->
                query(fn (Builder $query): Builder => $query->where('active', true)),
            ],layout:Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TagsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
