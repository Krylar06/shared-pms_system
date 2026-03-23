@extends('admin.layouts.app')

@section('title', 'Staff')
@section('page_title', 'Staff')

@section('content')
<div
    x-data="{
        addOpen:false,
        editOpen:false,
        deleteOpen:false,

        editStaff:{
            id:null,
            first_name:'',
            last_name:'',
            position:'',
            email:'',
            phone:'',
            is_active:true
        },

        deleteStaffId:null,

        openEdit(staff){
            this.editStaff = {
                id: staff.id,
                first_name: staff.first_name ?? '',
                last_name: staff.last_name ?? '',
                position: staff.position ?? '',
                email: staff.email ?? '',
                phone: staff.phone ?? '',
                is_active: !!staff.is_active
            }
            this.editOpen = true
        },

        openDelete(id){
            this.deleteStaffId = id
            this.deleteOpen = true
        }
    }"
    class="space-y-5"
>
    {{-- Breadcrumb --}}
    <div class="text-sm text-gray-500 leading-6 break-words">
        <a class="text-blue-600 hover:underline" href="{{ route('admin.colleges.index') }}">Colleges</a>
        <span class="mx-1">/</span>
        <a class="text-blue-600 hover:underline" href="{{ route('admin.offices.index', $office->college) }}">
            {{ $office->college->name }}
        </a>
        <span class="mx-1">/</span>
        <span class="text-gray-700 font-medium">{{ $office->name }}</span>
        <span class="mx-1">/</span>
        <span>Staff</span>
    </div>

    {{-- Top section --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Staff in {{ $office->name }}</h1>
        </div>

        <button
            type="button"
            class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            @click="addOpen = true"
        >
            + Add Staff
        </button>
    </div>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse($staff as $s)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <a
                            class="font-semibold text-blue-700 hover:underline"
                            href="{{ route('admin.staff.devices.index', $s) }}"
                        >
                            {{ $s->last_name }}, {{ $s->first_name }}
                        </a>

                        <div class="mt-1 text-sm text-gray-500">
                            {{ $s->position ?: 'No position set' }}
                        </div>
                    </div>

                    <div>
                        @if($s->is_active)
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                Active
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Email</div>
                        <div class="break-all text-gray-900">{{ $s->email ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Phone</div>
                        <div class="text-gray-900">{{ $s->phone ?: '-' }}</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                        @click="openEdit({
                            id: {{ $s->id }},
                            first_name: @js($s->first_name),
                            last_name: @js($s->last_name),
                            position: @js($s->position ?? ''),
                            email: @js($s->email ?? ''),
                            phone: @js($s->phone ?? ''),
                            is_active: {{ $s->is_active ? 'true' : 'false' }}
                        })"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                        @click="openDelete({{ $s->id }})"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No staff found.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Position</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Phone</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($staff as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a
                                    class="font-medium text-blue-700 hover:underline"
                                    href="{{ route('admin.staff.devices.index', $s) }}"
                                >
                                    {{ $s->last_name }}, {{ $s->first_name }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-gray-700">{{ $s->position ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $s->email ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $s->phone ?: '-' }}</td>

                            <td class="px-4 py-3">
                                @if($s->is_active)
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        @click="openEdit({
                                            id: {{ $s->id }},
                                            first_name: @js($s->first_name),
                                            last_name: @js($s->last_name),
                                            position: @js($s->position ?? ''),
                                            email: @js($s->email ?? ''),
                                            phone: @js($s->phone ?? ''),
                                            is_active: {{ $s->is_active ? 'true' : 'false' }}
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        @click="openDelete({{ $s->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No staff found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $staff->links() }}
    </div>

    {{-- Add modal --}}
    <x-modal show="addOpen" title="Add Staff">
        <form method="POST" action="{{ route('admin.staff.store', $office) }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-sm font-medium">First Name</label>
                <input name="first_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
            </div>

            <div>
                <label class="text-sm font-medium">Last Name</label>
                <input name="last_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
            </div>

            <div>
                <label class="text-sm font-medium">Position</label>
                <input name="position" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input name="email" type="email" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>

            <div>
                <label class="text-sm font-medium">Phone</label>
                <input name="phone" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" checked>
                Active
            </label>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="addOpen=false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- Edit modal --}}
    <x-modal show="editOpen" title="Edit Staff">
        <form
            method="POST"
            :action="`{{ url('/admin/offices/'.$office->id.'/staff') }}/${editStaff.id}`"
            class="space-y-3"
        >
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">First Name</label>
                <input name="first_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.first_name" required>
            </div>

            <div>
                <label class="text-sm font-medium">Last Name</label>
                <input name="last_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.last_name" required>
            </div>

            <div>
                <label class="text-sm font-medium">Position</label>
                <input name="position" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.position">
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input name="email" type="email" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.email">
            </div>

            <div>
                <label class="text-sm font-medium">Phone</label>
                <input name="phone" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.phone">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" x-model="editStaff.is_active">
                Active
            </label>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="editOpen=false">Cancel</button>
            </div>
        </form>
    </x-modal>

    {{-- Delete modal --}}
    <x-modal show="deleteOpen" title="Delete Staff">
        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this staff member?
            </div>

            <form
                method="POST"
                :action="`{{ url('/admin/offices/'.$office->id.'/staff') }}/${deleteStaffId}`"
                class="flex gap-2"
            >
                @csrf
                @method('DELETE')

                <button class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Yes, Delete</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="deleteOpen=false">Cancel</button>
            </form>
        </div>
    </x-modal>
</div>
@endsection