<?php

namespace Database\Factories;

use App\Models\KKN;
use Illuminate\Database\Eloquent\Factories\Factory;

class KKNFactory extends Factory
{
    protected $model = KKN::class;

    public function definition()
    {
        return [
            'nama' => 'KKN ' . $this->faker->year(),
            'thn_ajaran' => $this->faker->year() . '/' . ($this->faker->year() + 1),
            'tanggal_mulai' => $this->faker->date(),
            'tanggal_selesai' => $this->faker->date(),
            'status' => 'active',
        ];
    }
}
