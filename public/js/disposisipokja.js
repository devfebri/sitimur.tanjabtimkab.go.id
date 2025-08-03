// Disposisi ke Pokja Pemilihan
$("#disposisiPokja").on("click", function (e) {
    $("#disposisiModal").modal("show");
    loadPokjaList();
});
let pokjaDataCache = [];
let pokjaSelected = [];
let pokjaPage = 1;
const pokjaPerPage = 10;

function loadPokjaList() {
    $("#pokja-list").html('<tr><td colspan="4">Memuat data...</td></tr>');
    $.ajax({
        url: window.routeGetPokja,
        type: "GET",
        dataType: "json",
        success: function (res) {
            pokjaDataCache = res;
            pokjaPage = 1;
            renderPokjaTable(res);
        },
        error: function () {
            $("#pokja-list").html(
                '<tr><td colspan="4">Gagal mengambil data Pokja.</td></tr>'
            );
        },
    });
}

function renderPokjaTable(data) {
    // Urutkan: yang dipilih di atas
    let selectedIds = pokjaSelected;
    let selected = data.filter((item) =>
        selectedIds.includes(item.id.toString())
    );
    let unselected = data.filter(
        (item) => !selectedIds.includes(item.id.toString())
    );
    let allData = [...selected, ...unselected];

    // Paging
    let total = allData.length;
    let totalPages = Math.ceil(total / pokjaPerPage);
    if (pokjaPage > totalPages) pokjaPage = totalPages || 1;
    let start = (pokjaPage - 1) * pokjaPerPage;
    let end = start + pokjaPerPage;
    let pageData = allData.slice(start, end);

    // Render table
    let html = "";
    if (pageData.length > 0) {
        $.each(pageData, function (index, item) {
            let checked = pokjaSelected.includes(item.id.toString())
                ? "checked"
                : "";
            html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="pokja-checkbox" value="${
                                item.id
                            }" ${checked}>
                        </td>
                        <td>${item.nip ?? "-"}</td>
                        <td class="text-left">${item.name}</td>
                        <td>${item.jabatan ?? "-"}</td>
                    </tr>
                `;
        });
    } else {
        html = '<tr><td colspan="4">Tidak ada data Pokja tersedia.</td></tr>';
    }
    $("#pokja-list").html(html);

    // Render pagination
    let pagiHtml = "";
    if (totalPages > 1) {
        pagiHtml += `<nav><ul class="pagination justify-content-center">`;
        for (let i = 1; i <= totalPages; i++) {
            pagiHtml += `<li class="page-item ${
                i === pokjaPage ? "active" : ""
            }">
                    <a href="#" class="page-link pokja-page-link" data-page="${i}">${i}</a>
                </li>`;
        }
        pagiHtml += `</ul></nav>`;
    }
    $("#pokja-pagination").html(pagiHtml);

    $("#selected-count").text(pokjaSelected.length);
}

// Fitur pencarian Pokja di modal
$(document).on("input", "#search-pokja", function () {
    let keyword = $(this).val().toLowerCase();
    let filtered = pokjaDataCache.filter(function (item) {
        return (
            (item.name && item.name.toLowerCase().includes(keyword)) ||
            (item.nip && item.nip.toLowerCase().includes(keyword)) ||
            (item.jabatan && item.jabatan.toLowerCase().includes(keyword))
        );
    });
    pokjaPage = 1;
    renderPokjaTable(filtered);
});

// Simpan pilihan pokja
$(document).on("change", ".pokja-checkbox", function () {
    const max = 3;
    const min = 3;
    let val = $(this).val();
    if ($(this).is(":checked")) {
        if (pokjaSelected.length >= max) {
            $(this).prop("checked", false);
            $("#pokja-alert")
                .removeClass("d-none")
                .text("Maksimal hanya boleh memilih 3 Pokja.");
            setTimeout(() => $("#pokja-alert").addClass("d-none"), 2000);
            return;
        }
        // if( pokjaSelected.length < min && pokjaSelected.length > 0) {
        //      $(this).prop("checked", false);
        //     $("#pokja-alert")
        //         .removeClass("d-none")
        //         .text("Minimal harus memilih 3 Pokja.");
        //     setTimeout(() => $("#pokja-alert").addClass("d-none"), 2000);
        //     return;
        // }
        if (!pokjaSelected.includes(val)) pokjaSelected.push(val);
    } else {
        pokjaSelected = pokjaSelected.filter((id) => id !== val);
    }
    renderPokjaTable(pokjaDataCache);
});

// Paging klik
$(document).on("click", ".pokja-page-link", function (e) {
    e.preventDefault();
    pokjaPage = parseInt($(this).data("page"));
    renderPokjaTable(pokjaDataCache);
});

$("#form-disposisi-pokja").on("submit", function (e) {
    e.preventDefault();
    let selected = $(".pokja-checkbox:checked")
        .map(function () {
            return $(this).val();
        })
        .get();

    $.ajax({
        url: window.routeKirimPokja,
        type: "POST",
        data: {
            _token: window.csrfToken,
            pokja_ids: selected,
            pengajuan_id: window.pengajuanId,
        },
        success: function (res) {
            alertify.success("Disposisi berhasil dikirim.");
            $("#disposisiModal").modal("hide");
            window.location.reload();
        },
        error: function (xhr) {
            let msg = "Terjadi kesalahan saat mengirim disposisi.";
            if (xhr.responseJSON && xhr.responseJSON.message)
                msg = xhr.responseJSON.message;
            $("#pokja-alert").removeClass("d-none").text(msg);
        },
    });
});
