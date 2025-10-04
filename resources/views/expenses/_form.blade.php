@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Date -->
    <div>
        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
        <input type="date" id="date" name="date"
               value="{{ old('date', $expense->date ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
        @error('date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Category (datalist for flexibility + suggestions) -->
    <div>
        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
        <input list="categories" type="text" id="category" name="category"
               placeholder="Type or select a category"
               value="{{ old('category', $expense->category ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
        <datalist id="categories">
            @foreach($categories ?? [] as $cat)
                <option value="{{ $cat }}"></option>
            @endforeach
        </datalist>
        @error('category') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        <p class="text-gray-400 text-xs mt-1">Select a common category or type a custom one.</p>
    </div>

    <!-- Description -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <input type="text" id="description" name="description"
               value="{{ old('description', $expense->description ?? '') }}"
               placeholder="Optional, add details"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Project -->
    <div>
        <label for="project_id" class="block text-sm font-medium text-gray-700">Project</label>
        <select id="project_id" name="project_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
            <option value="">— None —</option>
            @foreach($projects as $id => $name)
                <option value="{{ $id }}"
                    {{ (string) old('project_id', $expense->project_id ?? '') === (string) $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('project_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Vendor / Client -->
    <div>
        <label for="client_id" class="block text-sm font-medium text-gray-700">Vendor / Supplier / Worker</label>
        <select id="client_id" name="client_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
            <option value="">— None —</option>
            @foreach($clients as $id => $name)
                <option value="{{ $id }}"
                    {{ (string) old('client_id', $expense->client_id ?? '') === (string) $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('client_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Amount -->
    <div>
        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
        <input type="number" step="0.01" id="amount" name="amount"
               value="{{ old('amount', $expense->amount ?? '') }}"
               placeholder="0.00"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
               required />
        @error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Payment Method -->
    <div>
        <label for="method" class="block text-sm font-medium text-gray-700">Method</label>
        <input type="text" id="method" name="method"
               value="{{ old('method', $expense->method ?? '') }}"
               placeholder="Cash, Bank Transfer, etc."
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
        @error('method') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Status -->
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <input type="text" id="status" name="status"
               value="{{ old('status', $expense->status ?? '') }}"
               placeholder="Pending, Paid, Reimbursed..."
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2" />
        @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Registered By -->
    <div class="md:col-span-2">
        <label for="user_id" class="block text-sm font-medium text-gray-700">Registered By</label>
        <textarea id="user_id" name="user_id" rows="3"
                  placeholder="Enter user ID or name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">{{ old('user_id', $expense->user_id ?? '') }}</textarea>
        @error('user_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>
