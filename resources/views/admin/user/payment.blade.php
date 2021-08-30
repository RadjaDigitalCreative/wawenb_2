@extends('layouts.app')

@section('content')
@foreach ($data as $row)
<div class="modal fade" id="basicModals{{$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Edit Harga</h5><i class="far fa-dollar"></i> 
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{route('user.payment.update',$row->id)}}" method="post" enctype="multipart/form-data">

				<div class="card-body">
					@csrf
					<div class="row">
						<label class="col-md-3 col-form-label">Jumlah Bulan</label>
						<div class="col-md-4">
							<div class="form-group">
								<input name="bulan" value="{{$row->bulan}}" type="text" class="form-control" required="">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-md-3 col-form-label">Harga</label>
						<div class="col-md-4">
							<div class="form-group">
								<input name="harga" value="{{$row->harga}}" type="text" class="form-control" required="">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-md-3 col-form-label">Keterangan</label>
						<div class="col-md-4">
							<div class="form-group">
								<input name="promo" value="{{$row->promo}}" type="text" class="form-control" >
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-md-3 col-form-label">Jumlah Nomor</label>
						<div class="col-md-4">
							<div class="form-group">
								<input name="jumlah_nomor" value="{{$row->jumlah_nomor}}" type="number" class="form-control">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-md-3 col-form-label">Database Nomor</label>
						<div class="col-md-4">
							<div class="form-group">
								<input name="image" value="{{$row->database_nomor}}" type="file" class="form-control" >
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer bg-whitesmoke br">
					<button type="submit" class="btn btn-success">Update</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endforeach
<section class="section">
	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4>Buat Harga</h4>
					</div>
					<div class="card-body">
						<form action="{{route('user.payment.store')}}" method="post" class="needs-validation" enctype="multipart/form-data">
							@csrf
							
							<div class="card-body">
								<div id="app">
									<div class="row" v-for="(order, index) in orders" :key="index">
										<div class="col-md-4">
											<div class="form-group">
												<input  type="number" name="bulan[]" class="form-control" required="" placeholder="Pemakain Bulan">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<input  type="number" placeholder="Harga" name="harga[]" class="form-control" required="" >
											</div>
										</div>

										<br><br>
										<div class="col-md-3">
											<div class="form-group">
												<input  type="text" placeholder="Keterangan(Promo)" name="promo[]" class="form-control"  >
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<input  type="number" name="jumlah_nomor[]" class="form-control" placeholder="Jumlah No.Telp didapat">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<input  type="file" name="image[]" class="form-control"  placeholder="File Download">
											</div>
										</div>
										<hr>
										<div class="buttons">
											<button type="button"  @click="delOrder(index)" class="btn btn-icon btn-danger"><i class="fas fa-trash"></i></button>
											<button type="button" @click="addOrder()" class="btn btn-icon btn-success"><i class="fas fa-plus"></i></button>
										</div>
										
									</div>
								</div>
							</div>
							<div class="card-footer text-right">
								<button type="submit" class="btn btn-primary">Buat Harga</button>
								<button type="reset" class="btn btn-danger">Reset</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="section">
	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4>List Harga Yang Ada</h4>
					</div>
					<div class="card-body">
						<div class="buttons text-right">
						</div>
						<div class="table-responsive">
							<table class="table table-striped" id="tableExport">
								<thead>
									<tr>
										<th class="text-center">
											#
										</th>
										<th>Jumlah Hari</th>
										<th>Harga</th>
										<th>Keterangan</th>
										<th>Database</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@php
									$nomor = 1;
									function rupiah($m)
									{
										$rupiah = "Rp ".number_format($m,0,",",".").",-";
										return $rupiah;
									}
									@endphp
									@foreach ($data as $row)
									<tr>
										<td>{{$nomor++}}</td>
										<td>{{$row->hari}} Hari</td>
										<td>{{rupiah($row->harga)}}</td>
										<td>{{$row->promo}}</td>
										@if($row->jumlah_nomor == NULL)
										<td>
											
											<a  href="#" class="btn btn-round btn-success btn-icon btn-sm ">Tidak ada database 
											</a>
										</td>
										@else
										<td>
											<button class="btn btn-round btn-success btn-icon btn-sm ">{{$row->jumlah_nomor}}</button>
											<a target="_blank" href="{{ URL::to('/') }}/images/{{$row->database_nomor}}" class="btn btn-round btn-success btn-icon btn-sm ">Database 
											</a>
										</td>
										@endif
										
										<td align="center">

											<form id="data-{{ $row->id }}" action="{{route('user.payment.delete',$row->id)}}" method="post">
												{{csrf_field()}}
												{{method_field('delete')}}
											</form>
											@csrf
											@method('DELETE')

											<a data-toggle="modal" data-target="#basicModals{{$row->id}}" href="" class="btn btn-round btn-warning btn-icon btn-sm "><i class="far fa-edit"></i> Edit</a>
											<button type="submit" onclick="deleteRow( {{ $row->id }} )" class="btn btn-round btn-danger btn-icon btn-sm remove"><i class="fas fa-times"></i> Hapus</button>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
<script type="text/javascript">
	new Vue({
		el: '#app',
		data: {
			orders: [
			{pesanan: 0, nama: "", harga: 0, jumlah: 1, subtotal: 0},
			],
			discount: 0,
			note: "",
		},
		methods: {
			addOrder(){
				var orders = {pesanan: 0, nama: "", harga: 0, jumlah: 1, subtotal: 0};
				this.orders.push(orders);
			},
			delOrder(index){
				if (index > 0){
					this.orders.splice(index,1);
				}
			},
		},
	});
</script>
@endsection
@endsection