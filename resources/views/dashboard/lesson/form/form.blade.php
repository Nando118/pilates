@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">Add New Lesson</h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lessons.index') }}">Lessons</a></li>                
                @if (isset($lesson_data))
                    <li class="breadcrumb-item active" aria-current="page">Update Lesson</li>
                @else                
                    <li class="breadcrumb-item active" aria-current="page">Add New Lesson</li>
                @endif
            </ol>
        </nav>

        <div class="card">            
            <div class="card-body">
                <form id="form_create_ticket" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($lesson_data))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $lesson_data->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" autocomplete="off" value="{{ old('name') ?? (isset($lesson_data) ? $lesson_data->name : "") }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="reformer" {{ old('type') == "reformer" ? 'selected' : (isset($lesson_data) && $lesson_data->type == "reformer" ? 'selected' : '') }}>Reformer</option>
                            <option value="private" {{ old('type') == "private" ? 'selected' : (isset($lesson_data) && $lesson_data->type == "private" ? 'selected' : '') }}>Private</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quota" class="form-label">Quota</label>
                        <input type="text" class="form-control @error('quota') is-invalid @enderror" id="quota" name="quota" autocomplete="off" value="{{ old('quota') ?? (isset($lesson_data) ? $lesson_data->quota : "") }}" required>
                        @error('quota')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                
                    <button id="create_lesson" class="btn btn-success" type="submit">
                        @if (isset($lesson_data))
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
