<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Design;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $designs = [];

        $design_images = [
            "1700562356_toby-hughes-xu2-g7_2-ta-unsplash.jpg",
            "1700573182_neom-iogsh4cbdjs-unsplash.jpg",
            "1700573194_dan-lefebvre-uf6i-jnjqsg-unsplash.jpg",
            "1700573214_liam-burnett-blue-19vr2qppmpm-unsplash.jpg",
            "1700573227_toby-hughes-xu2-g7_2-ta-unsplash.jpg",
            "1700573243_david-clode-gbr28_rdsp4-unsplash.jpg",
            "1700573274_nik-vzsatucl4co-unsplash.jpg",
            "1700573291_jigar-panchal-wgetd37mgn4-unsplash.jpg",
            "1700573302_karsten-winegeart-b-i8tpbvslm-unsplash.jpg",
            "1700573312_sol-reader-jd3t2k8qu3i-unsplash.jpg",
            "1700573327_resat-kuleli-syu-jzmvbe0-unsplash.jpg",
            "1700573337_lorenzo-hamers-6cklmdoahcc-unsplash.jpg",
            "1700573349_med-badr-chemmaoui-jxqzdlyavg8-unsplash.jpg",
            "1700573358_daniel-j-schwarz-mta4pk_yaz8-unsplash.jpg",
            "1700573385_vino-li-s3c6lv8i_bi-unsplash.jpg",
            "1700573399_europeana-0ipugoselbe-unsplash.jpg",
            "1700573411_zach-lezniewicz-fy7v732qo20-unsplash.jpg",
            "1700573434_jimmy-woo-6ei7ddkhkxg-unsplash.jpg",
            "1700573456_jhunelle-francis-sardido-lszmhnn5qem-unsplash.jpg",
            "1700573470_eugenivy_now-3nxrdbcp134-unsplash.jpg",
            "1700573481_imad-786-n_2bkkeve_0-unsplash.jpg",
            "1700573493_declan-sun-auho68r6xbu-unsplash.jpg",
            "1700573515_jigar-panchal-3znorci1dge-unsplash.jpg",
            "1700573545_karsten-winegeart-am7-eoabgaa-unsplash.jpg",
            "1700573557_manon-lince-wkx-6gxarlk-unsplash.jpg",
            "1700573570_barcs-tamas-ni8mgeq2f1w-unsplash.jpg",
            "1700573602_manon-lince-44dmoffmcag-unsplash.jpg",
            "1700573631_fabio-sasso-sd4o6hnzite-unsplash.jpg",
            "1700573754_e7aqce2wyaai1wj.jpeg",
            "1700671926_jigar-panchal-wgetd37mgn4-unsplash.jpg",
            "1700672029_neom-0suho_b0nus-unsplash.jpg",
            "1700672074_toby-hughes-xu2-g7_2-ta-unsplash.jpg",
            "1700672155_jigar-panchal-wgetd37mgn4-unsplash.jpg",
            "1700672233_imad-786-n_2bkkeve_0-unsplash.jpg"
        ];

        for ($i = 0; $i < 200; $i++) {
            $title = fake()->sentence(random_int(1, 2));
            $user_id = random_int(1, 16);
            $team_id = random_int(1, 2) === 2 && $user_id === 1 ? 2 : null;

            $designs[$i] = [
                "title" => $title,
                "slug" => Str::slug($title),
                "description" => fake()->word(random_int(22, 32)),
                "is_live" => true,
                "upload_successful" => true,
                "team_id" => $team_id,
                "user_id" =>  $user_id,
                "image" => $design_images[random_int(0, count($design_images) - 1)],
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }

        Design::insert($designs);
    }
}
