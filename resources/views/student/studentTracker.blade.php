@extends('base')

@section('content')

    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')

    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        <div class="p-6 bg-[#f2f4f7] min-h-screen">
            @include('student.components.titleSubmittedDocuments')

            @include('student.components.viewSearch')

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Document Type Dropdown -->
                    @include('student.components.viewDocumentTypeDropdown')

                    <!-- Status Dropdown -->
                    @include('student.components.viewStatusDropdownComponent')
                </div>
            </div>

            <!-- No Records Found -->
            @include('student.components.viewNoRecordsFoundComponent')
        </div>
    </div>

@endsection


