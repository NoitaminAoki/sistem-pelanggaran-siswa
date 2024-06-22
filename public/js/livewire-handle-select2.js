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

window.addEventListener("select2:reset-value", function (event) {
    $(event.detail.target).val(null).change();
});
