<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)->columnSpanFull(),
                Section::make('Permissions')
                    ->description('Select the permissions for this role')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('')
                            ->options(fn () => \Spatie\Permission\Models\Permission::all()->pluck('name', 'id'))
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->required()
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->modifyQueryUsing(fn (Builder $query) => $query->where('id', '!=', 1))
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Role $record) {
                        if ($record->hasUsers()) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete role')
                                ->body("This role is assigned to {$record->getUsersCount()} user(s). Please remove the role from all users before deleting.")
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
                            $rolesWithUsers = $records->filter(fn ($role) => $role->hasUsers());

                            if ($rolesWithUsers->isNotEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Cannot delete roles')
                                    ->body("Some roles are assigned to users: " . $rolesWithUsers->pluck('name')->join(', ') . ". Please remove these roles from all users before deleting.")
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
