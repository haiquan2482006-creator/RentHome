/**
 * RentHome - Province / City Dropdown Picker with Instant Search
 * Source API: https://provinces.open-api.vn/api/?depth=1
 */

const DEFAULT_VIETNAM_PROVINCES = [
    { code: 1, name: "Thành phố Hà Nội" },
    { code: 2, name: "Tỉnh Hà Giang" },
    { code: 4, name: "Tỉnh Cao Bằng" },
    { code: 6, name: "Tỉnh Bắc Kạn" },
    { code: 8, name: "Tỉnh Tuyên Quang" },
    { code: 10, name: "Tỉnh Lào Cai" },
    { code: 11, name: "Tỉnh Điện Biên" },
    { code: 12, name: "Tỉnh Lai Châu" },
    { code: 14, name: "Tỉnh Sơn La" },
    { code: 15, name: "Tỉnh Yên Bái" },
    { code: 17, name: "Tỉnh Hoà Bình" },
    { code: 19, name: "Tỉnh Thái Nguyên" },
    { code: 20, name: "Tỉnh Lạng Sơn" },
    { code: 22, name: "Tỉnh Quảng Ninh" },
    { code: 24, name: "Tỉnh Bắc Giang" },
    { code: 25, name: "Tỉnh Phú Thọ" },
    { code: 26, name: "Tỉnh Vĩnh Phúc" },
    { code: 27, name: "Tỉnh Bắc Ninh" },
    { code: 30, name: "Tỉnh Hải Dương" },
    { code: 31, name: "Thành phố Hải Phòng" },
    { code: 33, name: "Tỉnh Hưng Yên" },
    { code: 34, name: "Tỉnh Thái Bình" },
    { code: 35, name: "Tỉnh Hà Nam" },
    { code: 36, name: "Tỉnh Nam Định" },
    { code: 37, name: "Tỉnh Ninh Bình" },
    { code: 38, name: "Tỉnh Thanh Hóa" },
    { code: 40, name: "Tỉnh Nghệ An" },
    { code: 42, name: "Tỉnh Hà Tĩnh" },
    { code: 44, name: "Tỉnh Quảng Bình" },
    { code: 45, name: "Tỉnh Quảng Trị" },
    { code: 46, name: "Thành phố Huế" },
    { code: 48, name: "Thành phố Đà Nẵng" },
    { code: 49, name: "Tỉnh Quảng Nam" },
    { code: 51, name: "Tỉnh Quảng Ngãi" },
    { code: 52, name: "Tỉnh Bình Định" },
    { code: 54, name: "Tỉnh Phú Yên" },
    { code: 56, name: "Tỉnh Khánh Hòa" },
    { code: 58, name: "Tỉnh Ninh Thuận" },
    { code: 60, name: "Tỉnh Bình Thuận" },
    { code: 62, name: "Tỉnh Kon Tum" },
    { code: 64, name: "Tỉnh Gia Lai" },
    { code: 66, name: "Tỉnh Đắk Lắk" },
    { code: 67, name: "Tỉnh Đắk Nông" },
    { code: 68, name: "Tỉnh Lâm Đồng" },
    { code: 70, name: "Tỉnh Bình Phước" },
    { code: 72, name: "Tỉnh Tây Ninh" },
    { code: 74, name: "Tỉnh Bình Dương" },
    { code: 75, name: "Tỉnh Đồng Nai" },
    { code: 77, name: "Tỉnh Bà Rịa - Vũng Tàu" },
    { code: 79, name: "Thành phố Hồ Chí Minh" },
    { code: 80, name: "Tỉnh Long An" },
    { code: 82, name: "Tỉnh Tiền Giang" },
    { code: 83, name: "Tỉnh Bến Tre" },
    { code: 84, name: "Tỉnh Trà Vinh" },
    { code: 86, name: "Tỉnh Vĩnh Long" },
    { code: 87, name: "Tỉnh Đồng Tháp" },
    { code: 89, name: "Tỉnh An Giang" },
    { code: 91, name: "Tỉnh Kiên Giang" },
    { code: 92, name: "Thành phố Cần Thơ" },
    { code: 93, name: "Tỉnh Hậu Giang" },
    { code: 94, name: "Tỉnh Sóc Trăng" },
    { code: 95, name: "Tỉnh Bạc Liêu" },
    { code: 96, name: "Tỉnh Cà Mau" }
];

window.vietnamProvinces = [...DEFAULT_VIETNAM_PROVINCES];

function removeVietnameseTones(str) {
    if (!str) return '';
    str = String(str);
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
    str = str.replace(/Đ/g, "D");
    str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, "");
    str = str.replace(/\u02C6|\u0306|\u031B/g, "");
    return str.toLowerCase().trim();
}

async function loadProvincesFromApi() {
    try {
        const res = await fetch('https://provinces.open-api.vn/api/?depth=1');
        if (res.ok) {
            const data = await res.json();
            if (Array.isArray(data) && data.length > 0) {
                window.vietnamProvinces = data.map(p => ({
                    code: p.code,
                    name: p.name
                }));
                const currentKw = document.getElementById('location-search-input')?.value || '';
                renderProvincesList(currentKw);
                return;
            }
        }
    } catch (e) {
        // Fallback to local /provinces.json
    }

    try {
        const localRes = await fetch('/provinces.json');
        if (localRes.ok) {
            const localData = await localRes.json();
            if (Array.isArray(localData) && localData.length > 0) {
                window.vietnamProvinces = localData.map(p => ({
                    code: p.code,
                    name: p.name
                }));
                const currentKw = document.getElementById('location-search-input')?.value || '';
                renderProvincesList(currentKw);
            }
        }
    } catch (err) {}
}

function toggleLocationDropdown(event) {
    if (event) event.stopPropagation();
    const panel = document.getElementById('location-dropdown-panel');
    const arrow = document.getElementById('location-dropdown-arrow');
    const searchInput = document.getElementById('location-search-input');

    if (!panel) return;
    const isHidden = panel.classList.contains('hidden');

    if (isHidden) {
        panel.classList.remove('hidden');
        if (arrow) arrow.style.transform = 'rotate(180deg)';
        renderProvincesList(searchInput ? searchInput.value : '');
        setTimeout(() => {
            if (searchInput) searchInput.focus();
        }, 50);
    } else {
        panel.classList.add('hidden');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
}

function closeLocationDropdown() {
    const panel = document.getElementById('location-dropdown-panel');
    const arrow = document.getElementById('location-dropdown-arrow');
    if (panel) panel.classList.add('hidden');
    if (arrow) arrow.style.transform = 'rotate(0deg)';
}

function renderProvincesList(filterKeyword = '') {
    const container = document.getElementById('location-provinces-list');
    if (!container) return;

    const currentVal = document.getElementById('selected-location-input')?.value || '';
    const cleanKw = removeVietnameseTones(filterKeyword);

    let list = window.vietnamProvinces || DEFAULT_VIETNAM_PROVINCES;

    let filtered = list;
    if (cleanKw) {
        filtered = list.filter(p => {
            const cleanName = removeVietnameseTones(p.name);
            return cleanName.includes(cleanKw);
        });
    }

    let html = '';

    // "Tất cả tỉnh thành" option (only show when not searching or when keyword matches 'tat ca')
    if (!cleanKw || 'tat ca tinh thanh'.includes(cleanKw) || 'toan quoc'.includes(cleanKw)) {
        const isAllSelected = !currentVal || currentVal === 'Tất cả địa điểm' || currentVal === 'Tất cả tỉnh thành';
        html += `
            <div onclick="selectLocationProvince('')"
                class="px-4 py-2.5 hover:bg-brand-50 hover:text-brand-700 cursor-pointer flex items-center justify-between text-xs transition-colors ${isAllSelected ? 'bg-brand-50/90 text-brand-700 font-bold' : 'text-slate-700 font-semibold'}">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-earth-americas text-brand-500 text-sm"></i>
                    <span>Tất cả tỉnh thành</span>
                </span>
                ${isAllSelected ? '<i class="fa-solid fa-check text-brand-600 text-xs font-bold"></i>' : ''}
            </div>
        `;
    }

    if (filtered.length === 0 && html === '') {
        container.innerHTML = `
            <div class="py-6 px-4 text-center text-slate-400 text-xs font-medium">
                <i class="fa-solid fa-map-location-dot text-slate-300 text-2xl mb-2 block"></i>
                Không tìm thấy tỉnh / thành phố phù hợp
            </div>
        `;
        return;
    }

    filtered.forEach(prov => {
        const isSelected = currentVal === prov.name || 
            (currentVal && (prov.name.includes(currentVal) || currentVal.includes(prov.name)));
        html += `
            <div onclick="selectLocationProvince('${prov.name.replace(/'/g, "\\'")}')"
                class="px-4 py-2.5 hover:bg-brand-50 hover:text-brand-700 cursor-pointer flex items-center justify-between text-xs transition-colors ${isSelected ? 'bg-brand-50/90 text-brand-700 font-bold' : 'text-slate-700 font-semibold'}">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-location-dot text-xs ${isSelected ? 'text-brand-600' : 'text-slate-400'}"></i>
                    <span class="truncate">${prov.name}</span>
                </span>
                ${isSelected ? '<i class="fa-solid fa-check text-brand-600 text-xs font-bold shrink-0"></i>' : ''}
            </div>
        `;
    });

    container.innerHTML = html;
}

function filterProvincesList(kw) {
    const clearBtn = document.getElementById('location-search-clear-btn');
    if (clearBtn) {
        if (kw && kw.trim().length > 0) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }
    renderProvincesList(kw);
}

function clearProvinceSearch(event) {
    if (event) event.stopPropagation();
    const searchInput = document.getElementById('location-search-input');
    const clearBtn = document.getElementById('location-search-clear-btn');
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
    }
    if (clearBtn) clearBtn.classList.add('hidden');
    renderProvincesList('');
}

function selectLocationProvince(provinceName) {
    const input = document.getElementById('selected-location-input');
    const display = document.getElementById('selected-location-display');
    const clearBtn = document.getElementById('location-clear-btn');

    const cleanVal = (provinceName && provinceName !== 'Tất cả địa điểm' && provinceName !== 'Tất cả tỉnh thành') ? provinceName : '';

    if (input) input.value = cleanVal;
    if (display) {
        display.innerText = cleanVal ? cleanVal : 'Tất cả tỉnh thành';
        if (cleanVal) {
            display.classList.add('text-brand-700', 'font-bold');
            display.classList.remove('text-slate-700');
        } else {
            display.classList.remove('text-brand-700', 'font-bold');
            display.classList.add('text-slate-700');
        }
    }
    if (clearBtn) {
        if (cleanVal) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    closeLocationDropdown();

    if (typeof window.onLocationChange === 'function') {
        window.onLocationChange(cleanVal);
    }
}

function setSelectedLocation(provinceName) {
    const input = document.getElementById('selected-location-input');
    const display = document.getElementById('selected-location-display');
    const clearBtn = document.getElementById('location-clear-btn');

    let cleanVal = (provinceName && provinceName !== 'Tất cả địa điểm' && provinceName !== 'Tất cả tỉnh thành') ? provinceName.trim() : '';

    // If provinceName is something like "Hà Nội" without prefix, try to match official name
    if (cleanVal) {
        const cleanTone = removeVietnameseTones(cleanVal);
        const matched = (window.vietnamProvinces || DEFAULT_VIETNAM_PROVINCES).find(p => {
            const pTone = removeVietnameseTones(p.name);
            return pTone === cleanTone || pTone.endsWith(cleanTone) || pTone.includes(cleanTone);
        });
        if (matched) {
            cleanVal = matched.name;
        }
    }

    if (input) input.value = cleanVal;
    if (display) {
        display.innerText = cleanVal ? cleanVal : 'Tất cả tỉnh thành';
        if (cleanVal) {
            display.classList.add('text-brand-700', 'font-bold');
            display.classList.remove('text-slate-700');
        } else {
            display.classList.remove('text-brand-700', 'font-bold');
            display.classList.add('text-slate-700');
        }
    }
    if (clearBtn) {
        if (cleanVal) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }

    renderProvincesList('');
}

function clearSelectedLocation(event) {
    if (event) event.stopPropagation();
    selectLocationProvince('');
}

document.addEventListener('DOMContentLoaded', function() {
    loadProvincesFromApi();

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('location-select-wrapper');
        const panel = document.getElementById('location-dropdown-panel');
        if (wrapper && panel && !wrapper.contains(e.target)) {
            closeLocationDropdown();
        }
    });

    // Enter key in province search box selects first match
    const searchInput = document.getElementById('location-search-input');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstItem = document.querySelector('#location-provinces-list > div');
                if (firstItem) {
                    firstItem.click();
                }
            } else if (e.key === 'Escape') {
                closeLocationDropdown();
            }
        });
    }
});

