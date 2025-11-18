<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            إضافة شركة جديدة
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('companies.store') }}" class="space-y-6">
                        @csrf

                        <!-- معلومات الشركة الأساسية -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الشركة الأساسية</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- اسم الشركة بالعربي -->
                                <div>
                                    <x-input-label for="name" value="اسم الشركة (بالعربي)" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- اسم الشركة بالإنجليزي -->
                                <div>
                                    <x-input-label for="name_en" value="اسم الشركة (بالإنجليزي)" />
                                    <x-text-input id="name_en" class="block mt-1 w-full" type="text" name="name_en" :value="old('name_en')" />
                                    <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
                                </div>

                                <!-- الرقم الضريبي -->
                                <div>
                                    <x-input-label for="tax_number" value="الرقم الضريبي" />
                                    <x-text-input id="tax_number" class="block mt-1 w-full" type="text" name="tax_number" :value="old('tax_number')" required />
                                    <x-input-error :messages="$errors->get('tax_number')" class="mt-2" />
                                </div>

                                <!-- السجل التجاري -->
                                <div>
                                    <x-input-label for="commercial_register" value="السجل التجاري" />
                                    <x-text-input id="commercial_register" class="block mt-1 w-full" type="text" name="commercial_register" :value="old('commercial_register')" required />
                                    <x-input-error :messages="$errors->get('commercial_register')" class="mt-2" />
                                </div>

                                <!-- العنوان -->
                                <div>
                                    <x-input-label for="address" value="العنوان" />
                                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                                <!-- المدينة -->
                                <div>
                                    <x-input-label for="city" value="المدينة" />
                                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>

                                <!-- الهاتف -->
                                <div>
                                    <x-input-label for="phone" value="الهاتف" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <!-- البريد الإلكتروني -->
                                <div>
                                    <x-input-label for="email" value="البريد الإلكتروني" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- الموقع الإلكتروني -->
                                <div>
                                    <x-input-label for="website" value="الموقع الإلكتروني" />
                                    <x-text-input id="website" class="block mt-1 w-full" type="text" name="website" :value="old('website')" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- الفروع -->
                        <div class="border-b pb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">الفروع</h3>
                                <button type="button" onclick="addBranch()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    إضافة فرع
                                </button>
                            </div>

                            <div id="branches-container" class="space-y-4">
                                <!-- الفرع الأول (الفرع الرئيسي) -->
                                <div class="branch-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-medium text-gray-700">الفرع #1 (رئيسي)</h4>
                                    </div>

                                    <input type="hidden" name="branches[0][is_main]" value="1">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="branch_0_name" value="اسم الفرع" />
                                            <x-text-input id="branch_0_name" class="block mt-1 w-full" type="text" name="branches[0][name]" :value="old('branches.0.name')" required />
                                            <x-input-error :messages="$errors->get('branches.0.name')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_0_code" value="كود الفرع" />
                                            <x-text-input id="branch_0_code" class="block mt-1 w-full" type="text" name="branches[0][code]" :value="old('branches.0.code')" required />
                                            <x-input-error :messages="$errors->get('branches.0.code')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_0_address" value="العنوان" />
                                            <x-text-input id="branch_0_address" class="block mt-1 w-full" type="text" name="branches[0][address]" :value="old('branches.0.address')" required />
                                            <x-input-error :messages="$errors->get('branches.0.address')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_0_city" value="المدينة" />
                                            <x-text-input id="branch_0_city" class="block mt-1 w-full" type="text" name="branches[0][city]" :value="old('branches.0.city')" required />
                                            <x-input-error :messages="$errors->get('branches.0.city')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_0_phone" value="هاتف الفرع" />
                                            <x-text-input id="branch_0_phone" class="block mt-1 w-full" type="text" name="branches[0][phone]" :value="old('branches.0.phone')" required />
                                            <x-input-error :messages="$errors->get('branches.0.phone')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_0_manager_name" value="اسم المدير" />
                                            <x-text-input id="branch_0_manager_name" class="block mt-1 w-full" type="text" name="branches[0][manager_name]" :value="old('branches.0.manager_name')" />
                                            <x-input-error :messages="$errors->get('branches.0.manager_name')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_0_manager_phone" value="هاتف المدير" />
                                            <x-text-input id="branch_0_manager_phone" class="block mt-1 w-full" type="text" name="branches[0][manager_phone]" :value="old('branches.0.manager_phone')" />
                                            <x-input-error :messages="$errors->get('branches.0.manager_phone')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- الفرع الثاني -->
                                <div class="branch-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-medium text-gray-700">الفرع #2</h4>
                                        <button type="button" onclick="removeBranch(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            حذف
                                        </button>
                                    </div>

                                    <input type="hidden" name="branches[1][is_main]" value="0">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="branch_1_name" value="اسم الفرع" />
                                            <x-text-input id="branch_1_name" class="block mt-1 w-full" type="text" name="branches[1][name]" :value="old('branches.1.name')" required />
                                            <x-input-error :messages="$errors->get('branches.1.name')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_1_code" value="كود الفرع" />
                                            <x-text-input id="branch_1_code" class="block mt-1 w-full" type="text" name="branches[1][code]" :value="old('branches.1.code')" required />
                                            <x-input-error :messages="$errors->get('branches.1.code')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_1_address" value="العنوان" />
                                            <x-text-input id="branch_1_address" class="block mt-1 w-full" type="text" name="branches[1][address]" :value="old('branches.1.address')" required />
                                            <x-input-error :messages="$errors->get('branches.1.address')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_1_city" value="المدينة" />
                                            <x-text-input id="branch_1_city" class="block mt-1 w-full" type="text" name="branches[1][city]" :value="old('branches.1.city')" required />
                                            <x-input-error :messages="$errors->get('branches.1.city')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_1_phone" value="هاتف الفرع" />
                                            <x-text-input id="branch_1_phone" class="block mt-1 w-full" type="text" name="branches[1][phone]" :value="old('branches.1.phone')" required />
                                            <x-input-error :messages="$errors->get('branches.1.phone')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_1_manager_name" value="اسم المدير" />
                                            <x-text-input id="branch_1_manager_name" class="block mt-1 w-full" type="text" name="branches[1][manager_name]" :value="old('branches.1.manager_name')" />
                                            <x-input-error :messages="$errors->get('branches.1.manager_name')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="branch_1_manager_phone" value="هاتف المدير" />
                                            <x-text-input id="branch_1_manager_phone" class="block mt-1 w-full" type="text" name="branches[1][manager_phone]" :value="old('branches.1.manager_phone')" />
                                            <x-input-error :messages="$errors->get('branches.1.manager_phone')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الحفظ والإلغاء -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                إلغاء
                            </a>

                            <x-primary-button>
                                حفظ الشركة
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let branchCount = 2; // نبدأ من 2 لأن لدينا فرعين بالفعل

        function addBranch() {
            const container = document.getElementById('branches-container');
            const branchHtml = `
                <div class="branch-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">الفرع #${branchCount + 1}</h4>
                        <button type="button" onclick="removeBranch(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">
                            حذف
                        </button>
                    </div>

                    <input type="hidden" name="branches[${branchCount}][is_main]" value="0">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="branch_${branchCount}_name">اسم الفرع</label>
                            <input id="branch_${branchCount}_name" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="branches[${branchCount}][name]" required />
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="branch_${branchCount}_code">كود الفرع</label>
                            <input id="branch_${branchCount}_code" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="branches[${branchCount}][code]" required />
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="branch_${branchCount}_address">العنوان</label>
                            <input id="branch_${branchCount}_address" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="branches[${branchCount}][address]" required />
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="branch_${branchCount}_city">المدينة</label>
                            <input id="branch_${branchCount}_city" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="branches[${branchCount}][city]" required />
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="branch_${branchCount}_phone">هاتف الفرع</label>
                            <input id="branch_${branchCount}_phone" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="branches[${branchCount}][phone]" required />
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="branch_${branchCount}_manager_name">اسم المدير</label>
                            <input id="branch_${branchCount}_manager_name" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="branches[${branchCount}][manager_name]" />
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="branch_${branchCount}_manager_phone">هاتف المدير</label>
                            <input id="branch_${branchCount}_manager_phone" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" type="text" name="branches[${branchCount}][manager_phone]" />
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', branchHtml);
            branchCount++;
        }

        function removeBranch(button) {
            const branchItem = button.closest('.branch-item');
            branchItem.remove();
        }
    </script>
</x-app-layout>
