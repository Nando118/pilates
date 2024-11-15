@extends('home.layouts.main-layout')

@section('title_page', $title_page)

@push('styles')
    <style>
        /* Style for scrollable section */
        .scrollable-content {
            max-height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures minimum height */
            scrollbar-width: none;
        }
        .scrollable-content::-webkit-scrollbar {
            display: none;
        }
        .table-responsive {
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically when empty */
        }
    </style>
@endpush

@section('content')
    <div class="w-100" style="max-width: 400px;">
        <!-- Filter Date and Type -->
        <div class="row my-3">
            <!-- Filter by Date -->
            <div class="col-md-6">
                <label for="datePicker" class="form-label">Filter by Date</label>
                <div class="input-group">
                    <span class="input-group-text" id="date-filter"><i class="fas fa-calendar"></i></span>
                    <input type="text" id="datePicker" class="form-control" placeholder="Select Date" 
                           value="{{ $selectedDate }}" aria-describedby="date-filter">
                </div>
            </div>
            
            <!-- Filter by Type -->
            <div class="col-md-6">
                <label for="typeFilter" class="form-label">Filter by Type</label>
                <select id="typeFilter" class="form-select">
                    <option value="" {{ $selectedType === null ? 'selected' : '' }}>All</option>
                    <option value="add" {{ $selectedType === 'add' ? 'selected' : '' }}>Add</option>
                    <option value="deduct" {{ $selectedType === 'deduct' ? 'selected' : '' }}>Deduct</option>
                    <option value="return" {{ $selectedType === 'return' ? 'selected' : '' }}>Return</option>
                </select>
            </div>
        </div>

        <!-- Scrollable Content Section -->
        <div class="scrollable-content">
            <p class="fs-5"><strong>My Schedules</strong></p>
            <div class="table-responsive">
                @if($myTransactions->isEmpty())
                    <p class="text-muted text-center">You have no transactions yet.</p>
                @else
                    @foreach ($myTransactions as $transaction)
                        <div class="card my-3" style="width: 100%;">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <strong>
                                        <span style="font-size: 1.3rem;">{{ $transaction->transaction_code }}</span>                                         
                                    </strong>
                                    <br> 
                                    <span style="font-size: 1rem;">{{ $transaction->created_at->format('Y-m-d') }}</span>
                                </h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                    <span class="badge rounded-pill
                                        {{ $transaction->type === 'add' ? 'text-bg-success' : '' }}
                                        {{ $transaction->type === 'deduct' ? 'text-bg-danger' : '' }}
                                        {{ $transaction->type === 'return' ? 'text-bg-warning' : '' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </h6>
                                <p class="card-text" style="font-size: 0.85rem;">{{ $transaction->description }}</p>
                            </div>
                        </div>  
                    @endforeach                 
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#datePicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            }).on('changeDate', function() {
                updateFilters();
            });

            $('#typeFilter').on('change', function() {
                updateFilters();
            });

            function updateFilters() {
                const selectedDate = $('#datePicker').val();
                const selectedType = $('#typeFilter').val();
                const url = new URL(window.location.href);
                
                if (selectedDate) {
                    url.searchParams.set('date', selectedDate);
                } else {
                    url.searchParams.delete('date');
                }

                if (selectedType) {
                    url.searchParams.set('type', selectedType);
                } else {
                    url.searchParams.delete('type');
                }

                window.location.href = url;
            }
        });
    </script>
@endpush
