@extends('laravel-form-template::layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Template Analytics Dashboard</h1>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('template.analytics.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label for="template_id" class="form-label">Template</label>
                    <select class="form-select" id="template_id" name="template_id">
                        <option value="">All Templates</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ $templateId == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User ID</label>
                    <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $userId }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('template.analytics.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Template Usages</h5>
                    <h2 class="display-4">{{ $totalUsages }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Templates</h5>
                    <h2 class="display-4">{{ $templates->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Avg. Usages Per Template</h5>
                    <h2 class="display-4">
                        {{ $templates->count() > 0 ? round($totalUsages / $templates->count(), 1) : 0 }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Templates by Usage</h5>
                </div>
                <div class="card-body">
                    <canvas id="topTemplatesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Usage Over Time (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="usageOverTimeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Usage Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Template Usages</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Template</th>
                            <th>User ID</th>
                            <th>IP Address</th>
                            <th>Page URL</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usages as $usage)
                            <tr>
                                <td>{{ $usage->id }}</td>
                                <td>
                                    <a href="{{ route('template.analytics.show', $usage->template_id) }}">
                                        {{ $usage->template->name }}
                                    </a>
                                </td>
                                <td>{{ $usage->user_id ?? 'Guest' }}</td>
                                <td>{{ $usage->ip_address }}</td>
                                <td>
                                    <a href="{{ $usage->page_url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $usage->page_url }}
                                    </a>
                                </td>
                                <td>{{ $usage->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('template.analytics.show', $usage->template_id) }}" class="btn btn-sm btn-info">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No usage data found</td>
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
        // Top Templates Chart
        const topTemplatesCtx = document.getElementById('topTemplatesChart').getContext('2d');
        new Chart(topTemplatesCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($topTemplates as $template)
                        '{{ $template->template->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Usage Count',
                    data: [
                        @foreach($topTemplates as $template)
                            {{ $template->total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
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

