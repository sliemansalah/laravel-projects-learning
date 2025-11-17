<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            معلومات النظام
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">📋 معلومات النظام</h3>

                    <table class="w-full">
                        <tr class="border-b">
                            <td class="py-3 font-bold">اسم النظام:</td>
                            <td class="py-3">{{ $systemInfo['system_name'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">الإصدار:</td>
                            <td class="py-3">{{ $systemInfo['version'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">Laravel:</td>
                            <td class="py-3">{{ $systemInfo['laravel_version'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">PHP:</td>
                            <td class="py-3">{{ $systemInfo['php_version'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">قاعدة البيانات:</td>
                            <td class="py-3">{{ $systemInfo['database'] }}</td>
                        </tr>
                    </table>

                    <div class="mt-6 p-4 bg-green-100 rounded">
                        <p class="text-green-800 font-bold">✅ النظام يعمل بشكل صحيح!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
