<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class OrderController extends Controller
{
    /*lay danh sach*/
    public function index()
    {
        $orders = Order::all();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'orders' => $orders], 200);
    }

    /*lay bang id -> chi tiet */
    public function show($id)
    {
        $order = Order::find($id);
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'order' => $order], 200);
    }

    /* them */
    public function store(Request $request)
    {
        $order = new Order();
        $order->user_id = $request->user_id; //form
        $order->name = $request->name; //form
        $order->phone = $request->phone; //form
        $order->email = $request->email; //form
        $order->address = $request->address; //form
        $order->note = $request->note; //form
        $order->created_at = date('Y-m-d H:i:s');
        $order->status = $request->status; //form
        $order->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'order' => $order], 201);
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $order->user_id = $request->user_id; //form
        $order->name = $request->name; //form
        $order->phone = $request->phone; //form
        $order->email = $request->email; //form
        $order->address = $request->address; //form
        $order->note = $request->note; //form
        $order->created_at = date('Y-m-d H:i:s');
        $order->status = $request->status; //form
        $order->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'order' => $order], 200);
    }

    /* xoa */
    public function destroy($id)
    {
        $order = order::find($id);
        if ($order == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'order' => null],
                404
            );
        }
        $order->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $order],
            200
        );
    }

    public function order_list($position, $parent_id = 0, $status = 1)
    {
        $args = [
            ['position', '=', $position],
            ['parent_id', '=', $parent_id],
            ['status', '=', $status]
        ];
        $data = order::where($args)->orderBy('sort_order', 'ASC')->get();
        return response()->json($data, 200);
    }
}
