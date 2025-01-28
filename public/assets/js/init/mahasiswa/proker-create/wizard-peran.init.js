$(document).ready(function () {
    let allRoles = [];
    $(".select_peran").each(function () {
        let $select = $(this);

        // Initialize selectize
        $select.selectize({
            persist: false,
            createOnBlur: true,
            create: true,
            sortField: "text",
            placeholder: "Pilih atau tambah peran baru",
            options: allRoles.map((role) => ({ value: role, text: role })),
        });
    });
});
