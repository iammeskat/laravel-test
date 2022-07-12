<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    { 
        $variants = Variant::all();
        for ($i=0; $i<count($variants); $i++){
            $variants[$i]['productVariants']= ProductVariant::where('variant_id', $variants[$i]['id'])
                ->select('variant')
                ->groupBy('variant')
                ->get()
                ->pluck('variant');
        }
        
        $products = Product::with(['variantPrices' => function($query) use ($request){
                $query->select('*');
                if($request->query('price_from')){
                    $query->where('price', '>=', $request->query('price_from') );
                }
                if($request->query('price_to')){
                    $query->where('price', '<=', $request->query('price_to') );
                }
            }]);
        if($request->query('title')){
            $products->where('title', 'like', '%' . $request->query('title') . '%');
        }
        if($request->query('date')){
            $products->whereDate('created_at', $request->query('date'));
        }
        if($request->query('variant')){
            $products->join('product_variants','product_variants.product_id', 'products.id')
                ->where('product_variants.variant', $request->query('variant'))
                ->select('products.*');
        }
        
        return view('products.index', ['products'=>$products->paginate(5), 'variants'=>$variants, 'data'=>$request]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // return $request->input();
        try {
            
            $maxId = Product::max('id');
            $nextId = $maxId?$maxId+1:1;
            $product = Product::create([
                "id" => $nextId,
                "title"=>$request->title,
                "sku"=>$request->sku,
                "description"=>$request->description,
            ]);
            // if($request->has('product_image')){
            //     foreach($request->product_image as $img){
            //         ProductImage::create([]);
            //     }
            // }
            $productVariants = [];
            foreach($request->product_variant as $pv){
                
                foreach($pv['tags'] as $tag){
                    $productVariant = ProductVariant::create([
                        'variant' => $tag,
                        'variant_id' => $pv['option'],
                        'product_id' => $nextId //$product->id
                    ]);
                    
                    array_push($productVariants, $productVariant->toArray());
                }
                
            }
            
            foreach($request->product_variant_prices as $pvp){
                $tags = array_filter(explode('/', $pvp['title']));
                
                
                $vIds = [];
                foreach($tags as $tag){
                    for($i=0; $i<3;$i++){
                        if($tag==$productVariants[$i]['variant']){
                            array_push($vIds, $productVariants[$i]['id']);
                        }
                    }
                }
                $productVariantPrices = ProductVariantPrice::create([
                    "product_variant_one"=>count($vIds)?$vIds[0]:null,
                    "product_variant_two"=>count($vIds)>1?$vIds[1]:null,
                    "product_variant_three"=>count($vIds)>2?$vIds[2]:null,
                    "price"=>$pvp['price'],
                    "stock"=>$pvp['stock'],
                    "product_id"=>$nextId,
                ]);
                
            }
            return response()->json([
                'msg'=>'Product was added successfull!'
            ], 201);
            
        } catch (\Exception $th) {
            return response()->json([
                'msg'=>'Something went wrong'
            ], 500);
        }

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        // dd($product);
        $variants = Variant::all();
        return view('products.edit', compact('variants', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'sku' => 'required',
            'description' => 'required',
        ]);
        try {
            $product=Product::find($id);
            $product->update([
                "title"=>$request->title,
                "sku"=>$request->sku,
                "description"=>$request->description
            ]);
            return redirect()->to('/product');
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
