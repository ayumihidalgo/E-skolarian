<!-- STUDENT TRACKER PAGE -->

@extends('base')

<style>
    body {
        overflow: hidden;
    }
</style>

@section('content')
    @include('components.studentSideBarComponent')
    <div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
        <div class="flex-grow ">
            <div class="p-6 ">
                @include('student.components.titleSubmittedDocuments')

                <!-- Search and Filters in One Line -->
                <div class="flex justify-between items-center gap-4 mb-6">
                    <!-- Search on the left -->
                    <div class="flex-1">
                        @include('student.components.viewSearch')
                    </div>

                    <!-- Filters on the right -->
                    <div class="flex items-center gap-4">
                        <!-- Document Type Dropdown -->
                        @include('student.components.viewDocumentTypeDropdown')

                        <!-- Status Dropdown -->
                        @include('student.components.viewStatusDropdownComponent')
                    </div>
                </div>

                @include('student.components.viewSubmissionTrackerTable')
            </div>

        </div>
        @include('components.footer')
    </div>
@endsection
