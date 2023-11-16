<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /*lay danh sach*/
    // public function index()
    // {
    //     $users = User::all();
    //     return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'users' => $users], 200);
    // }
    public function index($roles)
    {
        $arg = [
            ['roles', '=', $roles],
            ['status', '!=', 0]
        ];
        $users = User::where($arg)->orderBy('created_at', 'desc')->get();
        $count_user = count($users);
        $arg1 = [
            ['roles', '=', $roles],
            ['status', '=', 0]
        ];
        $count_trash = User::where($arg1)->count();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'users' => $users, 'count_trash' => $count_trash, 'count_user' => $count_user], 200);
    }

    /*lay thuong hieu bang id -> chi tiet */
    public function show($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json(
                ['success' => false, 'message' => 'Tải dữ liệu không thành công', 'user' => null],
                404
            );
        }

        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'user' => $user], 200);
    }


    /* them */
    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name; //form
        $user->email = $request->email; //form
        $user->phone = $request->phone; //form
        $user->username = $request->username; //form
        $user->password = $request->password; //form
        $user->address = $request->address; //form
        //upload image
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg'])) {
                $filename = $user->name . '.' . $extension;
                $user->image = $filename;
                $files->move(public_path('images/user'), $filename);
            }
        }
        //
        $user->roles = $request->roles;
        $user->created_at = date('Y-m-d H:i:s');
        $user->created_by = 1;
        $user->status = $request->status; //form
        $user->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thêm thành công', 'data' => $user], 201);
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name; //form
        $user->email = $request->email; //form
        $user->phone = $request->phone; //form
        $user->username = $request->username; //form
        $user->password = $request->password; //form
        $user->address = $request->address; //form
        //upload image
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg'])) {
                $filename = $user->name . '.' . $extension;
                $user->image = $filename;
                $files->move(public_path('images/user'), $filename);
            }
        }
        //
        $user->roles = $request->roles;
        $user->created_at = date('Y-m-d H:i:s');
        $user->created_by = 1;
        $user->status = $request->status; //form
        $user->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'user' => $user], 200);
    }
    public function Login(Request $request)
    {
        $arg = [
            ['email', '=', $request->email],
            ['password', '=', $request->password],
            ['status', '=', 1],
        ];
        $user = User::where($arg)->get();
        if (count($user) > 0) {
            return response()->json(['success' => true, 'message' => 'Đăng nhập thành công', 'data' => $user], 200);
        } else {
            return response()->json(['message' => 'Đăng nhập thất bại', 'data' => null]);
        }
    }
    /* xoa */
    public function destroy($id)
    {
        $user = user::find($id);
        if ($user == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'user' => null],
                404
            );
        }
        $user->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $user],
            200
        );
    }
    public function AddUser(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->username = $request->username; //form
        $user->password = $request->password;
        $user->image = $request->image;
        $user->roles = $request->roles;
        $user->created_at = date('Y-m-d H:i:s');
        $user->created_by = 1;
        $user->status = 1; //form
        $user->save();
        return response()->json(['success' => true, 'message' => 'Đăng kí thành công', 'data' => $user], 201);
    }
    // trash
    public function trash($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu !']);
        }
        $user->status = 0;
        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();
        return response()->json(['success' => true, 'message' => 'Đã đưa vào thùng rác !']);
    }

    // phục hồi trash
    public function RescoverTrash($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu !']);
        }
        $user->status = 2;
        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();
        return response()->json(['success' => true, 'message' => 'Phục hồi thành công !']);
    }

    // get trash
    public function getTrashAll($roles)
    {
        $agr = [
            ['roles', '=', $roles],
            ['status', '=', 0]
        ];
        $trash = User::where($agr)->orderBy('updated_by', 'desc')->get();
        $count_trash = count($trash);
        return response()->json(['success' => true, 'message' => 'tai thanh cong', 'trash' => $trash, 'count_trash' => $count_trash]);
    }
}
