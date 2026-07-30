<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Persona;

class PersonaFactory extends Factory
{
    protected $model = Persona::class;

    public function definition()
    {
        return [
            'nombres' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName(),
            'cedula' => $this->faker->optional()->unique()->numerify('#########'),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'sexo' => $this->faker->randomElement(['M','F','Otro']),
            'foto' => null,
            'cedula_imagen' => null,
            'verified_at' => $this->faker->optional()->dateTime(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
