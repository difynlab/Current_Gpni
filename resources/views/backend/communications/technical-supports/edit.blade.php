@extends('backend.layouts.app')

@section('title', 'Reply Technical Support')

@section('content')

    <x-backend.breadcrumb page_name="Reply Technical Support"></x-backend.breadcrumb>

    <div class="static-pages">
        <form action="{{ route('backend.communications.technical-supports.update', $technical_support) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="section">
                <p class="inner-page-title text-center">{{ $technical_support->subject }}</p>

                <div class="row form-input justify-content-center">
                    <div class="col-8">
                        <div class="chat-box">
                            <div class="single-message user-single-message mb-3">
                                @if($user->image)
                                    <img src="{{ asset('storage/backend/persons/users/' . $user->image) }}" class="user-profile-image" alt="Profile Image">
                                @else
                                    <img src="{{ asset('storage/backend/main/' . App\Models\Setting::find(1)->no_profile_image) }}" class="user-profile-image" alt="Profile Image">
                                @endif

                                <div>
                                    <p class="user-message">{!! nl2br(e($technical_support->message)) !!}</p>
                                    <p class="time">{{ $technical_support->time_difference }}</p>
                                </div>
                            </div>

                            @foreach($technical_support_replies as $technical_support_reply)
                                @if($user->id == $technical_support_reply->replied_by)
                                    <div class="single-message user-single-message mb-3">
                                        @if($user->image)
                                            <img src="{{ asset('storage/backend/persons/users/' . $user->image) }}" class="user-profile-image" alt="Profile Image">
                                        @else
                                            <img src="{{ asset('storage/backend/main/' . App\Models\Setting::find(1)->no_profile_image) }}" class="user-profile-image" alt="Profile Image">
                                        @endif

                                        <div>
                                            <p class="user-message">{!! nl2br(e($technical_support_reply->message)) !!}</p>
                                            <p class="time">{{ $technical_support_reply->time_difference }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="single-message admin-single-message mb-3">
                                        <div>
                                            <p class="admin-message">{!! nl2br(e($technical_support_reply->message)) !!}</p>
                                            <p class="time">{{ $technical_support_reply->time_difference }}</p>
                                        </div>

                                        @if(App\Models\User::find($technical_support_reply->replied_by)->image)
                                            <img src="{{ asset('storage/backend/persons/admins/' . App\Models\User::find($technical_support_reply->replied_by)->image) }}" class="admin-profile-image" alt="Profile Image">
                                        @else
                                            <img src="{{ asset('storage/backend/main/' . App\Models\Setting::find(1)->no_profile_image) }}" class="admin-profile-image" alt="Profile Image">
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="row form-input justify-content-center">
                    <div class="col-8">
                        <div>
                            <label for="message" class="form-label">Message<span class="asterisk">*</span></label>
                            <textarea class="form-control" rows="3" id="message" name="message" placeholder="Message" required>{{ old('message') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="form-input">
                    <button type="submit" class="submit-button m-auto">Submit</button>
                </div>
            </div>
        </form>
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
