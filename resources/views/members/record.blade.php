@extends('layouts.member')

@section('style')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

<style>
    /* Header dan isi tabel rata tengah vertikal dan horizontal */
    #dataTable thead th,
    #dataTable tbody td {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    /* Responsive scrollbar horizontal */
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endsection

@section('content')
<div id="home" class="page active py-4">
    <div class="container">
        <div class="content-wrapper">
            <section class="contact-map-section">
                <div class="contact-map glass p-4 shadow-sm rounded">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>Data User</h2>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle text-center" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">No Produksi </th>
                                    <th style="width: 20%;">No Chasis Kanban</th>
                                    <th style="width: 20%;">No Chasis Scan</th>
                                    <th style="width: 20%;">Time</th>
                                    <th style="width: 20%;">Status</th>
                                    
                                </tr>
                            </thead>
                            
                            <tbody>
                                 @foreach ($records as $record)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-wrap">{{ $record->No_Produksi }}</td>
                                    <td class="text-wrap">{{ $record->No_Chasis_Kanban }}</td>
                                    <td class="text-wrap">{{ $record->No_Chasis_Scan }}</td>
                                    <td class="text-wrap">{{ $record->Time }}</td>
                                    <td class="text-wrap">{{ $record->Status_Record}}</td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        autoWidth: false,
        columnDefs: [
            { width: "5%", targets: 0 },
            { width: "20%", targets: [1,2,3,4] }
        ],
    });
});


</script>
@endsection