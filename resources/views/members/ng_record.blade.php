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
        background: #F7418F;
        color: white !important;
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

    .button-container {
        display: flex;
        justify-content: space-between; /* tombol kiri & kanan */
        align-items: center;
        width: 100%;
    }

    /* Overlay */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        inset: 0;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: flex-start;
        padding-top: 80px;
    }

    /* Konten modal transparan */
    .custom-modal-content {
        background: rgba(255, 255, 255, 0.2); /* transparan */
        backdrop-filter: blur(15px); /* efek blur kaca */
        border-radius: 12px;
        width: 1100px;
        max-width: 90%;
        overflow: hidden;
        animation: slideDown 0.3s ease forwards;
        transform: translateY(-50px);
        opacity: 0;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .content-modal-wrapper {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        height: 70vh; /* supaya tinggi modal body konsisten */
    }

    .text-section {
        flex: 1;
        overflow: hidden; /* biar ga ikut scroll */
    }

    /* Bagian kanan bisa scroll */
    .image-section {
        flex-shrink: 0;
        max-height: 100%;      /* batasi tinggi sesuai modal */
        overflow-y: auto;      /* aktifkan scroll khusus gambar */
    }

    .image-section img {
        max-width: 600px;
        border-radius: 8px;
        display: block;
        margin-bottom: 10px; /* biar ada jarak kalau gambar banyak */
    }

    /* Header */
    .custom-modal-header {
        background: rgba(220, 53, 69, 0.4); /* merah transparan */
        backdrop-filter: blur(10px);
        color: white;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-modal-header h5 {
        margin: 0;
    }

    .custom-modal-close {
        font-size: 20px;
        cursor: pointer;
    }

    /* Body */
    .custom-modal-body {
        max-height: 70vh;   /* batasi tinggi modal body, misalnya 70% layar */
        overflow-y: auto;   /* aktifkan scroll vertical */
        overflow-x: auto;   /* aktifkan scroll horizontal */
        padding: 15px;
        color: white;
    }

    /* Footer */
    .custom-modal-footer {
        padding: 10px 15px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Tombol */
    .btn-secondary {
        background: rgba(108, 117, 125, 0.6);
        color: white;
        border: none;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 4px;
        backdrop-filter: blur(5px);
    }

    .btn-danger {
        background: rgba(220, 53, 69, 0.6);
        color: white;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 4px;
        backdrop-filter: blur(5px);
    }

    .btn-secondary:hover {
        background: rgba(108, 117, 125, 0.8);
    }

    .btn-danger:hover {
        background: rgba(220, 53, 69, 0.8);
    }

    /* Animasi */
    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .badge-ok {
        background-color: #28a745; /* hijau */
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-ng {
        background-color: #dc3545; /* merah */
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-approved {
        background-color: #e7972e; /* oranye */
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .badge-default {
        background-color: #6c757d; /* abu-abu */
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .row {
        display: flex;
        margin-bottom: 5px;
    }
    
    .row .label {
        width: 150px; /* bisa disesuaikan */
        font-weight: bold;
    }

    .row .value {
        flex: 1;
    }

    .diff {
        background-color: #ffb3b3;
        font-weight: bold;
        color: #b30000;
        padding: 1px 3px;
        border-radius: 3px;
    }

    .chassis-compare {
        font-family: monospace;
        font-size: 15px;
        letter-spacing: 1px;
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
                        <h2 class="text-white">Data Record NG (Unvalidated)</h2>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle text-center custom-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-primary text-white bg-primary">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 10%;">Production Date</th>
                                    <th style="width: 10%;">No Instruksi</th>
                                    <th style="width: 5%;">Type</th>
                                    <th style="width: 20%;">No Chasis Cheksheet</th>
                                    <th style="width: 20%;">No Chasis Scan</th>
                                    <th style="width: 20%;">Time Record</th>
                                    <th style="width: 5%;">Status</th>          
                                </tr>
                            </thead>
                            
                            <tbody>
                                 @foreach ($records as $record)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-wrap">{{ $record->Tgl_Produksi ?? '' }}</td>
                                    <td class="text-wrap">{{ $record->No_Produksi }}</td>
                                    <td class="text-wrap">{{ $record->plan->Type_Plan ?? '' }}</td>
                                    <td class="text-wrap">{{ $record->No_Chasis_Kanban }}</td>
                                    <td class="text-wrap">{{ $record->No_Chasis_Scan }}</td>
                                    <td class="text-wrap">{{ $record->Time }}</td>
                                    <td class="text-wrap">
                                        <span class="badge-ng clickable-badge"
                                            data-type="{{ $record->plan->Type_Plan ?? '' }}"
                                            data-kanban="{{ $record->No_Chasis_Kanban }}"
                                            data-scan="{{ $record->No_Chasis_Scan }}"
                                            data-photo="{{ asset('uploads/'.$record->Photo_Ng_Path) }}"
                                            data-id="{{ $record->Id_Record }}"
                                            data-status="{{ $record->Status_Record }}"
                                            data-user="">
                                            {{ $record->Status_Record }}
                                        </span>
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
<div id="ngDetailModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5>NG Record Detail</h5>
            <span class="custom-modal-close" onclick="closeModal('ngDetailModal')">&times;</span>
        </div>
        <div class="custom-modal-body">
            <div class="content-modal-wrapper">
                <!-- Bagian kiri (teks) -->
                <div class="text-section">
                    <div class="row">
                        <div class="label">Type:</div>
                        <div class="value" id="modalType"></div>
                    </div>
                    <div class="row">
                        <div class="label">No Chasis Kanban:</div>
                        <div class="value chassis-compare" id="modalKanban"></div>
                    </div>
                    <div class="row">
                        <div class="label">No Chasis Scan:</div>
                        <div class="value chassis-compare" id="modalScan"></div>
                    </div>
                    <div class="row">
                        <div class="label">Similarity:</div>
                        <div class="value" id="modalSimilarity"></div>
                    </div>
                    <div class="row">
                        <div class="label">Approved By:</div>
                        <div class="value" id="modalUser"></div>
                    </div>
                </div>

                <!-- Bagian kanan (gambar) -->
                <div class="image-section">
                    <img id="modalPhoto" src="" alt="NG Photo"/>
                </div>
            </div>
        </div>
        <div class="custom-modal-footer">
            <button class="btn-secondary" onclick="closeModal('ngDetailModal')">Close</button>
            <button id="approveBtn" class="btn-danger">Approve</button>
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
    var table;

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        table = $('#dataTable').DataTable();
        table.page.len(100).draw();
    } else {
        table = $('#dataTable').DataTable({
            pageLength: 100,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ]
        });
    }
});

function openModal(id) {
    document.getElementById(id).style.display = "flex";
}

function closeModal(id) {
    document.getElementById(id).style.display = "none";
}

// Tutup modal jika klik di luar box
window.onclick = function(event) {
    const modals = document.querySelectorAll('.custom-modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}

function highlightDiff(checksheet, scan) {
    if (typeof checksheet !== "string") checksheet = checksheet ? String(checksheet) : "";
    if (typeof scan !== "string") scan = scan ? String(scan) : "";

    const m = checksheet.length;
    const n = scan.length;

    let dp = Array.from({length: m+1}, () => Array(n+1).fill(0));
    let path = Array.from({length: m+1}, () => Array(n+1).fill(''));

    for (let i=0; i<=m; i++) dp[i][0] = i, path[i][0] = 'U';
    for (let j=0; j<=n; j++) dp[0][j] = j, path[0][j] = 'L';
    path[0][0] = '';

    for (let i=1; i<=m; i++) {
        for (let j=1; j<=n; j++) {
            if (checksheet[i-1] === scan[j-1]) {
                dp[i][j] = dp[i-1][j-1];
                path[i][j] = 'D';
            } else {
                let del = dp[i-1][j] + 1;
                let ins = dp[i][j-1] + 1;
                let sub = dp[i-1][j-1] + 1;

                if (del <= ins && del <= sub) {
                    dp[i][j] = del;
                    path[i][j] = 'U';
                } else if (ins <= sub) {
                    dp[i][j] = ins;
                    path[i][j] = 'L';
                } else {
                    dp[i][j] = sub;
                    path[i][j] = 'D';
                }
            }
        }
    }

    let i = m, j = n;
    let checkArr = [], scanArr = [];
    while (i > 0 || j > 0) {
        if (i>0 && j>0 && path[i][j] === 'D') {
            if (checksheet[i-1] === scan[j-1]) {
                checkArr.unshift(checksheet[i-1]);
                scanArr.unshift(scan[j-1]);
            } else {
                checkArr.unshift(`<span class="diff">${checksheet[i-1]}</span>`);
                scanArr.unshift(`<span class="diff">${scan[j-1]}</span>`);
            }
            i--; j--;
        } else if (i>0 && path[i][j] === 'U') {
            checkArr.unshift(`<span class="diff">${checksheet[i-1]}</span>`);
            scanArr.unshift(`<span class="diff">-</span>`);
            i--;
        } else if (j>0 && path[i][j] === 'L') {
            scanArr.unshift(`<span class="diff">${scan[j-1]}</span>`);
            j--;
        } else {
            break;
        }
    }

    let distance = dp[m][n];
    let maxLen = Math.max(m, n);
    let similarity = maxLen > 0 ? ((1 - distance / maxLen) * 100).toFixed(2) : 0;

    $("#modalKanban").html(checkArr.join(''));
    $("#modalScan").html(scanArr.join(''));
    $("#modalSimilarity").text(similarity + "%");
}

$(document).ready(function() {
    $(document).on("click", ".clickable-badge", function() {
        let type = $(this).data("type") || "";
        let kanban = $(this).data("kanban") || "";
        let scan   = $(this).data("scan") || "";
        let user   = $(this).data("user") || "";
        let photo  = $(this).data("photo");
        let id     = $(this).data("id");
        let status = $(this).data("status");

        highlightDiff(kanban, scan);

        $("#modalType").text(type);
        $("#modalUser").text(user);

        if (photo) {
            $("#modalPhoto").attr("src", photo).show();
        } else {
            $("#modalPhoto").hide();
        }

        if (status === "NG") {
            $("#approveBtn").show().data("id", id);
        } else {
            $("#approveBtn").hide();
        }

        openModal("ngDetailModal");
    });

    $("#approveBtn").on("click", function() {
        let id = $(this).data("id");

        $.ajax({
            url: "{{ route('record.approve', ['Id_Record' => ':id']) }}".replace(':id', id),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                location.reload();
            },
            error: function(xhr) {
                alert("Approvement failed");
            }
        });
    });
});
</script>
@endsection
