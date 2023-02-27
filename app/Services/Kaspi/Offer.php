<?php
namespace App\Services\Kaspi;

use Illuminate\Support\Facades\Http;

class Offer
{

    public function __construct(private readonly string $link, private readonly ?string $cityId)
    {
    }

    public function handle(): array
    {
        preg_match_all('/\d{6,9}/', $this->link, $output);

        [$id, $cityId] = array_shift($output);

        $response = Http::contentType('application/json')
            ->withHeaders(['Referer' => $this->link])
            ->post(config('services.kaspi.domain') . '/yml/offer-view/offers/' . $id, [
                "cityId" => $this->cityId ?? $cityId,
                "id"     => (string)$id,
                "limit"  => 64
            ]);

        return array_filter(
            $response->json()['offers'],
            function ($offer) {
                return $offer['kaspiDelivery'] == false;
            }
        );
    }
}