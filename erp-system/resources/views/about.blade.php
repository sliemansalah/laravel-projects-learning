<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            حول النظام
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-6">ℹ️ حول النظام</h3>

                    <table class="w-full">
                        <tr class="border-b">
                            <td class="py-3 font-bold">اسم النظام:</td>
                            <td class="py-3">{{ $aboutInfo['system_name'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">الهدف من النظام:</td>
                            <td class="py-3">{{ $aboutInfo['goal'] }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-3 font-bold">المطور</td>
                            <td class="py-3">
                               تم بواسطة  <span style="font-weight: bold;"> {{ $aboutInfo['programmer'] }}</span>
                                 </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
