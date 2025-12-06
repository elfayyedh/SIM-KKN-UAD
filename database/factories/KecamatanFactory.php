<?php

namespace Database\Factories;

use App\Models\Kecamatan;
use App\Models\Kabupaten;
use Illuminate\Database\Eloquent\Factories\Factory;

class KecamatanFactory extends Factory
{
    protected $model = Kecamatan::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->citySuffix(),
            'id_kabupaten' => Kabupaten::factory()->create()->id,
        ];
    }
}
