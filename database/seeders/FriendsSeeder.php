<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Knot\Models\User;

class FriendsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $owner = User::whereEmail('collin@syropia.net')->first();
        if ($owner) {
            // Create some additional users
            $users = create(User::class, [], 6);

            // Add a couple users as friends and have them accept
            $owner->befriend($users[0]);
            $users[0]->acceptFriendRequest($owner);
            $owner->befriend($users[1]);
            $users[1]->acceptFriendRequest($owner);

            // Add a user as friend, but keep it pending
            $owner->befriend($users[2]);

            // Have a user add you as a friend, but keep it pending
            $users[3]->befriend($owner);
        }
    }
}
