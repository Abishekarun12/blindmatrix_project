<x-layouts.app :title="__('Dashboard')">
    <div x-data="employeeModal()" x-init="init()" class="relative">
        {{-- Page Header --}}
        <div class="flex items-center justify-between p-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Employees</h2>
            <button @click="document.documentElement.classList.toggle('dark')"
                class="rounded-lg border px-3 py-1 text-sm text-gray-800 hover:bg-gray-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700">
                <i class="fas fa-sun dark:hidden"></i>
                <i class="fas fa-moon hidden dark:inline"></i>
            </button>
        </div>

        {{-- Main Content --}}
        <div class="relative h-full overflow-auto rounded-xl border border-neutral-700 bg-neutral-900 p-6 text-white">
            {{-- Alerts --}}
            @if (session('success'))
                <div class="mb-4 rounded bg-green-700/50 px-4 py-2 text-green-100">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded bg-red-700/50 px-4 py-2 text-red-100">{{ session('error') }}</div>
            @endif

            {{-- Controls --}}
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between w-full">
                {{-- Import/Export --}}
                <form id="importForm" action="{{ route('employees.import') }}" method="POST"
                    enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <input type="file" name="file" required accept=".csv, .xls, .xlsx"
                        class="w-full sm:w-auto rounded border border-neutral-600 bg-neutral-800 px-4 py-2 text-white file:mr-4 file:rounded file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-white hover:file:bg-blue-500" />
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded bg-green-600 px-6 py-2 text-white hover:bg-green-500 shadow">Import</button>
                    <a href="{{ route('employees.export') }}"
                        class="inline-flex items-center gap-2 rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-500 shadow">Export</a>
                </form>

                <button @click="openModal('add')"
                    class="rounded-xl bg-white text-purple-700 border border-purple-700 px-6 py-2.5 hover:bg-purple-100 transition shadow-md flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Add Employee
                </button>
            </div>

            {{-- Employee Table --}}
            <div class="mt-6 overflow-x-auto rounded-lg border border-neutral-700">
                <table class="min-w-full divide-y divide-neutral-700 text-sm">
                    <thead class="bg-neutral-800 text-neutral-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Email</th>
                            <th class="px-4 py-3 text-left font-medium">Position</th>
                            <th class="px-4 py-3 text-left font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-700">
                        @forelse($employees as $employee)
                            <tr class="hover:bg-neutral-800" data-id="{{ $employee->id }}">
                                <td class="px-4 py-3">{{ $employee->name }}</td>
                                <td class="px-4 py-3">{{ $employee->email }}</td>
                                <td class="px-4 py-3">{{ $employee->position }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <button @click="openModal('edit', {{ $employee->id }})"
                                            class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded text-xs">Edit</button>
                                        <button type="button"
                                            class="bg-red-600 hover:bg-red-500 text-white px-3 py-1.5 rounded text-xs">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-neutral-400">No employees found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add Modal --}}
        <div x-show="showModal && modalType === 'add'" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
            <div
                class="w-full max-w-lg rounded-xl bg-neutral-900 border border-neutral-700 p-6 shadow-xl text-white relative z-10">
                <h3 class="text-xl font-semibold mb-4">Add Employee</h3>
                <form id="addEmployeeForm" @submit.prevent="submitAddForm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Name</label>
                        <input type="text" name="name" id="add-name"
                            class="w-full rounded border border-neutral-600 bg-neutral-800 px-3 py-2 text-white">
                        <p class="text-sm text-red-400 mt-1" id="error-add-name"></p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Email</label>
                        <input type="email" name="email" id="add-email"
                            class="w-full rounded border border-neutral-600 bg-neutral-800 px-3 py-2 text-white">
                        <p class="text-sm text-red-400 mt-1" id="error-add-email"></p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm mb-1">Position</label>
                        <input type="text" name="position" id="add-position"
                            class="w-full rounded border border-neutral-600 bg-neutral-800 px-3 py-2 text-white">
                        <p class="text-sm text-red-400 mt-1" id="error-add-position"></p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="closeModal"
                            class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-500">Cancel</button>
                        <button type="submit" class="bg-green-600 px-4 py-2 rounded hover:bg-green-500">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="showModal && modalType === 'edit'" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
            <div
                class="w-full max-w-lg rounded-xl bg-neutral-900 border border-neutral-700 p-6 shadow-xl text-white relative z-10">
                <h3 class="text-xl font-semibold mb-4">Edit Employee</h3>
                <form id="editEmployeeForm" @submit.prevent="submitEditForm">
                    @csrf
                    <input type="hidden" id="employeeId">
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Name</label>
                        <input type="text" name="name" id="edit-name"
                            class="w-full rounded border border-neutral-600 bg-neutral-800 px-3 py-2 text-white">
                        <p class="text-sm text-red-400 mt-1" id="error-edit-name"></p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Email</label>
                        <input type="email" name="email" id="edit-email"
                            class="w-full rounded border border-neutral-600 bg-neutral-800 px-3 py-2 text-white">
                        <p class="text-sm text-red-400 mt-1" id="error-edit-email"></p>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm mb-1">Position</label>
                        <input type="text" name="position" id="edit-position"
                            class="w-full rounded border border-neutral-600 bg-neutral-800 px-3 py-2 text-white">
                        <p class="text-sm text-red-400 mt-1" id="error-edit-position"></p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="closeModal"
                            class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-500">Cancel</button>
                        <button type="submit"
                            class="bg-green-600 px-4 py-2 rounded hover:bg-green-500">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Alpine.js Script --}}
    <script>
        function employeeModal() {
            return {
                showModal: false,
                modalType: '',
                employeeId: null,

                init() {
                    console.log("Alpine component initialized");
                },

                openModal(type, id = null) {
                    this.modalType = type;
                    this.showModal = true;
                    if (type === 'edit' && id) {
                        this.employeeId = id;
                        this.fetchEmployeeData(id);
                    }
                },

                closeModal() {
                    this.showModal = false;
                    this.clearErrors();
                },

                submitAddForm() {
                    const form = document.getElementById('addEmployeeForm');
                    const formData = new FormData(form);

                    fetch("{{ route('employees.store') }}", {
                            method: "POST",
                            headers: {
                                'Accept': 'application/json',
                            },
                            body: formData,
                        })
                        .then(async response => {
                            const data = await response.json();
                            this.clearErrors();

                            if (!response.ok) {
                                if (data?.errors) {
                                    Object.entries(data.errors).forEach(([field, messages]) => {
                                        const el = document.getElementById(`error-add-${field}`);
                                        if (el) el.textContent = messages[0];
                                    });
                                } else {
                                    alert("Something went wrong. Please try again.");
                                }
                            } else {
                                this.closeModal();
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error("Unexpected error:", error);
                            alert("An unexpected error occurred. Please try again.");
                        });
                },

                submitEditForm() {
                    const csrfTokenMetaTag = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfTokenMetaTag) {
                        console.error('CSRF token meta tag is missing.');
                        return;
                    }
                    const csrfToken = csrfTokenMetaTag.getAttribute('content');
                    const id = document.getElementById('employeeId').value;
                    const form = document.getElementById('editEmployeeForm');
                    const formData = new FormData(form);

                    fetch(`/employees/${id}`, {
                            method: "POST",
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-HTTP-Method-Override': 'PUT',
                            },
                            body: formData,
                        })
                        .then(async response => {
                            const data = await response.json();
                            this.clearErrors();

                            if (!response.ok) {
                                if (data?.errors) {
                                    Object.entries(data.errors).forEach(([field, messages]) => {
                                        const el = document.getElementById(`error-edit-${field}`);
                                        if (el) el.textContent = messages[0];
                                    });
                                } else {
                                    alert("Something went wrong. Please try again.");
                                }
                            } else {
                                this.closeModal();
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error("Unexpected error:", error);
                            alert("An unexpected error occurred. Please try again.");
                        });
                },

                clearErrors() {
                    document.querySelectorAll('.text-red-400').forEach(el => {
                        el.textContent = '';
                    });
                },
            }
        }
    </script>
</x-layouts.app>
