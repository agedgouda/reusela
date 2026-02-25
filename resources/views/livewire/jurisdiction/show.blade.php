<div class="bg-white">
    <div class="px-6 lg:px-12 space-y-10">

        {{-- 1. Navigation --}}
        @if($editable)
            <button wire:click="goBack"
                class="group bg-[#9adbe8] hover:!bg-[#89c6d3] cursor-pointer rounded-full px-[18px] h-[38px] flex items-center gap-[10px] text-[16px] font-bold tracking-[-0.02em] text-[#15121b] leading-[0]">
                <div class="flex items-center justify-center shrink-0">
                    <x-arrow-left />
                </div>
                <span class="inline-flex items-center">Back</span>
            </button>
        @else
            <button @click="resetUI"
                class="group bg-[#9adbe8] hover:!bg-[#89c6d3] cursor-pointer rounded-full px-[18px] h-[38px] flex items-center gap-[10px] text-[16px] font-bold tracking-[-0.02em] text-[#15121b] leading-[0]">
                <div class="flex items-center justify-center shrink-0">
                    <x-arrow-left />
                </div>
                <span class="inline-flex items-center mt-1">Change Address</span>
            </button>
        @endif
        {{-- 2. Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-[366px] md:w-full sm:justify-between">

            <div class="text-[#15121b] text-left font-sans text-[40px]/[36px] tracking-[-0.05em] font-bold">
                @if($jurisdiction->is_system_default)
                    Default Information
                @else
                    You're in
                    {{ $jurisdiction->name !== 'Unincorporated'
                        ? "the City of $jurisdiction->name!"
                        : "$jurisdiction->name LA County!" }}
                @endif
            </div>
            {{-- Add Section button --}}
            @if($editable && !$showAddSectionForm && $showAddSectionButton)
                <div class="sm:ml-auto">
                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full !bg-[#9adbe8] !text-[#15121b] hover:!bg-[#89c6d3] border-none !rounded-[12px]"
                        wire:click="$toggle('showAddSectionForm')"
                        >
                        Add Section
                    </flux:button>
                </div>
            @endif
        </div>

        {{-- 3. Add Section Form --}}
        @if($showAddSectionForm)
            <div class="bg-gray-50 rounded-[12px] p-6 border">
                <livewire:section.edit-section
                    :model="new \App\Models\Section"
                    :parent-model="$jurisdiction"
                    foreign-key="jurisdiction_id"
                    wire:key="add-section-form"
                />
            </div>
        @endif


        {{-- 4. Jurisdiction General Information Card --}}
        <x-jurisdiction-card :editable="$editable" >

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">

                    <div class="text-[#15121b] text-left sm:p-7 font-sans text-[24px] md:text-[32px]/[36px] tracking-[-0.05em] font-bold">
                        Jurisdiction Information

                        @if(empty($jurisdiction->general_information) && !$jurisdiction->is_system_default)
                            <span class="text-[12px] text-gray-400 font-normal italic ml-2">
                                (Default Template)
                            </span>
                        @endif
                    </div>

                    @if(!$showGeneralInfoEdit  && $editable)
                        <flux:button
                            wire:click="toggleEdit('general')"
                            color="blue"
                            variant="primary"
                            size="xs"
                            class="!rounded-[12px] !bg-[#9adbe8] !text-[#15121b] hover:!bg-[#89c6d3] "
                        >
                            Edit
                        </flux:button>
                    @endif
                </div>


            <div class="mt-4">
                @if(!$showGeneralInfoEdit)
                    <div class="prose max-w-none prose-gray text-[#1E1E1E]">
                        {!! $jurisdiction->display_general_info !!}
                    </div>
                @else
                    <livewire:jurisdiction.edit
                        :jurisdiction="$jurisdiction"
                        wire:key="general-edit-form-{{ $jurisdiction->id }}"
                    />
                @endif
            </div>

        </x-jurisdiction-card>


        {{-- 5. Dynamic Sections --}}
        <div class="space-y-6">

            @foreach($jurisdiction->display_sections as $section)
                @php
                    $isLocalSection = $section->jurisdiction_id === $jurisdiction->id;
                    $canEditThisSection = $editable && ($isLocalSection || $jurisdiction->is_system_default);
                @endphp

                <x-section-display
                    :model="$section"
                    :editable="$canEditThisSection"
                    :editing-id="$editingSectionId"
                    :parent="$jurisdiction"
                    wire:key="section-wrapper-{{ $section->id }}-{{ $editingSectionId }}"
                />
            @endforeach

            @if($jurisdiction->display_sections->isEmpty())
                <div class="text-gray-400 text-center py-10 border-2 border-dashed border-gray-200 rounded-[12px]">
                    No sections available.
                </div>
            @endif

        </div>


        {{-- 6. Shared Content --}}
        <livewire:jurisdiction.shared-content contentKey="jurisdiction.show" />

    </div>
</div>
