<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class CategoryProduct extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }
    public function add_category_product(){
        $this->AuthLogin();
        return view('admin.category_product.add_category_product');
    }

    public function all_category_product(){
        $this->AuthLogin();
        $all_category_product = DB::table('tbl_category_product')
            ->get();
        $manager_category_product = view('admin.category_product.all_category_product')
            ->with('all_category_product',$all_category_product);
        return view('admin_layout')
            ->with('admin.category_product.all_category_product',$manager_category_product);
        
    }
    public function save_category_product(Request $request){
        $this->AuthLogin();
        $data = array();
        $data['category_name'] = $request->category_product_name;
        $data['slug_category_name'] = $request->slug_category_name;
        $data['meta_keywords'] = $request->category_product_keywords;
        $data['category_desc'] = $request->category_product_desc;
        $data['category_status'] = $request->category_product_status;
        DB::table('tbl_category_product')
            ->insert($data);
        Session::put('message','Thêm danh mục sản phẩm thành công');
        return Redirect::to('add-category-product');
    }


    public function unactive_category_product($category_product_id){
        $this->AuthLogin();
        DB::table('tbl_category_product')
            ->where('category_id',$category_product_id)
            ->update(['category_status' => 1]);
        Session::put('message','Kích hoạt danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');
    }

    public function active_category_product($category_product_id){
        $this->AuthLogin();
        DB::table('tbl_category_product')
            ->where('category_id',$category_product_id)
            ->update(['category_status' => 0]);
        Session::put('message','Không kích hoạt danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');
    }

    public function edit_category_product($category_product_id){
        $this->AuthLogin();
        $edit_category_product = DB::table('tbl_category_product')
            ->where('category_id',$category_product_id)
            ->get();
        $manager_category_product = view('admin.category_product.edit_category_product')
            ->with('edit_category_product',$edit_category_product);
        return view('admin_layout')
                ->with('admin.category_product.edit_category_product',$manager_category_product);
        
    }

    public function update_category_product(Request $request,$category_product_id){
        $this->AuthLogin();
        $data = array();
        $data['category_name'] = $request->category_product_name;
        $data['category_desc'] = $request->category_product_desc;
        $data['meta_keywords'] = $request->category_product_keywords;
        $data['slug_category_name'] = $request->slug_category_name;
        DB::table('tbl_category_product')
            ->where('category_id',$category_product_id)
            ->update($data);
        Session::put('message','Cập nhật danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');
    }

    public function delete_category_product($category_product_id){
        $this->AuthLogin();
        DB::table('tbl_category_product')
            ->where('category_id',$category_product_id)
            ->delete();
        Session::put('message','Xóa danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');

    }

//end function admin page


    public function show_category_home(Request $request, $slug_category_name){

        $category_id = DB::table('tbl_category_product')
            ->where('slug_category_name', $slug_category_name)
            ->pluck('category_id');

        $category_product = DB::table('tbl_category_product')
            ->where('category_status','1')
            ->orderBy('category_id','desc')
            ->get();
        $brand_product = DB::table('tbl_brand_product')
            ->where('brand_status','1')
            ->orderBy('brand_id','desc')
            ->get();
        $category_by_id = DB::table('tbl_product')
            ->join('tbl_category_product', 'tbl_product.category_id', '=', 'tbl_category_product.category_id')
            ->where('tbl_product.category_id', $category_id) 
            ->get();
            
       
          

        foreach( $category_by_id as $key => $value){
            $meta_desc = $value -> category_desc;
            $meta_keywords = $value -> meta_keywords;
            $meta_title = $value -> category_name;
            $url_canonical = $request->url();
        }


        $category_name = DB::table('tbl_category_product')
            ->where('tbl_category_product.slug_category_name',$slug_category_name)
            ->limit(1)
            ->get();
        return view('pages.category.show_category')
            ->with('category',$category_product)
            ->with('brand',$brand_product)
            ->with('category_by_id',$category_by_id)
            ->with('category_name',$category_name)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);

    }

    
}
