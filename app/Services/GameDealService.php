<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GameDealService
{
    protected array $storeMap = [
        '1' => 'Steam',
        '2' => 'GamersGate',
        '3' => 'GreenManGaming',
        '6' => 'Direct2Drive',
        '7' => 'GOG',
        '8' => 'Origin',
        '11' => 'Humble Store',
        '13' => 'Uplay',
        '15' => 'Fanatical',
        '21' => 'WinGameStore',
        '23' => 'GameBillet',
        '24' => 'Voidu',
        '25' => 'Epic Games Store',
        '27' => 'Gamesplanet',
        '28' => 'Gamesload',
        '29' => '2Game',
        '30' => 'IndieGala',
        '31' => 'Blizzard Shop',
        '33' => 'DLGamer',
        '34' => 'Noctre',
        '35' => 'DreamGame',
    ];

    protected array $genreMap = [
        'open_world' => [
            'label' => 'Open World / Open Map',
            'keywords' => ['Grand Theft Auto', 'Witcher', 'Cyberpunk', 'Assassin', 'Far Cry', 'Watch Dogs', 'Red Dead', 'Dying Light', 'Just Cause', 'Sleeping Dogs'],
        ],
        'shooter' => [
            'label' => 'Shooter (FPS / TPS)',
            'keywords' => ['Battlefield', 'Call of Duty', 'DOOM', 'Borderlands', 'Metro', 'Crysis', 'Wolfenstein', 'Rainbow Six', 'Sniper', 'BioShock'],
        ],
        'rpg' => [
            'label' => 'RPG (Role-Playing)',
            'keywords' => ['Final Fantasy', 'Elder Scrolls', 'Fallout', 'Persona', 'Dragon Age', 'Mass Effect', 'Dark Souls', 'Monster Hunter', 'Yakuza', 'Divinity'],
        ],
        'horror' => [
            'label' => 'Horror & Survival',
            'keywords' => ['Resident Evil', 'Outlast', 'Dead Space', 'Amnesia', 'Little Nightmares', 'Alan Wake', 'Dead by Daylight', 'Subnautica', 'The Forest'],
        ],
        'racing' => [
            'label' => 'Racing & Sports',
            'keywords' => ['Need for Speed', 'Forza', 'Assetto Corsa', 'DIRT', 'GRID', 'The Crew', 'MotoGP', 'NBA', 'Rocket League'],
        ],
        'strategy' => [
            'label' => 'Strategy & Simulation',
            'keywords' => ['Civilization', 'Age of Empires', 'Total War', 'Cities: Skylines', 'Stellaris', 'XCOM', 'Anno', 'Frostpunk', 'Planet Zoo'],
        ],
        'action_adventure' => [
            'label' => 'Action & Adventure',
            'keywords' => ['Tomb Raider', 'Batman', 'Devil May Cry', 'God of War', 'Spider-Man', 'Star Wars', 'Mortal Kombat', 'Tekken', 'Control'],
        ],
    ];

    public function getGenres(): array
    {
        return collect($this->genreMap)->map(fn ($item) => $item['label'])->toArray();
    }

    protected function client()
    {
        return Http::withHeaders([
            'User-Agent' => 'DealRadar-StudentProject/1.0',
            'Accept' => 'application/json',
        ])->withoutVerifying()->timeout(15);
    }

    protected function mapDealItem(array $item, float $rate): array
    {
        $saleUsd = (float) $item['salePrice'];
        $normalUsd = (float) $item['normalPrice'];

        return [
            'dealID' => $item['dealID'],
            'gameID' => $item['gameID'],
            'title' => $item['title'],
            'store_name' => $this->storeMap[(string) $item['storeID']] ?? 'Official Store',
            'normal_price_usd' => $normalUsd,
            'sale_price_usd' => $saleUsd,
            'normal_price_idr' => round($normalUsd * $rate),
            'sale_price_idr' => round($saleUsd * $rate),
            'savings' => round((float) $item['savings']),
            'metacritic' => $item['metacriticScore'] ?? '-',
            'thumb' => $item['thumb'],
            'redirect_url' => 'https://www.cheapshark.com/redirect?dealID=' . $item['dealID'],
        ];
    }

    public function getUsdToIdrRate(): float
    {
        if (Cache::has('usd_to_idr_rate')) {
            return (float) Cache::get('usd_to_idr_rate');
        }

        $response = $this->client()->get('https://api.frankfurter.app/latest', [
            'from' => 'USD',
            'to' => 'IDR',
        ]);

        if ($response->successful() && isset($response->json()['rates']['IDR'])) {
            $rate = (float) $response->json()['rates']['IDR'];
            Cache::put('usd_to_idr_rate', $rate, 21600);
            return $rate;
        }

        return 16000.0;
    }

    public function searchDeals(string $title = '', ?string $storeId = null, ?string $genre = null): array
    {
        $title = trim($title);
        $cacheKey = 'deals_v2_' . md5($title . '_' . $storeId . '_' . $genre);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $rate = $this->getUsdToIdrRate();

        // Jika memilih Tipe/Genre Game tanpa mengetik judul spesifik
        if ($title === '' && $genre && isset($this->genreMap[$genre])) {
            $keywords = array_slice($this->genreMap[$genre]['keywords'], 0, 6);

            $responses = Http::pool(function (Pool $pool) use ($keywords, $storeId) {
                $reqs = [];
                foreach ($keywords as $kw) {
                    $params = ['title' => $kw, 'pageSize' => 6, 'sortBy' => 'Savings'];
                    if ($storeId) {
                        $params['storeID'] = $storeId;
                    }
                    $reqs[] = $pool->withHeaders([
                        'User-Agent' => 'DealRadar-StudentProject/1.0',
                        'Accept' => 'application/json',
                    ])->withoutVerifying()->timeout(12)->get('https://www.cheapshark.com/api/1.0/deals', $params);
                }
                return $reqs;
            });

            $merged = collect();
            foreach ($responses as $res) {
                if ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) {
                    $merged = $merged->merge($res->json());
                }
            }

            $results = $merged
                ->unique('gameID')
                ->map(fn ($item) => $this->mapDealItem($item, $rate))
                ->sortByDesc('savings')
                ->take(24)
                ->values()
                ->toArray();

            if (!empty($results)) {
                Cache::put($cacheKey, $results, 1800);
            }

            return $results;
        }

        // Pencarian berdasarkan judul game (atau filter toko saja)
        $params = ['pageSize' => 24, 'sortBy' => 'Savings'];
        if ($title !== '') {
            $params['title'] = $title;
        }
        if ($storeId) {
            $params['storeID'] = $storeId;
        }

        $response = $this->client()->get('https://www.cheapshark.com/api/1.0/deals', $params);
        if (!$response->successful()) {
            return [];
        }

        $results = collect($response->json())
            ->map(fn ($item) => $this->mapDealItem($item, $rate))
            ->toArray();

        if (!empty($results)) {
            Cache::put($cacheKey, $results, 1800);
        }

        return $results;
    }

    public function getCheapestEverUsd(string $gameId): float
    {
        $key = "cheapest_game_{$gameId}";
        if (Cache::has($key)) {
            return (float) Cache::get($key);
        }

        $response = $this->client()->get('https://www.cheapshark.com/api/1.0/games', [
            'id' => $gameId,
        ]);

        if ($response->successful() && isset($response->json()['cheapestPriceEver']['price'])) {
            $price = (float) $response->json()['cheapestPriceEver']['price'];
            Cache::put($key, $price, 3600);
            return $price;
        }

        return 0.0;
    }

    public function getHomepageSections(): array
    {
        $rate = $this->getUsdToIdrRate();

        $fetch = function (array $params) use ($rate) {
            $key = 'home_deals_v2_' . md5(json_encode($params));
            if (Cache::has($key)) {
                return Cache::get($key);
            }

            $res = $this->client()->get('https://www.cheapshark.com/api/1.0/deals', $params);
            if (!$res->successful()) {
                return [];
            }

            $data = collect($res->json())
                ->map(fn ($item) => $this->mapDealItem($item, $rate))
                ->toArray();

            if (!empty($data)) {
                Cache::put($key, $data, 1800);
            }

            return $data;
        };

        return [
            'rate' => $rate,
            'genres' => $this->getGenres(),
            'featured' => $fetch(['pageSize' => 3, 'sortBy' => 'Savings', 'metacritic' => 75]),
            'hot_deals' => $fetch(['pageSize' => 6, 'sortBy' => 'Deal Rating']),
            'trending' => $fetch(['pageSize' => 6, 'sortBy' => 'Reviews']),
            'best_deals' => $fetch(['pageSize' => 6, 'sortBy' => 'Price', 'lowerPrice' => 1]),
        ];
    }
}