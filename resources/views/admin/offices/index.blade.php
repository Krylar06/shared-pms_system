@extends('admin.layouts.app')

@section('title', 'Offices')
@section('page_title', 'Offices')

@section('content')
<div
    x-data="{
        addOpen: {{ $errors->any() ? 'true' : 'false' }},
        editOpen: false,
        deleteOpen: false,

        editOffice: { id: null, name: '' },
        deleteOfficeId: null,

        openEdit(office) {
            this.editOffice = office;
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteOfficeId = id;
            this.deleteOpen = true;
        }
    }"
    class="space-y-5"
>
    {{-- Breadcrumb --}}
    <div class="text-sm text-gray-500 leading-6 break-words">
        <a class="text-blue-600 hover:underline" href="{{ route('admin.colleges.index') }}">Colleges</a>
        <span class="mx-1">/</span>
        <span class="text-gray-700 font-medium">{{ $college->name }}</span>
        <span class="mx-1">/</span>
        <span>Offices</span>
    </div>

    {{-- Top section --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Offices in {{ $college->name }}</h1>
        </div>

        <button
            type="button"
            class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            @click="addOpen = true"
        >
            + Add Office
        </button>
    </div>

    {{-- Mobile cards --}}
    <div class="grid grid-cols-1 gap-3 md:hidden">
        @forelse ($offices as $o)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="space-y-3">
                    <div>
                        <a
                            class="font-semibold text-blue-700 hover:underline"
                            href="{{ route('admin.staff.index', $o) }}"
                        >
                            {{ $o->name }}
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <button
                            type="button"
                            class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                            @click="openEdit({
                                id: {{ $o->id }},
                                name: @js($o->name)
                            })"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                            @click="openDelete({{ $o->id }})"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No offices found.
            </div>
        @endforelse
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Office Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($offices as $o)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a
                                    class="font-medium text-blue-700 hover:underline"
                                    href="{{ route('admin.staff.index', $o) }}"
                                >
                                    {{ $o->name }}
                                </a>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        @click="openEdit({
                                            id: {{ $o->id }},
                                            name: @js($o->name)
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        @click="openDelete({{ $o->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                No offices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $offices->links() }}
    </div>

    {{-- Add modal --}}
    <x-modal show="addOpen" title="Add Office">
        <form method="POST" action="{{ route('admin.offices.store', $college) }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-sm font-medium">Office Name</label>
                <input
                    name="name"
                    value="{{ old('name') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    required
                >
                @error('name')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="addOpen = false"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Edit modal --}}
    <x-modal show="editOpen" title="Edit Office">
        <form
            method="POST"
            :action="`{{ url('/admin/colleges/' . $college->id . '/offices') }}/${editOffice.id}`"
            class="space-y-3"
        >
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">Office Name</label>
                <input
                    name="name"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editOffice.name"
                    required
                >
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="editOpen = false"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Delete modal --}}
    <x-modal show="deleteOpen" title="Delete Office">
        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this office?
            </div>

            <form
                method="POST"
                :action="`{{ url('/admin/colleges/' . $college->id . '/offices') }}/${deleteOfficeId}`"
                class="flex gap-2"
            >
                @csrf
                @method('DELETE')

                <button class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Yes, Delete</button>
                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="deleteOpen = false"
                >
                    Cancel
                </button>
            </form>
        </div>
    </x-modal>
</div>
@endsection