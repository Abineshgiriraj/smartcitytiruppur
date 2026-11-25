/**
 * Smart City Tiruppur - GPS Ward/Zone Resolver, Voice Input & Multi-File Controller
 * Features:
 * 1. Automatic GPS detection on load -> directly fetches Ward & Zone (no table suggestions)
 * 2. Manual GPS trigger & Ward selector
 * 3. Voice Input & Recording modal with English (India) & Tamil (தமிழ்)
 * 4. Multi-file upload dropzone with previews and delete support
 */
(() => {
    'use strict';

    const baseUrl = window.APP_BASE_URL || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // =========================================================================
    // 1. Geolocation & Direct Ward/Zone Resolution
    // =========================================================================
    const btnDetect = document.getElementById('use-complaint-location');
    const statusBox = document.getElementById('complaint-location-status');
    const locationInput = document.getElementById('location_name');
    const wardSelect = document.getElementById('ward_id');
    const zoneSelect = document.getElementById('zone_id');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    // Ray-casting Point In Polygon matching for official Tiruppur Corporation GIS Boundaries
    function pointInPolygon(point, vs) {
        const x = point[0], y = point[1];
        let inside = false;
        for (let i = 0, j = vs.length - 1; i < vs.length; j = i++) {
            const xi = vs[i][0], yi = vs[i][1];
            const xj = vs[j][0], yj = vs[j][1];
            const intersect = ((yi > y) !== (yj > y))
                && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    }

    function findGisWard(lat, lon) {
        if (typeof WARD_GEOJSON === 'undefined' || !WARD_GEOJSON.features) return null;
        for (const f of WARD_GEOJSON.features) {
            if (f.bbox && (lon < f.bbox[0] || lon > f.bbox[2] || lat < f.bbox[1] || lat > f.bbox[3])) {
                continue;
            }
            const geom = f.geometry;
            if (!geom) continue;
            const coords = geom.coordinates;
            if (geom.type === 'Polygon' && coords && coords[0]) {
                if (pointInPolygon([lon, lat], coords[0])) {
                    return { wardNum: parseInt(f.properties.Ward_Numbe || f.properties.Name, 10), zoneNum: parseInt(f.properties.Zone, 10) };
                }
            } else if (geom.type === 'MultiPolygon' && coords) {
                for (const poly of coords) {
                    if (poly && poly[0] && pointInPolygon([lon, lat], poly[0])) {
                        return { wardNum: parseInt(f.properties.Ward_Numbe || f.properties.Name, 10), zoneNum: parseInt(f.properties.Zone, 10) };
                    }
                }
            }
        }
        return null;
    }

    function setStatus(html, type = 'info') {
        if (!statusBox) return;
        statusBox.className = `alert alert-${type} py-2 px-3 small d-flex align-items-center gap-2 mb-3 shadow-sm`;
        statusBox.innerHTML = html;
        statusBox.style.display = 'flex';
    }

    function applyWardAndZone(wardId, zoneId) {
        if (wardSelect) wardSelect.value = wardId;
        if (zoneSelect) zoneSelect.value = zoneId;
    }

    async function detectLocation(isAuto = false) {
        if (!navigator.geolocation) {
            if (!isAuto) setStatus('<i class="bi bi-exclamation-circle text-warning"></i> Geolocation is not supported by your browser.', 'warning');
            return;
        }

        if (btnDetect) {
            btnDetect.disabled = true;
            btnDetect.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Capturing GPS...';
        }

        if (!isAuto) {
            setStatus('<span class="spinner-border spinner-border-sm text-primary me-1"></span> Capturing GPS location...', 'info');
        }

        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                const coords = pos.coords;
                if (latInput) latInput.value = coords.latitude.toFixed(7);
                if (lngInput) lngInput.value = coords.longitude.toFixed(7);

                // Instant client-side GIS polygon resolution
                const clientGis = findGisWard(coords.latitude, coords.longitude);
                if (clientGis && wardSelect) {
                    for (let i = 0; i < wardSelect.options.length; i++) {
                        const opt = wardSelect.options[i];
                        const wNo = opt.dataset.wardNo || opt.textContent || '';
                        const m = wNo.match(/\d+/);
                        if (m && parseInt(m[0], 10) === clientGis.wardNum) {
                            wardSelect.selectedIndex = i;
                            if (zoneSelect && opt.dataset.zoneId) {
                                zoneSelect.value = opt.dataset.zoneId;
                            }
                            break;
                        }
                    }
                }

                try {
                    const formData = new FormData();
                    formData.append('latitude', coords.latitude.toString());
                    formData.append('longitude', coords.longitude.toString());
                    formData.append('accuracy', coords.accuracy.toString());
                    formData.append('csrf_token', csrfToken);

                    const res = await fetch(`${baseUrl}/ajax/street-gps.php`, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();

                    if (!data.ok) {
                        setStatus(`<i class="bi bi-info-circle text-warning"></i> ${data.message || 'Unable to resolve GPS location. Please select Ward manually.'}`, 'warning');
                        return;
                    }

                    if (data.ward_id && data.zone_id) {
                        applyWardAndZone(data.ward_id, data.zone_id);

                        // If locality was returned from GPS and location field exists
                        if (data.locality && locationInput) {
                            locationInput.value = data.locality;
                        }

                        const localityDetail = data.locality ? ` &bull; <strong>${data.locality}</strong>` : '';
                        setStatus(
                            `<i class="bi bi-geo-alt-fill text-success fs-5"></i> 
                             <div>
                                <strong>GPS Location Captured:</strong> ${coords.latitude.toFixed(5)}, ${coords.longitude.toFixed(5)} (±${Math.round(coords.accuracy)}m)
                                <div class="small text-muted mt-1">Directly matched to <strong>${data.ward_no} &bull; ${data.zone_no}</strong>${localityDetail}</div>
                             </div>`,
                            'success'
                        );
                    } else {
                        setStatus(
                            `<i class="bi bi-geo-alt text-warning fs-5"></i> 
                             <div>
                                <strong>GPS Captured:</strong> ${coords.latitude.toFixed(5)}, ${coords.longitude.toFixed(5)}
                                <div class="small text-muted mt-1">${data.message || 'Please select your Ward and Zone manually from the dropdowns.'}</div>
                             </div>`,
                            'warning'
                        );
                    }
                } catch (err) {
                    console.error('GPS resolution failed:', err);
                    setStatus('<i class="bi bi-exclamation-triangle text-danger"></i> Error resolving GPS coordinates. You can choose Ward manually.', 'danger');
                } finally {
                    if (btnDetect) {
                        btnDetect.disabled = false;
                        btnDetect.innerHTML = '<i class="bi bi-crosshair me-1"></i> Capture Location';
                    }
                }
            },
            (err) => {
                if (btnDetect) {
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = '<i class="bi bi-crosshair me-1"></i> Capture Location';
                }
                if (!isAuto) {
                    const msg = err.code === 1 ? 'Location access was not granted. Please select your Ward manually.' : 'Unable to retrieve location. Please select Ward manually.';
                    setStatus(`<i class="bi bi-info-circle text-warning"></i> ${msg}`, 'warning');
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
        );
    }

    if (btnDetect) {
        btnDetect.addEventListener('click', () => detectLocation(false));
    }

    // Ward dropdown manual change -> auto-update Zone dropdown
    if (wardSelect) {
        wardSelect.addEventListener('change', () => {
            const opt = wardSelect.options[wardSelect.selectedIndex];
            if (opt && opt.dataset.zoneId) {
                if (zoneSelect) zoneSelect.value = opt.dataset.zoneId;
            }
        });
    }

    // Zone dropdown manual change -> filter / adjust Ward dropdown
    if (zoneSelect) {
        zoneSelect.addEventListener('change', () => {
            const selectedZoneId = zoneSelect.value;
            if (!wardSelect) return;

            const optgroups = wardSelect.querySelectorAll('optgroup');
            if (optgroups.length > 0) {
                optgroups.forEach((group) => {
                    if (!selectedZoneId || group.dataset.zoneId === selectedZoneId) {
                        group.style.display = '';
                    } else {
                        group.style.display = 'none';
                    }
                });
            }

            // If current selected ward doesn't belong to the selected zone, choose the first matching ward or reset
            const currentOpt = wardSelect.options[wardSelect.selectedIndex];
            if (selectedZoneId && currentOpt && currentOpt.dataset.zoneId && currentOpt.dataset.zoneId !== selectedZoneId) {
                let found = false;
                for (let i = 0; i < wardSelect.options.length; i++) {
                    const opt = wardSelect.options[i];
                    if (opt.dataset.zoneId === selectedZoneId) {
                        wardSelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    wardSelect.value = '';
                }
            }
        });
    }

    // =========================================================================
    // 2. Voice Input & Recording (English & Tamil)
    // =========================================================================
    const voiceModalEl = document.getElementById('voiceInputModal');
    const voiceLangSelect = document.getElementById('voiceLangSelect');
    const voiceStatusText = document.getElementById('voiceStatusText');
    const voiceStatusBadge = document.getElementById('voiceStatusBadge');
    const voiceTranscribedText = document.getElementById('voiceTranscribedText');
    const btnReRecord = document.getElementById('btnVoiceReRecord');
    const btnUseText = document.getElementById('btnVoiceUseText');
    const btnSaveRecording = document.getElementById('btnVoiceSaveRecording');
    const complaintDescTextarea = document.getElementById('complaint_description');

    let recognition = null;
    let isRecognizing = false;
    let currentFinalTranscript = '';

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    function initSpeechRecognition() {
        if (!SpeechRecognition) {
            if (voiceStatusText) {
                voiceStatusText.textContent = 'Speech recognition not supported in this browser. Please type directly or use Chrome / Edge.';
            }
            if (voiceStatusBadge) {
                voiceStatusBadge.className = 'badge bg-warning text-dark';
                voiceStatusBadge.textContent = 'Not Supported';
            }
            return null;
        }

        const recognizer = new SpeechRecognition();
        recognizer.continuous = true;
        recognizer.interimResults = true;
        recognizer.maxAlternatives = 1;

        recognizer.onstart = () => {
            isRecognizing = true;
            if (voiceStatusBadge) {
                voiceStatusBadge.className = 'badge bg-danger animate-pulse';
                voiceStatusBadge.innerHTML = '<i class="bi bi-record-circle-fill me-1"></i>Listening...';
            }
            if (voiceStatusText) {
                voiceStatusText.textContent = 'Listening... Speak into your microphone.';
            }
        };

        recognizer.onresult = (event) => {
            let interim = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const res = event.results[i];
                if (res.isFinal) {
                    currentFinalTranscript += (currentFinalTranscript ? ' ' : '') + res[0].transcript;
                } else {
                    interim += res[0].transcript;
                }
            }
            if (voiceTranscribedText) {
                voiceTranscribedText.value = currentFinalTranscript + (interim ? ' ' + interim : '');
            }
        };

        recognizer.onerror = (event) => {
            console.warn('Speech recognition error:', event.error);
            if (voiceStatusBadge) {
                voiceStatusBadge.className = 'badge bg-secondary';
                voiceStatusBadge.textContent = 'Paused / Idle';
            }
            if (voiceStatusText) {
                if (event.error === 'not-allowed') {
                    voiceStatusText.textContent = 'Microphone permission denied. Allow microphone access to use voice input.';
                } else if (event.error === 'no-speech') {
                    voiceStatusText.textContent = 'No speech detected. Click Re-record to try again.';
                } else {
                    voiceStatusText.textContent = 'Listening ended. Click Re-record to start speaking again.';
                }
            }
            isRecognizing = false;
        };

        recognizer.onend = () => {
            isRecognizing = false;
            if (voiceStatusBadge) {
                voiceStatusBadge.className = 'badge bg-success';
                voiceStatusBadge.textContent = 'Recorded';
            }
        };

        return recognizer;
    }

    function startListening() {
        if (!SpeechRecognition) return;
        if (!recognition) recognition = initSpeechRecognition();
        if (!recognition) return;

        const lang = voiceLangSelect ? voiceLangSelect.value : 'en-IN';
        recognition.lang = lang;

        try {
            if (isRecognizing) {
                recognition.stop();
            }
            recognition.start();
        } catch (e) {
            console.error('Recognition start error:', e);
        }
    }

    function stopListening() {
        if (recognition && isRecognizing) {
            try {
                recognition.stop();
            } catch (e) {}
            isRecognizing = false;
        }
    }

    if (voiceModalEl) {
        voiceModalEl.addEventListener('shown.bs.modal', () => {
            currentFinalTranscript = voiceTranscribedText?.value?.trim() || '';
            startListening();
        });

        voiceModalEl.addEventListener('hidden.bs.modal', () => {
            stopListening();
        });
    }

    if (voiceLangSelect) {
        voiceLangSelect.addEventListener('change', () => {
            stopListening();
            startListening();
        });
    }

    if (btnReRecord) {
        btnReRecord.addEventListener('click', () => {
            currentFinalTranscript = '';
            if (voiceTranscribedText) voiceTranscribedText.value = '';
            stopListening();
            startListening();
        });
    }

    if (btnUseText) {
        btnUseText.addEventListener('click', () => {
            stopListening();
            const text = voiceTranscribedText ? voiceTranscribedText.value.trim() : '';
            if (text && complaintDescTextarea) {
                const existing = complaintDescTextarea.value.trim();
                complaintDescTextarea.value = existing ? existing + '\n' + text : text;
            }
            if (window.bootstrap && voiceModalEl) {
                const modal = bootstrap.Modal.getInstance(voiceModalEl);
                modal?.hide();
            }
        });
    }

    if (btnSaveRecording) {
        btnSaveRecording.addEventListener('click', () => {
            btnUseText?.click();
        });
    }

    // =========================================================================
    // 3. Single Multi-File Upload Dropzone
    // =========================================================================
    const dropzone = document.getElementById('dropzoneContainer');
    const fileInput = document.getElementById('complaint_attachments');
    const browseBtn = document.getElementById('browseFilesBtn');
    const fileListPreview = document.getElementById('fileListPreview');

    const MAX_FILES = 5;
    const MAX_BYTES = 5 * 1024 * 1024; // 5 MB
    const ALLOWED_EXTS = ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx', 'xls', 'xlsx'];

    let currentFilesDataTransfer = new DataTransfer();

    if (dropzone && fileInput) {
        browseBtn?.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('click', (e) => {
            if (e.target !== browseBtn && !e.target.closest('button')) {
                fileInput.click();
            }
        });

        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-primary', 'bg-white');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-primary', 'bg-white');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                handleNewFiles(dt.files);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files.length > 0) {
                handleNewFiles(fileInput.files);
            }
        });
    }

    function handleNewFiles(incomingFiles) {
        let errors = [];

        Array.from(incomingFiles).forEach(file => {
            if (currentFilesDataTransfer.items.length >= MAX_FILES) {
                errors.push(`Maximum ${MAX_FILES} attachments allowed.`);
                return;
            }

            const ext = file.name.split('.').pop()?.toLowerCase() || '';
            if (!ALLOWED_EXTS.includes(ext)) {
                errors.push(`'${file.name}' has an unsupported file format (.${ext}).`);
                return;
            }

            if (file.size > MAX_BYTES) {
                errors.push(`'${file.name}' exceeds the 5 MB limit (${formatBytes(file.size)}).`);
                return;
            }

            let alreadyExists = false;
            for (let i = 0; i < currentFilesDataTransfer.files.length; i++) {
                const existing = currentFilesDataTransfer.files[i];
                if (existing.name === file.name && existing.size === file.size) {
                    alreadyExists = true;
                    break;
                }
            }

            if (!alreadyExists) {
                currentFilesDataTransfer.items.add(file);
            }
        });

        if (errors.length > 0) {
            alert(errors.join('\n'));
        }

        syncFileInput();
        renderFilePreviews();
    }

    function removeFile(index) {
        const newDt = new DataTransfer();
        for (let i = 0; i < currentFilesDataTransfer.files.length; i++) {
            if (i !== index) {
                newDt.items.add(currentFilesDataTransfer.files[i]);
            }
        }
        currentFilesDataTransfer = newDt;
        syncFileInput();
        renderFilePreviews();
    }

    function syncFileInput() {
        if (fileInput) {
            fileInput.files = currentFilesDataTransfer.files;
        }
    }

    function renderFilePreviews() {
        if (!fileListPreview) return;
        const files = currentFilesDataTransfer.files;

        if (files.length === 0) {
            fileListPreview.innerHTML = '<div class="text-muted small text-center py-2"><i class="bi bi-paperclip me-1"></i>No attachments selected</div>';
            return;
        }

        let html = `<div class="card border shadow-sm">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <span class="small fw-bold"><i class="bi bi-files me-1"></i>Selected Attachments (${files.length} / ${MAX_FILES})</span>
                <span class="badge bg-primary text-white">${formatBytes(Array.from(files).reduce((acc, f) => acc + f.size, 0))}</span>
            </div>
            <ul class="list-group list-group-flush">`;

        for (let i = 0; i < files.length; i++) {
            const f = files[i];
            const ext = f.name.split('.').pop()?.toLowerCase() || '';
            const icon = getFileIcon(ext);

            html += `<li class="list-group-item d-flex align-items-center justify-content-between py-2">
                <div class="d-flex align-items-center gap-2 text-truncate me-2">
                    <span class="fs-4">${icon}</span>
                    <div class="text-truncate">
                        <div class="fw-semibold small text-truncate" title="${escapeHtml(f.name)}">${escapeHtml(f.name)}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">${formatBytes(f.size)} &bull; .${ext.toUpperCase()}</div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle" style="width: 28px; height: 28px; line-height: 1;" onclick="window.removeAttachmentFile(${i})" title="Remove file">
                    <i class="bi bi-x fs-6"></i>
                </button>
            </li>`;
        }

        html += `</ul></div>`;
        fileListPreview.innerHTML = html;
    }

    window.removeAttachmentFile = removeFile;

    function getFileIcon(ext) {
        switch (ext) {
            case 'pdf':
                return '<i class="bi bi-file-earmark-pdf-fill text-danger"></i>';
            case 'doc':
            case 'docx':
                return '<i class="bi bi-file-earmark-word-fill text-primary"></i>';
            case 'xls':
            case 'xlsx':
                return '<i class="bi bi-file-earmark-excel-fill text-success"></i>';
            case 'jpg':
            case 'jpeg':
            case 'png':
                return '<i class="bi bi-file-earmark-image-fill text-info"></i>';
            default:
                return '<i class="bi bi-file-earmark-fill text-secondary"></i>';
        }
    }

    function formatBytes(bytes, decimals = 1) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // =========================================================================
    // 4. Auto-Detect Location on Page Load
    // =========================================================================
    document.addEventListener('DOMContentLoaded', () => {
        // If ward is not yet selected, automatically trigger GPS capture
        const currentWard = wardIdInput?.value;
        if (!currentWard || currentWard === '0') {
            detectLocation(true);
        }
    });
})();
