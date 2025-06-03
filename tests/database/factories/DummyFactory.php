<?php

namespace FlorianDomgjoni\AIFactory\Tests\Database\Factories;

use FlorianDomgjoni\AIFactory\Concerns\HasAIFactory;
use FlorianDomgjoni\AIFactory\Tests\Database\Models\Dummy;
use Illuminate\Database\Eloquent\Factories\Factory;

class DummyFactory extends Factory
{
    use HasAIFactory;

    protected $model = Dummy::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }

    public function aiFields(): array
    {
        return [
            'name' => 'A realistic full name',
            'email' => 'A fake but plausible email',
        ];
    }
}
