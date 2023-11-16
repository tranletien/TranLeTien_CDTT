<?php

namespace App\Http\Controllers\Api;

use App\Models\Slider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /*lay danh sach*/
    public function index()
    {
        $sliders = Slider::all();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'sliders' => $sliders], 200);
    }

    /*lay bang id -> chi tiet */
    public function show($id)
    {
        $slider = Slider::find($id);
        if ($slider == null) {
            return response()->json(
                ['success' => false, 'message' => 'Tải dữ liệu không thành công', 'sliders' => null],
                404
            );
        }
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'sliders' => $slider], 200);
    }

    /* them */
    public function store(Request $request)
    {
        $slider = new Slider();
        $slider->name = $request->name; //form
        $slider->link = $request->link; //form
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg'])) {
                $filename = $slider->name . '.' . $extension;
                $slider->image = $filename;
                $files->move(public_path('images/slider'), $filename);
            }
        }

        $slider->sort_order = $request->sort_order; //form
        $slider->position = $request->position; //form
        $slider->created_at = date('Y-m-d H:i:s');
        $slider->created_by = 1;
        $slider->status = $request->status; //form
        $slider->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thêm thành công', 'data' => $slider], 201);
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $slider = Slider::find($id);
        $slider->name = $request->name; //form
        $slider->link = $request->link; //form
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg'])) {
                $filename = $slider->name . '.' . $extension;
                $slider->image = $filename;
                $files->move(public_path('images/slider'), $filename);
            }
        }

        $slider->sort_order = $request->sort_order; //form
        $slider->position = $request->position; //form
        $slider->created_at = date('Y-m-d H:i:s');
        $slider->created_by = 1;
        $slider->status = $request->status; //form
        $slider->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => $slider], 200);
    }

    /* xoa */
    public function destroy($id)
    {
        $slider = slider::find($id);
        if ($slider == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'slider' => null],
                404
            );
        }
        $slider->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $slider],
            200
        );
    }


    /* lay du len trang frontend */
    public function slider_list($position)
    {
        $args = [
            ['position', '=', $position],
            ['status', '=', 1]
        ];
        $sliders = Slider::where($args)
            ->orderBy('sort_order', 'ASC')
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'sliders' => $sliders
            ],
            200
        );
    }
    public function trash($id)
    {
        $slider = Slider::find($id);
        if ($slider == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu !']);
        }
        $slider->status = 0;
        $slider->updated_at = date('Y-m-d H:i:s');
        $slider->save();
        return response()->json(['success' => true, 'message' => 'Đã đưa vào thùng rác !']);
    }

    // phục hồi trash
    public function RescoverTrash($id)
    {
        $slider = Slider::find($id);
        if ($slider == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu !']);
        }
        $slider->status = 2;
        $slider->updated_at = date('Y-m-d H:i:s');
        $slider->save();
        return response()->json(['success' => true, 'message' => 'Phục hồi thành công !']);
    }

    // get trash
    public function getTrashAll()
    {
        $trash = Slider::where('status', '=', 0)->orderBy('updated_by', 'desc')->get();
        $count_trash = Slider::where('status', '=', 0)->count();
        return response()->json(['success' => true, 'message' => 'tai thanh cong', 'trash' => $trash, 'count_trash' => $count_trash]);
    }
}
