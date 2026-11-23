document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-pagination]').forEach(function (container) {
        const itemsContainer = container.querySelector('[data-page-items]');
        const controls = container.querySelector('[data-pagination-controls]');

        if (!itemsContainer || !controls) return;

        const items = Array.from(itemsContainer.children)
            .filter(item => item.hasAttribute('data-page-item'));

        const pageSizeValue = Number.parseInt(container.dataset.pageSize, 10);
        const pageSize = Number.isInteger(pageSizeValue) && pageSizeValue > 0 ? pageSizeValue : 10;

        if (items.length <= pageSize) {
            controls.hidden = true;
            return;
        }

        let currentPage = 1;
        const totalPages = Math.ceil(items.length / pageSize);

        function createButton(label, page, active = false, disabled = false) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'table-pagination__button';
            button.textContent = label;
            button.disabled = disabled;

            if (active) {
                button.classList.add('is-active');
                button.setAttribute('aria-current', 'page');
            }

            button.addEventListener('click', function () {
                if (disabled || page === currentPage) return;
                currentPage = page;
                renderPagination();
            });

            return button;
        }

        function renderPagination() {
            const start = (currentPage - 1) * pageSize;
            const end = Math.min(start + pageSize, items.length);

            items.forEach(function (item, index) {
                item.hidden = index < start || index >= end;
            });

            const info = document.createElement('span');
            info.className = 'table-pagination__info';
            info.textContent = `Menampilkan ${start + 1}–${end} dari ${items.length} data`;

            const navigation = document.createElement('div');
            navigation.className = 'table-pagination__controls';
            navigation.appendChild(createButton('‹', currentPage - 1, false, currentPage === 1));

            const lastVisible = Math.min(currentPage + 1, totalPages);

            for (let page = currentPage; page <= lastVisible; page++) {
                navigation.appendChild(createButton(page, page, page === currentPage));
            }

            if (lastVisible < totalPages) {
                const dots = document.createElement('span');
                dots.className = 'table-pagination__ellipsis';
                dots.textContent = '...';
                navigation.appendChild(dots);
            }

            navigation.appendChild(createButton('›', currentPage + 1, false, currentPage === totalPages));
            controls.replaceChildren(info, navigation);
        }

        renderPagination();
    });
});


/* MODAL PELANGGAN */

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('customerModal');
    const form = document.getElementById('customerForm');

    if (!modal || !form) return;

    const title = document.getElementById('customerModalTitle');
    const description = document.getElementById('customerModalDescription');
    const method = document.getElementById('customerMethod');
    const customerId = document.getElementById('customerId');
    const fields = {
        nama: document.getElementById('customerName'),
        telepon: document.getElementById('customerPhone'),
        alamat: document.getElementById('customerAddress'),
        kota: document.getElementById('customerCity')
    };

    const storeUrl = form.dataset.storeUrl;
    let lastTrigger = null;

    function openModal(button = null, preserveValues = false) {
        if (!preserveValues) form.reset();

        const isEdit = button && button.dataset.updateUrl;

        if (isEdit) {
            title.textContent = 'Ubah Pelanggan';
            description.textContent = 'Perbarui informasi pelanggan.';
            form.action = button.dataset.updateUrl;
            method.disabled = false;
            method.value = 'PUT';
            customerId.value = button.dataset.customerId;

            if (!preserveValues) {
                fields.nama.value = button.dataset.name;
                fields.telepon.value = button.dataset.phone;
                fields.alamat.value = button.dataset.address;
                fields.kota.value = button.dataset.city;
            }
        } else {
            title.textContent = 'Tambah Pelanggan';
            description.textContent = 'Masukkan informasi pelanggan.';
            form.action = storeUrl;
            method.disabled = true;
            method.value = 'PUT';
            customerId.value = '';
        }

        lastTrigger = button;
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        const firstInput = form.querySelector('input:not([type="hidden"])');
        if (firstInput) firstInput.focus();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        if (lastTrigger) lastTrigger.focus();
    }

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) return;

        const openButton = event.target.closest('[data-open-customer-modal]');
        const closeButton = event.target.closest('[data-close-customer-modal]');

        if (openButton) {
            openModal(openButton);
            return;
        }

        if (closeButton || event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });

    if (modal.dataset.openOnLoad === 'true') {
        const oldId = customerId.value;
        const editButton = Array.from(document.querySelectorAll('[data-customer-id]'))
            .find(button => button.dataset.customerId === oldId);
        openModal(editButton || null, true);
    }
});


/* MODAL KURIR */

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('courierModal');
    const form = document.getElementById('courierForm');

    if (!modal || !form) return;

    const title = document.getElementById('courierModalTitle');
    const description = document.getElementById('courierModalDescription');
    const note = document.getElementById('courierModalNote');
    const method = document.getElementById('courierMethod');
    const courierId = document.getElementById('courierId');
    const fields = {
        nama: document.getElementById('courierName'),
        telepon: document.getElementById('courierPhone')
    };
    let lastTrigger = null;

    function openCourierModal(button = null, preserveValues = false) {
        if (!preserveValues) form.reset();

        const isEdit = button && button.dataset.updateUrl;

        if (isEdit) {
            title.textContent = 'Ubah Kurir';
            description.textContent = 'Perbarui informasi kurir.';
            if (note) note.hidden = true;
            form.action = button.dataset.updateUrl;
            method.disabled = false;
            method.value = 'PUT';
            courierId.value = button.dataset.kurirId;

            if (!preserveValues) {
                fields.nama.value = button.dataset.name;
                fields.telepon.value = button.dataset.phone;
            }
        } else {
            title.textContent = 'Tambah Kurir';
            description.textContent = 'Informasi kurir';
            if (note) note.hidden = false;
            form.action = form.dataset.storeUrl;
            method.disabled = true;
            courierId.value = '';
        }

        lastTrigger = button;
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        const firstInput = form.querySelector('input:not([type="hidden"])');
        if (firstInput) firstInput.focus();
    }

    function closeCourierModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        if (lastTrigger) lastTrigger.focus();
    }

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) return;

        const openButton = event.target.closest('[data-open-courier-modal]');
        const closeButton = event.target.closest('[data-close-courier-modal]');

        if (openButton) {
            openCourierModal(openButton);
            return;
        }

        if (closeButton || event.target === modal) closeCourierModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeCourierModal();
    });

    if (modal.dataset.openOnLoad === 'true') {
        const oldId = courierId.value;
        const editButton = oldId
            ? Array.from(document.querySelectorAll('[data-kurir-id]')).find(button => button.dataset.kurirId === oldId)
            : null;
        openCourierModal(editButton || null, true);
    }
});


/* MODAL TARIF */

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('tarifModal');
    const form = document.getElementById('tarifForm');

    if (!modal || !form) return;

    const title = document.getElementById('tarifModalTitle');
    const description = document.getElementById('tarifModalDescription');
    const zona = document.getElementById('tarifZona');
    const tarif = document.getElementById('tarifPerKg');
    let lastTrigger = null;

    function openTarifModal(button, preserveValues = false) {
        if (!preserveValues) {
            form.reset();
            zona.value = button.dataset.tarifZona;
            tarif.value = button.dataset.tarif;
        }

        title.textContent = 'Ubah Tarif';
        description.textContent = 'Zona ' + button.dataset.tarifZona + ' - ' + button.dataset.namaZona;
        form.action = button.dataset.updateUrl;
        lastTrigger = button;
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        tarif.focus();
    }

    function closeTarifModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        if (lastTrigger) lastTrigger.focus();
    }

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) return;

        const openButton = event.target.closest('[data-open-tarif-modal]');
        const closeButton = event.target.closest('[data-close-tarif-modal]');

        if (openButton) {
            openTarifModal(openButton);
            return;
        }

        if (closeButton || event.target === modal) closeTarifModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeTarifModal();
    });

    if (modal.dataset.openOnLoad === 'true') {
        const oldZona = zona.value;
        const editButton = Array.from(document.querySelectorAll('[data-tarif-zona]'))
            .find(button => button.dataset.tarifZona === oldZona);
        if (editButton) openTarifModal(editButton, true);
    }
});


/* MODAL FILTER LAPORAN */

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('reportFilterModal');
    const form = document.getElementById('reportFilterForm');

    if (!modal || !form) return;

    let lastTrigger = null;

    function openReportModal(button = null) {
        lastTrigger = button;
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        const firstInput = form.querySelector('input');
        if (firstInput) firstInput.focus();
    }

    function closeReportModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        if (lastTrigger) lastTrigger.focus();
    }

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) return;

        const openButton = event.target.closest('[data-open-report-modal]');
        const closeButton = event.target.closest('[data-close-report-modal]');

        if (openButton) {
            openReportModal(openButton);
            return;
        }

        if (closeButton || event.target === modal) closeReportModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeReportModal();
    });

    if (modal.dataset.openOnLoad === 'true') openReportModal();
});


/* FORM COD PAKET */

document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('cekCod');
    const field = document.getElementById('kolomNilaiCod');

    if (!checkbox || !field) return;

    function aturKolomCod() {
        field.hidden = !checkbox.checked;
    }

    checkbox.addEventListener('change', aturKolomCod);
    aturKolomCod();
});


/* MODAL TINDAKAN PAKET */

document.addEventListener('DOMContentLoaded', function () {
    let activeModal = null;
    let lastTrigger = null;

    function openModal(modal, trigger = null) {
        if (!modal) return;

        activeModal = modal;
        lastTrigger = trigger;
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        const firstInput = modal.querySelector('select, input:not([type="hidden"])');
        if (firstInput) firstInput.focus();
    }

    function closeModal(modal) {
        if (!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        if (lastTrigger) lastTrigger.focus();
        activeModal = null;
        lastTrigger = null;
    }

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) return;

        const openButton = event.target.closest('[data-open-package-modal]');
        const closeButton = event.target.closest('[data-close-package-modal]');

        if (openButton) {
            const modalId = openButton.getAttribute('data-open-package-modal');
            openModal(document.getElementById(modalId), openButton);
            return;
        }

        if (closeButton) {
            closeModal(closeButton.closest('[data-package-action-modal]'));
            return;
        }

        if (activeModal && event.target === activeModal) closeModal(activeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && activeModal) closeModal(activeModal);
    });
});


/* MODAL INPUT PAKET */

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('packageCreateModal');
    const form = document.getElementById('packageCreateForm');
    const customerSelect = document.getElementById('idPelanggan');

    if (!modal || !form || !customerSelect) return;

    const formUrl = new URL(modal.dataset.formUrl, window.location.href);
    let customersLoaded = false;
    let loadingPromise = null;
    let lastTrigger = null;

    function loadCustomers() {
        if (customersLoaded) return Promise.resolve();
        if (loadingPromise) return loadingPromise;

        customerSelect.disabled = true;
        customerSelect.innerHTML = '<option value="">Memuat pelanggan...</option>';

        loadingPromise = fetch(formUrl.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => {
                if (!response.ok) throw new Error('Gagal mengambil data pelanggan.');
                return response.text();
            })
            .then(html => {
                const page = new DOMParser().parseFromString(html, 'text/html');
                const sourceSelect = page.getElementById('idPelanggan');

                if (!sourceSelect) throw new Error('Pilihan pelanggan tidak ditemukan.');

                customerSelect.innerHTML = sourceSelect.innerHTML;
                customerSelect.value = customerSelect.dataset.selected || '';
                customersLoaded = true;
            })
            .catch(error => {
                console.error('Input Paket:', error);
                customerSelect.innerHTML = '<option value="">Gagal memuat pelanggan. Muat ulang halaman.</option>';
            })
            .finally(() => {
                customerSelect.disabled = false;
                loadingPromise = null;
            });

        return loadingPromise;
    }

    function openModal(button = null, preserveValues = false) {
        if (!preserveValues) {
            form.reset();
            if (customersLoaded) customerSelect.value = customerSelect.dataset.selected || '';
        }

        lastTrigger = button;
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        loadCustomers().then(() => {
            if (modal.classList.contains('is-open')) customerSelect.focus();
        });
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        if (lastTrigger) lastTrigger.focus();
    }

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) return;

        const openButton = event.target.closest('[data-open-package-create]');
        const closeButton = event.target.closest('[data-close-package-create]');

        if (openButton) {
            openModal(openButton);
            return;
        }

        if (closeButton || event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });

    if (modal.dataset.openOnLoad === 'true') openModal(null, true);
});
