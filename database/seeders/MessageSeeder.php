<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first();

        if ($admin) {
            // Create 10 unread messages
            Message::factory(10)->create();

            // Create 5 read messages
            Message::factory(5)->read()->create();

            // Create 3 responded messages
            Message::factory(3)
                ->responded()
                ->create([
                    'responded_by_id' => $admin->id,
                ]);

            // Create 2 archived messages
            Message::factory(2)->archived()->create();
        }
    }
}
