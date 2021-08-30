@extends('layouts.app')
@section('style')
@php
function rupiah($m)
{
	$rupiah = "Rp ".number_format($m,0,",",".").",-";
	return $rupiah;
}
@endphp
<!-- modal lihat bukti bayar -->
@foreach ($data as $row)
<div class="modal fade" id="basicModals{{$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Bukti Bayar</h5><i class="far fa-dollar"></i> 
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<div class="card-body">
				<div id="aniimated-thumbnials" class="list-unstyled row clearfix">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<a data-dismiss="modal" href="{{ URL::to('/') }}/images/{{ $row->bukti }}" data-sub-html="{{ URL::to('/') }}/images/{{ $row->bukti }}">
							<img class="img-responsive thumbnail" src="{{ URL::to('/') }}/images/{{ $row->bukti }}" alt="">
						</a>
						@foreach($data2 as $harga)
                        @if($harga->hari == $row->harga)
                        <label class="col-md-5 col-form-label">Harga</label>
                        <label class="col-md-3 col-form-label">{{ rupiah($harga->harga) }}</label><br>
                        <label class="col-md-5 col-form-label">Masa Pemakaian</label>
                        <label class="col-md-3 col-form-label">{{ $harga->hari }} Hari</label>
                        @endif
                        @endforeach
					</div>
				</div>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<form action="{{ route('user.payment.konfirmasi') }}" method="post">
					@csrf
					<input type="hidden" name="dibayar" value="{{ $row->hari }}">
					<input type="hidden" name="user_id" value="{{ $row->id }}">
					@if(auth()->user()->id_data == 1)
					<button type="submit" class="btn btn-success">Konfirmasi Pembayaran</button>
					@endif
				</form>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endforeach
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="card">
						<div class="body">
							<div id="mail-nav">
								<button type="button" class="btn btn-danger waves-effect btn-compose m-b-15">Notification</button>
								<ul class="" id="mail-folders">
									<li class="active">
										<a href="{{ route('notification') }}" title="Inbox">Notifikasi Baru
										</a>
									</li>
									<li>
										<a href="{{ route('notification.konfirmasi') }}" title="Sent">Terkonfirmasi</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
					<div class="card">
						<div class="boxs mail_listing">
							<div class="inbox-center table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>Nama</th>
											<th>Email</th>
											<th>File</th>
											<th>Notifikasi Masuk</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										@php
										$nomor = 1;
										@endphp
										@foreach ($data as $row)
										@if($row->is_read != 1)
										<tr class="unread">
											<td class="tbl-checkbox">
												{{ $nomor++}}
											</td>
											
											<td class="hidden-xs">{{ $row->name}}</td>
											<td class="max-texts">
												<a href="#">
													<span class="badge badge-primary">Konfirmasi</span>
													{{ $row->email}}</a>
												</td>
												<td class="hidden-xs">
													<a data-toggle="modal" data-target="#basicModals{{$row->id}}" href=""><i class="material-icons">attach_file</i></a>
												</td>
												<td class="text-right"> {{ $row->id}} </td>
												<td align="center">

    											<form id="data-{{ $row->id }}" action="{{route('user.payment.read',$row->id)}}" method="post">
    												{{csrf_field()}}
    												{{method_field('post')}}
    											</form>
    											@csrf
    											@method('DELETE')
    											<button type="submit" onclick="deleteRow( {{ $row->id }} )" class="btn btn-round btn-danger btn-icon btn-sm remove"><i class="fas fa-times"></i> Hapus</button>
    										    </td>
											</tr>
											@endif
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
		@endsection