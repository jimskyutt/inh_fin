<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $job = Job::inRandomOrder()->first() ?? Job::factory()->create();
        $serviceProvider = $job->service_provider ?? User::role('service_provider')->inRandomOrder()->first() ?? User::factory()->create();
        $homeowner = $job->homeowner ?? User::role('homeowner')->inRandomOrder()->first() ?? User::factory()->create();
        $service = $job->service ?? Service::inRandomOrder()->first() ?? Service::factory()->create();

        return [
            'job_id' => $job->id,
            'homeowner_id' => $homeowner->id,
            'service_provider_id' => $serviceProvider->id,
            'service_id' => $service->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->boolean(70) ? $this->faker->paragraph : null, // 70% chance of having a review
            'job_title' => $job->title,
            'service_name' => $service->service_name,
            'service_provider_name' => $serviceProvider->name,
            'location' => $job->location,
            'scheduled_date' => $job->scheduled_date,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'admin_feedback' => $this->faker->boolean(20) ? $this->faker->sentence : null, // 20% chance of having admin feedback
            'reviewed_at' => $this->faker->optional(0.7)->dateTimeThisYear(), // 70% chance of being reviewed
            'reviewed_by' => $this->faker->optional(0.7, null)->randomElement(
                User::role('admin')->pluck('id')->toArray()
            ),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the review is approved.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => User::role('admin')->inRandomOrder()->first()?->id ?? null,
            ];
        });
    }

    /**
     * Indicate that the review is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'reviewed_at' => null,
                'reviewed_by' => null,
            ];
        });
    }

    /**
     * Indicate that the review is rejected.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => User::role('admin')->inRandomOrder()->first()?->id ?? null,
                'admin_feedback' => $this->faker->sentence,
            ];
        });
    }
}
