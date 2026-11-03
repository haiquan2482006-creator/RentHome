<div class="relative w-full {{ $colClass ?? '' }}" id="location-select-wrapper">
    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Khu vực / Tỉnh thành</label>
    <div class="relative">
        <input type="hidden" id="selected-location-input" name="location" value="">
        <button type="button" id="location-select-trigger" onclick="toggleLocationDropdown(event)"
            class="w-full h-11.5 pl-10 pr-9 py-3 rounded-xl bg-slate-50 border border-slate-200 hover:bg-white hover:border-slate-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-slate-700 text-sm transition-all text-left flex items-center justify-between cursor-pointer shadow-xs">
            <span class="truncate block text-slate-700" id="selected-location-display">Tất cả tỉnh thành</span>
        </button>
        <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-brand-600 pointer-events-none text-sm"></i>
        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
            <button type="button" id="location-clear-btn" onclick="clearSelectedLocation(event)" title="Xóa chọn" class="hidden text-slate-400 hover:text-rose-500 text-xs p-1 cursor-pointer transition-colors">
                <i class="fa-solid fa-circle-xmark"></i>
            </button>
            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 pointer-events-none" id="location-dropdown-arrow"></i>
        </div>

        <!-- Dropdown Menu hiện ở dưới khi bấm vào có phần tìm kiếm danh sách tỉnh thành -->
        <div id="location-dropdown-panel"
            class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden w-full min-w-full">
            <!-- Thanh tìm kiếm danh sách tỉnh thành -->
            <div class="p-2.5 border-b border-slate-100 bg-slate-50/80">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input type="text" id="location-search-input" oninput="filterProvincesList(this.value)"
                        placeholder="Tìm kiếm tỉnh, thành phố..."
                        class="w-full pl-8 pr-7 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 placeholder:text-slate-400 placeholder:font-normal transition-all"
                        autocomplete="off">
                    <button type="button" id="location-search-clear-btn" onclick="clearProvinceSearch(event)" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-0.5">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Danh sách toàn bộ tỉnh thành Việt Nam (open-api depth=1) -->
            <div id="location-provinces-list" class="max-h-60 overflow-y-auto py-1 divide-y divide-slate-50 text-xs font-semibold custom-scrollbar">
            </div>
        </div>
    </div>
</div>

