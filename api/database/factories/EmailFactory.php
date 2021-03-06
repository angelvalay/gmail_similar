<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Email::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'mail_from' => $this->faker->unique()->safeEmail,
            'mail_to' => null,
            'title'=>$this->faker->realText(50),
            'body' => $this->faker->realText(1000),
            'is_important'=>false,
            'is_read'=>false,
            'is_deleted' => false
        ];
    }
}
