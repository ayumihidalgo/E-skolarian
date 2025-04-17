@extends('base')

@section('content')
    <div class="flex">
        <!-- Super Admin Navigation Bar Layout-->
        <div class="flex-grow w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-start space-x-6">
            <!-- Navigation Bar -->
            <a href = "#"><img src="{{ asset('images/officialLogo.svg') }}" alt="Logo" class="h-15 w-20 ml--2 -mt 5"></a>
            <a href = "#"><p class = "font-[Marcellus_SC] text-xl mt-2 -ml-4">E-SKOLARIAN</p></a>
        </div>
    </div>
@endsection

