<?php

use Illuminate\Database\Seeder;
use App\Models\Reply;

class RepliesTableSeeder extends Seeder
{
    public function run()
    {
        //数据表中存在的所有用户id
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();

        //话题中存在的所有话题id
        $topic_ids = \App\Models\Topic::all()->pluck('id')->toArray();

        //获取faker实例
        $faker = app(Faker\Generator::class);

        $replies = factory(Reply::class)->times(50)
            ->make()
            ->each(function ($reply, $index)
            use ($user_ids,$topic_ids,$faker)
            {
                //从用户id数组中随机取出一个并赋值
                $reply->user_id = $faker->randomElement($user_ids);

                //话题id原理同上
                $reply->topic_id = $faker->randomElement($topic_ids);
        });

        Reply::insert($replies->toArray());
    }

}

