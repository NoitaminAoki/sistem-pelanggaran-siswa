function setValueSelect2(target, data) {
    $(target).empty().change();
    data.forEach((item) => {
        $(target).append(new Option(item.text, item.id, false, false));
    });
    $(target).val(null).change();
}
window.addEventListener("select2:init-value", function (event) {
    setValueSelect2(event.detail.target, event.detail.data);
});

window.addEventListener("select2:set-value", function (event) {
    $(event.detail.target).val(event.detail.data).change();
});
window.addEventListener("select2:set-value-server-side", function (event) {
    $(event.detail.target).val(event.detail.data).change();
    // create the option and append to Select2
    var option = new Option(
        event.detail.data.text,
        event.detail.data.id,
        true,
        true
    );
    $(event.detail.target).append(option).trigger("change");

    // manually trigger the `select2:select` event
    $(event.detail.target).trigger({
        type: "select2:select",
        params: {
            data: event.detail.data,
        },
    });
});

window.addEventListener("select2:reset-value", function (event) {
    $(event.detail.target).val(null).change();
});
