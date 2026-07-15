@extends('layouts.app')

@section('title', 'Laporan Live Chat')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="fw-bold">Laporan Live Chat</h4>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.report') }}" class="row gx-3 gy-2 align-items-center">
                <div class="col-sm-auto">
                    <label class="visually-hidden" for="date">Tanggal</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $date }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted fw-bold mb-3">Total Sesi Chat Berakhir ({{ \Carbon\Carbon::parse($date)->format('d M Y') }})</h6>
                    <h2 class="display-6 text-primary mb-0">{{ $finishedChatsCount }} <span class="fs-6 text-muted">sesi.</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted fw-bold mb-3">Rata-rata Waktu Respon Semua Room ({{ \Carbon\Carbon::parse($date)->format('d M Y') }})</h6>
                    <h2 class="display-6 text-success mb-0">{{ $globalAvgText }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-3">

            <div class="card shadow-sm">
                <div class="card-header bg-white pt-3 pb-2">
                    <h6 class="fw-bold mb-0">Rata-rata Waktu Respon per Room ({{ \Carbon\Carbon::parse($date)->format('d M Y') }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Room Code</th>
                                    <th>Total Chat</th>
                                    <th>Rata-rata Keseluruhan</th>
                                    <th>Respon Admin (Rata-rata)</th>
                                    <th>Respon Member (Rata-rata)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roomStats as $stat)
                                    <tr>
                                        <td><code>{{ $stat->room_code }}</code></td>
                                        <td>{{ $stat->total_chat }} pesan</td>
                                        <td>
                                            @if($stat->avg_overall > 0)
                                                <span class="fw-bold text-dark">{{ $stat->avg_overall_text }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($stat->avg_admin > 0)
                                                <span class="text-success fw-medium">{{ $stat->avg_admin_text }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($stat->avg_member > 0)
                                                <span class="text-primary fw-medium">{{ $stat->avg_member_text }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada percakapan pada tanggal ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
