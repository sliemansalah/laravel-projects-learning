<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('لوحة التحكم - نظام ERP') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- رسالة الترحيب --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-8 text-white">
                    <h3 class="text-3xl font-bold mb-2">مرحباً {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-blue-100 text-lg">أهلاً بك في نظام ERP المتكامل لإدارة الأعمال</p>
                </div>
            </div>

            {{-- بطاقات الإحصائيات --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                {{-- إجمالي المبيعات --}}
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 overflow-hidden shadow-lg sm:rounded-xl transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm opacity-90 mb-1">إجمالي المبيعات</p>
                                <p class="text-3xl font-bold mt-2">0.00</p>
                                <p class="text-xs opacity-75 mt-1">ريال سعودي</p>
                            </div>
                            <div class="text-5xl opacity-30">💰</div>
                        </div>
                    </div>
                </div>

                {{-- عدد الفواتير --}}
                <div class="bg-gradient-to-br from-red-400 to-red-600 overflow-hidden shadow-lg sm:rounded-xl transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm opacity-90 mb-1">عدد الفواتير</p>
                                <p class="text-3xl font-bold mt-2">0</p>
                                <p class="text-xs opacity-75 mt-1">فاتورة</p>
                            </div>
                            <div class="text-5xl opacity-30">📄</div>
                        </div>
                    </div>
                </div>

                {{-- عدد العملاء --}}
                <div class="bg-gradient-to-br from-pink-500 to-pink-600 overflow-hidden shadow-lg sm:rounded-xl transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm opacity-90 mb-1">عدد العملاء</p>
                                <p class="text-3xl font-bold mt-2">0</p>
                                <p class="text-xs opacity-75 mt-1">عميل</p>
                            </div>
                            <div class="text-5xl opacity-30">👥</div>
                        </div>
                    </div>
                </div>

                {{-- المخزون --}}
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 overflow-hidden shadow-lg sm:rounded-xl transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm opacity-90 mb-1">المنتجات</p>
                                <p class="text-3xl font-bold mt-2">0</p>
                                <p class="text-xs opacity-75 mt-1">منتج</p>
                            </div>
                            <div class="text-5xl opacity-30">📦</div>
                        </div>
                    </div>
                </div>

                 {{-- عدد الموردين --}}
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-lg sm:rounded-xl transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm opacity-90 mb-1">الموردين</p>
                                <p class="text-3xl font-bold mt-2">0</p>
                                <p class="text-xs opacity-75 mt-1">مورد</p>
                            </div>
                            <div class="text-5xl opacity-30">📊</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- الوحدات الرئيسية --}}
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">الوحدات الرئيسية</h3>
                        <span class="text-sm text-gray-500">اختر الوحدة للبدء</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        {{-- المحاسبة --}}
                        <a href="#" class="group block p-6 bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-500 hover:to-blue-600 rounded-xl border-2 border-blue-200 hover:border-blue-600 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">📊</div>
                            <h4 class="text-xl font-bold mb-2 text-blue-900 group-hover:text-white transition-colors duration-300">المحاسبة</h4>
                            <p class="text-sm text-blue-700 group-hover:text-blue-50 transition-colors duration-300">شجرة الحسابات، القيود، التقارير المالية</p>
                        </a>

                        {{-- المبيعات --}}
                        <a href="#" class="group block p-6 bg-gradient-to-br from-green-50 to-green-100 hover:from-green-500 hover:to-green-600 rounded-xl border-2 border-green-200 hover:border-green-600 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🛒</div>
                            <h4 class="text-xl font-bold mb-2 text-green-900 group-hover:text-white transition-colors duration-300">المبيعات</h4>
                            <p class="text-sm text-green-700 group-hover:text-green-50 transition-colors duration-300">الفواتير، العملاء، عروض الأسعار</p>
                        </a>

                        {{-- المشتريات --}}
                        <a href="#" class="group block p-6 bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-500 hover:to-purple-600 rounded-xl border-2 border-purple-200 hover:border-purple-600 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🛍️</div>
                            <h4 class="text-xl font-bold mb-2 text-purple-900 group-hover:text-white transition-colors duration-300">المشتريات</h4>
                            <p class="text-sm text-purple-700 group-hover:text-purple-50 transition-colors duration-300">الموردين، فواتير المشتريات</p>
                        </a>

                        {{-- المخزون --}}
                        <a href="#" class="group block p-6 bg-gradient-to-br from-orange-50 to-orange-100 hover:from-orange-500 hover:to-orange-600 rounded-xl border-2 border-orange-200 hover:border-orange-600 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">📦</div>
                            <h4 class="text-xl font-bold mb-2 text-orange-900 group-hover:text-white transition-colors duration-300">المخزون</h4>
                            <p class="text-sm text-orange-700 group-hover:text-orange-50 transition-colors duration-300">المنتجات، المستودعات، حركات المخزون</p>
                        </a>

                        {{-- الموارد البشرية --}}
                        <a href="#" class="group block p-6 bg-gradient-to-br from-red-50 to-red-100 hover:from-red-500 hover:to-red-600 rounded-xl border-2 border-red-200 hover:border-red-600 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">👥</div>
                            <h4 class="text-xl font-bold mb-2 text-red-900 group-hover:text-white transition-colors duration-300">الموارد البشرية</h4>
                            <p class="text-sm text-red-700 group-hover:text-red-50 transition-colors duration-300">الموظفين، الحضور، الرواتب</p>
                        </a>

                        {{-- التقارير --}}
                        <a href="#" class="group block p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 hover:from-indigo-500 hover:to-indigo-600 rounded-xl border-2 border-indigo-200 hover:border-indigo-600 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">📈</div>
                            <h4 class="text-xl font-bold mb-2 text-indigo-900 group-hover:text-white transition-colors duration-300">التقارير</h4>
                            <p class="text-sm text-indigo-700 group-hover:text-indigo-50 transition-colors duration-300">التقارير المالية والتحليلية</p>
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
