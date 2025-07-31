import "./bootstrap";

import $ from "jquery";
window.$ = window.jQuery = $;

// AJAX setup for CSRF token
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
