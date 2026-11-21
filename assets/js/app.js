(() => {
    'use strict';

    const baseUrl = window.APP_BASE_URL || '';

    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            if (!window.confirm(element.dataset.confirm || 'Are you sure?')) {
                event.preventDefault();
            }
        });
    });


    const departmentSelect = document.querySelector('[data-category-source]');
    const categorySelect = document.querySelector('#complaint_category_id');
    const categorySearch = document.querySelector('#complaint_category_search');
    const categoryCount = document.querySelector('#complaint_category_count');
    if (departmentSelect && categorySelect) {
        const previouslySelected = String(categorySelect.dataset.selected || '');
        let allCategories = [];
        let loading = true;
        function renderCategories(query = '', keepSelected = '') {
            const term = query.trim().toLocaleLowerCase();
            const original = String(keepSelected || categorySelect.value || '');
            const preferredDepartment = String(departmentSelect.value || '');
            const matches = allCategories.filter((category) =>
                (String(category.category_name) + ' ' + String(category.department_name)).toLocaleLowerCase().includes(term)
            );
            matches.sort((left, right) => {
                const leftRank = String(left.department_id) === preferredDepartment ? 0 : 1;
                const rightRank = String(right.department_id) === preferredDepartment ? 0 : 1;
                if (leftRank !== rightRank) return leftRank - rightRank;
                return String(left.department_name).localeCompare(String(right.department_name)) ||
                    String(left.category_name).localeCompare(String(right.category_name));
            });
            categorySelect.replaceChildren();
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = loading ? 'Loading complaint categories...' :
                matches.length ? 'Select complaint category (all departments)' : 'No matching complaint categories';
            categorySelect.appendChild(placeholder);
            const grouped = new Map();
            for (const category of matches) {
                const deptId = String(category.department_id);
                let group = grouped.get(deptId);
                if (!group) {
                    group = document.createElement('optgroup');
                    group.label = String(category.department_name);
                    categorySelect.appendChild(group);
                    grouped.set(deptId, group);
                }
                const option = document.createElement('option');
                option.value = String(category.id);
                option.textContent = String(category.category_name) + ' (' + category.sla_days + ' days)';
                group.appendChild(option);
            }
            categorySelect.value = original && matches.some((item) => String(item.id) === original)
                ? original : '';
            if (categoryCount) categoryCount.textContent = loading ? 'Loading categories...' :
                matches.length + ' of ' + allCategories.length + ' categories across all departments';
        }
        async function loadAllCategories() {
            try {
                const response = await fetch(baseUrl + '/ajax/categories.php?all=1', { credentials: 'same-origin' });
                if (!response.ok) throw new Error('Category endpoint returned ' + response.status);
                const payload = await response.json();
                if (!Array.isArray(payload)) throw new Error('Category endpoint returned invalid data');
                allCategories = payload;
                loading = false;
                const stored = allCategories.find((item) => String(item.id) === previouslySelected);
                if (stored) departmentSelect.value = String(stored.department_id);
                renderCategories('', previouslySelected);
            } catch (error) {
                loading = false;
                console.error('Category loading failed', error);
                categorySelect.replaceChildren();
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Category list unavailable; refresh to retry';
                categorySelect.appendChild(option);
                if (categoryCount) categoryCount.textContent = 'Unable to load categories. Please retry.';
            }
        }
        departmentSelect.addEventListener('change', () => {
            if (categorySearch) categorySearch.value = '';
            renderCategories();
            categorySelect.value = '';
        });
        categorySelect.addEventListener('change', () => {
            const chosen = allCategories.find((item) => String(item.id) === categorySelect.value);
            if (!chosen) return;
            departmentSelect.value = String(chosen.department_id);
            if (categorySearch) categorySearch.value = '';
            renderCategories('', String(chosen.id));
        });
        if (categorySearch) {
            categorySearch.addEventListener('input', () => {
                renderCategories(categorySearch.value, '');
            });
        }
        renderCategories();
        loadAllCategories();
    }

    const streetInput = document.querySelector('#street_name');
    const streetResults = document.querySelector('#street_results');

    if (streetInput && streetResults) {
        let timer = null;

        streetInput.addEventListener('input', () => {
            clearTimeout(timer);
            const query = streetInput.value.trim();

            if (query.length < 2) {
                streetResults.innerHTML = '';
                streetResults.classList.add('d-none');
                return;
            }

            timer = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `${baseUrl}/ajax/street-search.php?q=${encodeURIComponent(query)}`
                    );
                    const rows = await response.json();

                    streetResults.innerHTML = '';

                    rows.forEach((row) => {
                        const item = document.createElement('div');
                        item.className = 'autocomplete-item';
                        item.textContent = `${row.street_name} — Ward ${row.ward_no}, Zone ${row.zone_no}`;
                        item.addEventListener('click', () => {
                            streetInput.value = row.street_name;
                            document.querySelector('#street_id').value = row.id;
                            document.querySelector('#ward_id').value = row.ward_id;
                            document.querySelector('#zone_id').value = row.zone_id;
                            document.querySelector('#ward_display').value = row.ward_no;
                            document.querySelector('#zone_display').value = row.zone_no;
                            streetResults.innerHTML = '';
                            streetResults.classList.add('d-none');
                        });
                        streetResults.appendChild(item);
                    });

                    streetResults.classList.toggle('d-none', rows.length === 0);
                } catch (error) {
                    console.error('Unable to load streets', error);
                }
            }, 250);
        });

        document.addEventListener('click', (event) => {
            if (!streetResults.contains(event.target) && event.target !== streetInput) {
                streetResults.classList.add('d-none');
            }
        });
    }

    document.querySelectorAll('[data-copy-address]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const communicationAddress = document.querySelector('#communication_address');
            const locationAddress = document.querySelector('#location_address');

            if (checkbox.checked && communicationAddress && locationAddress) {
                locationAddress.value = communicationAddress.value;
                locationAddress.readOnly = true;
            } else if (locationAddress) {
                locationAddress.readOnly = false;
            }
        });
    });

    async function sendOtp(mobile, purpose, button) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const form = new FormData();
        form.append('mobile', mobile);
        form.append('purpose', purpose);
        form.append('csrf_token', token);

        button.disabled = true;

        try {
            const response = await fetch(`${baseUrl}/ajax/send-otp.php`, {
                method: 'POST',
                body: form
            });
            const data = await response.json();
            alert(data.message);
            if (data.dev_otp) {
                console.info('Local development OTP:', data.dev_otp);
            }
        } catch (error) {
            alert('Unable to send OTP. Please try again.');
        } finally {
            button.disabled = false;
        }
    }

    async function verifyOtp(mobile, otp, purpose, button) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const form = new FormData();
        form.append('mobile', mobile);
        form.append('otp', otp);
        form.append('purpose', purpose);
        form.append('csrf_token', token);

        button.disabled = true;

        try {
            const response = await fetch(`${baseUrl}/ajax/verify-otp.php`, {
                method: 'POST',
                body: form
            });
            const data = await response.json();
            alert(data.message);

            if (data.ok) {
                const verifiedField = document.querySelector('#otp_verified');
                if (verifiedField) verifiedField.value = '1';
                document.querySelectorAll('[data-otp-state]').forEach((el) => {
                    el.textContent = 'Mobile verified';
                    el.className = 'text-success small fw-semibold';
                });
            }
        } catch (error) {
            alert('Unable to verify OTP. Please try again.');
        } finally {
            button.disabled = false;
        }
    }

    document.querySelectorAll('[data-send-otp]').forEach((button) => {
        button.addEventListener('click', () => {
            const mobileSelector = button.dataset.mobileTarget;
            const mobile = document.querySelector(mobileSelector)?.value.trim() || '';
            const purpose = button.dataset.purpose || 'complaint';

            if (!/^[6-9]\d{9}$/.test(mobile)) {
                alert('Enter a valid 10-digit mobile number.');
                return;
            }

            sendOtp(mobile, purpose, button);
        });
    });

    document.querySelectorAll('[data-verify-otp]').forEach((button) => {
        button.addEventListener('click', () => {
            const mobile = document.querySelector(button.dataset.mobileTarget)?.value.trim() || '';
            const otp = document.querySelector(button.dataset.otpTarget)?.value.trim() || '';
            const purpose = button.dataset.purpose || 'complaint';

            if (!/^[6-9]\d{9}$/.test(mobile) || !/^\d{6}$/.test(otp)) {
                alert('Enter a valid mobile number and 6-digit OTP.');
                return;
            }

            verifyOtp(mobile, otp, purpose, button);
        });
    });

    const statusSearchType = document.querySelectorAll('input[name="search_type"]');
    const mobileOtpArea = document.querySelector('#mobile_otp_area');

    statusSearchType.forEach((radio) => {
        radio.addEventListener('change', () => {
            if (mobileOtpArea) {
                mobileOtpArea.classList.toggle('d-none', radio.value !== 'mobile' || !radio.checked);
            }
        });
    });
})();

// Same-design public UI helpers.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.auto-dismiss-alert').forEach((alertElement) => {
        window.setTimeout(() => {
            if (window.bootstrap) {
                bootstrap.Alert.getOrCreateInstance(alertElement).close();
            }
        }, 5000);
    });

    const fileInputs = document.querySelectorAll('input[type="file"][name="attachments[]"]');
    fileInputs.forEach((input) => {
        input.addEventListener('change', () => {
            const label = input.closest('.attachment-field')?.querySelector('[data-file-name]');
            if (label) label.textContent = input.files?.[0]?.name || 'No file selected';
        });
    });
});
