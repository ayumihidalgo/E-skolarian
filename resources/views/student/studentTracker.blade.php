<!-- STUDENT TRACKER PAGE -->

@extends('base')

<style>
    body {
        overflow: hidden;
    }
</style>

@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')

    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        <div class="p-6 bg-[#f2f4f7] min-h-screen">
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
    </div>
@endsection
