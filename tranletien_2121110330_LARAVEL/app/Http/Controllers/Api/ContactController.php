<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use App\Models\Contact;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /*lay danh sach*/
    public function index()
    {

        $contacts = Contact::where('status', '!=', 0)->orderBy('created_at', 'desc')->get();
        $count_contact = count($contacts);
        $count_trash = Contact::where('status','=',0)->count();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'contacts' => $contacts,'count_contact'=>$count_contact,'count_trash'=>$count_trash],200);
    }

    /*lay bang id -> chi tiet */
    public function show($id)
    {
        $contact = Contact::find($id);
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'contacts' => $contact], 200);
    }

    /* them */
    public function store(Request $request)
    {
        $contact = new Contact();
        $contact->name = $request->name; //form
        $contact->email = $request->email; //form
        $contact->phone = $request->phone; //form
        $contact->title = $request->title; //form
        $contact->content = $request->content; //form
        // $category->image = $request->name;
        $contact->user_id = $request->user_id; //form
        $contact->replay_id = $request->replay_id; //form
        $contact->created_by = 1;
        $contact->status = $request->status; //form
        $contact->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'contacts' => $contact], 201);
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);
        $contact->name = $request->name; //form
        $contact->email = $request->email; //form
        $contact->phone = $request->phone; //form
        $contact->title = $request->title; //form
        $contact->content = $request->content; //form
        // $category->image = $request->name;
        $contact->user_id = $request->user_id; //form
        $contact->replay_id = $request->replay_id; //form
        $contact->created_by = 1;
        $contact->status = $request->status; //form
        $contact->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'contact' => $contact], 201);
    }

    /* xoa */
    public function destroy($id)
    {
        $Contact = Contact::find($id);
        if ($Contact == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'Contact' => null],
                404
            );
        }
        $Contact->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $Contact],
            200
        );
    }

    /* lay du lieu len frontend */
    public function contact_list($parent_id = 0, $status = 1)
    {
        $args = [
            ['parent_id', '=', $parent_id],
            ['status', '=', $status]
        ];
        $data = Contact::where($args)->orderBy('sort_order', 'ASC')->get();
        return response()->json($data, 200);
    }
    public function submit(Request $request)
    {
        $contact = new Contact();
        $contact->name = $request->name; //form
        $contact->email = $request->email; //form
        $contact->title = $request->title; //form

        $contact->phone = $request->phonenumber; //form
        $contact->content = $request->content; //form
        $contact->created_at = date('Y-m-d H:i:s');
        $contact->created_by = 1;
        $contact->status = 1; //form
        $contact->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'data' => $contact], 201);
    }
    // public function submitN(Request $request)
    // {
    //     $contact = new Contact();
    //     $contact->email = $request->email; //form
    //     $contact->content = $request->content; //form
    //     $contact->created_at = date('Y-m-d H:i:s');
    //     $contact->created_by = 1;
    //     $contact->status = 1; //form
    //     $contact->save(); //Luuu vao CSDL
    //     return response()->json(['success' => true, 'message' => 'Thành công', 'data' => $contact], 201);
    // }
    public function trash($id){
        $contact = Contact::find($id);
        if($contact == null){
            return response()->json(['success' => false, 'message' =>'Không tìm thấy dữ liệu !']);
        }
        $contact->status = 0;
        $contact->updated_at = date('Y-m-d H:i:s');
        $contact->save();
        return response()->json(['success' => true, 'message' =>'Đã đưa vào thùng rác !']);
    }
    
    // phục hồi trash
    public function RescoverTrash($id){
        $contact = Contact::find($id);
        if($contact == null){
            return response()->json(['success' => false, 'message' =>'Không tìm thấy dữ liệu !']);
        }
        $contact->status = 2;
        $contact->updated_at = date('Y-m-d H:i:s');
        $contact->save();
        return response()->json(['success' => true, 'message' =>'Phục hồi thành công !']);
    }

    // get trash
    public function getTrashAll(){
        $trash = Contact::where('status','=',0)->orderBy('updated_by', 'desc')->get();
        $count_trash = Contact::where('status','=',0)->count();
        return response()->json(['success' => true,'message' =>'tai thanh cong','trash'=>$trash,'count_trash'=>$count_trash]);
    }

}
