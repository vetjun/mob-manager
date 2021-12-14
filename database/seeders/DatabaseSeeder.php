<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Credential;
use App\Models\Repositories\AccountRepository;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        foreach (range(0, 10) as $item) {
            $application = Application::query()->create([
                'app_id' => Str::random(10),
                'name' => $faker->name(),
                'description' => $faker->domainName(),
            ]);
            Credential::query()->create([
                'app_id' => $application->getAttribute('app_id'),
                'username' => $faker->userName(),
                'password' => $faker->password(),
                'provider' => 'ios',
            ]);
            Credential::query()->create([
                'app_id' => $application->getAttribute('app_id'),
                'username' => $faker->userName(),
                'password' => $faker->password(),
                'provider' => 'google',
            ]);
            foreach (range(0, 10) as $item) {
                $params = [
                    'device_uid' => Str::random(32),
                    'app_id' => $application->getAttribute('app_id'),
                    'language' => $faker->randomElement(['TR', 'EN', 'FR']),
                    'operation_system' => $faker->randomElement(['ios', 'google']),
                    'client_token' => $faker->linuxPlatformToken()
                ];
                (new AccountRepository())->insert($params);
            }
        }



        // \App\Models\User::factory(10)->create();
    }
}
