@extends('frontend.layouts.app')

@section('title', 'Histories')

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/student-main.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/histories.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/sidebar.css') }}">
@endpush

@section('content')

    <div class="container-fluid dashboard">
        <div class="row p-lg-5 p-3">
            <x-frontend.sidebar :student="$student"></x-frontend.sidebar>

            <div class="col-12 col-lg-8 main-content ps-lg-5 ps-md-4 ps-3">
                <div class="container-main">
                    <div class="header-section">
                        <h1>{{ $student_dashboard_contents->ask_the_experts_history_title }}</h1>

                        <a href="{{ route('frontend.technical-supports.index') }}">
                            <img src="{{ asset('storage/frontend/question-icon.svg') }}" class="icon-question" alt="Question Icon" width="28" height="28">
                            {{ $student_dashboard_contents->ask_the_experts_history_sub_title }}
                        </a>
                    </div>

                    <div class="questions-list">
                        @if($technical_supports->isNotEmpty())
                            @foreach($technical_supports as $technical_support)
                                <a href="{{ route('frontend.technical-supports.show', $technical_support) }}" style="text-decoration: none; color: inherit;">
                                    <div class="question-card">
                                        <div class="question-header">
                                            <div class="d-flex align-items-center">
                                                <div class="title">{{ $technical_support->subject }}</div>

                                                @if($technical_support->replied)
                                                    <div class="answered-status-badge"> {{ $student_dashboard_contents->ask_the_experts_history_answered }}</div>
                                                @else
                                                    <div class="unanswered-status-badge"> {{ $student_dashboard_contents->ask_the_experts_history_unanswered }}</div>
                                                @endif
                                            </div>

                                            <div class="question-date">{{ Carbon\Carbon::parse($technical_support->created_at)->toDateString() }} {{ Carbon\Carbon::parse($technical_support->created_at)->toTimeString() }}</div>
                                        </div>
                                        <div class="question-content">
                                            Message: {{ $technical_support->message }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
