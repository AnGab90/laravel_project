<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{

    public function definition()
    {
        return [
            "title"=>$this->faker->jobTitle,
            "image"=>$this->faker->image('public/storage',600,480, null, false),
            "description"=>$this->faker->text(50),
            "user_id"=>rand(1,1)
        ];
    }
}
