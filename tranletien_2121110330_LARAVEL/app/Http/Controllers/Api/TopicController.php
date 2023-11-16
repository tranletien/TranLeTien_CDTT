<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class TopicController extends Controller
{
    /*lay danh sach thuong hieu*/
    // public function index()
    // {
    //     $topics = Topic::all();
    //     return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'topics' => $topics], 200);
    // }
    public function index()
    {
        $topics = Topic::where('status', '!=', 0)->get();
        $count = count($topics);
        $count_trash = Topic::where('status', '=', 0)->count();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'topics' => $topics, 'count' => $count, 'count_trash' => $count_trash], 200);
    }


    /*lay thuong hieu bang id -> chi tiet */
    public function show($id)
    {
        $topic = Topic::find($id);
        if ($topic == null) {
            return response()->json(
                ['success' => false, 'message' => 'Tải dữ liệu không thành công', 'topic' => null],
                404
            );
        }
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'topic' => $topic], 200);
    }

    /* them thuong hieu */
    public function store(Request $request)
    {
        $topic = new Topic();
        $topic->name = $request->name; //form
        $topic->slug = Str::of($request->name)->slug('-');
        $topic->parent_id = $request->parent_id;
        $topic->metakey = $request->metakey; //form
        $topic->metadesc = $request->metadesc; //form
        $topic->created_at = date('Y-m-d H:i:s');
        $topic->created_by = 1;
        $topic->status = $request->status; //form
        $topic->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thêm thành công', 'topics' => $topic], 201);
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $topic = Topic::find($id);
        $topic->name = $request->name; //form
        $topic->slug = Str::of($request->name)->slug('-');
        $topic->parent_id = $request->parent_id;
        $topic->metakey = $request->metakey; //form
        $topic->metadesc = $request->metadesc; //form
        $topic->created_at = date('Y-m-d H:i:s');
        $topic->created_by = 1;
        $topic->status = $request->status; //form
        $topic->save(); //Luu vao CSDL
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'topics' => $topic], 200);
    }

    /* xoa */
    public function destroy($id)
    {
        $topic = topic::find($id);
        if ($topic == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'topic' => null],
                404
            );
        }
        $topic->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $topic],
            200
        );
    }
    // trash
    public function trash($id){
        $topic = Topic::find($id);
        if($topic == null){
            return response()->json(['success' => false, 'message' =>'Không tìm thấy thương hiệu !']);
        }
        $count_post = Post::where('topic_id','=',$id)->count();
        if($count_post > 0){
            return response()->json(['success' => false, 'message' =>'Chủ đề đã có bài viết không thể xóa !']);
        }
        $topic->status = 0;
        $topic->updated_at = date('Y-m-d H:i:s');
        $topic->save();
        return response()->json(['success' => true, 'message' =>'Đã đưa vào thùng rác !']);
    }
    
    // phục hồi trash
    public function RescoverTrash($id){
        $topic = Topic::find($id);
        if($topic == null){
            return response()->json(['success' => false, 'message' =>'Không tìm thấy thương hiệu !']);
        }
        $topic->status = 2;
        $topic->updated_at = date('Y-m-d H:i:s');
        $topic->save();
        return response()->json(['success' => true, 'message' =>'Phục hồi thành công !']);
    }

    // get trash
    public function getTrashAll(){
        $trash = Topic::where('status','=',0)->orderBy('updated_by', 'desc')->get();
        $count_trash = Topic::where('status','=',0)->count();
        return response()->json(['success' => true,'message' =>'tai thanh cong','trash'=>$trash,'count_trash'=>$count_trash]);
    }    
}
