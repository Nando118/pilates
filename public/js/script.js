$(document).ready(function () {
    $("#form_input").submit(function (e) {
        // Menonaktifkan tombol submit
        $("#btn_submit").prop("disabled", true);

        // Melakukan pengiriman form secara manual
        // Anda bisa menggunakan AJAX atau langsung submit form
        // Di sini saya menggunakan submit() untuk mengirim form secara langsung
        this.submit();
    });
});