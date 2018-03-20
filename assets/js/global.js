$("#phoneNumber-fields-list :input").prop("disabled", true);

$(document).ready(function () {

    $("#phoneNumber-fields-list :input").prop("disabled", false);
    const removeIcon = '<a href="#" class="removePhoneNumberInput"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';

    $("#phoneNumber-fields-list").find(".phoneNumber").after(removeIcon);

    $('.add-another-collection-widget').click(function (e) {
        e.preventDefault();
        var list = $($(this).attr('data-list'));

        var counter = list.children().length;

        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);

        var newElement = $(list.attr('data-widget-phoneNumbers')).append(newWidget);

        newElement.children(":first").prop("placeholder", 'Phone number');
        newElement.append(removeIcon);
        newElement.appendTo(list);
    });

    $('#phoneNumber-fields-list').on('click', 'div a.removePhoneNumberInput', function(e) {
        e.preventDefault();
        $(this).parent().remove();
    });

});
