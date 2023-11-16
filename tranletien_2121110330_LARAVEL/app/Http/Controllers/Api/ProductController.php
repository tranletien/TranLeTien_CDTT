<?php

namespace App\Http\Controllers\Api;

use App\Models\Orderdetail;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\str;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /*lay danh sach*/
    // public function index()
    // {
    //     // $data=Product::orderBy('created_at','DESC')->paginate(15);
    //     $products = Product::all();
    //     // if($key=request ()->key){     
    //     //     $data=Product::orderBy('created_at','DESC')->where('name','like','%'.$key.'%')->paginate(15);
    //     // }
    //     return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'products' => $products], 200);
    // }
    public function index($limit, $page = 1)
    {
        $count_all = Product::count();
        $count_trash = Product::where('status', '=', 0)->count();

        $end_page = 1;
        if ($count_all > $limit) {
            $end_page = ceil($count_all / $limit);
        }
        $offset = ($page - 1) * $limit;

        $products = Product::where('db_product.status', '!=', 0)
            ->join('db_category', "db_category.id", '=', "db_product.category_id")
            ->join('db_brand', "db_brand.id", '=', "db_product.brand_id")
            ->select("db_product.id", "db_product.name", "db_product.image", "db_product.slug", "db_product.status", "db_product.price", "db_product.price_sale", "db_category.name as categoryname", "db_brand.name as brandname")->orderBy("db_product.created_at", 'DESC')
            ->offset($offset)->limit($limit)->get();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'products' => $products, 'count_all' => $count_all, 'count_trash' => $count_trash, 'end_page' => $end_page], 200);
    }

    // trash
    public function trash($id)
    {
        $product = Product::find($id);
        if ($product == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm !']);
        }
        $count_orderdetail = Orderdetail::where('product_id', '=', $id)->count();
        if ($count_orderdetail > 0) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm đã bán không thể xóa !']);
        }
        $product->status = 0;
        $product->updated_at = date('Y-m-d H:i:s');
        $product->save();
        return response()->json(['success' => true, 'message' => 'Đã đưa sản phẩm vào thùng rác !']);
    }

    // phuc hoi trash
    public function RecoverTrash($id)
    {
        $product = Product::find($id);
        if ($product == null) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm !']);
        }
        $product->status = 2;
        $product->updated_at = date('Y-m-d H:i:s');
        $product->save();
        return response()->json(['success' => true, 'message' => 'Đã đưa phục hồi sản phẩm !']);
    }

    // get trash 
    public function getTrashAll()
    {
        $products = Product::where('db_product.status', '=', 0)
            ->join('db_category', "db_category.id", '=', "db_product.category_id")
            ->join('db_brand', "db_brand.id", '=', "db_product.brand_id")
            ->select("db_product.id", "db_product.name", "db_product.image", "db_product.slug", "db_product.status", "db_product.price", "db_category.name as categoryname", "db_brand.name as brandname")->orderBy("db_product.updated_at", 'DESC')->get();
        // $trash = Product::where('status','=',0)->orderBy('updated_by', 'desc')->get();
        $count_trash = Product::where('status', '=', 0)->count();
        return response()->json(['success' => true, 'message' => 'tai thanh cong', 'trash' => $products, 'count_trash' => $count_trash]);
    }


    /*lay bang id -> chi tiet */
    public function show($id)
    {
        $product = Product::where('db_product.id', '=', $id)
            ->join('db_category', "db_category.id", '=', "db_product.category_id")
            ->join('db_brand', "db_brand.id", '=', "db_product.brand_id")
            ->select("db_product.*", "db_category.name as categoryname", "db_brand.name as brandname")->first();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'product' => $product], 200);
    }
    /* them */
    public function store(Request $request)
    {
        $product = new Product();
        $product->category_id = $request->category_id; //form
        $product->brand_id = $request->brand_id; //form
        $product->name = $request->name; //form
        $product->slug = Str::of($request->name)->slug('-');
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg', 'tmp'])) {
                $filename = $product->slug . '.' . $extension;
                $product->image = $filename;
                $files->move(public_path('images/product'), $filename);
            }
        }
        $product->price = $request->price; //form
        $product->price_sale = $request->price_sale; //form
        $product->qty = $request->qty; //form
        $product->detail = $request->detail; //form
        $product->metakey = $request->metakey; //form
        $product->metadesc = $request->metadesc; //form
        $product->created_at = date('Y-m-d H:i:s');
        $product->created_by = 1;
        $product->status = $request->status; //form
        $product->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'products' => $product], 201);
    }
    public function product_category($limit, $category_id)
    {
        $listid = array();
        array_push($listid, $category_id + 0);
        $args_cat1 = [
            ['parent_id', '=', $category_id + 0],
            ['status', '=', 1]
        ];
        $list_category1 = Category::where($args_cat1)->get();
        if (count($list_category1) > 0) {
            foreach ($list_category1 as $row1) {
                array_push($listid, $row1->id);
                $args_cat2 = [
                    ['parent_id', '=', $row1->id],
                    ['status', '=', 1]
                ];
                $list_category2 = Category::where($args_cat2)->get();
                if (count($list_category2) > 0) {
                    foreach ($list_category2 as $row2) {
                        array_push($listid, $row2->id);
                    }
                }
            }
        }
        $products = Product::where('status', 1)
            ->whereIn('category_id', $listid)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'products' => $products
            ],
            200
        );
    }

    /*update*/
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->category_id = $request->category_id; //form
        $product->brand_id = $request->brand_id; //form
        $product->name = $request->name; //form
        $product->slug = Str::of($request->name)->slug('-');
        $files = $request->image;
        if ($files != null) {
            $extension = $files->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'gif', 'webp', 'jpeg', 'tmp'])) {
                $filename = $product->slug . '.' . $extension;
                $product->image = $filename;
                $files->move(public_path('images/product'), $filename);
            }
        }
        $product->price = $request->price; //form
        $product->price_sale = $request->price_sale; //form
        $product->qty = $request->qty; //form
        $product->detail = $request->detail; //form

        $product->metakey = $request->metakey; //form
        $product->metadesc = $request->metadesc; //form
        $product->created_at = date('Y-m-d H:i:s');
        $product->created_by = 1;
        $product->status = $request->status; //form
        $product->save(); //Luuu vao CSDL
        return response()->json(['success' => true, 'message' => 'Thành công', 'products' => $product], 200);
    }

    /* xoa */
    public function destroy($id)
    {
        $product = product::find($id);
        if ($product == null) {
            return response()->json(
                ['success' => false, 'message' => 'Xóa không thành công', 'product' => null],
                404
            );
        }
        $product->delete();
        return response()->json(
            ['success' => true, 'message' => 'Xóa thành công', 'id' => $product],
            200
        );
    }

    /*Lay du lieu len frontend */
    public function product_home($limit, $category_id = 0)
    {
        $listid = array();
        array_push($listid, $category_id + 0);
        $args_cat1 = [
            ['parent_id', '=', $category_id + 0],
            ['status', '=', 1]
        ];
        $list_category1 = Category::where($args_cat1)->get();
        if (count($list_category1) > 0) {
            foreach ($list_category1 as $row1) {
                array_push($listid, $row1->id);
                $args_cat2 = [
                    ['id', '=', $row1->id],
                    ['status', '=', 1]
                ];
                $list_category2 = Category::where($args_cat2)->get();
                if (count($list_category2) > 0) {
                    foreach ($list_category2 as $row2) {
                        array_push($listid, $row2->id);
                    }
                }
            }
        }
        $products = Product::where('status', '=', 1)
            ->whereIn('category_id', $listid)
            ->orderBy('created_at', 'DESC')->limit($limit)->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'products' => $products
            ],
            200
        );
    }
    public function product_all($limit, $page = 1)
    {
        $offset = ($page - 1) * $limit;
        $products = Product::where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'products' => $products
            ],
            200
        );
    }
    //Lấy ra tất cả sản phẩm theo thương hiệu có phân trang
    public function product_brand($brand_id, $limit)
    {
        $products = Product::where([['brand_id', '=', $brand_id], ['status', '=', 1]])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'products' => $products
            ],
            200
        );
    }
    //
    public function product_detail($slug)
    {
        $product = Product::where([['slug', '=', $slug], ['status', '=', 1]])->first();
        if ($product == null) {
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
        array_push($listid, $product->category_id);
        $args_cat1 = [
            ['parent_id', '=', $product->category_id],
            ['status', '=', 1]
        ];
        $list_category1 = Category::where($args_cat1)->get();
        if (count($list_category1) > 0) {
            foreach ($list_category1 as $row1) {
                array_push($listid, $row1->id);
                $args_cat2 = [
                    ['parent_id', '=', $row1->id],
                    ['status', '=', 1]
                ];
                $list_category2 = Category::where($args_cat2)->get();
                if (count($list_category2) > 0) {
                    foreach ($list_category2 as $row2) {
                        array_push($listid, $row2->id);
                    }
                }
            }
        }

        $product_other = Product::where([['id', '!=', $product->id], ['status', '=', 1]])
            ->whereIn('category_id', $listid)
            ->orderBy('created_at', 'DESC')
            ->limit(8)
            ->get();
        return response()->json(
            [
                'success' => true,
                'message' => 'Tải dữ liệu thành công',
                'product' => $product,
                'product_other' => $product_other,
            ],
            200
        );
    }
    public function search_product($key, $limit, $page = 1)
    {
        $count_products = Product::where([['name', 'like', '%' . $key . '%'], ['status', '=', 1]])->get();
        $end_page = 1;
        if (count($count_products) > $limit) {
            $end_page = ceil(count($count_products) / $limit);
        }
        $offset = ($page - 1) * $limit;
        $products = Product::where([['name', 'like', '%' . $key . '%'], ['status', '=', 1]])->orderBy('created_at', 'DESC')->offset($offset)->limit($limit)->get();
        return response()->json(['success' => true, 'message' => 'Tải dữ liệu thành công', 'products' => $products, 'end_page' => $end_page], 200);
    }
    // public function Compare_product(Request $request,$id){
    //     $products = Product::where('id', $product->id)->get();

    //     // Kiểm tra nếu không tìm thấy sản phẩm
    //     if ($products->isEmpty()) {
    //         // Xử lý khi không tìm thấy sản phẩm
    //         return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
    //     }

    //     return response()->json(['products' => $products], 200);
    // }
    public function ProductNew($sale = 0, $limit)
    {
        $arg = [
            // ['sale','=',$sale],
            ['status', '=', 1]
        ];
        $products = Product::where($arg)->orderBy('created_at', 'DESC')->limit($limit)->get();
        return response()->json(['success' => true, 'message' => "Tải dữ liệu thành công", 'products' => $products], 200);
    }
}
