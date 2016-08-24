$("input[name=name]")
.focusin(function() {
    if($(this).val() == "jméno")
        $(this).val("");
})
.focusout(function() {
    if($(this).val() == "")
        $(this).val("jméno");
});

$("input[name=email]")
.focusin(function() {
    if($(this).val() == "e-mail")
        $(this).val("");
})
.focusout(function() {
    if($(this).val() == "")
        $(this).val("e-mail");
});

$("textarea")
.focusin(function() {
    if($(this).val() == "zpráva")
        $(this).val("");
})
.focusout(function() {
    if($(this).val() == "")
        $(this).val("zpráva");
});