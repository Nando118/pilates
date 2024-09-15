@extends('dashboard.layouts.main-layout')

@section('title', $title_page)

@section('content_header')
    <h1 class="ml-2">
        @if (isset($lessonType))
            Update Lesson
        @else
            Add New Lesson
        @endif
    </h1>
@endsection

@section('content')
    <div class="container-fluid pb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lessons.index') }}">Lessons</a></li>
                @if (isset($lesson))
                    <li class="breadcrumb-item active" aria-current="page">Update Lesson</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">Add Lesson</li>
                @endif
            </ol>
        </nav>

        <div class="card">
            <div class="card-body">
                <form id="form_input" action="{{ $action }}" method="{{ $method }}">
                    @csrf

                    @if(isset($lesson))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $lesson->id }}">
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Name<span style="color: red;">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" autocomplete="off" value="{{ old('name') ?? (isset($lesson) ? $lesson->name : "") }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button id="btn_submit" class="btn btn-success" type="submit">
                        @if (isset($lesson))
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
