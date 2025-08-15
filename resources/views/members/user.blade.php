@extends('layouts.member')

@section('style')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}" />

    <style>
        /* Header dan isi tabel rata tengah vertikal dan horizontal */

        .btn.glass {
            min-width: 90px;
            padding-left: 0.8rem;
            padding-right: 0.8rem;
            padding-top: 3px;
            padding-bottom: 3px;
            text-align: center;
            white-space: nowrap;
            color: #fff !important;
            font-size: 14px !important;
            /* tulisan putih */
        }

        /* Jika mau tombol kecil */
        .btn-sm.glass {
            min-width: 80px;
            padding-left: 0.6rem;
            padding-right: 0.6rem;
        }

        /* Tombol Add User biasanya lebih besar, bisa diberi class khusus */
        .btn-add {
            min-width: 110px;
            padding-left: 1.2rem;
            padding-right: 1.2rem;
        }

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
                            <!-- Tombol sekarang jadi link ke halaman create -->
                            <button type="button" class="btn btn-primary btn-sm glass btn-add"
                                onclick="window.location='{{ route('user.create') }}'">Add User</button>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle text-center" id="dataTable"
                                width="100%" cellspacing="0">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 20%;">Username</th>
                                        <th style="width: 20%;">Name</th>
                                        <th style="width: 20%;">Password</th>
                                        <th style="width: 20%;">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-wrap">{{ $user->Username_User }}</td>
                                            <td class="text-wrap">{{ $user->Name_User }}</td>
                                            <td class="text-wrap">{{ $user->Password_User }}</td>
                                            <td>
                                                <!-- Tombol edit jadi link ke halaman edit -->
                                                <button type="button"
                                                    class="btn btn-outline-success glass btn-sm px-3 py-1 me-2"
                                                    onclick="window.location='{{ route('user.edit', $user->Id_User) }}'">Edit</button>

                                                <form action="{{ route('user.destroy', $user->Id_User) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-outline-danger glass btn-sm px-3 py-1">Delete</button>

                                                </form>
                                            </td>
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
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                autoWidth: false,
                columnDefs: [{
                        width: "5%",
                        targets: 0
                    },
                    {
                        width: "20%",
                        targets: [1, 2, 3, 4]
                    }
                ],
            });
        });
    </script>
@endsection
