@extends('backend.layouts.app')

@section('title', 'Ask Questions')

@section('content')

    <x-backend.breadcrumb page_name="Ask Questions"></x-backend.breadcrumb>

    <div class="pages">
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('backend.communications.ask-questions.filter') }}" method="GET" class="filter-form">

                    <div class="row align-items-center">
                        <div class="col">
                            <input type="text" class="form-control" name="name" value="{{ $name ?? '' }}" placeholder="Name">
                        </div>

                        <div class="col">
                            <input type="text" class="form-control" name="subject" value="{{ $subject ?? '' }}" placeholder="Subject">
                        </div>

                        <div class="col">
                            <input type="date" class="form-control" name="date" value="{{ $date ?? '' }}" placeholder="Date">
                        </div>

                        <div class="col-4 col-xl-2 d-flex justify-content-between">
                            <button type="submit" class="filter-search-button" name="action" value="search">SEARCH</button>

                            <button type="submit" class="filter-reset-button" name="action" value="reset">RESET</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <x-backend.pagination-form items="{{ $items }}"></x-backend.pagination-form>
            
                <div class="table-container mb-3">
                    <table class="table table-striped w-100">
                    <thead>
                        <tr>
                            <th scope="col">ID </th>
                            <th scope="col">User</th>
                            <th scope="col">Email</th>
                            <th scope="col">Subject</th>
                            <th scope="col" style="width: 300px; min-width: 300px; max-width: 300px;">Initial Message</th>
                            <th scope="col">Date</th>
                            <th scope="col">Time</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(count($ask_questions) > 0)
                            @foreach($ask_questions as $ask_question)
                                <tr>
                                    @php
                                        $new_replied_questions = App\Models\AskQuestionReply::where('status', '1')
                                            ->where('admin_viewed', '0')
                                            ->where('ask_question_id', $ask_question->id)
                                            ->get();
                                    @endphp

                                    @if($ask_question->admin_viewed == '0' || $new_replied_questions->isNotEmpty())
                                        <td class="position-relative">#{{ $ask_question->id }} <i class="bi bi-circle-fill new-icon"></i></td>
                                    @else
                                        <td>#{{ $ask_question->id }}</td>
                                    @endif
                                    
                                    <td>{{ $ask_question->user }}</td>
                                    <td>{{ $ask_question->email }}</td>
                                    <td>{{ $ask_question->subject }}</td>
                                    <td style="width: 300px; min-width: 300px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $ask_question->initial_message }}</td>
                                    <td>{{ $ask_question->date }}</td>
                                    <td>{{ $ask_question->time }}</td>
                                    <td>{!! $ask_question->action !!}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" style="text-align: center;">No data available in table</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                </div>

                {{ $ask_questions->appends(request()->except('page'))->links("pagination::bootstrap-5") }}
            </div>
        </div>

        <x-backend.delete-data title="Ask Question"></x-backend.delete-data>
    </div>

@endsection


@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('.pages .table .delete-button').on('click', function() {
                let id = $(this).attr('id');
                let url = "{{ route('backend.communications.ask-questions.destroy', [':id']) }}";
                destroy_url = url.replace(':id', id);

                $('.pages .delete-modal form').attr('action', destroy_url);
                $('.pages .delete-modal').modal('show');
            });

            $(".pages .pagination-form select").change(function () {
                window.location = "{!! $ask_questions->url(1) !!}&items=" + this.value; 
            });
        });
    </script>
@endpush