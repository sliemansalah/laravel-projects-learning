<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                الشركات
            </h2>
            <a href="{{ route('companies.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                إضافة شركة جديدة
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2">
                                <th class="text-right py-3">#</th>
                                <th class="text-right py-3">اسم الشركة</th>
                                <th class="text-right py-3">الرقم الضريبي</th>
                                <th class="text-right py-3">المدينة</th>
                                <th class="text-right py-3">عدد الفروع</th>
                                <th class="text-right py-3">الحالة</th>
                                <th class="text-right py-3">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3">{{ $loop->iteration }}</td>
                                    <td class="py-3">
                                        <div class="font-bold">{{ $company->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $company->name_en }}</div>
                                    </td>
                                    <td class="py-3">{{ $company->tax_number }}</td>
                                    <td class="py-3">{{ $company->city }}</td>
                                    <td class="py-3">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            {{ $company->branches->count() }} فرع
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if($company->is_active)
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">نشطة</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded">غير نشطة</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="flex gap-2">
                                            <a href="{{ route('companies.show', $company) }}"
                                               class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">
                                                عرض
                                            </a>
                                            <form action="{{ route('companies.switch', $company) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded">
                                                    تبديل
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">
                                        لا توجد شركات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
