<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create events for each user
        $users = User::all();

        if ($users->count() === 0) {
            $this->command->warn('No users found. Please run UserSeeder or register at least one user.');
            return;
        }

        foreach ($users as $user) {
            foreach (range(1, 10) as $i) {
                $type = collect(['task', 'event', 'appointment'])->random();
                $start_time = now()->addDays(rand(-30, 30))->setTime(rand(8, 18), 0);
                $end_time = (clone $start_time)->addHours(1);

                Event::create([
                    'user_id'    => $user->id,
                    'title'      => ucfirst($type) . ' ' . Str::random(5),
                    'description'=> 'Sample description for ' . $type,
                    'type'       => $type,
                    'start_time'      => $start_time,
                    'end_time'        => $end_time,
                ]);
            }
        }
    }
}
