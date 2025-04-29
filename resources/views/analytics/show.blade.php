@extends('laravel-form-template::layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Template Analytics: {{ $template->name }}</h1>
        <a href="{{ route('template.analytics.index') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Date Range Filter</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('template.analytics.show', $template->id) }}" class="row g-3">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Template Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Template Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $template->name }}</p>
                    <p><strong>Created:</strong> {{ $template->created_at->format('Y-m-d H:i:s') }}</p>
                    <p><strong>Model:</strong> {{ $template->model_fqn ?? 'None' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Usages:</strong> {{ $template->usages->count() }}</p>
                    <p><strong>Unique Users:</strong> {{ $uniqueUsers }}</p>
                    <p><strong>Last Used:</strong> {{ $template->usages->count() > 0 ? $template->usages->sortByDesc('created_at')->first()->created_at->format('Y-m-d H:i:s') : 'Never' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Usage Over Time Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Usage Over Time (Last 30 Days)</h5>
        </div>
        <div class="card-body">
            <canvas id="usageOverTimeChart" height="300"></canvas>
        </div>
    </div>
    
    <!-- Template Form Data Preview -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Template Form Data</h5>
        </div>
        <div class="card-body">
            <pre class="bg-light p-3 rounded"><code>{{ json_encode(json_decode($template->form), JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>
    
    <!-- Usage History Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Usage History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Page URL</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usages as $usage)
                            <tr>
                                <td>{{ $usage->id }}</td>
                                <td>{{ $usage->user_id ?? 'Guest' }}</td>
                                <td>{{ $usage->ip_address }}</td>
                                <td class="text-truncate" style="max-width: 200px;">{{ $usage->user_agent }}</td>
                                <td>
                                    <a href="{{ $usage->page_url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $usage->page_url }}
                                    </a>
                                </td>
                                <td>{{ $usage->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No usage data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $usages->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Usage Over Time Chart
        const usageOverTimeCtx = document.getElementById('usageOverTimeChart').getContext('2d');
        new Chart(usageOverTimeCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($usageOverTime as $usage)
                        '{{ $usage->date }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Usage Count',
                    data: [
                        @foreach($usageOverTime as $usage)
                            {{ $usage->total }},
                        @endforeach
                    ],
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

