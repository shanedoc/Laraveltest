<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request)
	{
		$topics = Topic::withOrder($request->order)
            ->with('user','category') //预防N+1问题
            ->paginate(30);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic,Request $request)
    {
        // URL 矫正
        if ( !empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }
        $replies = Reply::where('topic_id',$topic->id)->get();
        $user = Auth::user();
        return view('topics.show', compact('topic','replies','user'));
    }

	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();
        return redirect()->to($topic->link())->with('success', '成功创建话题！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

        return redirect()->to($topic->link())->with('success', '成功创建话题！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功');
	}

	/*
	 * 上传文件
	 */
	public function uploadImage(Request $request,ImageUploadHandler $uploader)
    {
        $data = [
            'success' => false,
            'msg' => '上传失败',
            'file_path' => '',
        ];
        if($file = $request->upload_file){
            $result = $uploader->save($file,'topics',Auth::id(),1024);
            if($result){
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }
}