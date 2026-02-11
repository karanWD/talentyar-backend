    <?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $provinceId = Province::query()->inRandomOrder()->first()?->id;
        $cityId = $provinceId
            ? City::query()->where('province_id', $provinceId)->inRandomOrder()->first()?->id
            : null;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => '09' . fake()->numerify('#########'),
            'email' => fake()->optional(0.8)->safeEmail(),
            'username' => strtolower(fake()->userName()),
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'gender' => fake()->randomElement(User::GENDERS),
            'birth_date' => fake()->date('Y-m-d', '-18 years'),
            'weight' => fake()->numberBetween(50, 100),
            'height' => fake()->numberBetween(160, 200),
            'foot_specialization' => fake()->randomElement(User::FOOT_SPECIALIZATION),
            'post_skill' => fake()->randomElement(User::POST_SKILL),
            'skill_level' => fake()->randomElement(User::SKILL_LEVEL),
            'activity_history' => fake()->boolean(40),
            'team_name' => fake()->optional(0.5)->company(),
            'favorite_iranian_team' => fake()->optional(0.6)->company(),
            'favorite_foreign_team' => fake()->optional(0.5)->company(),
            'shirt_number' => fake()->optional(0.5)->numberBetween(1, 99),
            'bio' => fake()->optional(0.4)->sentence(8),
        ];
    }
}
