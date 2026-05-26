@extends('frontend.layouts.app')

@section('title', 'Reply Technical Support')

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/student-main.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/ask-reply-questions.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/sidebar.css') }}">
@endpush

@section('content')

    <div class="container-fluid dashboard">
        <div class="row p-lg-5 p-3">
            <x-frontend.sidebar :student="$student"></x-frontend.sidebar>

            <div class="col-12 col-lg-8 main-content ps-lg-5 ps-md-4 ps-3">
                <div class="container-main">
                    <div class="header-section">
                        <h1>{{ $student_dashboard_contents->ask_the_experts_chat_title }}</h1>
                        <a href="{{ route('frontend.technical-supports.histories') }}">
                            <img src="{{ asset('storage/frontend/history-clock-icon.svg') }}" class="icon-history" alt="History Icon" width="22" height="22">
                            {{ $student_dashboard_contents->ask_the_experts_chat_sub_title }}
                        </a>
                    </div>

                    <div class="row form-input justify-content-center">
                        <div class="col-12">
                            <div class="chat-box">
                                <div class="single-message student-single-message mb-3">
                                    <div>
                                        <p class="student-message">{!! nl2br(e($technical_support->message)) !!}</p>
                                        <p class="time">{{ $technical_support->time_difference }}</p>
                                    </div>

                                    @if($student->image)
                                        <img src="{{ asset('storage/backend/persons/users/' . $student->image) }}" class="student-profile-image" alt="Profile Image">
                                    @else
                                        <img src="{{ asset('storage/backend/main/' . App\Models\Setting::find(1)->no_profile_image) }}" class="student-profile-image" alt="Profile Image">
                                    @endif
                                </div>

                                @if($technical_support_replies->isNotEmpty())
                                    @foreach($technical_support_replies as $technical_support_reply)
                                        @if($student->id == $technical_support_reply->replied_by)
                                            <div class="single-message student-single-message mb-3">
                                                <div>
                                                    <p class="student-message">{!! nl2br(e($technical_support_reply->message)) !!}</p>
                                                    <p class="time">{{ $technical_support_reply->time_difference }}</p>
                                                </div>

                                                @if($student->image)
                                                    <img src="{{ asset('storage/backend/persons/users/' . $student->image) }}" class="student-profile-image" alt="Profile Image">
                                                @else
                                                    <img src="{{ asset('storage/backend/main/' . App\Models\Setting::find(1)->no_profile_image) }}" class="student-profile-image" alt="Profile Image">
                                                @endif
                                            </div>
                                        @else
                                            <div class="single-message admin-single-message mb-3">
                                                @if(App\Models\User::find($technical_support_reply->replied_by)->image)
                                                    <img src="{{ asset('storage/backend/persons/admins/' . App\Models\User::find($technical_support_reply->replied_by)->image) }}" class="admin-profile-image" alt="Profile Image">
                                                @else
                                                    <img src="{{ asset('storage/backend/main/' . App\Models\Setting::find(1)->no_profile_image) }}" class="admin-profile-image" alt="Profile Image">
                                                @endif

                                                <div>
                                                    <p class="admin-message">{!! nl2br(e($technical_support_reply->message)) !!}</p>
                                                    <p class="time">{{ $technical_support_reply->time_difference }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('frontend.technical-supports.update', $technical_support) }}" method="POST">
                        @csrf
                        <div class="message-input">
                            <label for="message" class="form-label">{{ $student_dashboard_contents->ask_the_experts_chat_leave_message }}</label>
                            <textarea class="form-control textarea" id="message" rows="3" name="message" placeholder="{{ $student_dashboard_contents->ask_the_experts_chat_leave_message_placeholder }}" required></textarea>
                            <button type="submit" class="btn-send">{{ $student_dashboard_contents->ask_the_experts_chat_button }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after-scripts')
    <script>
        $(document).ready(function () {
            var chatBox = $(".chat-box");

            if(chatBox[0].scrollHeight > chatBox[0].clientHeight) {
                chatBox.scrollTop(chatBox[0].scrollHeight);
            }
        });
    </script>
@endpush
