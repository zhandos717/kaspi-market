<?php

namespace App\Services\Kaspi\Merchant;

use App\Models\City;
use App\Models\Merchant;
use Illuminate\Support\Facades\Http;

class Collector
{

    public function __construct(private readonly string $link, private readonly ?string $cityId)
    {
    }

    public function handle()
    {
        preg_match_all('/\d{6,9}/', $this->link, $output_array);

        [$id, $cityId] = array_shift($output_array);

        $response = Http::contentType('application/json; charset=UTF-8')
            ->withHeaders(['Referer' => $this->link])
            ->post('https://kaspi.kz/yml/offer-view/offers/' . $id, [
                "cityId" => $this->cityId ?? $cityId,
                "id" => (string)$id,
                "merchantUID" => "",
                "limit" => 64,
                "page" => 0,
                "sort" => true
            ]);

        return array_filter(
            $response->json()['offers'],
            function ($offer) {
                return $offer['kaspiDelivery'] == false;
            }
        );
    }
}