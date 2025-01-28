$(document).ready(function () {
    // Validasi saat mengetik di new_password
    $("#new_password").on("input", function () {
        validateNewPassword();
    });

    // Validasi saat mengetik di confirm_password
    $("#confirm_password").on("input", function () {
        validateConfirmPassword();
    });

    // Fungsi validasi new_password
    function validateNewPassword() {
        var newPassword = $("#new_password").val();
        var newPasswordError = $("#new-password-error");

        // Aturan password: minimal 8 karakter, 1 huruf kapital, 1 huruf kecil, 1 angka, 1 karakter khusus
        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        if (!passwordRegex.test(newPassword)) {
            newPasswordError.text(
                "Password harus minimal 8 karakter, mengandung minimal 1 huruf kapital, 1 huruf kecil, 1 angka, dan 1 karakter khusus."
            );
        } else {
            newPasswordError.text("");
        }

        // Validasi konfirmasi password ulang jika password baru diubah
        validateConfirmPassword();
    }

    // Fungsi validasi confirm_password
    function validateConfirmPassword() {
        var newPassword = $("#new_password").val();
        var confirmPassword = $("#confirm_password").val();
        var confirmPasswordError = $("#confirm-password-error");

        if (confirmPassword !== newPassword) {
            confirmPasswordError.text(
                "Konfirmasi password tidak cocok dengan password baru."
            );
        } else {
            confirmPasswordError.text("");
        }
    }

    // Mencegah form submit jika ada error
    $("#passwordForm").on("submit", function (event) {
        var newPasswordError = $("#new-password-error").text();
        var confirmPasswordError = $("#confirm-password-error").text();

        if (newPasswordError || confirmPasswordError) {
            event.preventDefault(); // Mencegah form submit jika ada error
            alert("Silakan perbaiki kesalahan sebelum mengirimkan form.");
        }
    });
});
