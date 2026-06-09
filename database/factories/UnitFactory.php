<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\Lokasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->company(),
            'id_lokasi' => Lokasi::factory()->create()->id,
            'id_tim_monev' => null,
        ];
    }
}
