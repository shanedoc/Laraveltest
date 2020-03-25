<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{

    public function creating(Reply $reply)
    {
        //dd(clean($reply->content, 'user_topic_body'));
        $reply->content = clean($reply->content, 'user_topic_body');
    }

    public function created(Reply $reply)
    {
        //先创建，成功后再对话题下的所有回复进行统计
        $reply->topic->reply_count = $reply->topic->replies->count();
        $reply->topic->save();

        $topic_user = $reply->topic->user;

        if($topic_user->id != $reply->user->id){
            //通知话题作者
            $reply->topic->user->notify(new TopicReplied($reply));
        }

    }

    public function updating(Reply $reply)
    {
        //
    }
}