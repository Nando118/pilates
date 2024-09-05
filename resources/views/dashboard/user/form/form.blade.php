@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    @if (isset($user_data))        
        <h1 class="ml-2">Update User Profile</h1>
    @else
        <h1 class="ml-2">Add New User</h1>        
    @endif
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                @if (isset($user_data))
                    <li class="breadcrumb-item active" aria-current="page">Update User Profile</li>
                @else                
                    <li class="breadcrumb-item active" aria-current="page">Add New User</li>
                @endif
            </ol>
        </nav>

        <div class="card">            
            <div class="card-body">
                <form id="form_create_ticket" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($user_data))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $user_data->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="" disabled selected>Select role</option>
                            <option value="2" {{ old('role') == "2" ? 'selected' : (isset($user_data) && $user_role == "2" ? 'selected' : '') }}>Coach</option>
                            <option value="3" {{ old('role') == "3" ? 'selected' : (isset($user_data) && $user_role == "3" ? 'selected' : '') }}>Client</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="branch" class="form-label">Branch</label>
                        <select class="form-control" id="branch" name="branch" required>
                            <option value="" disabled selected>Select branch</option>
                            <option value="jakarta" {{ old('branch') == "jakarta" ? 'selected' : (isset($user_data) && $user_data->profile->branch == "jakarta" ? 'selected' : '') }}>Jakarta</option>
                            <option value="tangerang" {{ old('branch') == "tangerang" ? 'selected' : (isset($user_data) && $user_data->profile->branch == "tangerang" ? 'selected' : '') }}>Tangerang</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" autocomplete="off" value="{{ old('name') ?? (isset($user_data) ? $user_data->name : "") }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" autocomplete="off" value="{{ old('username') ?? (isset($user_data) ? $user_data->profile->username : "") }}" @isset($user_data) readonly @endisset required>
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="" disabled selected>Select your gender</option>
                            <option value="male" {{ old('gender') == "male" ? 'selected' : (isset($user_data) && $user_data->profile->gender == "male" ? 'selected' : '') }}>Male</option>
                            <option value="female" {{ old('gender') == "female" ? 'selected' : (isset($user_data) && $user_data->profile->gender == "female" ? 'selected' : '') }}>Female</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" autocomplete="off" value="{{ old('phone') ?? (isset($user_data) ? $user_data->profile->phone : "") }}" required>
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" rows="3" name="address" autocomplete="off" required>{{ old('address') ?? (isset($user_data) ? $user_data->profile->address : "") }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" autocomplete="off" value="{{ old('email') ?? (isset($user_data) ? $user_data->email : "") }}" @isset($user_data) readonly @endisset required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    @if (isset($user_data))
                        <div class=""></div>
                    @else                    
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="off" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="off" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif                    
                    <button id="create_user" class="btn btn-success" type="submit">
                        @if (isset($user_data))
                            Update
                        @else
                            Create
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
