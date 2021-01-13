<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailsFactory extends Factory
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
            'mail_to' => $this->faker->unique()->safeEmail,
            'body' => $this->faker->text,
            'is_important'=>$this->faker->boolean,
            'is_deleted' => $this->faker->boolean
        ];
    }
}
