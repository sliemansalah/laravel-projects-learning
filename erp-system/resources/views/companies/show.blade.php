<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            تفاصيل الشركة: {{ $company->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- معلومات الشركة --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">📋 معلومات الشركة</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">اسم الشركة (عربي)</p>
                            <p class="font-bold">{{ $company->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">اسم الشركة (إنجليزي)</p>
                            <p class="font-bold">{{ $company->name_en ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">الرقم الضريبي</p>
                            <p class="font-bold">{{ $company->tax_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">السجل التجاري</p>
                            <p class="font-bold">{{ $company->commercial_register }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">العنوان</p>
                            <p class="font-bold">{{ $company->address }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">المدينة</p>
                            <p class="font-bold">{{ $company->city }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">الهاتف</p>
                            <p class="font-bold">{{ $company->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                            <p class="font-bold">{{ $company->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الفروع --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">🏢 الفروع ({{ $company->branches->count() }})</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($company->branches as $branch)
                            <div class="border rounded-lg p-4 {{ $branch->is_main ? 'bg-blue-50 border-blue-300' : 'bg-gray-50' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-lg">{{ $branch->name }}</h4>
                                    @if($branch->is_main)
                                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">رئيسي</span>
                                    @endif
                                </div>

                                <div class="space-y-1 text-sm">
                                    <p><span class="text-gray-600">الكود:</span> <span class="font-bold">{{ $branch->code }}</span></p>
                                    <p><span class="text-gray-600">المدينة:</span> {{ $branch->city }}</p>
                                    <p><span class="text-gray-600">العنوان:</span> {{ $branch->address }}</p>
                                    <p><span class="text-gray-600">الهاتف:</span> {{ $branch->phone }}</p>
                                    @if($branch->manager_name)
                                        <p><span class="text-gray-600">المدير:</span> {{ $branch->manager_name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
