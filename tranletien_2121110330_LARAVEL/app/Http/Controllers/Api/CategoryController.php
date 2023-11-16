<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\str;

class CategoryController extends Controller
{
    /*lay danh sach*/
    public function index()
    {
        $categorys = Category::where('status', '!=', 0)->orderBy('created_at', 'desc')->get();
        $count_cat = count($categorys);
        $count_trash = Category::where('status', '=', 0)->count();

        return response()->json(['success' => true, 'message' => "succes", 'categorys' => $categorys, 'count_cat' => $count_cat, 'count_trash' => $count_trash], 200);
    }

    /*lay bang id -> chi tiet */
    public function show($id)
    {
        if (is_numeric($id)) {
            $category = Category::find($id);
        } else {
            $category = Category::where('slug', '=', $id)->first();
        }
        if ($category == null) {
            return response()->json(
                ['success' => false, 'message' => 'Tải dữ liệu không thành công', 'categorys' => null],
                404
            );
        }
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'categorys' => $category], 200);
    }

    /* them */
    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->name; //form
        $category->slug = Str::of($request->name)->slug('-');
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg', 'tmp'])) {
                $filename = $category->slug . '.' . $extension;
                $category->image = $filename;
                $files->move(public_path('images/Category'), $filename);
            }
        }
        $category->parent_id = $request->parent_id; //form
        $category->sort_order = $request->sort_order; //form
        $category->metakey = $request->metakey; //form
        $category->metadesc = $request->metadesc; //form
        $category->created_at = date('Y-m-d H:i:s');
        $category->created_by = 1;
        $category->status = $request->status; //form
        $category->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'categorys' => $category], 201);
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $category->name = $request->name; // form
        $category->slug = Str::of($request->name)->slug('-');
        // $category->image = $request->name;
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg', 'tmp'])) {
                $filename = $category->slug . '.' . $extension;
                $category->image = $filename;
                $files->move(public_path('images/Category'), $filename);
            }
        }
        $category->parent_id = $request->parent_id;
        $category->sort_order = $request->sort_order; //form
        $category->metakey = $request->metakey; //form
        $category->metadesc = $request->metadesc; //form
        $category->updated_at = date('Y-m-d H:i:s');
        $category->updated_by = 1;
        $category->status = $request->status; //form
        $category->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'categorys' => $category], 200);
    }

    /* xoa */
    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'category' => null],
                404
            );
        }
        $category->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $category],
            200
        );
    }


    /* lay du lieu len frontend */
    public function category_list($parent_id = 0)
    {
        $args = [
            ['parent_id', '=', $parent_id],
            ['status', '=', 1]
        ];
        $categorys = Category::where($args)
            ->orderBy('sort_order', 'ASC')
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'categorys' => $categorys
            ],
            200
        );
    }
    public function trash($id)
    {
        $category = Category::find($id);
        if ($category == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy danh mục !']);
        }
        $count_product = Product::where('category_id', '=', $id)->count();
        if ($count_product > 0) {
            return response()->json(['success' => false, 'message' => 'Danh mục đã có sản phẩm không thể xóa !']);
        }
        $category->status = 0;
        $category->updated_at = date('Y-m-d H:i:s');
        $category->save();
        return response()->json(['success' => true, 'message' => 'Đã đưa vào thùng rác !']);
    }

    // phục hồi trash
    public function RescoverTrash($id)
    {
        $category = Category::find($id);
        if ($category == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy danh mục !']);
        }
        $category->status = 2;
        $category->updated_at = date('Y-m-d H:i:s');
        $category->save();
        return response()->json(['success' => true, 'message' => 'Phục hồi thành công !']);
    }

    // get trash
    public function getTrashAll()
    {
        $trash = Category::where('status', '=', 0)->orderBy('updated_by', 'desc')->get();
        $count_trash = Category::where('status', '=', 0)->count();
        return response()->json(['success' => true, 'message' => 'tai thanh cong', 'trash' => $trash, 'count_trash' => $count_trash]);
    }
}
