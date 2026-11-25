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
    const categoryHelp = document.querySelector('#complaint-category-help');
    const categoryData = document.querySelector('#complaint-category-data');

    if (departmentSelect && categorySelect && categoryData) {
        let categoriesByDepartment = {};
        try {
            categoriesByDepartment = JSON.parse(categoryData.textContent || '{}');
        } catch (error) {
            console.error('Failed to decode category list', error);
            if (categoryHelp) categoryHelp.textContent = 'Categories unavailable; reload this page.';
        }

        // All active categories are embedded by PHP from the same database.
        // Changing departments never needs a separate fetch or stale BASE_URL.
        function refreshCategories(preserveSelected = false) {
            const departmentId = String(departmentSelect.value || '');
            const selectedId = preserveSelected
                ? String(categorySelect.value || categorySelect.dataset.selected || '') : '';
            const list = Object.prototype.hasOwnProperty.call(categoriesByDepartment, departmentId)
                && Array.isArray(categoriesByDepartment[departmentId])
                ? categoriesByDepartment[departmentId] : [];

            categorySelect.replaceChildren();
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select complaint category';
            categorySelect.appendChild(placeholder);

            list.forEach((item) => {
                const option = document.createElement('option');
                option.value = String(item.id);
                option.textContent = String(item.category_name) + ' (' + item.sla_days + ' days)';
                categorySelect.appendChild(option);
            });

            categorySelect.value = selectedId && list.some((item) => String(item.id) === selectedId)
                ? selectedId : '';
            categorySelect.disabled = departmentId === '';
            if (categoryHelp) {
                categoryHelp.textContent = !departmentId
                    ? 'Select a department to display its complaint categories.'
                    : list.length
                        ? list.length + ' categories available for this department.'
                        : 'No active categories found for this department in the database.';
            }
        }
        departmentSelect.addEventListener('change', () => refreshCategories(false));
        refreshCategories(true);
    }

    const streetInput = document.querySelector('#street_name');
    const streetResults = document.querySelector('#street_results');

    if (streetInput && streetResults && !document.getElementById('use-complaint-location')) {
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
                            if (streetInput) streetInput.value = row.street_name;
                            const stId = document.querySelector('#street_id');
                            if (stId) stId.value = row.id;
                            const wId = document.querySelector('#ward_id');
                            if (wId) wId.value = row.ward_id;
                            const zId = document.querySelector('#zone_id');
                            if (zId) zId.value = row.zone_id;
                            const wDisp = document.querySelector('#ward_display');
                            if (wDisp) wDisp.value = row.ward_no;
                            const zDisp = document.querySelector('#zone_display');
                            if (zDisp) zDisp.value = row.zone_no;
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
