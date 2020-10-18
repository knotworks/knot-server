<?php

namespace Knot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:legacy-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports data from the legacy Knot database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $env;

    public function __construct()
    {
        parent::__construct();
        $this->env = app()->environment();
    }

    private function toArrayDeep($data = [])
    {
        return json_decode(json_encode($data), true);
    }

    protected function importUsers()
    {
        DB::connection('legacy')->table('users')->get()->each(function ($user) {
            DB::table('users')->insert([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $user->password,
                'avatar' => "{$this->env}/{$user->profile_image}",
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'telegram_user_id' => $user->telegram_user_id,
            ]);
        });
    }

    protected function importFriendships()
    {
        $friendships = $this->toArrayDeep(DB::connection('legacy')->table('friendships')->get()->toArray());
        DB::table('friendships')->insert($friendships);
    }

    protected function importPosts()
    {
        DB::connection('legacy')->table('posts')->get()->each(function ($post) {
            $type = $post->postable_type;
            if ($type === "Knot\Models\TextPost") {
                $textPost = DB::connection('legacy')->table('text_posts')->find($post->id);
                DB::table('posts')->insert([
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'body' => $textPost->body,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ]);
            } else {
                $mediaPost = DB::connection('legacy')->table('photo_posts')->find($post->id);
                $id = DB::table('posts')->insertGetId([
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'body' => $mediaPost->body,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ]);
                DB::table('post_media')->insert([
                    'post_id' => $id,
                    'type' => 'image',
                    'path' => str_replace('photo-posts/', "{$this->env}/media/", $mediaPost->image_path),
                    'created_at' => $mediaPost->created_at,
                    'updated_at' => $mediaPost->updated_at,
                ]);
            }
        });
    }

    protected function importComments()
    {
        $comments = $this->toArrayDeep(DB::connection('legacy')->table('comments')->get()->toArray());
        DB::table('comments')->insert($comments);
    }

    protected function importReactions()
    {
        $reactions = $this->toArrayDeep(DB::connection('legacy')->table('reactions')->get()->toArray());
        DB::table('reactions')->insert($reactions);
    }

    protected function importAccompaniments()
    {
        DB::connection('legacy')->table('accompaniments')->get()->each(function ($accompaniment) {
            DB::table('accompaniments')->insert([
                'id' => $accompaniment->id,
                'post_id' => $accompaniment->post_id,
                'user_id' => $accompaniment->user_id,
                'created_at' => $accompaniment->created_at,
                'updated_at' => $accompaniment->updated_at,
            ]);
        });
    }

    protected function importLocations()
    {
        DB::connection('legacy')->table('locations')->get()->each(function ($location) {
            DB::table('locations')->insert([
                'id' => $location->id,
                'locatable_type' => $location->locatable_type,
                'locatable_id' => $location->locatable_id,
                'lat' => $location->lat,
                'long' => $location->long,
                'city' => $location->city,
                'name' => $location->name,
                'created_at' => $location->created_at,
                'updated_at' => $location->updated_at,
            ]);
        });
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Beginning data import...");
        DB::transaction(function () {
            $this->info("Importing users...");
            $this->importUsers();
            $this->info("Importing friendships...");
            $this->importFriendships();
            $this->info("Importing posts...");
            $this->importPosts();
            $this->info("Importing comments...");
            $this->importComments();
            $this->info("Importing reactions...");
            $this->importReactions();
            $this->info("Importing accompaniments...");
            $this->importAccompaniments();
            $this->info("Importing locations...");
            $this->importLocations();
            $this->info("All done!");
        });

    }
}
