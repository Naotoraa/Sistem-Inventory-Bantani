 document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Tom Select untuk #id_barang
        const idBarang = new TomSelect("#id_barang", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "-- Pilih ID Barang --",
            onInitialize: function() {
                const wrapper = this.wrapper; 
                const control = wrapper.querySelector('.ts-control');
                const icon = document.createElement('i');
                icon.className = 'fas fa-barcode icon';
                control.insertAdjacentElement('afterbegin', icon);
                control.style.paddingLeft = '42px';
            }
        });

        const nameSelect = new TomSelect("#name", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "-- Pilih Nama Barang --",
            onInitialize: function() {
                const wrapper = this.wrapper;
                const control = wrapper.querySelector('.ts-control');
                const icon = document.createElement('i');
                icon.className = 'fas fa-box icon';
                control.insertAdjacentElement('afterbegin', icon);
                control.style.paddingLeft = '42px';
            }
        });
    });