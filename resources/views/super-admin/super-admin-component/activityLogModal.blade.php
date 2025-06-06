<div id="activityLogModal" class="fixed hidden z-50" style="margin-top: 10px;">
    <!-- Modal Content -->
    <div class="bg-white rounded-[16px] shadow-xl w-[500px] relative">
        <div class="p-4">
            <div class="divide-y divide-gray-200 max-h-[370px] overflow-y-auto">
                @forelse($activities as $activity)
                    <div class="py-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-[18px] text-black text[Lexend]">
                                @if($activity->user)
                                    @if(in_array($activity->user_role_name, ['Admin', 'Superadmin']))
                                        {{ $activity->user->role_name }}
                                    @else
                                        {{ $activity->user->username }}
                                    @endif
                                @else
                                    Unknown User
                                @endif
                            </span>
                            <span class="font-bold text-[16px] text-black text[Lexend]">
                                {{ \Carbon\Carbon::parse($activity->created_at)->format('h:i A') }}
                            </span>
                        </div>
                        <p class="text-[14px] text-[#666666] mt-1">{{ $activity->description }}</p>
                    </div>
                @empty
                    <div class="py-4 text-center text-[#666666] text-[14px]">
                        No recent activities
                    </div>
                @endforelse
            </div>
            
            <button id="viewAllActivities" 
                class="font-bold text-black font-[Lexend] text-[14px] hover:underline block text-right mt-3 ml-auto cursor-pointer underline">
                View all
            </button>
        </div>
    </div>
</div>