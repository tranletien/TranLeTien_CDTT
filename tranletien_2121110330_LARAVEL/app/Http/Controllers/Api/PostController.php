<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Topic;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /*lay danh sach*/
    public function index($type)
    {
        $posts = Post::where([["db_post.status", '!=', 0], ["db_post.type", '=', $type]])
            ->join('db_topic', "db_topic.id", '=', "db_post.topic_id")
            ->select("db_post.id", "db_post.title", "db_post.slug", "db_post.status", "db_post.image", "db_post.detail")
            ->orderBy("db_post.created_at", "desc")
            ->get();
        $count_post = count($posts);
        $arg = [
            ['type', '=', 'post'],
            ['status', '=', 1]
        ];
        $count_trash = Post::where($arg)->count();
        return response()->json(['success' => true, 'message' => 'Tải dữ liệu thành công', 'posts' => $posts, 'count_post' => $count_post, 'count_trash' => $count_trash], 200);

        // $posts = Post::all();

        // return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'posts' => $posts], 200);
    }
    public function getPostFE($type)
    {
        $posts = Post::where([["db_post.status", '=',1], ["db_post.type", '=', $type]])
            ->join('db_topic', "db_topic.id", '=', "db_post.topic_id")
            ->select("db_post.id", "db_post.title", "db_post.slug", "db_post.status", "db_post.image", "db_post.detail")
            ->orderBy("db_post.created_at", "desc")
            ->get();
        $count_post = count($posts);
        $arg = [
            ['type', '=', 'post'],
            ['status', '=', 1]
        ];
        $count_trash = Post::where($arg)->count();
        return response()->json(['success' => true, 'message' => 'Tải dữ liệu thành công', 'posts' => $posts, 'count_post' => $count_post, 'count_trash' => $count_trash], 200);

        // $posts = Post::all();

        // return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'posts' => $posts], 200);
    }


    /*lay bang id -> chi tiet */
    public function show($id)
    {
        $post = Post::find($id);
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'post' => $post], 200);
    }

    /* them */
    public function store(Request $request)
    {
        $post = new Post();
        $post->topic_id = $request->topic_id; //form
        $post->title = $request->title; //form
        $post->slug = Str::of($request->title)->slug('-');
        $post->detail = $request->detail; //form
        // $brand->image = $request->name;
        //upload hình ảnh
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg'])) {
                $filename = $post->slug . '.' . $extension;
                $post->image = $filename;
                $files->move(public_path('images/post'), $filename);
            }
        }
        //
        $post->type = $request->pages; //form
        $post->metakey = $request->metakey; //form
        $post->metadesc = $request->metadesc; //form
        $post->created_at = date('Y-m-d H:i:s');
        $post->status = $request->status; //form
        $post->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'data' => $post], 201);
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->topic_id = $request->topic_id; //form
        $post->title = $request->title; //form
        $post->slug = Str::of($request->title)->slug('-');
        $post->detail = $request->detail; //form
        // $brand->image = $request->name;
        //upload hình ảnh
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg'])) {
                $filename = $post->slug . '.' . $extension;
                $post->image = $filename;
                $files->move(public_path('images/post'), $filename);
            }
        }
        //
        $post->type = $request->type; //form
        $post->metakey = $request->metakey; //form
        $post->metadesc = $request->metadesc; //form
        $post->created_at = date('Y-m-d H:i:s');
        $post->status = $request->status; //form
        $post->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'post' => $post], 201);
    }

    /* xoa */
    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'post' => null],
                404
            );
        }
        $post->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $post],
            200
        );
    }

    function post_list($limit)
    {
        $args = [
            ['status', '=', 1]
        ];
        $posts = Post::where($args)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'posts' => $posts
            ],
            200
        );
    }
    //chi tiết
    public function post_detail($slug)
    {
        $post = post::where([['slug', '=', $slug], ['status', '=', 1]])->first();
        if ($post == null) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                    'product' => null
                ],
                404
            );
        }
        $listid = array();
        array_push($listid, $post->topic_id);
        $args_top1 = [
            ['parent_id', '=', $post->topic_id],
            ['status', '=', 1]
        ];
        $list_topic1 = Topic::where($args_top1)->get();
        if (count($list_topic1) > 0) {
            foreach ($list_topic1 as $row1) {
                array_push($listid, $row1->id);
                $args_top2 = [
                    ['parent_id', '=', $row1->id],
                    ['status', '=', 1]
                ];
                $list_topic2 = Topic::where($args_top2)->get();
                if (count($list_topic2) > 0) {
                    foreach ($list_topic2 as $row2) {
                        array_push($listid, $row2->id);
                    }
                }
            }
        }
        $post_other = Post::where([['id', '!=', $post->id], ['status', '=', 1]])
            ->whereIn('topic_id', $listid)
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'post' => $post,
                'post_other' => $post_other
            ],
            200
        );
    }

    public function post_topic($topic_id, $limit)
    {
        $posts = Post::where([['topic_id', '=', $topic_id], ['status', '=', 1]])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'posts' => $posts
            ],
            200
        );
    }

    public function post_all($limit)
    {
        $posts = Post::where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'posts' => $posts
            ],
            200
        );
    }
    //pages
    public function getPageAll()
    {
        $agr = [
            ['status', '!=', 0],
            ['type', '=', 'page']
        ];
        $page = Post::where($agr)->orderBy('created_at', 'desc')->get();
        $count_page = count($page);
        $agr1 = [
            ['status', '=', 0],
            ['type', '=', 'page']
        ];
        $count_trash = Post::where($agr1)->count();
        return response()->json(['success' => true, 'message' => 'tai thanh cong', 'page' => $page, 'count_trash' => $count_trash, 'count_page' => $count_page]);
    }

    // get page by id 
    public function getPageById($id)
    {
        $page = Post::where('id', '=', $id)->first();
        return response()->json(['success' => true, 'message' => 'tai thanh cong', 'page' => $page]);
    }

    // trash
    public function trash($id)
    {
        $post = Post::find($id);
        if ($post == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu !']);
        }
        $post->status = 0;
        $post->updated_at = date('Y-m-d H:i:s');
        $post->save();
        return response()->json(['success' => true, 'message' => 'Đã đưa vào thùng rác !']);
    }

    // phục hồi trash
    public function RescoverTrash($id)
    {
        $post = Post::find($id);
        if ($post == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu !']);
        }
        $post->status = 2;
        $post->updated_at = date('Y-m-d H:i:s');
        $post->save();
        return response()->json(['success' => true, 'message' => 'Phục hồi thành công !']);
    }

    // get trash
    public function getTrashAll($type)
    {
        $agr = [
            ['db_post.type', '=', $type],
            ['db_post.status', '=', 0]
        ];
        $trash = Post::where($agr)
            ->join("db_topic", "db_topic.id", '=', "db_post.topic_id")
            ->select("db_post.*", "db_topic.name as topicname")
            ->orderBy('updated_by', 'desc')->get();
        $count_trash = count($trash);
        return response()->json(['success' => true, 'message' => 'tai thanh cong', 'trash' => $trash, 'count_trash' => $count_trash]);
    }
}
