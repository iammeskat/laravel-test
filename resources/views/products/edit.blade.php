@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <!-- <div id="app">
        <create-product :variants="{{ $variants }}" :product_name="ffnf" >Loading</create-product>
    </div> -->
    <div id="app2">
        <section>
            <form method="POST" action="{{route('product.update', $product->id)}}">
                @csrf
        <div class="row">
            <div class="col-md-6">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Product Name</label>
                            <input
                                type="text"
                                v-model="product_name"
                                placeholder="Product Name"
                                class="form-control"
                                name="title"
                                value="{{$product->title}}"
                            />
                        </div>
                        <div class="form-group">
                            <label for="">Product SKU</label>
                            <input
                                type="text"
                                v-model="product_sku"
                                placeholder="Product Name"
                                class="form-control"
                                name="sku"
                                value="{{$product->sku}}"
                            />
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea
                                v-model="description"
                                id=""
                                cols="30"
                                rows="4"
                                class="form-control"
                                name="description"
                            >{{$product->description}}"</textarea>
                        </div>
                    </div>
                </div>

                
            </div>

            <div class="col-md-6">
                
            </div>
        </div>

        <button
            @click="saveProduct"
            type="submit"
            class="btn btn-lg btn-primary"
        >
            Update
        </button>
        <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
        </form>
    </section>
    </div>
@endsection
