@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            {{-- <h1 class="m-0">Data Nasabah</h1> --}}
        </div>
    </div>
@stop

@section('content')
@include('layouts.alert')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="float-left"><b>Data Kunjungan Nasabah : {{ $customer->name }}</b> </h4>
                    @canany(['isCreditCollection', 'isSuperRole'])
                    <a href="{{ route(Auth::user()->Role->code.'.customer.create-visit', $customer->id) }}">
                        <button type="button" class="btn btn-warning float-right">
                            <i class="fa fa-plus"></i> Tambah Kunjungan
                        </button>
                    </a>                    
                    @endcanany
                </div>
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12 col-md-6"></div>
                            <div class="col-sm-12 col-md-6"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="customer-table" class="table table-bordered table-hover dataTable dtr-inline" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>TANGGAL</th>
                                            <th>HASIL KUNJUNGAN</th>
                                            <th>REKOMENDASI</th>
                                            <th>DEADLINE</th>
                                            <th>STATUS</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($visits as $visit)
                                            @php
                                                $warn = '';
                                                $button = '';
                                                if($visit->status == 'new' && (Gate::check('isHeadOfficeAdmin') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-warning';
                                                    $button = 'Edit';
                                                }
                                                if($visit->status == 'action_approve' && (Gate::check('isHeadOfficeAdmin') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-warning';
                                                    $button = 'Edit';
                                                }
                                                if($visit->status == 'recommendation_validation' && (Gate::check('isSupervisor') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-warning';
                                                    $button = 'Edit';
                                                }
                                                if($visit->status == 'recommendation_approve' && (Gate::check('isCreditCollection') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-warning';
                                                    $button = 'Edit';
                                                }                                                
                                                if($visit->status == 'input_deadline' && (Gate::check('isCreditCollection') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-info';
                                                    $button = 'Edit';
                                                }
                                                if($visit->status == 'action_validation' && (Gate::check('isCreditManager') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-warning';
                                                    $button = 'Edit';
                                                }
                                                if($visit->status == 'action_realized' && (Gate::check('isCreditManager') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-success';
                                                    $button = 'Lihat';
                                                }
                                                if($visit->status == 'action_realized' && (Gate::check('isHeadOfficeAdmin') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-success';
                                                    $button = 'Lihat';
                                                }
                                                if($visit->status == 'recommendation_revision' && (Gate::check('isHeadOfficeAdmin') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-danger';
                                                    $button = 'Edit';
                                                }
                                                if($visit->status == 'action_revision' && (Gate::check('isCreditCollection') || Gate::check('isSuperRole'))) {
                                                    $warn = 'table-danger';
                                                    $button = 'Edit';
                                                }
                                            @endphp
                                            <tr class="{{ $warn }}">
                                                <td></td>
                                                <td>{{ $visit->visit_at }}</td>
                                                <td>{{ $visit->result ?: '-' }}</td>
                                                <td>{{ $visit->recommendation ? $visit->recommendation->recommendation : '-' }}</td>
                                                <td>{{ isset($visit->action_plan->deadline) ? $visit->action_plan->deadline : '-' }}</td>
                                                <td>
                                                    @if ($visit->status == 'new')
                                                        Butuh Rekomendasi        
                                                    @elseif($visit->status == 'recommendation_validation')
                                                        Review Rekomendasi
                                                    @elseif($visit->status == 'recommendation_revision')
                                                        Revisi Rekomendasi
                                                    @elseif($visit->status == 'recommendation_approve')
                                                        Rekomendasi Disetujui
                                                    @elseif($visit->status == 'action_validation')
                                                        Review Action Plan
                                                    @elseif($visit->status == 'action_revision')
                                                        Revisi Action Plan
                                                    @elseif($visit->status == 'action_approve')
                                                        Action Plan Disetujui
                                                    @elseif($visit->status == 'input_deadline')
                                                        Deadline Sudah Ada
                                                    @elseif($visit->status == 'action_realized')
                                                        Action Plan Sudah Dilakukan
                                                    @else
                                                        -                                                        
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route(Auth::user()->Role->code.'.customer.edit-visit', $visit->id) }}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Tambah Kunjungan">
                                                        {{ $button ? $button : 'Lihat' }}
                                                    </a>
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
    </div>
@stop

@section('plugins.Datatables', true)

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        $(document).ready(function() {
            var t = $('#customer-table').DataTable({
                "pageLength": 25,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": []
            });
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        });
    </script>
@stop