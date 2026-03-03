@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of the system')

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 h-100 border-0 shadow-sm" style="background: linear-gradient(145deg, #1e293b, #0f172a);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-uppercase tracking-wider small fw-bold text-muted mb-2">Total Users</div>
                        <div class="display-5 fw-bold text-white">{{ number_format($stats['users']) }}</div>
                    </div>
                    <div class="p-3 rounded-4 bg-primary bg-opacity-10">
                        <i class="bi bi-people fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100 border-0 shadow-sm" style="background: linear-gradient(145deg, #1e293b, #0f172a);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-uppercase tracking-wider small fw-bold text-muted mb-2">Transactions</div>
                        <div class="display-5 fw-bold text-white">{{ number_format($stats['transfers']) }}</div>
                    </div>
                    <div class="p-3 rounded-4 bg-info bg-opacity-10">
                        <i class="bi bi-receipt fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100 border-0 shadow-sm" style="background: linear-gradient(145deg, #1e293b, #0f172a);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-uppercase tracking-wider small fw-bold text-muted mb-2">Success Rate</div>
                        <div class="display-5 fw-bold text-white">{{ number_format($stats['success_transfers']) }}</div>
                    </div>
                    <div class="p-3 rounded-4 bg-success bg-opacity-10">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-8">
            <div class="card p-4 h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-white mb-1">Transaction Volume</h5>
                        <div class="text-muted small">Overview of the last 7 days</div>
                    </div>
                    <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                        <i class="bi bi-graph-up me-1"></i> Live Data
                    </div>
                </div>
                <div style="height: 320px; width: 100%;">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4 h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-white mb-0">Quick Actions</h5>
                </div>
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-primary p-3 text-start d-flex align-items-center justify-content-between hover-lift">
                        <span><i class="bi bi-receipt me-2"></i> View All Payments</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('admin.kyc.index') }}" class="btn btn-outline-light p-3 text-start d-flex align-items-center justify-content-between border-opacity-10 hover-lift">
                        <span><i class="bi bi-person-badge me-2"></i> Pending KYC</span>
                        <span class="badge bg-warning text-dark rounded-pill">Check</span>
                    </a>
                    <div class="p-3 rounded-3 bg-opacity-50" style="background-color: rgba(255,255,255,0.02);">
                        <div class="small text-white-50 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem;">System Status</div>
                        <div class="d-flex align-items-center text-success">
                            <i class="bi bi-circle-fill me-2" style="font-size: 0.5rem;"></i>
                            <span class="fw-medium">Operational</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('transactionChart');
            if (ctx) {
                Chart.defaults.color = 'rgba(226, 232, 240, 0.8)';
                Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(collect($chartData)->pluck('date')) !!},
                        datasets: [{
                            label: 'Transactions',
                            data: {!! json_encode(collect($chartData)->pluck('count')) !!},
                            borderColor: '#a855f7',
                            backgroundColor: 'rgba(168, 85, 247, 0.15)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#a855f7',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#a855f7'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#1e293b',
                                titleColor: '#f8fafc',
                                bodyColor: '#cbd5e1',
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }
        });
    </script>
@endsection
