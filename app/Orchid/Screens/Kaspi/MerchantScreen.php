<?php

namespace App\Orchid\Screens\Kaspi;

use App\Services\Kaspi\Offer;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class MerchantScreen extends Screen
{
    public function query(): iterable
    {
        $item = \Cache::get('item');

        if (empty($item)) {
            return [];
        }

        $data = (new Offer($item['url'], $item['city_id']))->handle();

        return [
            'table' => $data
        ];
    }

    public function name(): ?string
    {
        return 'Kaspi продавцы';
    }

    public function description(): ?string
    {
        return 'Данные парсинга';
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить данные для парсинга')
                ->modal('exampleModal')
                ->method('showToast')
                ->icon('plus'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('table', [
                TD::make('masterCategory', 'Категория')
                    ->render(fn($data) => $data['masterCategory']),

                TD::make('merchantName', 'Продавец')
                    ->render(fn($data) => $data['merchantName']),

                TD::make('price', 'Цена')
                    ->render(fn($data) => number_format($data['price'], 2)),
            ]),

            Layout::modal(
                'exampleModal',
                Layout::rows([
                    Input::make('url')
                        ->title('url')
                        ->placeholder('http://example.com')
                        ->required(),

                    Input::make('city_id')
                        ->title('city')
                        ->placeholder('710000000'),
                ])
            )->title('Введите данные для получения цен'),
        ];
    }

    public function showToast(Request $request)
    {
        \Cache::set('item', [
            'url'     => $request->url,
            'city_id' => $request->city_id,
        ]);
    }
}