@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Add User Credits</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user-credits.index') }}">User Credits</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add User Credits</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">

                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <div class="d-flex align-items-center">
                            <h3 class="card-title mb-0">{{ $user->name }}</h3>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-venus-mars mr-2"></i>{{ ucfirst($user->profile->gender) }}
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-phone mr-2"></i>{{ ucfirst($user->profile->phone) }}
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-coins mr-2"></i>{{ ucfirst($user->credit_balance) }} credits
                            </li>
                        </ul>
                    </div>
                </div>

                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($user))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $user->id }}">
                    @endif
            
                    <div class="mb-3">
                        <label for="credit_balance" class="form-label">Credit Balance<span style="color: red;">*</span></label>
                        <input type="number" class="form-control @error('credit_balance') is-invalid @enderror" id="credit_balance" name="credit_balance" autocomplete="off" value="{{ old('credit_balance') }}" min="1" required>
                        @error('credit_balance')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button id="btn_submit" class="btn btn-success" type="submit">Add Credits</button>
                </form>
            </div>
        </div>
    </div>
@endsection