@extends('layouts.master')


@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Products</h2>
            </div>
            <div class="pull-right">

                <a class="btn btn-success" href=""  id="add_product" data-toggle="modal"
                data-target="#product-modal"
                data-id=""> Create New Product</a>

            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif


    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Prices</th>
            <th width="280px">Action</th>
        </tr>
	    @foreach ($products as $product)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $product->product_name }}</td>
	        <td>{{ $product->shortname }}</td>
	        <td>
                @role('admin')
                <form action="{{ route('products.destroy',$product->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('products.show',$product->id) }}">Show</a>
                   {{-- @can('product-edit') --}}
                    <a class="btn btn-primary" href="{{ route('products.edit',$product->id) }}">Edit</a>
                    {{-- @endcan --}}


                    @csrf
                    @method('DELETE')
                   {{-- @can ('product-delete') --}}
                    <button type="submit" class="btn btn-danger">Delete</button>
                    {{-- @endcan --}}
                    @endrole
                </form>
	        </td>
	    </tr>
	    @endforeach
    </table>


    {!! $products->links() !!}

    <div class="modal fade" id="product-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
           <div class="modal-content">
              <div class="modal-header modal-header-primary">
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                 <h3><i class="fa fa-edit m-r-5"></i> Add Product</h3>
              </div>
              <div class="modal-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">

                              <label for="firstname">Product</label>
                              <input id="lastname" type="text" class="form-control" name="product_name" placeholder="Product" required="">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="lastname">Price</label>
                              <input id="lastname" type="text" class="form-control" name="shortname" placeholder="Price" required="">
                            </div>
                          </div>
                          <input type="text" name="company_id" value="{{Auth::user()->company_id}}" hidden>
                          <input type="text" name="region_id" value="{{Auth::user()->region_id}}" hidden>







                          {{-- <div class="col-md-12 text-center">
                            <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send message</button>

                          </div> --}}

                        </div>
                        <!-- /.row-->


                    <div class="modal-footer">
                        <button  class="btn btn-success ">Submit</button>
                     </div>
                </form>
              </div>

           </div>
           <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
     </div>
    </div>


     <script>
        $(document).on('click', '#add_product', function () {
        // var id = $(this).attr('data-id');
        // alert();





    });
    </script>
@endsection


